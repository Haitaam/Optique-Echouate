<?php

namespace App\Services\Metadata;

use Illuminate\Support\Str;

class ProductAssembler
{
    protected array $brandPriceMap = [
        'Versace' => 320, 'Gucci' => 420, 'Prada' => 380, 'Tom Ford' => 380,
        'Dolce & Gabbana' => 350, 'Valentino' => 350, 'Balenciaga' => 400,
        'Victoria Beckham' => 360, 'Porsche Design' => 500,
        'Armani' => 250, 'Emporio Armani' => 260, 'Armani Style' => 240,
        'Hugo Boss' => 240, 'Michael Kors' => 170,
        'Ray-Ban' => 150, 'Oakley' => 140, 'Persol' => 300, 'Vogue' => 90,
        'Carrera' => 130, 'Police' => 110, 'Maui Jim' => 320,
        'Biaggi' => 220, 'Bolon' => 180,
    ];

    protected array $materialMap = [
        'titanium' => 'Titanium', 'acetate' => 'Acetate', 'metal' => 'Stainless Steel',
        'plastic' => 'Polycarbonate', 'gold' => 'Stainless Steel',
        'carbon' => 'Carbon Fiber', 'wood' => 'Wood', 'horn' => 'Acetate',
    ];

    protected array $descriptionTemplates = [
        "Monture %s en %s signée %s. Design raffiné alliant confort et élégance.",
        "Lunettes %s %s — %s. Une pièce d'exception pour un regard unique.",
        "%s — Monture %s en %s. Style contemporain et qualité premium.",
        "Sublimez votre regard avec cette monture %s %s signée %s.",
    ];

    public function assemble(array $scanData, array $folderData, array $filenameData, string $detectedColor, ?string $secondaryColor = null): array
    {
        $brand = $filenameData['brand'];
        $name = $filenameData['name'];
        $shape = $filenameData['frame_shape'];
        $tags = $filenameData['tags'];

        $slug = Str::slug($name . '-' . Str::random(6));
        $price = $this->inferPrice($brand, $shape, $folderData['is_luxury']);
        $material = $this->inferMaterial($tags, $name);
        $color = $detectedColor;
        $gender = $folderData['gender'];
        $isLuxury = $folderData['is_luxury'];

        $template = $this->descriptionTemplates[array_rand($this->descriptionTemplates)];
        $desc = sprintf($template, $shape, $color, $brand);

        if ($isLuxury) {
            $desc .= " Pièce exclusive de notre collection luxe.";
        } elseif (in_array($brand, ['Ray-Ban', 'Oakley', 'Persol'])) {
            $desc .= " Une référence signée {$brand}.";
        }

        return [
            'name' => $name,
            'slug' => $slug,
            'description' => $desc,
            'price' => $price,
            'image' => '/' . $scanData['url'],
            'brand' => $brand,
            'color' => $color,
            'secondary_color' => $secondaryColor,
            'frame_shape' => $shape,
            'gender' => ucfirst($gender),
            'material' => $material,
            'is_featured' => $this->isFeatured($brand),
            'is_luxury' => $isLuxury,
            'style_tags' => $tags,
        ];
    }

    protected function inferPrice(string $brand, string $shape, bool $isLuxury): float
    {
        $base = $this->brandPriceMap[$brand] ?? 150;

        $shapeMultiplier = match ($shape) {
            'Aviator', 'Cat-eye', 'Butterfly' => 1.15,
            'Wrap' => 1.2,
            default => 1.0,
        };

        $luxuryMultiplier = $isLuxury ? 1.8 : 1.0;

        return round($base * $shapeMultiplier * $luxuryMultiplier, 2);
    }

    protected function inferMaterial(array $tags, string $name): string
    {
        $combined = strtolower(implode(' ', $tags) . ' ' . $name);

        foreach ($this->materialMap as $keyword => $material) {
            if (Str::contains($combined, $keyword)) {
                return $material;
            }
        }

        $brandLuxury = ['Versace', 'Gucci', 'Prada', 'Tom Ford', 'Persol', 'Armani'];
        foreach ($brandLuxury as $b) {
            if (Str::contains($combined, strtolower($b))) {
                return 'Acetate';
            }
        }

        return 'Acetate';
    }

    protected function isFeatured(string $brand): bool
    {
        return in_array($brand, [
            'Ray-Ban', 'Oakley', 'Persol', 'Biaggi',
            'Gucci', 'Tom Ford', 'Versace',
        ]);
    }
}
