<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SellerApplicationApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $storeName;
    public $username;
    public $password;
    public $loginUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(string $storeName, string $username, string $password, string $loginUrl)
    {
        $this->storeName = $storeName;
        $this->username = $username;
        $this->password = $password;
        $this->loginUrl = $loginUrl;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Seller Panel Credentials – Kutoot')
            ->view('emails.seller_application_approved');
    }
}

