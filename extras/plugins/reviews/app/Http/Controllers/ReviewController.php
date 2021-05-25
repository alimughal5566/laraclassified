<?php

namespace extras\plugins\reviews\app\Http\Controllers;

use App\Http\Controllers\FrontController;
use App\Models\Scopes\ReviewedScope;
use App\Models\Scopes\VerifiedScope;
use extras\plugins\reviews\app\Http\Requests\ReviewRequest;
use extras\plugins\reviews\app\Models\Post;
use extras\plugins\reviews\app\Models\Review;

class ReviewController extends FrontController
{
	/**
	 * DetailsController constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * @param $postId
	 * @param ReviewRequest $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function create($postId, ReviewRequest $request)
	{
		// Get Post
		$post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])->find($postId);
		if (empty($post)) {
			abort(404, t('Post not found'));
		}
		
		// Instantiate Rating model
		$review = new Review();
		
		// Store the review in DB, otherwise return to product page with error message
		$review->storeReviewForItem($post->id, $request->input('comment'), $request->input('rating'));
		
		// Redirect
		return redirect(config('app.locale') . '/' . $post->uri . '#item-reviews')->with('review_posted', true);
	}
	
	/**
	 * @param $reviewId
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function delete($reviewId)
	{
		$post = Review::deleteReviewForItem($reviewId);
		if (empty($post)) {
			return redirect('/');
		}
		
		// Redirect
		return redirect(config('app.locale') . '/' . $post->uri . '#item-reviews')->with('review_removed', true);
	}
}
