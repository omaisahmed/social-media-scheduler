<?php

declare(strict_types=1);

return [
    'disk' => env('MEDIA_STORAGE_DISK', 'public'),
    'max_size' => (int) env('MAX_MEDIA_SIZE', 51200),
];
