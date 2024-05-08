<?php

namespace App\Exceptions;

use Exception;

/**
 * @property $my_code 4**
 */
class MyMissingApiException extends Exception
{
    protected $my_code;
    protected $details = [];

    public function __construct($message = "", $details = [], $previous = null)
    {
        parent::__construct($message, $this->my_code, $previous);
        $this->details = $details;
    }

    public function getDetails()
    {
        return $this->details;
    }

}
