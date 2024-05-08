<?php

namespace App\Exceptions;

class DenyException extends MyMissingApiException
{
    protected $my_code = 403;
}
