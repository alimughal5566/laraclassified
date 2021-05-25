@if (isset($post) and !empty($post))
    <?php
    $rvPost = \extras\plugins\reviews\app\Models\Post::find($post->id);
    ?>
    @if (isset($rvPost) and !empty($rvPost))
        <div class="reviews-widget ratings info-row text-center d-flex flex-column">
            <span class="stars-wrap">
                @for ($i=1; $i <= 5 ; $i++)
                    <span class="icon-star{{ ($i <= $rvPost->rating_cache) ? '' : '-empty' }}"></span>
                @endfor
            </span>
            <span class="rating-label">{{ $rvPost->rating_count }} {{ trans_choice('reviews::messages.count_reviews', getPlural($rvPost->rating_count)) }}</span>
        </div>
    @endif

@section('reviews_styles')
    <style>
        .reviews-widget > span.icon-star,
        .reviews-widget > span.icon-star-empty {
            margin-top: 5px;
            font-size: 16px;
            @if (config('lang.direction') == 'rtl')
                  margin-left: -8px;
            @else
                  margin-right: -8px;
        @endif



        }

        .reviews-widget > span.rating-label {
            margin-top: 5px;
            font-size: 14px;
            @if (config('lang.direction') == 'rtl')
                  margin-right: 4px;
            @else
                  margin-left: 4px;
        @endif



        }

        .reviews-widget > span.icon-star,
        .reviews-widget > span.icon-star-empty {
            color: #ffc32b;
        }

        .featured-list-slider span {
            display: inline;
        }

        .featured-list-slider .reviews-widget > span.icon-star,
        .featured-list-slider .reviews-widget > span.icon-star-empty {
            margin-top: 5px;
            font-size: 16px;
            @if (config('lang.direction') == 'rtl')
                  margin-left: -8px;
            @else
                  margin-right: -8px;
        @endif



        }

        .featured-list-slider .reviews-widget > span.rating-label {
            margin-top: 5px;
            font-size: 12px;
            @if (config('lang.direction') == 'rtl')
                  margin-right: 4px;
            @else
                  margin-left: 4px;
        @endif


        }
    </style>
@endsection
@endif