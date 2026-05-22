<?php

namespace App\Features\Quiz\Services;

use App\Features\Products\Models\Product;
use App\Shared\Services\FaceCompatibilityService;

class QuizRecommendationService
{
    protected array $weights = [
        'frame_shape' => 30,
        'color' => 20,
        'material' => 20,
        'gender' => 15,
        'style_lifestyle' => 15,
    ];

    public function recommend(array $answers): array
    {
        $products = Product::all()->filter(fn($p) => $p->imageExists());
        $scored = [];

        foreach ($products as $product) {
            $score = 0;
            $matches = [];

            $shapeScore = $this->matchShape($answers['shape'], $product->frame_shape);
            $score += $shapeScore * ($this->weights['frame_shape'] / 100);
            if ($shapeScore > 0) $matches[] = "La forme de la monture correspond à votre visage";

            $colorScore = $this->matchColor($answers['color'], $product->color);
            $score += $colorScore * ($this->weights['color'] / 100);
            if ($colorScore > 0) $matches[] = "La couleur complète votre style";

            $materialScore = $this->matchMaterial($answers['material'], $product->material);
            $score += $materialScore * ($this->weights['material'] / 100);
            if ($materialScore > 0) $matches[] = "Le matériau correspond à vos préférences";

            $genderScore = $this->matchGender($answers['style'], $product->gender);
            $score += $genderScore * ($this->weights['gender'] / 100);
            if ($genderScore > 0) $matches[] = "Le style correspond à votre genre";

            $lifestyleScore = $this->matchLifestyle($answers['lifestyle'], $product);
            $score += $lifestyleScore * ($this->weights['style_lifestyle'] / 100);
            if ($lifestyleScore > 0) $matches[] = "S'adapte à votre mode de vie actif";

            $percentage = round($score * 100);

            if ($percentage > 0) {
                $faceData = $product->faceCompatibility($answers['shape']);
                $scored[] = [
                    'product' => $product,
                    'percentage' => min($percentage, 99),
                    'matches' => $matches,
                    'reasoning' => $this->generateReasoning($answers, $product, $percentage),
                    'face' => $faceData,
                ];
            }
        }

        usort($scored, fn($a, $b) => $b['percentage'] <=> $a['percentage']);

        return array_slice($scored, 0, 5);
    }

    protected function matchShape(string $answer, string $productShape): float
    {
        $map = [
            'round' => ['Round'],
            'square' => ['Square', 'Rectangle'],
            'oval' => ['Aviator', 'Butterfly'],
            'heart' => ['Cat-eye', 'Butterfly'],
            'diamond' => ['Aviator', 'Cat-eye'],
        ];

        return in_array($productShape, $map[$answer] ?? []) ? 1.0 : 0.3;
    }

    protected function matchColor(string $answer, string $productColor): float
    {
        $preferences = [
            'gold' => ['Gold', 'Rose Gold'],
            'silver' => ['Silver', 'Gunmetal'],
            'black' => ['Black', 'Matte Black'],
            'tortoise' => ['Tortoise'],
            'crystal' => ['Crystal', 'White'],
            'blue' => ['Blue'],
        ];

        $preferred = $preferences[$answer] ?? [];
        $colorLower = strtolower($productColor);

        foreach ($preferred as $p) {
            if (str_contains($colorLower, strtolower($p))) return 1.0;
        }

        return 0.4;
    }

    protected function matchMaterial(string $answer, string $productMaterial): float
    {
        $map = [
            'titanium' => ['Titanium'],
            'acetate' => ['Acetate'],
            'stainless' => ['Stainless Steel'],
            'polycarbonate' => ['Polycarbonate'],
            'metal' => ['Titanium', 'Stainless Steel'],
        ];

        return in_array($productMaterial, $map[$answer] ?? []) ? 1.0 : 0.3;
    }

    protected function matchGender(string $answer, string $productGender): float
    {
        if ($productGender === 'Unisex') return 1.0;

        $map = [
            'men' => 'Men',
            'women' => 'Women',
            'unisex' => 'Unisex',
        ];

        return ($map[$answer] ?? null) === $productGender ? 1.0 : 0.2;
    }

    protected function matchLifestyle(string $answer, \App\Features\Products\Models\Product $product): float
    {
        $digital = ['blue-light-protection', 'optical-collection'];
        $active = ['solar-collection'];
        $fashion = ['solar-collection'];
        $work = ['optical-collection'];

        $categories = $product->categories->pluck('slug')->toArray();

        return match ($answer) {
            'digital' => (int)!empty(array_intersect($categories, $digital)),
            'active' => (int)!empty(array_intersect($categories, $active)),
            'fashion' => (int)!empty(array_intersect($categories, $fashion)),
            'professional' => (int)!empty(array_intersect($categories, $work)),
            default => 0.5,
        };
    }

    protected function generateReasoning(array $answers, Product $product, int $percentage): string
    {
        return "La {$product->name} en {$product->color} correspond à votre visage de forme {$answers['shape']} " .
               "avec son design {$product->frame_shape}. Fabriquée en {$product->material}, " .
               "elle s'aligne avec votre préférence pour les matériaux {$answers['material']}. " .
               "Cette monture atteint {$percentage}% de compatibilité avec votre profil de style.";
    }
}
