<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderSuccessfully extends Mailable
{
    use Queueable, SerializesModels;

    public $template;
    public $subject;
    public function __construct($template,$subject)
    {
        $this->template=$template;
        $this->subject=$subject;
    }

    public function build()
    {   $template = $this->template;
        $subject = $this->subject;
        return $this->subject($this->subject)->view('user.order_email_template', compact('template'));
    }
}
