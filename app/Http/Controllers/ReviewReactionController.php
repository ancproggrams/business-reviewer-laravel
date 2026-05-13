<?php

namespace App\Http\Controllers;

use App\Review;
use Illuminate\Http\Request;

class ReviewReactionController extends Controller
{

    public function store(Review $review)
    {
        $type = request()->validate([
            'type' => ['required', 'in:funny,useful'],
        ])['type'];

        $review->reactionExists($type) ? $review->removeReaction($type) : $review->addReaction($type);
        return back();
    }
}
