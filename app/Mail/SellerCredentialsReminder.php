<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SellerCredentialsReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $storeName;
    public $username;
    public $loginUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(string $storeName, string $username, string $loginUrl)
    {
        $this->storeName = $storeName;
        $this->username = $username;
        $this->loginUrl = $loginUrl;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Seller Login Details – Kutoot')
            ->view('emails.seller_credentials_reminder');
    }
}
