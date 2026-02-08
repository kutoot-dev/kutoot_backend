@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Create Coin Campaign')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Create Coin Campaign')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('admin.all-coin-campaigns') }}">{{__('admin.All Coin Campaigns')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Create Coin Campaign')}}</div>
            </div>
          </div>

          <div class="section-body">
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.store-coin-campaign') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="form-group col-3">
                                    <label>{{__('Thumbnail Preview')}}</label>
                                    <div>
                                        <img id="preview-img" class="admin-preview-img" src="{{ asset('uploads/website-images/preview.png') }}" alt="">
                                    </div>
                                </div>
                                     <div class="form-group col-3">
                                    <label>{{__('Image 1 Preview')}}</label>
                                    <div>
                                        <img id="image1" class="admin-preview-img" src="{{ asset('uploads/website-images/preview.png') }}" alt="">
                                    </div>
                                </div>
                                     <div class="form-group col-3">
                                    <label>{{__('Image 2 Preview')}}</label>
                                    <div>
                                        <img id="image2" class="admin-preview-img" src="{{ asset('uploads/website-images/preview.png') }}" alt="">
                                    </div>
                                </div>

                                <div class="form-group col-sm-6 col-md-4 col-lg-3 col-3">
                                    <label>{{__('Thumbnail Video Preview')}}</label>
                                    <div>

                                        <video  id="preview-video"   controls style="max-width:100%">
                                          <source src="movie.mp4" type="video/mp4">

                                        </video>


                                    </div>
                                </div>


                                <div class="form-group col-sm-6 col-md-4 col-lg-3">
                                    <label>{{__('admin.Thumbnail Image')}} </label>
                                    <input type="file" class="form-control-file"  name="img" onchange="previewImage(event, 'preview-img')">
                                </div>


                                <div class="form-group col-sm-6 col-md-4 col-lg-3">
                                    <label>{{__(' Image1')}} </label>
                                    <input type="file" class="form-control-file"  name="image1" onchange="previewImage(event, 'image1')">
                                </div>

                                <div class="form-group col-sm-6 col-md-4 col-lg-3">
                                    <label>{{__('Image2')}} </label>
                                    <input type="file" class="form-control-file"  name="image2" onchange="previewImage(event, 'image2')">
                                </div>

                                <div class="form-group col-sm-6 col-md-4 col-lg-3">
                                    <label>{{__('Thumbnail Video')}}</label>
                                    <input type="file" class="form-control-file"  name="video" onchange="previewThumnailvideo(event)">
                                </div>


                                <div class="form-group col-12">
                                    <label>{{__('admin.Title')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="title" class="form-control"  name="title" value="{{ old('title') }}" required>
                                </div>

                                <div class="form-group col-6">
                                    <label>{{__('Title1')}} </label>
                                    <input type="text" id="title1" class="form-control"  name="title1" value="{{ old('title1') }}">
                                </div>

                                <div class="form-group col-6">
                                    <label>{{__('Campaign Slug')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="campaign_id" class="form-control"  name="campaign_id" value="{{ old('campaign_id') }}" placeholder="Give campaign id like CP001" required>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('Title2')}} </label>
                                    <input type="text" id="title2" class="form-control"  name="title2" value="{{ old('title2') }}">
                                </div>

                                 <div class="form-group col-8">
                                    <label>{{__('Short Description')}}</label>
                                    <input type="text" id="short_description" class="form-control"  name="short_description" value="{{ old('short_description') }}" >
                                </div>

                                 <div class="form-group col-4">
                                    <label>{{__('Ticket Price')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="ticket_price" class="form-control"  name="ticket_price" value="{{ old('ticket_price') }}" required>
                                </div>


<div class="form-group col">
    <label>Series Prefix <span class="text-danger">*</span></label>
    <input type="text" name="series_prefix" class="form-control" maxlength="1" value="{{ old('series_prefix', $campaign->series_prefix ?? '') }}"
    placeholder="1 character like A" required>
</div>

<div class="d-flex justify-content-center mb-3 ">
    <div class="form-group col">
        <label>Min Number <span class="text-danger">*</span></label>
        <input type="number" name="number_min" class="form-control" value="{{ old('number_min', $campaign->number_min ?? 1) }}" required>
    </div>
    <div class="form-group col">
        <label>Max Number <span class="text-danger">*</span></label>
        <input type="number" name="number_max" class="form-control" value="{{ old('number_max', $campaign->number_max ?? 49) }}" required>
    </div>
</div>

   <div class="form-group col-sm-6 col-md-4 col-lg-3">
        <label>{{__('Numbers Per Ticket')}} <span class="text-danger">*</span></label>
         <select name="numbers_per_ticket"  class="form-control" onChange="updateTotalTickets()">
            @foreach([2,3,4,5,6] as $n)
                {{-- <option value="{{ $n }}" {{ old('numbers_per_ticket', $campaign->numbers_per_ticket ?? 6) == $n ? 'selected' : '' }}>
                 {{ $n }}
                </option> --}}
                <option value="{{ $n }}" {{ old('numbers_per_ticket', $campaign->numbers_per_ticket ?? null) == $n ? 'selected' : '' }}>
    {{ $n }}
</option>

            @endforeach
        </select>
</div>



  <div class="form-group col-sm-6 col-md-4 col-lg-3">
                                    <label>{{__('coin_campaign.total_tickets')}} </label>
                                    <input type="number" start="1" step="1" id="total_tickets" class="form-control"  name="total_tickets" value="{{ old('total_tickets') }}" required readonly>
                                </div>

                                <div class="form-group col-sm-6 col-md-4">
                                    <label>{{__('Max Coins Limit')}} <small class="text-muted">(Total distributable)</small></label>
                                    <input type="number" step="1" min="0" id="max_coins" class="form-control"  name="max_coins" value="{{ old('max_coins') }}" placeholder="Leave empty for unlimited">
                                </div>

                                <div class="form-group col-sm-6 col-md-4">
                                    <label>{{__('Max Coupons Limit')}} <small class="text-muted">(Total distributable)</small></label>
                                    <input type="number" step="1" min="0" id="max_coupons" class="form-control"  name="max_coupons" value="{{ old('max_coupons') }}" placeholder="Leave empty for unlimited">
                                </div>

                                <div class="form-group col-sm-6 col-md-4">
                                    <label>{{__('Marketing Start Percent')}} (%) <span class="text-danger">*</span></label>
                                    <input type="number" step="1" min="0" max="100" id="marketing_start_percent" class="form-control"  name="marketing_start_percent" value="10" required>
                                </div>

                                <div class="form-group col-sm-6 col-md-4">
                                    <label>{{__('Marketing Goal Status')}} (%) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" max="100" id="marketing_goal_status" class="form-control"  name="marketing_goal_status" value="{{ old('marketing_goal_status', 0) }}" required>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Current percentage of tickets sold relative to total tickets.
                                    </small>
                                </div>

                                <div class="form-group col-sm-4 col-md-4">
                                    <label>{{__('admin.Status')}}</label>
                                 <select name="status" class="form-control">
    <option value="1" {{ old('status', $campaign->status ?? '') == 1 ? 'selected' : '' }}>
        {{__('admin.Active')}}
    </option>
    <option value="0" {{ old('status', $campaign->status ?? '') == 0 ? 'selected' : '' }}>
        {{__('admin.Inactive')}}
    </option>
</select>

                                </div>

                                <div class="form-group col-sm-6 col-md-4">
                                    <label>{{__('Actual Status')}} (%) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" max="100" id="actual_status" class="form-control"  name="actual_status" value="{{ old('actual_status', 0) }}" required>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Overall campaign completion percentage based on time and ticket sales.
                                    </small>
                                </div>

                                 <div class="form-group col-sm-4 col-md-4">
                                    <label>{{__('Promotion Type')}}</label>
                                 <select name="promotion" class="form-control">
    <option value="Featured" {{ old('promotion', $campaign->promotion ?? '') == 'Featured' ? 'selected' : '' }}>
        {{__('Featured')}}
    </option>
    <option value="Top-Banner" {{ old('promotion', $campaign->promotion ?? '') == 'Top-Banner' ? 'selected' : '' }}>
        {{__('Top-Banner')}}
    </option>
</select>

                                </div>

                                <div class="form-group col-4">
                                    <label>{{__('Category')}} <span class="text-danger">*</span></label>
                                    <select name="category" class="form-control">
                                        <option value="Luxury">Luxury</option>
                                        <option value="Sports">Sports</option>
                                        <option value="Electronics">Electronics</option>
                                    </select>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label>{{__('coin_campaign.start_date')}} <span class="text-danger">*</span></label>
                                    <input type="date" id="start_date" class="form-control"  name="start_date" value="{{ old('start_date') }}" required>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label>{{__('coin_campaign.end_date')}} </label>
                                    <input type="date" id="end_date" class="form-control"  name="end_date" value="{{ old('end_date') }}">
                                </div>

                                <div class="form-group col-sm-4">
                                    <label>{{__('Tag 1')}}</label>
                                    <input type="text" id="tag1" class="form-control"  name="tag1" value="{{ old('tag1') }}" >
                                </div>

                                <div class="form-group col-sm-4">
                                    <label>{{__('Tag 2')}}</label>
                                    <input type="text" id="tag2" class="form-control"  name="tag2" value="{{ old('tag2') }}" >
                                </div>

                                  <div class="form-group col-sm-4">
                                    <label>{{__('Winner Announcement Date')}} </label>
                                    <input type="date" id="winner_announcement_date" class="form-control"  name="winner_announcement_date" value="{{ old('end_date') }}" >
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Description')}} </label>
                                    <textarea name="description" id="" cols="30" rows="10" class="summernote">{{ old('description') }}</textarea>
                                </div>

                            {{-- add highlight --}}
<div id="highlightsWrapper">
    <!-- First Object -->
      <label>{{__('Highlights')}} </label>
    <div class="highlight-object border p-2 mb-3">
        <div class="kv-pairs ">
            <div class="kv-pair mb-2 d-flex">
                <input type="text" name="highlights[0][key][]" placeholder="Key" class="form-control me-2" />
                <input type="text" name="highlights[0][value][]" placeholder="Value" class="form-control me-2" />
                <button type="button" class="btn btn-danger remove-pair">X</button>
            </div>
        </div>
        <button type="button" class="btn btn-secondary btn-sm add-pair">Add Key-Value</button>
    </div>

</div>

 <button type="button" id="addObject" class="btn btn-primary mt-3">Add Object</button>

{{-- end highlight --}}
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-primary">{{__('admin.Save')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>
      <script>

function previewImage(event, targetId) {
    console.log("Previewing image for target:", targetId);
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById(targetId);
        output.src = reader.result;
        output.load();
    };
    reader.readAsDataURL(event.target.files[0]);
}
          function previewThumnailvideo(event) {
                    var reader = new FileReader();
                    reader.onload = function(){
                        var output = document.getElementById('preview-video');
                        output.src = reader.result;
                        // Optionally, you can also update the <source> tag, though setting src directly on <video> is usually sufficient for preview
                        // var source = output.querySelector('source');
                        // if (source) {
                        //     source.src = reader.result;
                        // }
                        output.load(); // Reload the video element to show the new source
                    }
                    reader.readAsDataURL(event.target.files[0]);
                };

      </script>
                      <script>
    function factorial(n) {
        if (n < 0) return 0;
        let result = 1;
        for (let i = 2; i <= n; i++) {
            result *= i;
        }
        return result;
    }

    function calculateCombinations(n, r) {
        if (n < r) return 0;
        return factorial(n) / (factorial(r) * factorial(n - r));
    }

    function updateTotalTickets() {
        const min = parseInt($('input[name="number_min"]').val());
        const max = parseInt($('input[name="number_max"]').val());
        const r = parseInt($('select[name="numbers_per_ticket"]').val());
        const prefix = $('input[name="series_prefix"]').val().toUpperCase();

        const n = max - min + 1;

        // Calculate multiplier based on series_prefix: A=1, B=2, C=3, etc.
        let multiplier = 1;
        if (prefix && /^[A-Z]$/.test(prefix)) {
            multiplier = prefix.charCodeAt(0) - 64; // 'A'.charCodeAt(0) === 65
        }

        if (!isNaN(n) && !isNaN(r)) {
            const total = Math.floor(calculateCombinations(n, r)) * multiplier;
            $('#total_tickets').val(total);
        } else {
            $('#total_tickets').val('');
        }
    }

    $(document).ready(function() {
        $('input[name="number_min"], input[name="number_max"], select[name="numbers_per_ticket"]').on('input change', updateTotalTickets);

        // Initial calculation on page load
        updateTotalTickets();
    });
</script>

<script>
let objectIndex = 0; // Start with first object index as 0

// ✅ Add Object
document.getElementById('addObject').addEventListener('click', function () {
    objectIndex++; // Increase object index for unique names
    let objHtml = `
    <div class="highlight-object border p-2 mb-3">
        <div class="kv-pairs">
            <div class="kv-pair mb-2 d-flex">
                <input type="text" name="highlights[${objectIndex}][key][]" placeholder="Key" class="form-control me-2" />
                <input type="text" name="highlights[${objectIndex}][value][]" placeholder="Value" class="form-control me-2" />
                <button type="button" class="btn btn-danger remove-pair">X</button>
            </div>
        </div>
        <button type="button" class="btn btn-secondary btn-sm add-pair">Add Key-Value</button>
    </div>`;

    document.getElementById('highlightsWrapper').insertAdjacentHTML('beforeend', objHtml);
});

// ✅ Add Key-Value Pair (Scoped to the Right Object)
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('add-pair')) {
        let highlightObject = e.target.closest('.highlight-object');
        let kvPairsDiv = highlightObject.querySelector('.kv-pairs');

        // Figure out the index of THIS object in the wrapper
        let objIdx = Array.from(document.querySelectorAll('.highlight-object')).indexOf(highlightObject);

        let kvHtml = `
        <div class="kv-pair mb-2 d-flex">
            <input type="text" name="highlights[${objIdx}][key][]" placeholder="Key" class="form-control me-2" />
            <input type="text" name="highlights[${objIdx}][value][]" placeholder="Value" class="form-control me-2" />
            <button type="button" class="btn btn-danger remove-pair">X</button>
        </div>`;
        kvPairsDiv.insertAdjacentHTML('beforeend', kvHtml);
    }

    // ✅ Remove Pair
    if (e.target.classList.contains('remove-pair')) {
        e.target.closest('.kv-pair').remove();
    }
});
</script>
@endsection
