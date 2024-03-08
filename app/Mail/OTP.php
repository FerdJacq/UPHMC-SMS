<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OTP extends Mailable
{
    use Queueable, SerializesModels;
    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $title =  "TWO-FACTOR AUTHENTICATION";
        return $this->markdown('emails.otp')
        ->from($address=env('MAIL_USERNAME','Admin'),$name = env('APP_NAME','Admin'))
        ->subject($title)
        ->with('data', $this->data);
    }
}
