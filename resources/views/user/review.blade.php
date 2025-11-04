@extends('user.layout')
@section('title')
    <title>{{__('Reviews')}}</title>
@endsection
@section('user-content')
<div class="row">
    <div class="col-xl-9 col-xxl-10 col-lg-9 ms-auto">
      <div class="dashboard_content mt-2 mt-md-0">
        <h3><i class="far fa-star"></i> {{__('Reviews')}}</h3>
        <div class="wsus__dashboard_review">
          <div class="row">

            @foreach ($reviews as $review)
            <div class="col-xl-6">
              <div class="wsus__dashboard_review_item">
                <div class="wsus__dash_rev_img">
                  <img src="{{ asset($review->product->thumb_image) }}" alt="product" class="img-fluid w-100">
                </div>
                <div class="wsus__dash_rev_text">
                  <h5><a href="{{ route('product-detail', $review->product->slug) }}">{{ $review->product->short_name }}</a> <span>{{ $review->created_at->format('d M, Y') }}</span></h5>
                  <p class="wsus__dash_review">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= $review->rating)
                        <i class="fas fa-star"></i>
                        @else
                        <i class="fal fa-star"></i>
                        @endif
                    @endfor
                  </p>
                  <p>{{ $review->review }}</p>
                  <ul>
                    <li><a href="#" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne-{{ $review->id }}"><i
                          class="fal fa-edit"></i> {{__('edit')}}</a></li>
                  </ul>
                  <div class="accordion accordion-flush" id="accordionFlushExample">
                    <div class="accordion-item">
                      <div id="flush-collapseOne-{{ $review->id }}" class="accordion-collapse collapse"
                        aria-labelledby="flush-collapseOne-{{ $review->id }}" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                          <form action="{{ route('user.update-review',$review->id) }}" method="POST">
                            @csrf
                            <div class="wsus__riv_edit_single">
                              <i class="fas fa-star"></i>
                              <select class="select_2" name="rating">
                                <option {{ $review->rating == 1 ? 'selected' : ''  }} value="1">1</option>
                                <option {{ $review->rating == 2 ? 'selected' : ''  }} value="2">2</option>
                                <option {{ $review->rating == 3 ? 'selected' : ''  }} value="3">3</option>
                                <option {{ $review->rating == 4 ? 'selected' : ''  }} value="4">4</option>
                                <option {{ $review->rating == 5 ? 'selected' : ''  }} value="5">5</option>
                              </select>
                            </div>
                            <div class="wsus__riv_edit_single text_area">
                              <i class="far fa-edit"></i>
                              <textarea cols="3" name="review" rows="3" placeholder="{{ $review->review }}">{{ $review->review }}</textarea>
                            </div>
                            <button type="submit" class="common_btn">{{__('Update')}}</button>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            @endforeach
                <div class="col-12">
                    <div id="pagination">
                        {{ $reviews->links('custom_paginator') }}
                    </div>
                </div>
            </div>

          </div>
        </div>
      </div>
    </div>
@endsection
