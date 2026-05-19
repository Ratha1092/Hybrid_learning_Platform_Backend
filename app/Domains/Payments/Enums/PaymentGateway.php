<?php

namespace App\Domains\Payments\Enums;

enum PaymentGateway: string
{
    case Bakong = 'bakong';
    case Khqr = 'khqr';
    case Aba = 'aba';
    case Stripe = 'stripe';
    case Paypal = 'paypal';
}
