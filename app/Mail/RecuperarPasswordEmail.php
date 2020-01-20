<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecuperarPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject = 'Recuperación de contraseña - Tatan Express';

    public $codigo;
    public $nombre; 

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nombre, $codigo)
    {
        // obtener datos
        $this->nombre = $nombre;
        $this->codigo = $codigo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('correos.email_recuperar');
    }
}
