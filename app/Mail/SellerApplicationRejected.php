<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SellerApplicationRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $storeName;
    public $reason;

    /**
     * Create a new message instance.
     */
    public function __construct(string $storeName, string $reason)
    {
        $this->storeName = $storeName;
        $this->reason = $reason;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Seller Application Rejected – Kutoot')
            ->view('emails.seller_application_rejected');
    }
}

