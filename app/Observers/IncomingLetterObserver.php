<?php

namespace App\Observers;

use App\Models\IncomingLetter;

class IncomingLetterObserver
{
    public function creating(IncomingLetter $incomingLetter)
    {
        $incomingLetter->letter_number = generativeNumber('IncomingLetter', $incomingLetter->incoming_type);
    }
}
