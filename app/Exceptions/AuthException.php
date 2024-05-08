<?php

namespace App\Exceptions;

class AuthException extends MyMissingApiException
{
    protected $my_code = 401;
}
