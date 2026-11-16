<?php

return [
    // Keep disabled until a real sender and mail transport have been verified.
    'email_enabled' => (bool) env('RTHQUICK_EMAIL_NOTIFICATIONS_ENABLED', false),
    'support_email' => env('RTHQUICK_SUPPORT_EMAIL'),
    'support_phone' => env('RTHQUICK_SUPPORT_PHONE'),
    // Only set after confirming this number has an active WhatsApp inbox.
    'support_whatsapp' => env('RTHQUICK_SUPPORT_WHATSAPP'),
];
