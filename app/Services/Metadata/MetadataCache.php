<?php

namespace App\Services\Metadata;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class MetadataCache
{
    protected string $cacheKey = 'glasses_metadata';
    protected int $ttl = 86400;

    public function remember(callable $callback): array
    {
        $hash = $this->buildHash();

        return Cache::remember($this->cacheKey . '_' . $hash, $this->ttl, $callback);
    }

    public function forget(): void
    {
        Cache::forget($this->cacheKey);
    }

    protected function buildHash(): string
    {
        $basePath = public_path('images/glasses');
        $files = File::allFiles($basePath);
        $timestamps = '';

        foreach ($files as $file) {
            $ext = strtolower($file->getExtension());
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                $timestamps .= $file->getMTime() . $file->getFilename();
            }
        }

        return md5($timestamps);
    }
}
