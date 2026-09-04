<?php

/**
 * Verify a SendByte webhook signature without exposing the signing secret.
 *
 * SendByte signs: "<unix timestamp>.<raw request body>" and sends the result
 * as `sendbyte-signature: t=<timestamp>,v1=<hex hmac-sha256>`.
 */
function medcon_verify_sendbyte_webhook(
    string $rawBody,
    string $signatureHeader,
    string $secret,
    int $toleranceSeconds = 300
): bool {
    if ($rawBody === '' || $signatureHeader === '' || $secret === '') {
        return false;
    }

    $parts = [];
    foreach (explode(',', $signatureHeader) as $part) {
        $pair = explode('=', trim($part), 2);
        if (count($pair) === 2) {
            $parts[$pair[0]] = $pair[1];
        }
    }

    $timestamp = $parts['t'] ?? '';
    $providedSignature = $parts['v1'] ?? '';

    if (!ctype_digit($timestamp) || !preg_match('/^[a-f0-9]{64}$/i', $providedSignature)) {
        return false;
    }

    if (abs(time() - (int) $timestamp) > $toleranceSeconds) {
        return false;
    }

    $expectedSignature = hash_hmac('sha256', $timestamp . '.' . $rawBody, $secret);

    return hash_equals($expectedSignature, strtolower($providedSignature));
}

