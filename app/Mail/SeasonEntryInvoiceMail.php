<?php

namespace App\Mail;

use App\Models\SeasonEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SeasonEntryInvoiceMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public SeasonEntry $entry)
    {
        $this->afterCommit();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: sprintf('%s registration invoice', $this->entry->season->name),
        );
    }

    public function content(): Content
    {
        $entry = $this->entry->loadMissing([
            'season',
            'teams.ruleset',
            'teams.secondRuleset',
            'knockoutRegistrations.knockout',
        ]);

        return new Content(
            markdown: 'mail.season-entry-invoice',
            with: [
                'entry' => $entry,
                'season' => $entry->season,
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(function (): string {
                $entry = $this->entry->loadMissing([
                    'season',
                    'teams.ruleset',
                    'teams.secondRuleset',
                    'knockoutRegistrations.knockout',
                ]);

                return Pdf::loadView('season-entry.invoice', [
                    'entry' => $entry,
                    'season' => $entry->season,
                ])->output();
            }, sprintf('%s-invoice.pdf', $this->entry->reference))
                ->withMime('application/pdf'),
        ];
    }
}
