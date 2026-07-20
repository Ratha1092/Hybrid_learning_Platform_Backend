<?php

namespace App\Exceptions;

use App\Domains\Users\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AccountSuspendedException extends HttpException
{
    public const ERROR_CODE = 'account_suspended';

    public function __construct(string $message = User::SUSPENDED_MESSAGE)
    {
        parent::__construct(403, $message);
    }
}
