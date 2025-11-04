@php
    $setting = App\Models\Setting::first();
@endphp

<style>
#wsus__topbar,
.wsus__icon_area li a span,
.wsus_menu_category_bar,
.common_btn,
#wsus__banner .slick-dots li.slick-active button,
.wsus__scroll_btn,
.wsus__new,
.wsus__pro_det_video,
.modal_slider .prv_arr,
.modal_slider .nxt_arr,
.product_popup_modal .btn-close:hover,
.add_cart,
#wsus__hot_deals .nxt_arr,
#wsus__hot_deals .prv_arr,
.wsus__footer_social .facebook:hover,
.wsus__footer_bottom,
#pagination nav ul li a.page_active,
#pagination nav ul li a:hover,
#pagination2 nav ul li a.page_active,
#pagination2 nav ul li a:hover,
.exzoom .exzoom_btn a,
.wsus__comment_area h4 span,
.wsus__daily_deals_single_img p,
.wsus__daily_deals_single_img .blue,
.wsus__section_header .simply-days-section,
.wsus__section_header .simply-hours-section,
.wsus__section_header .simply-minutes-section,
.wsus__section_header .simply-seconds-section,
.wsus__single_brand .new,
.check_mark::after,
.wsus__order_details_img span,
.razorpay-payment-button,
.wsus__address_btn a,
.wishlist .wsus__pro_img a:hover,
.wsus__mobile_menu_icon,
.wsus__mobile_menu_close:hover,
#wsus__weekly_best .nxt_arr,
#wsus__weekly_best .prv_arr,
.wsus__single_blog .blue,
.wsus__pop_up_text #cross,
.wsus__chatlist h2,
.wsus__chat_area_header h2,
.single_chat_2 .wsus__chat_single_text p,
.wsus__chat_area_footer form button,
.wsus__mini_cart h4 span:hover,
.wsus__stock_area .in_stock,
.wsus__button_area li .buy_now,
.wsus__ofer_det_footer_btn .buy_now,
.wsus__single_pro_icon li a {
    background: {{ $setting->theme_one }} !important;
}

.add_cart:hover {
  color: #fff;
  background: {{ $setting->theme_one }} !important;
}

.wsus__single_pro_icon li a {
    border: 1px solid {{ $setting->theme_one."60" }} !important;
}
.wsus__mobile_menu_header_icon li a {
    background: {{ $setting->theme_one }} !important;
    border: 1px solid {{ $setting->theme_one }} !important;
}

.wsus__pop_up_text form .news_input {
    border: 1px solid {{ $setting->theme_one }} !important;
}
.wsus__dashboard_order .status a {
    color: {{ $setting->theme_one }} !important;
    border: 1px solid {{ $setting->theme_one }} !important;
}

.wsus__dashboard_order .status a:hover {
    background: {{ $setting->theme_one }} !important;
    color: #fff !important;
}

#pagination nav ul li a, #pagination2 nav ul li a {
    border: 1px solid {{ $setting->theme_one }} !important;
}

.wsus__button_area li:nth-child(3) a:hover,
.wsus__button_area li:nth-child(4) a:hover {
  border-color: {{ $setting->theme_one }} !important;
  background:  {{ $setting->theme_one }} !important;
}

.wsus__cart_list table tr th,
.wsus__dashboard_order table thead tr th,
.wsus__invoice_description th,
.wsus__invoice_footer {
    background: {{ $setting->theme_one."30" }} !important;
}

.wsus__dash_add_single h4 {
    background: {{ $setting->theme_one."30" }} !important;
    border-bottom : 1px solid {{ $setting->theme_one."30" }} !important;
}

.wsus__dash_add_single {
    border : 1px solid {{ $setting->theme_one."30" }} !important;
}

.wsus__cart_list_footer_top input {
    border: 1px solid {{ $setting->theme_one }} !important;
}

.wsus__pay_info_area .tab-pane,
.wsus__pay_booking_summary {
    background: {{ $setting->theme_one."30" }} !important;
}

#wsus__brand_sleder .nxt_arr,
#wsus__brand_sleder .prv_arr {
    background: {{ $setting->theme_one."80" }} !important;
}

#wsus__brand_sleder .nxt_arr:hover,
#wsus__brand_sleder .prv_arr:hover {
  background: {{ $setting->theme_one }} !important;
}

.wsus__login_reg_area .nav-pills .nav-link.active,
.wsus__login_reg_area .nav-pills .show > .nav-link {
    color: {{ $setting->theme_one }} !important;
    border-color: {{ $setting->theme_one }} !important;
}

.wsus__login_input i {
    border: 1px solid {{ $setting->theme_one."60" }} !important;
}

#wsus__banner .slick-dots li button {
    background: {{ $setting->theme_one."90" }} !important;
}

.accordion-button {
    background: {{ $setting->theme_one."30" }} !important;
}

.wsus__order_details {
    background: {{ $setting->theme_one."20" }} !important;
    border : 1px solid {{ $setting->theme_one."60" }} !important;
}



.common_btn:hover {
  background: {{ $setting->theme_one."150" }} !important;
}

.shop_btn:hover,
.wsus__hot_deals_text ul li:nth-child(2) a:hover,
.wsus__hot_deals_text ul li:nth-child(3) a:hover {
  background: {{ $setting->theme_one }} !important;
  border-color: {{ $setting->theme_one }} !important;
}

.wsus__product_details .wsus__pro_name:hover {
  color: {{ $setting->theme_one }} !important;
}

.check_mark::before {
    background: {{ $setting->theme_one }} !important;
    color: #fff !important;
    border-color: {{ $setting->theme_one }} !important;
}

#wsus__mobile_menu .nav-pills .nav-link.active, #wsus__mobile_menu #pills-tab li button:hover {
    color: {{ $setting->theme_one }} !important;
    border-color: {{ $setting->theme_one }} !important;
}

.wsus__pro_det_description .nav {
    border: 1px solid {{ $setting->theme_one }} !important;
}

.wsus__dashboard_review_item ul li a {
    background: {{ $setting->theme_one }} !important;
    color: #fff !important;
}

.wsus__dash_pro_img input {
    background: {{ $setting->theme_one."60" }} !important;
}
.wsus_pro_hot_deals .simply-amount {
    color: {{ $setting->theme_one }} !important;
    border: 1px solid {{ $setting->theme_one }} !important;
}
.wsus__dashboard_review_item ul li a:hover {
    color : #000 !important;
}

.close_icon .dash_bar {
    background: {{ $setting->theme_one }} !important;
    color: #fff !important;
}

.wsus__menu_item > li:hover > a,
.wsus__menu_item li a.active,
.wsus_menu_cat_item > li:hover > a,
.wsus_menu_cat_droapdown li a:hover,
.wsus_menu_cat_droapdown li a:hover,
.wsus__single_slider_text h6,
.wsus__call_text a:hover,
.wsus__flash_coundown .end_text,
.wsus__pro_details_text h4 span,
.wsus__pro_details_text h4,
.wsus__quentity h3,
.wsus__quentity h3 span,
.monthly_top_filter button:hover,
.monthly_top_filter button.active,
.monthly_top_filter2 button:hover,
.monthly_top_filter2 button.active,
.wsus__hot_deals__single:hover h5,
.wsus__product_details .wsus__price,
.wsus__hot_title:hover,
.wsus__hot_deals_proce,
.wsus__home_services_single i,
.wsus__home_services_single:hover h5,
.see_btn,
.wsus__blog_text_center a:hover,
.wsus__blog_text_center .date span,
.wsus__footer_content .action:hover,
.wsus__footer_menu li,
.wsus__footer_menu li a:hover,
#wsus__breadcrumb ul li:last-child a,
.wsus__product_sidebar ul li a:hover,
.form-check label:hover,
.wsus__pro_details_text .title:hover,
.brand_model a:hover,
.wsus_pro_det_sidebar_single i,
.wsus__blog_post_single a:hover,
.wsus__main_blog_header span i,
.wsus_menu_cat_item li .wsus__droap_arrow::after,
.wsus__daily_deals_text .deals_title:hover,
.wsus__offer_countdown .end_text,
.wsus__contact_single i,
.wsus__contact_single a:hover,
.wsus__compare_text a:hover,
.wsus__compare_stock,
.wsus__mini_cart .wsus__cart_text a:hover,
.wsus__mini_cart .wsus__cart_text p,
.wsus__cart_tab li a:hover,
.wsus__order_active,
.wsus__pro_name a:hover,
.wsus__pro_status p,
.wsus__login_input i,
.wsus__pro_report,
.wsus__order_details .form-check label a,
.wsus__invoice_description .name p a:hover,
.wsus__dash_rev_text a:hover,
.wsus__dash_rev_text h5 span,
.wsus__dash_pro_single i,
.wsus__mobile_menu_header_icon li a span,
.wsus_mobile_menu_category li:hover > a,
.wsus__mobile_menu_main_menu li:hover > a,
.wsus__mobile_menu_main_menu .accordion-button:hover,
.wsus__forget_area .qiestion_icon i,
.wsis__del_icon:hover,
.wsus__pro_det_description .nav-pills .nav-link.active,
.wsus__pro_det_description .nav-pills .nav-link:hover {
    color: {{ $setting->theme_one }} !important;
}

#wsus__flash_sell .nxt_arr,
#wsus__flash_sell .prv_arr,
#wsus__electronic2 .prv_arr,
#wsus__electronic2 .nxt_arr,
#wsus__electronic .prv_arr,
#wsus__electronic .nxt_arr{
    background: {{ $setting->theme_one."80" }} !important;
}

#wsus__flash_sell .nxt_arr:hover,
#wsus__flash_sell .prv_arr:hover,
#wsus__electronic2 .nxt_arr:hover,
#wsus__electronic2 .prv_arr:hover,
#wsus__electronic .nxt_arr:hover,
#wsus__electronic .prv_arr:hover {
  background: {{ $setting->theme_one }} !important;
}

.wsus__daily_deals_single {
    background: {{ $setting->theme_one."30" }} !important;
    border: 1px solid {{ $setting->theme_one."30" }} !important;
}

.wsus__main_blog_header,
.wsus__share_blog {
    background: {{ $setting->theme_one."30" }} !important;
}

.flat-slider .ui-slider-handle {
    background-color : {{ $setting->theme_one }} !important;
}

.flat-slider .ui-widget-content {
    background: {{ $setting->theme_one."90" }} !important;
}

.accordion-button {
    background: {{ $setting->theme_one."30" }} !important;
}

.wsus__product_topbar_left #v-pills-tab button.active {
    background: {{ $setting->theme_one }} !important;
    color: #fff !important;
    border-color: {{ $setting->theme_one }} !important;
}

.form-check-input:checked {
  background-color: {{ $setting->theme_one }} !important;
  border-color: {{ $setting->theme_one }} !important;
}

.wsus__product_topbar_left #v-pills-tab button {
    color : {{ $setting->theme_one }} !important;
    background: #fff !important;
}

.wsus__single_pro_icon li a:hover {
    border-color: {{ $setting->theme_one }} !important;
    background: {{ $setting->theme_one }} !important;
}

.wsus__icon_area li a:hover, .wsus__icon_area li .active {
    color: {{ $setting->theme_one }} !important;
    border-color: {{ $setting->theme_one }} !important;
}

.select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
    background-color: {{ $setting->theme_one }} !important;
}



/* start theme color two */

.wsus__single_slider_text h1 {
    color: {{ $setting->theme_two }} !important;
}

.wsus__main_menu,
#wsus__subscribe,
.wsus__payment_area form,
.wsus__pay_info_area .nav-pills .nav-link.active,
.dashboard_sidebar,
.wsus__dashboard_menu,
#wsus__mobile_menu,
.wsus__mobile_menu_main_menu .accordion-body {
    background:  {{ $setting->theme_two }} !important;
}

.wsus__home_services_single:hover i,
.wsus__home_services_single h5 {
  color: {{ $setting->theme_two }} !important;
}

.wsus__chat_single_text p {
    background:  {{ $setting->theme_two."20" }} !important;
}


.wsus__offer_time .simply-days-section,
.wsus__offer_time .simply-hours-section,
.wsus__offer_time .simply-minutes-section,
.wsus__offer_time .simply-seconds-section {
    background:  {{ $setting->theme_two }} !important;
}

.wsus__hot_deals_img .simply-countdown {
    background:  {{ $setting->theme_two. "90" }} !important;
}

</style>
