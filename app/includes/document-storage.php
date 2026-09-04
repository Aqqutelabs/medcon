<?php

function document_storage_config(): array
{
    $config = require __DIR__ . '/config.php';
    return $config['document_storage'];
}

function document_type_options(): array
{
    return [
        'passport_photograph' => 'Passport photograph',
        'international_passport' => 'International passport',
        'wassce_result' => 'WASSCE (WAEC/NECO)',
        'birth_certificate' => 'Birth certificate',
        'jamb_result' => 'JAMB result',
        'transcript' => 'Transcript (where applicable)',
        'supporting_document' => 'Other supporting document',
    ];
}

function required_document_types(): array
{
    return ['passport_photograph','international_passport','wassce_result','birth_certificate','jamb_result'];
}

function validate_application_document_upload(array $upload): array
{
    $config = document_storage_config();
    if (($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Choose a file to upload.');
    }
    $size = (int) ($upload['size'] ?? 0);
    if ($size < 1 || $size > (int) $config['max_bytes']) {
        throw new RuntimeException('The file must be no larger than 5MB.');
    }

    $allowed = [
        'application/pdf' => 'pdf',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    ];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($upload['tmp_name']);
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Only PDF, JPG, and PNG files are allowed.');
    }
    return ['mime_type'=>$mime,'extension'=>$allowed[$mime],'file_size'=>$size,'original_name'=>basename((string)$upload['name'])];
}

function store_application_document(array $upload, int $studentId, int $applicationId, ?array $validated = null): array
{
    $config = document_storage_config();
    if (($config['driver'] ?? '') !== 'local') {
        throw new RuntimeException('The configured document storage driver is not available.');
    }
    $validated ??= validate_application_document_upload($upload);

    $relativeDirectory = 'students/' . $studentId . '/applications/' . $applicationId;
    $directory = rtrim($config['root'], '/\\') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativeDirectory);
    if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
        throw new RuntimeException('Document storage is temporarily unavailable.');
    }
    $storedName = bin2hex(random_bytes(20)) . '.' . $validated['extension'];
    $storageKey = $relativeDirectory . '/' . $storedName;
    $destination = $directory . DIRECTORY_SEPARATOR . $storedName;
    if (!move_uploaded_file($upload['tmp_name'], $destination)) {
        throw new RuntimeException('The document could not be saved. Please try again.');
    }

    return [
        'storage_key' => $storageKey,
        'original_name' => $validated['original_name'],
        'mime_type' => $validated['mime_type'],
        'file_size' => $validated['file_size'],
    ];
}

function document_storage_path(string $storageKey): string
{
    $config = document_storage_config();
    $safeKey = str_replace(['..', '\\'], ['', '/'], ltrim($storageKey, '/'));
    return rtrim($config['root'], '/\\') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $safeKey);
}
