@if (isset($post) and !empty($post))
    <?php
    if (!isset($rvPost) || empty($rvPost)) {
        // $rvPost = \extras\plugins\reviews\app\Models\Post::find($post->id);
    }
    ?>
    @if (isset($rvPost) and !empty($rvPost))
        <div class="reviews-widget ratings text-center">
            <?php
            /*
            <p class="pull-right rating-label">{{ $rvPost->rating_count }} {{ trans_choice('reviews::messages.count_reviews', getPlural($rvPost->rating_count)) }}</p>
            */
            ?>
            <p class="d-flex flex-column">
                <span class="stars-wrap">
                    @for ($i=1; $i <= 5 ; $i++)
                        <span class="icon-star{{ ($i <= $rvPost->rating_cache) ? '' : '-empty' }}"></span>
                    @endfor
                </span>
                <span class="rating-label">
                {{ number_format($rvPost->rating_cache, 1) }} {{ trans_choice('reviews::messages.count_stars', getPlural(number_format($rvPost->rating_cache))) }}
                </span>
            </p>
        </div>
    @endif

@section('after_styles')
    @parent
    <style>
        .reviews-widget span.icon-star,
        .reviews-widget span.icon-star-empty {
            margin-top: 5px;
            font-size: 18px;
            @if (config('lang.direction') == 'rtl')
           margin-left: -9px;
            @else
           margin-right: -9px;
        @endif











        }

        .reviews-widget .rating-label {
            margin-top: 5px;
            font-size: 16px;
            @if (config('lang.direction') == 'rtl')
           margin-right: 6px;
            @else
           margin-left: 6px;
        @endif











        }

        .reviews-widget span.icon-star,
        .reviews-widget span.icon-star-empty {
            color: #ffc32b;
        }
    </style>
@endsection
@endif