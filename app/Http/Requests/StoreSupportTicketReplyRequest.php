<?php

namespace App\Http\Requests;

use App\Models\SupportTicket;
use Illuminate\Foundation\Http\FormRequest;

class StoreSupportTicketReplyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');

        return $ticket instanceof SupportTicket
            && ! $ticket->isClosing()
            && $ticket->user_id === $this->user()?->getKey();
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:5000'],
        ];
    }
}
