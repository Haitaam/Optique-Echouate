<?php

namespace App\Services\Metadata;

use Illuminate\Support\Facades\Log;

class ColorAnalyzer
{
    protected array $colorMap = [
        'black' => ['hue' => [0, 360], 'sat_max' => 30, 'val_max' => 30],
        'gunmetal' => ['hue' => [0, 360], 'sat_max' => 25, 'val_min' => 30, 'val_max' => 55],
        'silver' => ['hue' => [0, 360], 'sat_max' => 20, 'val_min' => 55, 'val_max' => 95],
        'gold' => ['hue' => [35, 55], 'sat_min' => 40, 'val_min' => 50],
        'rose-gold' => ['hue' => [340, 20], 'sat_min' => 30, 'val_min' => 60],
        'tortoise' => ['hue' => [20, 45], 'sat_min' => 25, 'sat_max' => 60, 'val_min' => 25, 'val_max' => 65],
        'brown' => ['hue' => [15, 40], 'sat_min' => 25, 'val_max' => 55],
        'red' => ['hue' => [340, 20], 'sat_min' => 50, 'val_min' => 40],
        'blue' => ['hue' => [180, 260], 'sat_min' => 30, 'val_min' => 30],
        'green' => ['hue' => [80, 160], 'sat_min' => 30, 'val_min' => 30],
        'crystal' => ['hue' => [0, 360], 'sat_max' => 15, 'val_min' => 85],
        'white' => ['hue' => [0, 360], 'sat_max' => 10, 'val_min' => 90],
    ];

    public function analyze(string $imagePath): string
    {
        if (!extension_loaded('gd')) {
            return 'black';
        }

        try {
            $image = $this->loadImage($imagePath);
            if (!$image) {
                return 'black';
            }

            $pixels = $this->sampleCenter($image, 40);
            imagedestroy($image);

            if (empty($pixels)) {
                return 'black';
            }

            return $this->findDominantColor($pixels);
        } catch (\Throwable $e) {
            Log::warning("ColorAnalyzer failed for {$imagePath}: {$e->getMessage()}");
            return 'black';
        }
    }

    protected function loadImage(string $path): ?\GdImage
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($ext) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($path),
            'png' => @imagecreatefrompng($path),
            'webp' => @imagecreatefromwebp($path),
            'gif' => @imagecreatefromgif($path),
            default => null,
        };
    }

    protected function sampleCenter(\GdImage $image, int $sampleSize): array
    {
        $w = imagesx($image);
        $h = imagesy($image);

        $thumb = imagecreatetruecolor(80, 80);
        imagecopyresampled($thumb, $image, 0, 0, 0, 0, 80, 80, $w, $h);

        $pixels = [];
        $startX = (80 - $sampleSize) / 2;
        $startY = (80 - $sampleSize) / 2;

        for ($y = (int)$startY; $y < (int)($startY + $sampleSize); $y++) {
            for ($x = (int)$startX; $x < (int)($startX + $sampleSize); $x++) {
                $rgb = imagecolorat($thumb, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                if ($this->isBackground($r, $g, $b)) {
                    continue;
                }

                $pixels[] = ['r' => $r, 'g' => $g, 'b' => $b];
            }
        }

        imagedestroy($thumb);
        return $pixels;
    }

    protected function isBackground(int $r, int $g, int $b): bool
    {
        $total = $r + $g + $b;
        return $total > 720;
    }

    protected function rgbToHsv(int $r, int $g, int $b): array
    {
        $r /= 255;
        $g /= 255;
        $b /= 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $delta = $max - $min;

        $h = 0;
        $s = ($max == 0) ? 0 : $delta / $max;
        $v = $max;

        if ($delta != 0) {
            if ($r == $max) {
                $h = 60 * fmod(($g - $b) / $delta, 6);
            } elseif ($g == $max) {
                $h = 60 * (($b - $r) / $delta + 2);
            } else {
                $h = 60 * (($r - $g) / $delta + 4);
            }
        }

        if ($h < 0) $h += 360;

        return ['h' => $h, 's' => $s * 100, 'v' => $v * 100];
    }

    protected function findDominantColor(array $pixels): string
    {
        $scores = [];

        foreach ($this->colorMap as $colorName => $rules) {
            $count = 0;

            foreach ($pixels as $pixel) {
                $hsv = $this->rgbToHsv($pixel['r'], $pixel['g'], $pixel['b']);

                if ($this->matchesColor($hsv, $rules)) {
                    $count++;
                }
            }

            $scores[$colorName] = $count;
        }

        arsort($scores);
        return key($scores) ?: 'black';
    }

    protected function matchesColor(array $hsv, array $rules): bool
    {
        $h = $hsv['h'];
        $s = $hsv['s'];
        $v = $hsv['v'];

        $hueRange = $rules['hue'] ?? [0, 360];
        if ($hueRange[0] <= $hueRange[1]) {
            if ($h < $hueRange[0] || $h > $hueRange[1]) return false;
        } else {
            if ($h < $hueRange[0] && $h > $hueRange[1]) return false;
        }

        if (isset($rules['sat_min']) && $s < $rules['sat_min']) return false;
        if (isset($rules['sat_max']) && $s > $rules['sat_max']) return false;
        if (isset($rules['val_min']) && $v < $rules['val_min']) return false;
        if (isset($rules['val_max']) && $v > $rules['val_max']) return false;

        return true;
    }

    public function detectSecondary(string $imagePath): ?string
    {
        if (!extension_loaded('gd')) {
            return null;
        }

        try {
            $image = $this->loadImage($imagePath);
            if (!$image) return null;

            $pixels = $this->sampleCenter($image, 40);
            imagedestroy($image);

            if (empty($pixels)) return null;

            $primary = $this->findDominantColor($pixels);

            $scores = [];
            foreach ($this->colorMap as $colorName => $rules) {
                if ($colorName === $primary) continue;
                $count = 0;
                foreach ($pixels as $pixel) {
                    $hsv = $this->rgbToHsv($pixel['r'], $pixel['g'], $pixel['b']);
                    if ($this->matchesColor($hsv, $rules)) $count++;
                }
                $scores[$colorName] = $count;
            }

            arsort($scores);
            $top = key($scores);

            return ($top && $scores[$top] > count($pixels) * 0.15) ? $top : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
