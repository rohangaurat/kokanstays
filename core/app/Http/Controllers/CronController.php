<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Lib\CurlRequest;
use App\Models\CronJob;
use App\Models\CronJobLog;
use App\Models\Owner;
use App\Models\ReminderNotification;
use App\Models\Transaction;
use Carbon\Carbon;

class CronController extends Controller {
    public function cron() {
        $general            = gs();
        $general->last_cron = now();
        $general->save();

        $crons = CronJob::with('schedule');

        if (request()->alias) {
            $crons->where('alias', request()->alias);
        } else {
            $crons->where('next_run', '<', now())->where('is_running', Status::YES);
        }
        $crons = $crons->get();
        foreach ($crons as $cron) {
            $cronLog              = new CronJobLog();
            $cronLog->cron_job_id = $cron->id;
            $cronLog->start_at    = now();
            if ($cron->is_default) {
                $controller = new $cron->action[0];
                try {
                    $method = $cron->action[1];
                    $controller->$method();
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            } else {
                try {
                    CurlRequest::curlContent($cron->url);
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            }
            $cron->last_run = now();
            $cron->next_run = now()->addSeconds((int) $cron->schedule->interval);
            $cron->save();

            $cronLog->end_at = $cron->last_run;

            $startTime         = Carbon::parse($cronLog->start_at);
            $endTime           = Carbon::parse($cronLog->end_at);
            $diffInSeconds     = $startTime->diffInSeconds($endTime);
            $cronLog->duration = $diffInSeconds;
            $cronLog->save();
        }
        if (request()->target == 'all') {
            $notify[] = ['success', 'Cron executed successfully'];
            return back()->withNotify($notify);
        }
        if (request()->alias) {
            $notify[] = ['success', keyToTitle(request()->alias) . ' executed successfully'];
            return back()->withNotify($notify);
        }
    }

    public function sendUpcomingPaymentNotification() {
        $general = gs();
        $days    = $general->remind_before_days;
        $owners  = Owner::owner()->whereDate('expire_at', '<', now())->get();

        foreach ($owners as $owner) {
            $nextPaymentDate = Carbon::parse($owner->expire_at)->addDays($general->payment_before);

            $sendStatus = false;
            foreach ($days as $day) {
                $notificationDate = (clone $nextPaymentDate)->subDays($day);
                $isAlreadySend    = ReminderNotification::where('owner_id', $owner->id)->whereDate('send_at', now())->exists();

                if ($notificationDate->format('Y-m-d') == now()->format('Y-m-d') && !$isAlreadySend) {
                    $sendStatus = true;
                    break;
                }
            }

            if ($sendStatus) {
                notify($owner, 'BILL_PAYMENT_REMINDER', [
                    'payment_deadline' => $nextPaymentDate->format('d M, Y'),
                    'bill_per_month'   => showAmount($general->bill_per_month, currencyFormat: false),
                    'expired_at'       => showDateTime($owner->expire_at, 'd M, Y'),
                ]);

                $reminderNotification           = new ReminderNotification();
                $reminderNotification->owner_id = $owner->id;
                $reminderNotification->send_at  = now();
                $reminderNotification->save();
            }
        }
    }

    public function autoPaymentMonthlyBill() {
        $owners = Owner::active()->whereDate('expire_at', '<=', now())->where('auto_payment', 1)->get();

        $general = gs();
        foreach ($owners as $owner) {
            if ($owner->balance < $general->bill_per_month) {
                continue;
            }
            
            $owner->balance -= $general->bill_per_month;
            $owner->expire_at = Carbon::parse($owner->expire_at)->addMonth()->subDay();
            $owner->save();

            $transaction               = new Transaction();
            $transaction->owner_id     = $owner->id;
            $transaction->amount       = $general->bill_per_month;
            $transaction->post_balance = $owner->balance;
            $transaction->charge       = 0;
            $transaction->trx_type     = '-';
            $transaction->details      = 'Payment for ' . now()->format('F') . ' month';
            $transaction->trx          = getTrx();
            $transaction->remark       = 'monthly_bill_payment';
            $transaction->save();

            notify($owner, 'BILL_PAYMENT_COMPLETED', [
                'amount_per_month' => showAmount($transaction->amount, currencyFormat: false),
                'total_month'      => 1,
                'amount'           => showAmount($transaction->amount, currencyFormat: false),
                'charge'           => showAmount($transaction->charge, currencyFormat: false),
                'final_amount'     => showAmount($transaction->amount, currencyFormat: false),
                'expire_at'        => showDateTime($owner->expire_at, 'd M, Y'),
                'trx'              => $transaction->trx,
            ]);
        }
    }
}
