<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserForgetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $template;
    public $subject;
    public $user;
    public function __construct($template,$subject,$user)
    {
        $this->template=$template;
        $this->subject=$subject;
        $this->user=$user;
    }

    public function build()
    {   $template = $this->template;
        $subject = $this->subject;
        $user = $this->user;
        return $this->subject($this->subject)->view('user_reset_mail', compact('template','user'));
    }
}
