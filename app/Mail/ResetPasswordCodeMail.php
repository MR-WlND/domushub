<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $code)
    {
    }

    public function build(): self
    {
        return $this->subject('Mã xác nhận đặt lại mật khẩu DomusHub')
            ->view('emails.reset-password-code')
            ->with([
                'code' => $this->code,
            ]);
    }
}
