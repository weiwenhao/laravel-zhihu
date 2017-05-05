<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CaptchaEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $captcha_code; //该data属性可以在视图中被读取到

    /**
     * Create a new message instance.
     * @param $captcha_code
     */
    public function __construct($captcha_code)
    {

        $this->captcha_code = $captcha_code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from(env('MAIL_USERNAME'))
            ->view('email.captcha');
    }
}
