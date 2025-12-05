<?php

// app/Mail/RedactarCorreoMailable.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RedactarCorreoMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $periodo;
    public $comentarios;

    public function __construct($periodo, $comentarios)
    {
        $this->periodo = $periodo;
        $this->comentarios = $comentarios;
    }

    public function build()
    {
        return $this->subject('Comentarios de periodo')
            ->view('emails.redactar-correo');
    }
}
