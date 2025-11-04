@php
    $setting = App\Models\Setting::first();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
   <link rel="shortcut icon"  href="{{ asset($setting->favicon) }}"  type="image/x-icon">
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  @yield('title')
  <title>{{__('admin.Login')}}</title>


  <link rel="stylesheet" href="{{ asset('backend/css/bootstrap.min.css') }}">
  <link href="{{ asset('backend/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('backend/fontawesome/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/bootstrap-social.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/components.css') }}">
  @if ($setting->text_direction == 'rtl')
    <link rel="stylesheet" href="{{ asset('backend/css/rtl.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/dev_rtl.css') }}">
    @endif
  <link rel="stylesheet" href="{{ asset('toastr/toastr.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/bootstrap4-toggle.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/dev.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/select2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/tagify.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/bootstrap-tagsinput.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/fontawesome-iconpicker.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/bootstrap-datepicker.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/summernote/summernote.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/clockpicker/dist/bootstrap-clockpicker.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/datetimepicker/jquery.datetimepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/iziToast.min.css') }}">

  <script src="{{ asset('backend/js/jquery-3.6.0.min.js') }}"></script>
    @yield('style')
<style>
    .fade.in {
        opacity: 1 !important;
    }
</style>

</head>
