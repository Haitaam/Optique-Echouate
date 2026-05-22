<?php

namespace App\Services\Metadata;

class FolderClassifier
{
    protected array $categoryMap = [
        'optical-collection' => ['slug' => 'optical-collection', 'name' => 'Optical Collection'],
        'solar-collection' => ['slug' => 'solar-collection', 'name' => 'Solar Collection'],
        'clip-on-collection' => ['slug' => 'clip-on-collection', 'name' => 'Clip-On Collection'],
        'blue-light-protection' => ['slug' => 'blue-light-protection', 'name' => 'Blue Light Protection'],
    ];

    public function classify(string $relativePath): array
    {
        $parts = explode('/', trim($relativePath, '/'));
        $gender = null;
        $categorySlug = 'optical-collection';
        $isLuxury = false;

        foreach ($parts as $part) {
            $part = strtolower($part);

            if ($part === 'luxury') {
                $isLuxury = true;
            }

            if (in_array($part, ['men', 'women'])) {
                $gender = $part;
            }

            if ($part === 'sunglasses') {
                $categorySlug = 'solar-collection';
            }
        }

        if ($isLuxury && $gender === null) {
            $gender = 'unisex';
        }

        if ($gender === null) {
            $gender = 'unisex';
        }

        return [
            'gender' => $gender,
            'category_slug' => $categorySlug,
            'is_luxury' => $isLuxury,
        ];
    }
}
