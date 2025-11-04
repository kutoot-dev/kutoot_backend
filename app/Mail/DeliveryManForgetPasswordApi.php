<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeliveryManForgetPasswordApi extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $deliveryMan;
    protected $message;
    public $subject;
    public function __construct($deliveryMan,$message,$subject)
    {
        $this->deliveryMan=$deliveryMan;
        $this->subject=$subject;
        $this->message=$message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $template=$this->subject;
        $deliveryMan=$this->deliveryMan;
        return $this->subject($this->subject)->view('deliveryman.delivery_man_reset_api_mail',compact('deliveryMan','template'));
    }
}
