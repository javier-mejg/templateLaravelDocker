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
    public $nombreUsuario;
    public $idUsuario;
    public $correoUsuario;

    public function __construct($periodo, $comentarios, $nombreUsuario, $idUsuario, $correoUsuario)
    {
        $this->periodo = $periodo;
        $this->comentarios = $comentarios;
        $this->nombreUsuario = $nombreUsuario;
        $this->idUsuario = $idUsuario;
        $this->correoUsuario = $correoUsuario;
    }

    public function build()
    {
        return $this->subject('Comentarios de periodo')
            // Opcional, pero muy útil: que la respuesta vaya al alumno
            ->replyTo($this->correoUsuario, $this->nombreUsuario)
            ->view('emails.redactar-correo');
    }
}
