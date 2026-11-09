<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/*',
        'steadfast/webhook',
        'steadfast/hook',
        'steadfast-webhook',
        'woocommerce/webhook',
        'woocommerce/webl',
        'woocommerce/webhooks',
        'woocommerce/hook',
    ];
}
