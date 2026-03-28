<?php

namespace App\Exceptions;

use App\Models\Result;
use RuntimeException;

class StaleResultDraftException extends RuntimeException
{
    public function __construct(
        public readonly Result $result,
        string $message = 'Another editor updated this result before your changes could be saved.',
    ) {
        parent::__construct($message);
    }
}
