<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $subscriber;
    public $template;
    public $subject;
    public function __construct($subscriber,$template,$subject)
    {
        $this->subscriber=$subscriber;
        $this->template=$template;
        $this->subject=$subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subscriber=$this->subscriber;
        $template=$this->template;
        return $this->subject($this->subject)->view('subscription_verification_email',compact('subscriber','template'));
    }
}
