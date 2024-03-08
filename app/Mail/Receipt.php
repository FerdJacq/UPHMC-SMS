<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Receipt extends Mailable
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
        $title =  env('APP_NAME','Admin') . " Transaction Receipt"."(".$this->data["or_number"].")";
        return $this->markdown('emails.online_receipt')
        ->from($address=env('MAIL_USERNAME','Admin'),$name = env('APP_NAME','Admin'))
        ->subject($title)
        ->with('data', $this->data);
    }
}
