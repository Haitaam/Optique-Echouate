<?php

namespace App\Services\Metadata;

use Illuminate\Support\Str;

class FilenameParser
{
    protected array $brandPrefixes = [
        'AS' => 'Armani Style',
        'BG' => 'Biaggi',
        'BL' => 'Bolon',
        'EA' => 'Emporio Armani',
        'XC' => 'XC Vision',
        'VC' => 'Versace Collection',
        'YC' => 'YC Collection',
        'EH' => 'EH Collection',
        'SS' => 'SS Collection',
        'MK' => 'Michael Kors',
        'TF' => 'Tom Ford',
        'VB' => 'Victoria Beckham',
        'PD' => 'Porsche Design',
        'BB' => 'Balenciaga',
        'VN' => 'Valentino',
    ];

    protected array $brandKeywords = [
        'versace' => 'Versace',
        'tom ford' => 'Tom Ford',
        'biaggi' => 'Biaggi',
        'valentino' => 'Valentino',
        'porsche' => 'Porsche Design',
        'balenciaga' => 'Balenciaga',
        'victoria beckham' => 'Victoria Beckham',
        'armani' => 'Armani',
        'boss' => 'Hugo Boss',
        'ray-ban' => 'Ray-Ban',
        'rayban' => 'Ray-Ban',
        'oakley' => 'Oakley',
        'persol' => 'Persol',
        'vogue' => 'Vogue',
        'carrera' => 'Carrera',
        'prada' => 'Prada',
        'dolce' => 'Dolce & Gabbana',
        'gabbana' => 'Dolce & Gabbana',
        'gucci' => 'Gucci',
        'police' => 'Police',
        'maui jim' => 'Maui Jim',
        'michael kors' => 'Michael Kors',
    ];

    protected array $shapeKeywords = [
        'round' => 'Round',
        'oval' => 'Oval',
        'square' => 'Square',
        'rectangle' => 'Rectangle',
        'aviator' => 'Aviator',
        'navigator' => 'Aviator',
        'cat-eye' => 'Cat-eye',
        'cateye' => 'Cat-eye',
        'butterfly' => 'Butterfly',
        'wrap' => 'Wrap',
        'maxi' => 'Square',
        'biggie' => 'Square',
        'shield' => 'Wrap',
        'clubmaster' => 'Rectangle',
        'wayfarer' => 'Square',
        'hexagonal' => 'Rectangle',
    ];

    protected array $stopWords = [
        'lunettes', 'de', 'vue', 'solaire', 'optical', 'eyewear',
        'glasses', 'sunglasses', 'hd', 'image', 'photo', 'model',
        'by', 'for', 'the', 'with', 'and', 'noir', 'black',
    ];

    protected array $suffixPatterns = [
        '/-\d{3,4}x\d{3,4}(-\d+)?(-\d+)?/',  // -1536x1536, -1080x1080-1
        '/-\d{1,2}x\d{1,2}/',
        '/-hd-\d+/', '/-hd$/',
        '/-hq-\d+/', '/-hq$/',
        '/-id-\d+/', '/-id$/',
        '/-1-\d+x\d+/',
        '/\s+-1-\s*$/',                     // trailing " -1-" from parens
        '/-\d{1,2}-$/',                     // trailing -1-, -2-
        '/-\d+$/',                          // trailing number after model
        '/_O\d{3,6}[^-]*/',                 // Versace _O2290..., _O4506U
        '/\-[A-Z]\d{5,}[^-]*/',             // -OGB18754, -O10028761
        '/^90_/',                           // leading 90_
        '/\b90\b/',                         // standalone 90 left from 90_ prefix
        '/^[A-Z]\d{4,6}[-_]/',              // model code prefix like O2290-
        '/[_-]ONUL[_-]?\d*/',               // Versace ONUL code
        '/\bONUL\b/i',                      // standalone ONUL
        '/\b(sunglass|sunglasses|glasses|eyewear|optical)\b/i',
        '/\b(lunettes|de\s+vue|solaire)\b/i',
        '/-Versace$/i',                     // trailing -Versace
        '/\b(Black|Grey|Silver|Gold)\b/i',  // color names in filename
        '/\b(Verres|Interchangeables)\b/i',
        '/\b(Lenses)\b/i',
        '/\b(Sunglass|Sung|Ve|Vc|Op|D)\b/i', // leftover fragments
        '/^-/',                               // leading hyphens
    ];

    protected array $colorCodePatterns = [
        '/[-_][A-Z]\d{1,3}([-_]\d+)?$/',    // -C01, -C3-1, C01-2
        '/[-_]\d{3}[a-z]{0,2}\d*$/',         // -001, -001a, -807ir
        '/[-_]\d{3}[A-Z]\d*$/',               // -01E, -J5G
    ];

    protected array $modelCodePatterns = [
        '/\b\d{7,}[a-z]?\b/i',               // long digit sequences (IDs)
        '/\b[a-f0-9]{5,}\b/i',               // hex strings (UUID fragments)
        '/\b\d{2,}[a-z]{3,}\d{2,}\b/i',      // mixed junk
    ];

    protected array $uuidPatterns = [
        '/[-_][0-9a-f]{8,9}[-_][0-9a-f]{4,5}[-_][0-9a-f]{4,5}[-_][0-9a-f]{4,5}[-_][0-9a-f]{8,12}/i',
        '/[0-9a-f]{8}[-_][0-9a-f]{4}[-_][0-9a-f]{4}[-_][0-9a-f]{4}[-_][0-9a-f]{8,12}/i',
    ];

    public function parse(string $filename, string $basename): array
    {
        $brand = $this->detectBrand($filename, $basename);
        $cleanName = $this->cleanName($basename, $brand);
        $shape = $this->detectShape($cleanName, $filename);
        $tags = $this->extractTags($cleanName);
        $modelName = $this->buildModelName($cleanName, $brand);

        return [
            'brand' => $brand,
            'name' => $modelName,
            'frame_shape' => $shape,
            'tags' => $tags,
        ];
    }

    protected function detectBrand(string $filename, string $basename): string
    {
        $lower = strtolower($filename);
        $lowerBase = strtolower($basename);

        $normalized = str_replace('-', ' ', $lower);

        foreach ($this->brandKeywords as $keyword => $brand) {
            if (Str::contains($normalized, $keyword)) {
                return $brand;
            }
        }

        foreach ($this->brandKeywords as $keyword => $brand) {
            if (Str::contains($lower, $keyword) || Str::contains($lowerBase, $keyword)) {
                return $brand;
            }
        }

        $prefix = strtoupper(substr($basename, 0, 2));
        if (isset($this->brandPrefixes[$prefix])) {
            return $this->brandPrefixes[$prefix];
        }

        $prefix3 = strtoupper(substr($basename, 0, 3));
        if (isset($this->brandPrefixes[$prefix3])) {
            return $this->brandPrefixes[$prefix3];
        }

        foreach ($this->brandPrefixes as $code => $brand) {
            $codeLower = strtolower($code);
            if (preg_match('/\b' . preg_quote($codeLower, '/') . '\d/', $lower)) {
                return $brand;
            }
        }

        if (preg_match('/^(\d+)_O\d/', $basename)) {
            return 'Versace';
        }

        $fallback = Str::title($prefix) . ' Collection';

        // If fallback is numeric like "28 Collection", use simply "Collection"
        if (preg_match('/^\d+\s+Collection$/', $fallback)) {
            $fallback = 'Luxe';
        }

        return $fallback;
    }

    protected function cleanName(string $basename, string $brand): string
    {
        $name = $basename;

        // 1. Remove UUIDs first
        foreach ($this->uuidPatterns as $pattern) {
            $name = preg_replace($pattern, '', $name);
        }

        // 2. Split CamelCase BEFORE pattern matching
        //    (so "GrecaMaxiSunglasses" becomes "Greca Maxi Sunglasses")
        $name = preg_replace('/([a-z])([A-Z])/', '$1 $2', $name);
        $name = preg_replace('/([A-Z]{2,})([A-Z][a-z])/', '$1 $2', $name);

        // 3. Remove suffix/versioning patterns
        foreach ($this->suffixPatterns as $pattern) {
            $name = preg_replace($pattern, ' ', $name);
        }

        // 4. Remove color codes
        foreach ($this->colorCodePatterns as $pattern) {
            $name = preg_replace($pattern, ' ', $name);
        }

        // 5. Remove junk model codes (long IDs, hex sequences)
        foreach ($this->modelCodePatterns as $pattern) {
            $name = preg_replace($pattern, ' ', $name);
        }

        // 6. Normalize separators
        $name = preg_replace('/[-_]+/', ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name);

        $brandLower = strtolower($brand);
        $brandSlug = Str::slug($brand);

        $words = explode(' ', trim($name));
        $filtered = [];

        foreach ($words as $word) {
            $lower = strtolower(trim($word));
            if (empty($lower)) continue;
            if (in_array($lower, $this->stopWords)) continue;
            if ($lower === $brandLower || $lower === $brandSlug) continue;
            if (strlen($lower) <= 2 && !is_numeric($lower)) continue;
            if (preg_match('/^[a-z]{1,2}$/i', $lower)) continue;
            $filtered[] = ucfirst($lower);
        }

        $result = implode(' ', $filtered);

        // If nothing meaningful remains, keep the model number as-is
        if (empty(trim($result))) {
            $name = preg_replace('/[-_]+/', ' ', $basename);
            $name = preg_replace('/\s+/', ' ', $name);
            $words = array_filter(explode(' ', $name), fn($w) => strlen(trim($w)) > 0);
            $filtered = [];
            foreach ($words as $w) {
                $lower = strtolower(trim($w));
                if ($lower === $brandLower || $lower === $brandSlug) continue;
                if (in_array($lower, $this->stopWords)) continue;
                if (strlen($lower) <= 2) continue;
                $filtered[] = ucfirst($lower);
            }
            if (!empty($filtered)) {
                $result = implode(' ', array_slice($filtered, 0, 3)); // keep at most 3 parts
            }
        }

        return $result;
    }

    protected function detectShape(string $cleanName, string $filename): string
    {
        $combined = strtolower($cleanName . ' ' . $filename);

        foreach ($this->shapeKeywords as $keyword => $shape) {
            if (Str::contains($combined, $keyword)) {
                return $shape;
            }
        }

        return 'Square';
    }

    protected function extractTags(string $cleanName): array
    {
        $tags = [];
        $words = explode(' ', $cleanName);

        foreach ($words as $word) {
            $lower = strtolower(trim($word));
            if (strlen($lower) < 3) continue;
            if (in_array($lower, ['the', 'and', 'for', 'with', 'new', 'old', 'de', 'vue'])) continue;
            $tags[] = $lower;
        }

        return array_values(array_unique($tags));
    }

    protected function buildModelName(string $cleanName, string $brand): string
    {
        $parts = explode(' ', $cleanName);
        $significant = array_filter($parts, fn($p) => strlen(trim($p)) > 0);
        $significant = array_values($significant);

        if (empty($significant)) {
            return $brand;
        }

        $name = implode(' ', $significant);

        $brandShort = strtolower(Str::substr($brand, 0, 4));
        $nameLower = strtolower($name);

        if (!Str::contains($nameLower, $brandShort)) {
            $name = $brand . ' ' . $name;
        }

        return Str::limit(trim($name), 70);
    }
}
