<?php

namespace App\Exceptions;

use Exception;

class FEException extends Exception
{

    protected $message;
    protected $detailed_message;
    protected $status;

    public function __construct($message, $exception_message, $status)
    {
        $this->message = $message;
        $this->detailed_message = $exception_message;
        $this->status = $status;
    }

    public function render()
    {
        return response()->json([
            'message' => $this->message,
            'detailed_message' => $this->detailed_message,
            'exception' => 'FEEXCEPTION'
        ], $this->status);
    }
}
