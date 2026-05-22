<?php

namespace App\Console\Commands;

use App\Features\Categories\Models\Category;
use App\Features\Products\Models\Product;
use App\Services\Metadata\ColorAnalyzer;
use App\Services\Metadata\FilenameParser;
use App\Services\Metadata\FolderClassifier;
use App\Services\Metadata\ImageScanner;
use App\Services\Metadata\ProductAssembler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File as FileFacade;

class GlassesImportCommand extends Command
{
    protected $signature = 'glasses:sync
        {--dry-run : Preview changes without modifying the database}';

    protected $description = 'Full catalog sync — scan images, rebuild products, clear cache';

    public function handle(
        ImageScanner $scanner,
        FolderClassifier $classifier,
        FilenameParser $parser,
        ColorAnalyzer $colorAnalyzer,
        ProductAssembler $assembler,
    ): int {
        $basePath = public_path('images/glasses');
        if (!FileFacade::exists($basePath)) {
            $this->error("Directory not found: {$basePath}");
            return Command::FAILURE;
        }

        // 1. Scan all images fresh
        $this->line('Scanning images...');
        $images = $scanner->scan();

        if (empty($images)) {
            $this->warn('No images found.');
            return Command::SUCCESS;
        }

        $this->info('Found ' . count($images) . ' images.');
        $this->newLine();

        // Validate each image exists
        $valid = [];
        foreach ($images as $img) {
            if (file_exists($img['pathname'])) {
                $valid[] = $img;
            }
        }
        $this->line('Valid files: ' . count($valid));
        $this->newLine();

        // 2. Dry-run: preview only
        if ($this->option('dry-run')) {
            $this->table(
                ['File', 'Brand', 'Name', 'Category', 'Gender', 'Color'],
                array_map(fn($img) => [
                    $img['filename'],
                    $parser->parse($img['filename'], $img['basename'])['brand'],
                    $parser->parse($img['filename'], $img['basename'])['name'],
                    $classifier->classify($img['folder'])['category_slug'],
                    $classifier->classify($img['folder'])['gender'],
                    $colorAnalyzer->analyze($img['pathname']),
                ], array_slice($valid, 0, 20))
            );
            $this->info("Dry-run complete. Use without --dry-run to sync.");
            return Command::SUCCESS;
        }

        // 3. Truncate existing products + pivot
        $this->warn('Clearing existing products...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Product::query()->truncate();
        DB::table('category_product')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // 4. Ensure categories exist
        $this->line('Ensuring categories...');
        $categories = [
            ['slug' => 'optical-collection', 'name' => 'Optical Collection'],
            ['slug' => 'solar-collection', 'name' => 'Solar Collection'],
            ['slug' => 'clip-on-collection', 'name' => 'Clip-On Collection'],
            ['slug' => 'blue-light-protection', 'name' => 'Blue Light Protection'],
        ];
        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
        $categoryCache = [];
        foreach ($categories as $cat) {
            $categoryCache[$cat['slug']] = Category::where('slug', $cat['slug'])->first();
        }

        // 5. Import all products
        $this->line('Importing products...');
        $bar = $this->output->createProgressBar(count($valid));
        $bar->start();

        $count = 0;

        foreach ($valid as $image) {
            $folderData = $classifier->classify($image['folder']);
            $filenameData = $parser->parse($image['filename'], $image['basename']);

            $detectedColor = $colorAnalyzer->analyze($image['pathname']);
            $secondary = $colorAnalyzer->detectSecondary($image['pathname']);

            $productData = $assembler->assemble($image, $folderData, $filenameData, $detectedColor, $secondary);

            $product = Product::create($productData);

            $categorySlug = $folderData['category_slug'];
            if (isset($categoryCache[$categorySlug])) {
                $product->categories()->attach($categoryCache[$categorySlug]->id);
            }

            $count++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // 6. Clean up orphans (belt-and-suspenders: remove any product whose image file vanished)
        $removed = Product::all()->filter(fn($p) => !file_exists(public_path(ltrim($p->image, '/'))));
        if ($removed->isNotEmpty()) {
            $ids = $removed->pluck('id');
            DB::table('category_product')->whereIn('product_id', $ids)->delete();
            Product::whereIn('id', $ids)->delete();
            $this->line('Removed ' . $ids->count() . ' orphan products with missing images.');
        }

        // 7. Clear all caches
        $this->line('Clearing caches...');
        Cache::flush();
        $this->callSilently('view:clear');
        $this->callSilently('config:clear');

        $this->info("Sync complete! {$count} products imported.");
        return Command::SUCCESS;
    }
}
