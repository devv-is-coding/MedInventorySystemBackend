<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateCsrfToken
{
     protected $except = [
        'api/*', // Exclude all API routes from CSRF
    ];
}
