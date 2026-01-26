@extends('store.master_layout')

@section('store-content')
<section class="section">
    <div class="section-header">
        <h1>Store Profile</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h4>Store Details</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('store.store-profile.update') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label>Shop Name</label>
                            <input class="form-control" name="shop_name" value="{{ old('shop_name', $shop?->store_name) }}" required>
                        </div>

                        <div class="form-group">
                            <label>Category</label>
                            <select class="form-control" name="category">
                                <option value="">-- Select --</option>
                                @foreach(($categories ?? []) as $cat)
                                    <option value="{{ $cat->name }}" {{ old('category', $shop?->store_type) === $cat->name ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Owner Name</label>
                            <input class="form-control" name="owner_name" value="{{ old('owner_name', $shop?->owner_name) }}">
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Phone</label>
                                <input class="form-control" name="phone" value="{{ old('phone', $shop?->owner_mobile) }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Email</label>
                                <input class="form-control" name="email" value="{{ old('email', $shop?->owner_email) }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>GST Number</label>
                            <input class="form-control" name="gst_number" value="{{ old('gst_number', $shop?->gst_number) }}">
                        </div>

                        <div class="form-group">
                            <label>Address</label>
                            <input class="form-control" name="address" value="{{ old('address', $shop?->store_address) }}">
                        </div>

                        <div class="form-group">
                            <label>Google Map URL</label>
                            <input class="form-control" name="google_map_url" value="{{ old('google_map_url', $shop?->google_map_url) }}">
                            <small class="text-muted">Optional: paste your Google Maps link for this store.</small>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Latitude</label>
                                <input class="form-control" name="location_lat" value="{{ old('location_lat', $shop?->lat) }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Longitude</label>
                                <input class="form-control" name="location_lng" value="{{ old('location_lng', $shop?->lng) }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Upload Images</label>
                            <input type="file" class="form-control" name="images[]" multiple>
                            <small class="text-muted">Optional: upload multiple images.</small>
                        </div>

                        <button class="btn btn-primary" type="submit">Save</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h4>Read-only KPIs</h4></div>
                <div class="card-body">
                    <div>Discount: <strong>{{ $shop?->discount_percent ?? 0 }}%</strong></div>
                    <div>Commission: <strong>{{ $shop?->commission_percent ?? 0 }}%</strong></div>
                    <div>Min Bill Amount: <strong>{{ $shop?->min_bill_amount ?? 0 }}</strong></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4>Images</h4></div>
                <div class="card-body">
                    @if($images->count() === 0)
                        <div class="text-muted">No images uploaded yet.</div>
                    @else
                        <div class="row">
                            @foreach($images as $img)
                                <div class="col-6 mb-3">
                                    <img src="{{ str_starts_with($img->image_url, 'http') ? $img->image_url : asset($img->image_url) }}" class="img-fluid rounded" alt="shop image">
                                    <form method="POST" action="{{ route('store.store-image.delete', $img->id) }}" onsubmit="return confirm('Delete this image?')" class="mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger btn-block" type="submit">Delete</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


