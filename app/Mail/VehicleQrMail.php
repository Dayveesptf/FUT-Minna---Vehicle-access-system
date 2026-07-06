<?php

namespace App\Mail;

use App\Models\Vehicle;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VehicleQrMail extends Mailable
{
    use Queueable, SerializesModels;

    public Vehicle $vehicle;

    public function __construct(Vehicle $vehicle)
    {
        $this->vehicle = $vehicle;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Vehicle Access QR Code — ' . $this->vehicle->plate_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vehicle-qr',
        );
    }

    public function attachments(): array
    {
        $qr = $this->vehicle->activeQrCode;

        $svg = (string) QrCode::format('svg')->size(400)->margin(1)->generate($qr->encrypted_payload);

        return [
            Attachment::fromData(fn () => $svg, 'vehicle-' . $this->vehicle->plate_number . '-qr.svg')
                ->withMime('image/svg+xml'),
        ];
    }
}
