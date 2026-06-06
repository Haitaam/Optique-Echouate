<?php

namespace App\Console\Commands;

use App\Features\Products\Models\Product;
use App\Features\Quiz\Models\QuizAnswer;
use App\Models\Order;
use App\Models\Review;
use App\Models\Wishlist;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DataCleanupCommand extends Command
{
    protected $signature = 'app:cleanup';
    protected $description = 'Audit and clean up stale data';

    public function handle()
    {
        $this->info('Starting data cleanup...');
        $output = [];

        // 1. Orphan reviews (product no longer exists)
        $orphanReviews = Review::whereDoesntHave('product')->count();
        if ($orphanReviews > 0) {
            Review::whereDoesntHave('product')->delete();
            $output[] = "Deleted {$orphanReviews} orphan review(s).";
        }

        // 2. Orphan wishlist items (product no longer exists)
        $orphanWishlist = Wishlist::whereDoesntHave('product')->count();
        if ($orphanWishlist > 0) {
            Wishlist::whereDoesntHave('product')->delete();
            $output[] = "Deleted {$orphanWishlist} orphan wishlist item(s).";
        }

        // 3. Products with missing images
        $brokenImages = Product::all()->filter(fn($p) => !$p->imageExists());
        if ($brokenImages->isNotEmpty()) {
            $output[] = "Found {$brokenImages->count()} product(s) with missing images.";
            foreach ($brokenImages as $p) {
                $output[] = "  - #{$p->id} {$p->name} ({$p->image})";
            }
        }

        // 4. Orphan image files on disk with no DB record
        $imageFiles = collect(File::allFiles(public_path('images')))
            ->filter(fn($f) => in_array($f->getExtension(), ['jpg', 'jpeg', 'png', 'webp', 'gif']));
        $orphanCount = 0;
        foreach ($imageFiles as $file) {
            $relative = 'images/' . $file->getFilename();
            $exists = Product::where('image', 'like', '%' . $file->getFilename())->exists();
            if (!$exists) {
                $orphanCount++;
                $output[] = "  Orphan file: {$relative}";
            }
        }
        if ($orphanCount > 0) {
            $output[] = "Found {$orphanCount} orphan image file(s) on disk.";
        }

        // 5. Abandoned quiz answers older than 30 days
        $cutoff = now()->subDays(30);
        $oldQuiz = QuizAnswer::where('created_at', '<', $cutoff)->count();
        if ($oldQuiz > 0) {
            $output[] = "Found {$oldQuiz} abandoned quiz answer(s) older than 30 days.";
        }

        // 6. Pending orders older than 14 days
        $stalePending = Order::pendingConfirmation()
            ->where('created_at', '<', now()->subDays(14))
            ->count();
        if ($stalePending > 0) {
            $output[] = "Found {$stalePending} pending order(s) older than 14 days.";
        }

        if (empty($output)) {
            $this->info('Nothing to clean up.');
        } else {
            foreach ($output as $line) {
                $this->line($line);
            }
        }

        $this->info('Cleanup complete.');
    }
}
