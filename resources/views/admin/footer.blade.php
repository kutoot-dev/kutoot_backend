@php
    $setting = App\Models\Setting::first();
@endphp

<div class="modal fade" tabindex="-1" role="dialog" id="deleteModal">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{__('admin.Item Delete Confirmation')}}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>{{__('admin.Are You sure delete this item ?')}}</p>
        </div>
        <div class="modal-footer bg-whitesmoke br">
            <form id="deleteForm" action="" method="POST">
                @csrf
                @method("DELETE")
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('admin.Close')}}</button>
                <button type="submit" class="btn btn-primary">{{__('admin.Yes, Delete')}}</button>
            </form>
        </div>
      </div>
    </div>
  </div>



  @yield('script')
  <script src="{{ asset('backend/pos/assets/js/custom.js') }}"></script>
  <script src="{{ asset('backend/js/popper.min.js') }}"></script>
  <script src="{{ asset('backend/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('backend/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('backend/datatables/dataTables.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('backend/js/jquery.nicescroll.min.js') }}"></script>
  <script src="{{ asset('backend/js/moment.min.js') }}"></script>
  <script src="{{ asset('backend/js/stisla.js') }}"></script>
  <script src="{{ asset('backend/js/scripts.js') }}"></script>
  <script src="{{ asset('backend/js/custom.js') }}"></script>
  <script src="{{ asset('backend/js/select2.min.js') }}"></script>
  <script src="{{ asset('backend/js/tagify.js') }}"></script>
  <script src="{{ asset('toastr/toastr.min.js') }}"></script>
  <script src="{{ asset('backend/js/bootstrap4-toggle.min.js') }}"></script>
  <script src="{{ asset('backend/js/fontawesome-iconpicker.min.js') }}"></script>
  <script src="{{ asset('backend/js/bootstrap-datepicker.min.js') }}"></script>
  <script src="{{ asset('backend/summernote/summernote.min.js') }}"></script>
  <script src="{{ asset('backend/clockpicker/dist/bootstrap-clockpicker.js') }}"></script>
  <script src="{{ asset('backend/datetimepicker/jquery.datetimepicker.js') }}"></script>
  <script src="{{ asset('backend/js/iziToast.min.js') }}"></script>
  <script src="{{ asset('backend/js/modules-toastr.js') }}"></script>


    <script>
        @if(Session::has('messege'))
        var type="{{Session::get('alert-type','info')}}"
        switch(type){
            case 'info':
                toastr.info("{{ Session::get('messege') }}");
                break;
            case 'success':
                toastr.success("{{ Session::get('messege') }}");
                break;
            case 'warning':
                toastr.warning("{{ Session::get('messege') }}");
                break;
            case 'error':
                toastr.error("{{ Session::get('messege') }}");
                break;
        }
        @endif
    </script>

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <script>
                toastr.error('{{ $error }}');
            </script>
        @endforeach
    @endif



<!-- Global Image Fallback Handler -->
<script>
(function() {
    'use strict';

    // Icon mapping for different image types
    var iconMap = {
        'avatar-img': 'fas fa-user-circle',
        'product-img': 'fas fa-box',
        'logo-img': 'fas fa-building',
        'banner-img': 'fas fa-image',
        'category-img': 'fas fa-folder',
        'brand-img': 'fas fa-tag',
        'blog-img': 'fas fa-newspaper',
        'default': 'fas fa-image'
    };

    // Size mapping
    var sizeMap = {
        'avatar-img': { width: 80, height: 80, iconSize: 40 },
        'product-img': { width: 80, height: 80, iconSize: 40 },
        'logo-img': { width: 150, height: 50, iconSize: 30 },
        'banner-img': { width: 200, height: 120, iconSize: 50 },
        'category-img': { width: 80, height: 80, iconSize: 40 },
        'brand-img': { width: 80, height: 80, iconSize: 40 },
        'blog-img': { width: 80, height: 80, iconSize: 40 },
        'default': { width: 80, height: 80, iconSize: 40 }
    };

    function getImageType(img) {
        var classes = img.className.split(' ');
        for (var i = 0; i < classes.length; i++) {
            if (iconMap[classes[i]]) return classes[i];
        }
        return 'default';
    }

    function replaceWithIcon(img) {
        var type = getImageType(img);
        var icon = iconMap[type];
        var size = sizeMap[type];

        // Get dimensions from img or use defaults
        var width = img.width || img.getAttribute('width') || size.width;
        var height = img.height || img.getAttribute('height') || size.height;

        // Create fallback container
        var wrapper = document.createElement('span');
        wrapper.className = 'img-fallback-icon ' + type + '-fallback';
        wrapper.style.cssText = 'display:inline-flex;align-items:center;justify-content:center;' +
            'width:' + width + 'px;height:' + height + 'px;' +
            'background:linear-gradient(135deg,#f0f0f0,#e0e0e0);' +
            'border-radius:' + (type === 'avatar-img' ? '50%' : '8px') + ';' +
            'color:#999;font-size:' + size.iconSize + 'px;';

        // Add icon
        var iconEl = document.createElement('i');
        iconEl.className = icon;
        wrapper.appendChild(iconEl);

        // Replace image
        if (img.parentNode) {
            img.parentNode.replaceChild(wrapper, img);
        }
    }

    // Handle image errors globally
    document.addEventListener('error', function(e) {
        if (e.target.tagName === 'IMG') {
            replaceWithIcon(e.target);
        }
    }, true);

    // Check existing images on load
    document.addEventListener('DOMContentLoaded', function() {
        var images = document.querySelectorAll('img');
        images.forEach(function(img) {
            if (!img.src || img.src === '' || img.src === window.location.href) {
                replaceWithIcon(img);
            } else if (img.complete && img.naturalHeight === 0) {
                replaceWithIcon(img);
            }
        });
    });
})();
</script>

<script>
    (function($) {
    "use strict";
    $(document).ready(function () {
        $('#dataTable').DataTable();
        $('.select1').select2();
        $('.select2').select2();
        $('.select3').select2();
        $('.select4').select2();
        $('.select5').select2();
        $('.sub_cat_one').select2();
        $('.tags').tagify();
        $('.summernote').summernote();
        $('.datetimepicker_mask').datetimepicker({
            format:'Y-m-d H:i',

        });
        $('.custom-icon-picker').iconpicker({
            templates: {
                popover: '<div class="iconpicker-popover popover"><div class="arrow"></div>' +
                    '<div class="popover-title"></div><div class="popover-content"></div></div>',
                footer: '<div class="popover-footer"></div>',
                buttons: '<button class="iconpicker-btn iconpicker-btn-cancel btn btn-default btn-sm">Cancel</button>' +
                    ' <button class="iconpicker-btn iconpicker-btn-accept btn btn-primary btn-sm">Accept</button>',
                search: '<input type="search" class="form-control iconpicker-search" placeholder="Type to filter" />',
                iconpicker: '<div class="iconpicker"><div class="iconpicker-items"></div></div>',
                iconpickerItem: '<a role="button" href="javascript:;" class="iconpicker-item"><i></i></a>'
            }
        })
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            startDate: '-Infinity'
        });
        $('.clockpicker').clockpicker();

    });

    })(jQuery);
</script>

</body>
</html>
