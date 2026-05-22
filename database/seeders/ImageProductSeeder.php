<?php

namespace Database\Seeders;

use App\Features\Categories\Models\Category;
use App\Features\Products\Models\Product;
use App\Services\Metadata\ColorAnalyzer;
use App\Services\Metadata\FilenameParser;
use App\Services\Metadata\FolderClassifier;
use App\Services\Metadata\ImageScanner;
use App\Services\Metadata\MetadataCache;
use App\Services\Metadata\ProductAssembler;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImageProductSeeder extends Seeder
{
    public function run(): void
    {
        $scanner = app(ImageScanner::class);
        $classifier = app(FolderClassifier::class);
        $parser = app(FilenameParser::class);
        $colorAnalyzer = app(ColorAnalyzer::class);
        $assembler = app(ProductAssembler::class);
        $cache = app(MetadataCache::class);

        $images = $scanner->scan();

        if (empty($images)) {
            $this->command?->warn('No images found in public/images/glasses/');
            return;
        }

        $categoryCache = [];
        $count = 0;

        foreach ($images as $image) {
            $folderData = $classifier->classify($image['folder']);
            $filenameData = $parser->parse($image['filename'], $image['basename']);
            $detectedColor = $colorAnalyzer->analyze($image['pathname']);

            $productData = $assembler->assemble($image, $folderData, $filenameData, $detectedColor);

            $product = Product::updateOrCreate(
                ['image' => $productData['image']],
                $productData
            );

            $categorySlug = $folderData['category_slug'];
            if (!isset($categoryCache[$categorySlug])) {
                $categoryCache[$categorySlug] = Category::where('slug', $categorySlug)->first();
            }

            if ($categoryCache[$categorySlug]) {
                $product->categories()->syncWithoutDetaching([$categoryCache[$categorySlug]->id]);
            }

            $count++;
        }

        $this->command?->info("Created/updated {$count} products from images.");
    }
}
