@if (isset($post) and !empty($post) and isset($post->user) and !empty($post->user))
    <?php
        if (!isset($rvPost) || empty($rvPost)) {
            $rvPost = \extras\plugins\reviews\app\Models\Post::find($post->id);
        }
        $userRating = $rvPost->getRatingByUser($post->user->id);
	    $userRatingCount = $rvPost->getCountRatingByUser($post->user->id);
    ?>
    @if (isset($rvPost) and !empty($rvPost))
    <div class="rating">
        <div class="reviews-widget ratings">
            <p class="p-0 m-0">
                @for ($i=1; $i <= 5 ; $i++)
                    <span class="icon-star{{ ($i <= $userRating) ? '' : '-empty' }}"></span>
                @endfor
                <span class="rating-label">
                    {{ $userRatingCount  }} {{ mb_strtolower(trans_choice('reviews::messages.count_ratings', getPlural($userRatingCount))) }}
                </span>
            </p>
        </div>
    </div>
    @endif
@endif
@section('after_styles')
    @parent
    @if (isset($post) and !empty($post) and isset($post->user) and !empty($post->user))
    <style>
        .block-cell .rating {
            padding: 2px !important;
            width: 130px !important;
        }
        .block-cell .rating .reviews-widget span.icon-star,
        .block-cell .rating .reviews-widget span.icon-star-empty {
            margin-top: 5px !important;
            font-size: 14px !important;
            @if (config('lang.direction') == 'rtl')
            margin-left: -9px !important;
            @else
            margin-right: -9px !important;
            @endif
        }
        .block-cell .rating .reviews-widget .rating-label {
            margin-top: 0 !important;
            font-size: 10px !important;
            text-transform: none !important;
        }
        .block-cell .rating .reviews-widget span.icon-star,
        .block-cell .rating .reviews-widget span.icon-star-empty {
            color: #ffc32b;
        }
    </style>
    @endif
@endsection
