<?php

/**
 * Loads email credentials from environment variables first, then from the
 * private XAMPP secrets directory. This file contains no credentials.
 */
function medcon_email_config(): array
{
    $privateConfigPath = 'C:/xampp/secrets/medcon-email.php';
    $fileConfig = is_file($privateConfigPath) ? require $privateConfigPath : [];

    if (!is_array($fileConfig)) {
        $fileConfig = [];
    }

    $environment = [
        'provider' => getenv('MEDCON_EMAIL_PROVIDER') ?: null,
        'api_key' => getenv('MEDCON_EMAIL_API_KEY') ?: null,
        'from_email' => getenv('MEDCON_EMAIL_FROM') ?: null,
        'from_name' => getenv('MEDCON_EMAIL_FROM_NAME') ?: null,
        'reply_to' => getenv('MEDCON_EMAIL_REPLY_TO') ?: null,
        'webhook_secret' => getenv('MEDCON_EMAIL_WEBHOOK_SECRET') ?: null,
    ];

    return array_replace(
        [
            'provider' => '',
            'api_key' => '',
            'from_email' => '',
            'from_name' => 'Medcon Edu',
            'reply_to' => 'info@medconedu.org',
            'webhook_secret' => '',
        ],
        $fileConfig,
        array_filter($environment, static fn ($value) => $value !== null)
    );
}

