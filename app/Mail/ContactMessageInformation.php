<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessageInformation extends Mailable
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
    {
        $template = $this->template;
        $subject = $this->subject;
        return $this->subject($subject)->view('contact_message', compact('template'));
    }
}
