<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Traits\SupportTicketManager;

class TicketController extends Controller
{
    use SupportTicketManager;

    public function __construct()
    {
        parent::__construct();
        $this->user = authOwner();
        $this->activeTemplate = null;
        $this->redirectLink = 'owner.ticket.view';
        $this->layout = null;
        $this->userType = 'owner';
        $this->column   = 'owner_id';
        $this->hasTemplate  = false;
    }

    public function supportTicket()
    {
        $pageTitle = 'Support Tickets';
        $items     = SupportTicket::where('owner_id', authOwner()->id)->orderBy('id', 'desc')->with('user')->paginate(getPaginate());
        return view('owner.support.tickets', compact('items', 'pageTitle'));
    }

    public function openSupportTicket()
    {
        $pageTitle = "Open Ticket";
        return view('owner.support.create', compact('pageTitle'));
    }
}
