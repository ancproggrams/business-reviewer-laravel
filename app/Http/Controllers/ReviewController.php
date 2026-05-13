<?php

namespace App\Http\Controllers;

use App\Business;
use App\Review;

use Illuminate\Http\Request;

class ReviewController extends Controller
{

    public function index(Business $business)
    {
        return $business->reviews()->with(['author.avatar', 'image', 'reply'])->get();
    }

    public function store(Business $business)
    {
        $this->authorize('addReview', $business);

        $attributes = request()->validate([
            'body' => ['required', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'image' => ['file', 'nullable']
        ]);


        isset($attributes['image']) ?
            $business->addReview($attributes['body'], $attributes['rating'], $attributes['image']) :
            $business->addReview($attributes['body'], $attributes['rating']);

        return redirect($business->path());
    }


    public function fetch(Review $review)
    {
        $review->load(['author.avatar', 'image', 'reply']);

        return compact('review');
    }


    public function showcased(Business $business)
    {
       $showcasedReviews =  $business->reviews()->with(['author.avatar', 'image', 'reply'])->where('showcased', true)->get();
       return compact('showcasedReviews');
    }
}
