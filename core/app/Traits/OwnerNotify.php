<?php

namespace App\Traits;

use App\Constants\Status;

trait OwnerNotify
{
    public static function notifyToOwner()
    {
        return [
            'allOwners'                => 'All Vendors',
            'selectedOwners'           => 'Selected Vendors',
            'withBalance'              => 'With Balance Vendors',
            'emptyBalanceOwners'       => 'Empty Balance Vendors',
            'twoFaDisableOwners'       => '2FA Disable Vendor',
            'twoFaEnableOwners'        => '2FA Enable Vendor',
            'hasDepositedOwners'       => 'Deposited Vendors',
            'notDepositedOwners'       => 'Not Deposited Vendors',
            'pendingDepositedOwners'   => 'Pending Deposited Vendors',
            'rejectedDepositedOwners'  => 'Rejected Deposited Vendors',
            'topDepositedOwners'       => 'Top Deposited Vendors',
            'hasWithdrawOwners'        => 'Withdraw Vendors',
            'pendingWithdrawOwners'    => 'Pending Withdraw Vendors',
            'rejectedWithdrawOwners'   => 'Rejected Withdraw Vendors',
            'pendingTicketOwner'        => 'Pending Ticket Vendors',
            'answerTicketOwner'         => 'Answer Ticket Vendors',
            'closedTicketOwner'         => 'Closed Ticket Vendors',
            'notLoginOwners'           => 'Last Few Days Not Login Vendors',
        ];
    }

    public function scopeSelectedOwners($query)
    {
        return $query->whereIn('id', request()->owner ?? []);
    }

    public function scopeAllOwners($query)
    {
        return $query;
    }

    public function scopeEmptyBalanceOwners($query)
    {
        return $query->where('balance', '<=', 0);
    }

    public function scopeTwoFaDisableOwners($query)
    {
        return $query->where('ts', Status::DISABLE);
    }

    public function scopeTwoFaEnableOwners($query)
    {
        return $query->where('ts', Status::ENABLE);
    }

    public function scopeHasDepositedOwners($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->successful();
        });
    }

    public function scopeNotDepositedOwners($query)
    {
        return $query->whereDoesntHave('deposits', function ($q) {
            $q->successful();
        });
    }

    public function scopePendingDepositedOwners($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->pending();
        });
    }

    public function scopeRejectedDepositedOwners($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->rejected();
        });
    }

    public function scopeTopDepositedOwners($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->successful();
        })->withSum(['deposits' => function ($q) {
            $q->successful();
        }], 'amount')->orderBy('deposits_sum_amount', 'desc')->take(request()->number_of_top_deposited_user ?? 10);
    }

    public function scopeHasWithdrawOwners($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->approved();
        });
    }

    public function scopePendingWithdrawOwners($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->pending();
        });
    }

    public function scopeRejectedWithdrawOwners($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->rejected();
        });
    }

    public function scopePendingTicketOwner($query)
    {
        return $query->whereHas('tickets', function ($q) {
            $q->whereIn('status', [Status::TICKET_OPEN, Status::TICKET_REPLY]);
        });
    }

    public function scopeClosedTicketOwner($query)
    {
        return $query->whereHas('tickets', function ($q) {
            $q->where('status', Status::TICKET_CLOSE);
        });
    }

    public function scopeAnswerTicketOwner($query)
    {
        return $query->whereHas('tickets', function ($q) {
            $q->where('status', Status::TICKET_ANSWER);
        });
    }

    public function scopeNotLoginOwners($query)
    {
        return $query->whereDoesntHave('loginLogs', function ($q) {
            $q->whereDate('created_at', '>=', now()->subDays(request()->number_of_days ?? 10));
        });
    }
}
