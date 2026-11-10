<?php

namespace App\Mail;

use App\Models\Plato;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Aviso a la cocina de que hay un plato nuevo. ShouldQueue: se envía en segundo plano.
 */
class PlatoCreado extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Plato $plato)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Nuevo plato en la carta: ' . $this->plato->nombre);
    }

    public function content(): Content
    {
        return new Content(view: 'mail.plato-creado');
    }
}
