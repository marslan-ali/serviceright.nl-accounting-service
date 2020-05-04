<?php

namespace App\Listeners\Sale;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\Sale\InvoiceCreated as Event;
use Carbon\Carbon;
use Moneybird;
use Log;

class InvoiceCreated
{

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Event $event)
    {
        $invoice = $event->invoice;
        // $company = $invoice->company;
        // dd($company);
        $name = explode(' ', $invoice->contact->name);

        $invoiceContact = Moneybird::contact();
        $invoiceContact->firstname = isset($name[0]) ? $name[0] : '';
        $invoiceContact->lastname = isset($name[1]) ? $name[1] : '';
        $invoiceContact->company_name = $invoice->company->domain;

        $contact_id = $invoiceContact->save()->id;

        $sale = Moneybird::salesInvoice();
        $sale->contact_id = $contact_id;
        $sale->currency_code = $invoice->currency;
        $sale->invoiced_date = Carbon::parse($invoice->invoiced_at)->format('Y-m-d');
        $sale->firstname = 'Invoice First Name';
        
        
        //@todo need to work on invoice items at the moment proof of concept
        $invoiceDetail = Moneybird::salesInvoiceDetail();
        $invoiceDetail->amount = '1 x'; //hard coded for now. Need to work on invoice items 
        $invoiceDetail->price = $invoice->amount;
        $invoiceDetail->description = "Todo need to work on invouce items";

        $sale->details = [$invoiceDetail];
        $sale->contact = $invoiceContact;
        $sale->save();

        Log::info("Money Bird Invoice Generated ID = ".$sale->id);
    }
}
