@props([
    'product',
    'link' => false,
])

@php
    $isAdmin = (int)($product->vendor_id ?? 0) === 0;
    $label = $isAdmin
        ? 'Admin'
        : (($product->seller->shop_name ?? null)
            ?: ($product->seller->user->name ?? null)
            ?: 'N/A');
@endphp

@if($link && !$isAdmin && !empty($product->vendor_id))
    <a href="{{ route('admin.seller-show', $product->vendor_id) }}">{{ $label }}</a>
@else
    {{ $label }}
@endif


