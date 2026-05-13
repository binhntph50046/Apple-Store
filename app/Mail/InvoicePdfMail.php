<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoicePdfMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $pdfContent;

    public function __construct($invoice, $pdfContent)
    {
        $this->invoice = $invoice;
        $this->pdfContent = $pdfContent;
    }

    public function build()
    {
        return $this->subject('Hóa đơn của bạn')
            ->view('emails.invoice')
            ->with(['invoice' => $this->invoice])
            ->attachData($this->pdfContent, 'hoa-don.pdf', ['mime' => 'application/pdf']);
    }
} 