<?php

namespace App\Observers;

use App\Models\OutgoingLetter;

class OutgoingLetterObserver
{
    public function creating(OutgoingLetter $outgoingLetter)
    {
        $outgoingLetter->letter_number = generativeNumber('OutgoingLetter', $outgoingLetter->outgoing_type);
    }
}
