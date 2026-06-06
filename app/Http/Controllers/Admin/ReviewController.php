<?php

namespace App\Http\Controllers\Admin;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $reviews = Review::with('product', 'customer')
            ->latest()
            ->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);

        return redirect()->back()->with('success', 'Avis approuvé.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()->back()->with('success', 'Avis supprimé.');
    }
}
