<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeliveryManForgetPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
 
    public $deliveryMan;
    public $url;
    public function __construct($deliveryMan, $url)
    {
        
        $this->deliveryMan=$deliveryMan;
        $this->url=$url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {   
        $deliveryMan = $this->deliveryMan;
        $url = $this->url;
        return $this->subject($this->subject)->view('deliveryman.delivery_man_reset_mail', compact('deliveryMan', 'url'));
    }
}
