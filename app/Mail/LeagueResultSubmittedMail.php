<?php

namespace App\Mail;

use App\Models\Result;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeagueResultSubmittedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Result $result)
    {
        $this->afterCommit();
        $this->onQueue('emails');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: sprintf(
                'League result submitted: %s %d-%d %s',
                $this->result->home_team_name,
                (int) $this->result->home_score,
                (int) $this->result->away_score,
                $this->result->away_team_name,
            ),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.league-result-submitted',
        );
    }
}
