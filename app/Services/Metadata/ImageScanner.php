<?php

namespace App\Services\Metadata;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImageScanner
{
    protected array $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    public function scan(): array
    {
        $basePath = public_path('images/glasses');
        if (!File::exists($basePath)) {
            return [];
        }

        $realBase = realpath($basePath);
        if (!$realBase) {
            return [];
        }

        $files = File::allFiles($realBase);
        $images = [];
        $seenPaths = [];

        foreach ($files as $file) {
            $ext = strtolower($file->getExtension());
            if (!in_array($ext, $this->allowedExtensions)) {
                continue;
            }

            $fullPath = $file->getPathname();
            $normalizedPath = str_replace('\\', '/', $fullPath);

            if (isset($seenPaths[$normalizedPath])) {
                continue;
            }
            $seenPaths[$normalizedPath] = true;

            $baseName = $file->getBasename('.' . $file->getExtension());

            $relative = $this->relativePath($fullPath, $realBase);

            $images[] = [
                'pathname' => $fullPath,
                'filename' => $file->getFilename(),
                'basename' => $baseName,
                'extension' => $ext,
                'relative_path' => $relative,
                'folder' => dirname($relative),
                'url' => 'images/glasses/' . ltrim(str_replace('\\', '/', $relative), '/'),
                'size' => $file->getSize(),
                'mtime' => $file->getMTime(),
            ];
        }

        return $images;
    }

    protected function relativePath(string $pathname, string $basePath): string
    {
        $normalized = str_replace('\\', '/', $pathname);
        $base = str_replace('\\', '/', $basePath);
        $result = Str::after($normalized, $base . '/');
        return $result ?: basename($normalized);
    }
}
