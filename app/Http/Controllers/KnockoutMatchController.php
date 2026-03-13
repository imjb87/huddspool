<?php

namespace App\Http\Controllers;

use App\Models\KnockoutMatch;
use Illuminate\View\View;

class KnockoutMatchController extends Controller
{
    public function submit(KnockoutMatch $match): View
    {
        $this->authorize('openSubmission', $match);

        return view('knockouts.submit-result', [
            'match' => $match->load('round.knockout', 'homeParticipant', 'awayParticipant', 'forfeitParticipant'),
        ]);
    }
}
