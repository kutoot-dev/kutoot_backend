@extends('layout')
@section('title')
    <title>{{__('FAQ')}}</title>
@endsection
@section('meta')
    <meta name="description" content="cart">
@endsection

@section('public-content')


    <!--============================
         BREADCRUMB START
    ==============================-->
    <section id="wsus__breadcrumb" style="background: url({{  asset($banner->image) }});">
        <div class="wsus_breadcrumb_overlay">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h4>{{__('FAQ')}}</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">{{__('home')}}</a></li>
                            <li><a href="{{ route('faq') }}">{{__('FAQ')}}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--============================
        BREADCRUMB END
    ==============================-->




    <!--============================
        FAQ START
    ==============================-->
    <section id="wsus__faq">
        <div class="container">
            <div class="row">
                <div class="col-12 m-auto">
                    <h4>Frequent Asked Questions</h4>
                    <div class="accordion" id="accordionExample">
                        @foreach ($faqs as $index => $faq)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingNumber-{{ $faq->id }}">
                                <button class="accordion-button {{ $index != 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNumber-{{ $faq->id }}" aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="collapseNumber-{{ $faq->id }}">
                                    {{ $faq->question }}
                                </button>
                                </h2>
                                <div id="collapseNumber-{{ $faq->id }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }} " aria-labelledby="headingNumber-{{ $faq->id }}" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    {!! clean($faq->answer) !!}
                                </div>
                                </div>
                            </div>
                        @endforeach
                      </div>
                </div>

            </div>
        </div>
    </section>
    <!--============================
        FAQ END
    ==============================-->






@endsection
