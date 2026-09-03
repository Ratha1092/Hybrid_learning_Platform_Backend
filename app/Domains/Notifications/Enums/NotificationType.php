<?php

namespace App\Domains\Notifications\Enums;

enum NotificationType: string
{
    case INSTRUCTOR_VERIFICATION = 'instructor_verification';
    case ORDER = 'order';
    case PAYMENT = 'payment';
    case COURSE = 'course';
    case FINANCE = 'finance';
    case SYSTEM = 'system';
    case DISCUSSION = 'comment_reply';
}
