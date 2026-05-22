<?php

namespace App\Shared\Services;

use App\Features\Products\Models\Product;

class FaceCompatibilityService
{
    protected array $shapeRules = [
        'round' => [
            'best' => ['Square', 'Rectangle', 'Aviator'],
            'avoid' => ['Round'],
            'advice' => 'Les montures angulaires équilibrent les rondeurs du visage en ajoutant des lignes structurées.',
        ],
        'oval' => [
            'best' => ['Square', 'Rectangle', 'Aviator', 'Round', 'Cat-eye', 'Butterfly'],
            'avoid' => [],
            'advice' => 'Le visage ovale est polyvalent — presque toutes les formes de montures vous conviennent. Essayez des styles audacieux.',
        ],
        'square' => [
            'best' => ['Round', 'Aviator', 'Butterfly', 'Cat-eye'],
            'avoid' => ['Square', 'Rectangle'],
            'advice' => 'Les montures rondes et organiques adoucissent les angles forts d\'un visage carré.',
        ],
        'heart' => [
            'best' => ['Round', 'Aviator', 'Butterfly'],
            'avoid' => ['Rectangle', 'Square'],
            'advice' => 'Les montures qui s\'élargissent vers le bas équilibrent un front large et un menton étroit.',
        ],
        'diamond' => [
            'best' => ['Cat-eye', 'Aviator', 'Oval'],
            'avoid' => ['Rectangle', 'Square'],
            'advice' => 'Les montures avec des lignes douces et des détails sur le dessus mettent en valeur les pommettes saillantes.',
        ],
    ];

    public function analyze(Product $product, string $faceShape): array
    {
        $rules = $this->shapeRules[$faceShape] ?? $this->shapeRules['oval'];
        $shape = $product->frame_shape;

        $percentage = $this->calculatePercentage($shape, $rules);
        $explanation = $this->generateExplanation($product, $faceShape, $shape, $percentage, $rules['advice']);

        return [
            'percentage' => $percentage,
            'explanation' => $explanation,
            'shape' => $faceShape,
        ];
    }

    public function analyzeAll(Product $product): array
    {
        $results = [];
        foreach ($this->shapeRules as $shape => $rules) {
            $results[$shape] = $this->analyze($product, $shape);
        }
        return $results;
    }

    protected function calculatePercentage(string $productShape, array $rules): int
    {
        if (in_array($productShape, $rules['best'])) return rand(82, 98);
        if (in_array($productShape, $rules['avoid'])) return rand(25, 45);
        return rand(50, 75);
    }

    protected function generateExplanation(Product $product, string $faceShape, string $productShape, int $percentage, string $advice): string
    {
        $shapeLabels = [
            'round' => 'rond', 'oval' => 'ovale', 'square' => 'carré',
            'heart' => 'en cœur', 'diamond' => 'en diamant',
        ];

        $label = $shapeLabels[$faceShape] ?? $faceShape;

        return match (true) {
            $percentage >= 80 => "La monture {$productShape} est parfaite pour un visage {$label}. {$advice}",
            $percentage >= 50 => "La monture {$productShape} peut convenir à un visage {$label}, mais nous recommandons d'autres options. {$advice}",
            default => "Cette monture n'est pas idéale pour un visage {$label}. {$advice}",
        };
    }
}
