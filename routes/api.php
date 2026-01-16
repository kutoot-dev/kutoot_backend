<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;


use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\Auth\AdminForgotPasswordController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductSubCategoryController;
use App\Http\Controllers\Admin\ProductChildCategoryController;
use App\Http\Controllers\Admin\ProductBrandController;
use App\Http\Controllers\Admin\SpecificationKeyController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductGalleryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\AboutUsController;
use App\Http\Controllers\Admin\ContactPageController;
use App\Http\Controllers\Admin\CustomPageController;
use App\Http\Controllers\Admin\TermsAndConditionController;
use App\Http\Controllers\Admin\PrivacyPolicyController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\PopularBlogController;
use App\Http\Controllers\Admin\BlogCommentController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\ProductVariantItemController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\EmailConfigurationController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\ProductReviewController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ErrorPageController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\CountryStateController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\SellerController;
use App\Http\Controllers\Admin\MegaMenuController;
use App\Http\Controllers\Admin\MegaMenuSubCategoryController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\HomePageController;
use App\Http\Controllers\Admin\ShippingMethodController;
use App\Http\Controllers\Admin\WithdrawMethodController;
use App\Http\Controllers\Admin\SellerWithdrawController;
use App\Http\Controllers\Admin\ProductReportController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\BreadcrumbController;
use App\Http\Controllers\Admin\FooterController;
use App\Http\Controllers\Admin\FooterSocialLinkController;
use App\Http\Controllers\Admin\FooterLinkController;
use App\Http\Controllers\Admin\HomepageVisibilityController;
use App\Http\Controllers\Admin\MenuVisibilityController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\AdvertisementController;
use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\Admin\ImageItemController;



use App\Http\Controllers\Seller\SellerDashboardController;
use App\Http\Controllers\Seller\SellerProfileController;
use App\Http\Controllers\Seller\SellerProductController;
use App\Http\Controllers\Seller\SellerProductGalleryController;
use App\Http\Controllers\Seller\SellerProductVariantController;
use App\Http\Controllers\Seller\SellerProductVariantItemController;
use App\Http\Controllers\Seller\SellerProductReviewController;
use App\Http\Controllers\Seller\WithdrawController;
use App\Http\Controllers\Seller\SellerProductReportControler;
use App\Http\Controllers\Seller\SellerOrderController;
use App\Http\Controllers\Seller\SellerMessageContoller;
use App\Http\Controllers\Seller\SellerBrandController;


use App\Http\Controllers\Deliveryman\MyReviewController;
use App\Http\Controllers\Deliveryman\MyWithdrawController;


use App\Http\Controllers\Deliveryman\DeliveryMessageController;
use App\Http\Controllers\Deliveryman\DeliveryManOrderController;
use App\Http\Controllers\Deliveryman\DeliveryManProfileController;

use App\Http\Controllers\Deliveryman\DeliveryManDashboardController;
use App\Http\Controllers\Deliveryman\Auth\DeliveryManLoginController;
use App\Http\Controllers\Deliveryman\DeliveryManRegistrationController;
use App\Http\Controllers\Deliveryman\Auth\DeliveryManResetPasswordController;



use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\User\UserProfileController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\User\PaypalController;
use App\Http\Controllers\User\MessageController;
use App\Http\Controllers\User\AddressCotroller;
use App\Http\Controllers\User\UserDashboardController;


use App\Http\Controllers\ShippingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\WEB\Admin\CoinCampaignController;
use App\Http\Controllers\WEB\Admin\PurchasedCoinsController;
use App\Http\Controllers\WEB\Admin\BasePlanController;
use App\Http\Controllers\UserAuthController;
// use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ZohoInvoiceController;
use App\Http\Controllers\ZohoController;

// Route::post('/user/coinpurchase-test', [CheckoutController::class, 'purchasestore']);
// Route::post('/admin/order-cancel/{order}',
//     [\App\Http\Controllers\WEB\Admin\OrderController::class, 'cancel']
// )->name('admin.order.cancel');
// Route::post('/user-login', [UserAuthController::class, 'login']);
// Route::get('/user-me', [UserAuthController::class, 'me'])->middleware('auth:api');
// Route::post('/user-logout', [UserAuthController::class, 'logout'])->middleware('auth:api');
Route::post('/zoho/sync-order/{order}', [ZohoController::class, 'syncOrder']);

Route::post('/order-cancel/{order}', [OrderController::class, 'cancel']);
Route::post('/zoho/create-invoice', [ZohoInvoiceController::class, 'handlePaymentSuccess']);

Route::get('/test-shiprocket', function (\App\Services\ShiprocketService $shiprocket) {
    return ['message' => 'Shiprocket service loaded'];
});
Route::post('/ship-order/{orderId}', [ShippingController::class, 'shipOrder']);
Route::post('/check-pincode', [ShippingController::class, 'checkPincode']);


Route::post('social-login', [LoginController::class, 'sociallogin'])
    ->withoutMiddleware([
        \App\Http\Middleware\RedirectIfAuthenticated::class,
        'auth:admin-api',
        'auth:api',
        'Authenticate:admin-api',
    ]);

Route::group([
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);

});
Route::group(['middleware' => ['demo','XSS']], function () {

Route::group([], function () {
    Route::get('/coin-campaigns', [CoinCampaignController::class, 'indexAPI'])->name('coin-campaigns');

    Route::get('/baseplans', [BasePlanController::class, 'indexAPI'])->name('baseplans');

    Route::get('/winners', [CoinCampaignController::class, 'winnerslist'])->name('winners');
    Route::get('/homepage', [CoinCampaignController::class, 'homepage'])->name('homepage');



    Route::get('/coin-campaigns/details/{id}', [CoinCampaignController::class, 'details'])->name('coin-campaigns-show');
    Route::get('/website-setup', [HomeController::class, 'websiteSetup'])->name('website-setup');
    Route::get('/subcategory-by-category/{id}', [HomeController::class, 'subCategoriesByCategory'])->name('subcategory-by-category');
    Route::get('/childcategory-by-subcategory/{id}', [HomeController::class, 'childCategoriesBySubCategory'])->name('childcategory-by-subcategory');
    Route::get('/category-list', [HomeController::class, 'categoryList'])->name('category-list');
    Route::get('/category/{id}', [HomeController::class, 'category'])->name('category');
    Route::get('/sub-category/{id}', [HomeController::class, 'subCategory'])->name('sub-category');
    Route::get('/child-category/{id}', [HomeController::class, 'childCategory'])->name('child-category');

    Route::get('/product-by-category/{id}', [HomeController::class, 'productByCategory'])->name('product-by-category');

    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/webinfo', [HomeController::class, 'index'])->name('home');
    Route::get('/about-us', [HomeController::class, 'aboutUs'])->name('about-us');
    Route::get('/contact-us', [HomeController::class, 'contactUs'])->name('contact-us');
    Route::post('/send-contact-message', [HomeController::class, 'sendContactMessage'])->name('send-contact-message');
    Route::get('/blog', [HomeController::class, 'blog'])->name('blog');
    Route::get('/blog/{slug}', [HomeController::class, 'blogDetail'])->name('blog-detail');
    Route::post('/blog-comment', [HomeController::class, 'blogComment'])->name('blog-comment');

    Route::get('/track-order-response/{id}', [HomeController::class, 'trackOrderResponse'])->name('track-order-response');
    Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
    Route::get('/page', [HomeController::class, 'allCustomPage'])->name('custom-page');
    Route::get('/page/{slug}', [HomeController::class, 'customPage'])->name('page');
    Route::get('/terms-and-conditions', [HomeController::class, 'termsAndCondition'])->name('terms-and-conditions');
    Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy-policy');
    Route::get('/seller-terms-conditoins', [HomeController::class, 'sellerTemsCondition'])->name('seller-terms-conditoins');

    Route::get('/sellers', [HomeController::class, 'seller'])->name('sellers');
    Route::get('/sellers/{shop_name}', [HomeController::class, 'sellerDetail'])->name('seller-detail');
    Route::get('/product', [HomeController::class, 'product'])->name('product');
    Route::get('/variant-items-by-variant/{variant_name}', [HomeController::class, 'variantItemsByVariant'])->name('variant-items-by-variant');
    Route::get('/search-product', [HomeController::class, 'searchProduct'])->name('search-product');
    Route::get('/product/{slug}', [HomeController::class, 'productDetail'])->name('product-detail');
    Route::get('/product-review-list/{id}', [HomeController::class, 'productReviewList'])->name('product-review-list');

    Route::get('/compare', [HomeController::class, 'compare'])->name('compare');
    Route::get('/add-to-compare/{id}', [HomeController::class, 'addToCompare'])->name('add-to-compare');
    Route::get('/remove-compare/{id}', [HomeController::class, 'removeCompare'])->name('remove-compare');
    Route::get('/flash-sale', [HomeController::class, 'flashSale'])->name('flash-sale');
    Route::post('subscribe-request', [HomeController::class, 'subscribeRequest'])->name('subscribe-request');
    Route::get('subscriber-verification/{token}', [HomeController::class, 'subscriberVerifcation'])->name('subscriber-verification');

    Route::get('/cart', [CartController::class, 'cart'])->name('cart');
    Route::get('/add-to-cart', [CartController::class, 'addToCart'])->name('add-to-cart');
    Route::get('/cart-clear', [CartController::class, 'cartClear'])->name('cart-clear');
    Route::get('/cart-item-remove/{id}', [CartController::class, 'cartItemRemove'])->name('cart-item-remove');
    Route::get('/cart-item-increment/{id}', [CartController::class, 'cartItemIncrement'])->name('cart-item-increment');
    Route::get('/cart-item-decrement/{id}', [CartController::class, 'cartItemDecrement'])->name('cart-item-decrement');

    Route::get('/apply-coupon', [CartController::class, 'applyCoupon'])->name('apply-coupon');
    Route::post('/reedemcoins', [CartController::class, 'reedemcoins'])->name('reedemcoins');

    Route::get('/calculate-product-price', [CartController::class, 'calculateProductPrice'])->name('calculate-product-price');

    Route::get('login/google',[LoginController::class, 'redirectToGoogle'])->name('login-google');
    Route::get('/callback/google',[LoginController::class,'googleCallBack'])->name('callback-google');

    Route::get('login/facebook',[LoginController::class, 'redirectToFacebook'])->name('login-facebook');
    Route::get('/callback/facebook',[LoginController::class,'facebookCallBack'])->name('callback-facebook');


    Route::get('/login', [LoginController::class, 'loginPage'])->name('login');
    Route::post('/store-login', [LoginController::class, 'storeLogin'])->name('store-login');
    Route::post('/verify-otp', [LoginController::class, 'verifyOtp'])->name('verifyOtp');

    Route::post('/logintrigger', [LoginController::class, 'logintrigger'])->name('logintrigger');

    Route::post('/resend-register-code', [RegisterController::class, 'resendRegisterCode'])->name('resend-register-code');
    Route::post('/store-register', [RegisterController::class, 'storeRegister'])->name('store-register');
    Route::get('/user-verification/{token}/{id}', [RegisterController::class, 'userVerification'])->name('user-verification');
    Route::get('/forget-password', [LoginController::class, 'forgetPage'])->name('forget-password');
    Route::post('/send-forget-password', [LoginController::class, 'sendForgetPassword'])->name('send-forget-password');
    Route::get('/reset-password/{token}', [LoginController::class, 'resetPasswordPage'])->name('reset-password');
    Route::post('/store-reset-password/{token}', [LoginController::class, 'storeResetPasswordPage'])->name('store-reset-password');
    Route::get('/user/logout', [LoginController::class, 'userLogout'])->name('user.logout');

    Route::group(['as'=> 'user.', 'prefix' => 'user'],function (){

        // Newsletter subscription
        Route::post('/subscribe-newsletter', [UserDashboardController::class, 'subscribeNewsletter'])->name('subscribe-newsletter');

        // Dashboard statistics
        Route::get('/my-dashboard', [UserDashboardController::class, 'myDashboard'])->name('my-dashboard');

        // My purchased campaigns
        Route::get('/my-campaigns', [UserDashboardController::class, 'myCampaigns'])->name('my-campaigns');

        // Deactivate account
        Route::post('/deactivate-account', [UserDashboardController::class, 'deactivateAccount'])->name('deactivate-account');

        Route::post('/coinpurchase', [CheckoutController::class, 'purchasestore']);
        // Route::get('/coinpurchase', [CheckoutController::class, 'purchasestore']);
        Route::get('/getcoupons', [CheckoutController::class, 'generateCoupons']);

        Route::post('/singlecoderegenerate', [CheckoutController::class, 'singlecoderegenerate']);


        Route::get('/mycoupons', [CheckoutController::class, 'mycoupons']);

        Route::post('/payment_status', [CheckoutController::class, 'payment_status']);

        Route::post('/checkwinnerclaim', [CheckoutController::class, 'checkwinnerclaim'])->name('checkwinnerclaim');

        Route::get('/mystatements', [CheckoutController::class, 'mystatements']);

        Route::get('/my-purchases', [CheckoutController::class, 'myPurchases']);
        Route::get('/purchasedetails/{id}', [CheckoutController::class, 'Purchasedetails']);

        Route::get('dashboard', [UserProfileController::class, 'dashboard'])->name('dashboard');
        Route::get('order', [UserProfileController::class, 'order'])->name('order');
        Route::get('pending-order', [UserProfileController::class, 'pendingOrder'])->name('pending-order');
        Route::get('complete-order', [UserProfileController::class, 'completeOrder'])->name('complete-order');
        Route::get('declined-order', [UserProfileController::class, 'declinedOrder'])->name('declined-order');
        Route::get('order-show/{id}', [UserProfileController::class, 'orderShow'])->name('order-show');
        Route::get('review', [UserProfileController::class, 'review'])->name('review');
        Route::get('get-review/{id}', [UserProfileController::class, 'showReview'])->name('show-review');
        Route::get('my-profile', [UserProfileController::class, 'myProfile'])->name('my-profile');
        Route::post('v1/update-profile', [UserProfileController::class, 'v1updateProfile'])->name('v1updateProfile');
       Route::post('v2/update-profile', [UserProfileController::class, 'newupdateProfile'])->name('newupdateProfile');
        Route::post('update-profile', [UserProfileController::class, 'updateProfile'])->name('update-profile');
        Route::get('address', [UserProfileController::class, 'address'])->name('address');
        Route::post('update-password', [UserProfileController::class, 'updatePassword'])->name('update-password');


        Route::resource('address', AddressCotroller::class);

        Route::get('compare-product', [UserProfileController::class, 'compareProducts'])->name('compare-product');
        Route::get('add-compare-product/{id}', [UserProfileController::class, 'addCompareProducts'])->name('add-compare-product');
        Route::delete('delete-compare-product/{id}', [UserProfileController::class, 'deleteCompareProduct'])->name('delete-compare-product');

        Route::post('seller-request', [UserProfileController::class, 'sellerRequest'])->name('seller-request');
        Route::get('wishlist', [UserProfileController::class, 'wishlist'])->name('wishlist');
        Route::get('add-to-wishlist/{id}', [UserProfileController::class, 'addToWishlist'])->name('add-to-wishlist');
        Route::get('remove-wishlist/{id}', [UserProfileController::class, 'removeWishlist'])->name('remove-wishlist');
        Route::get('clear-wishlist', [UserProfileController::class, 'clearWishlist'])->name('clear-wishlist');
        Route::post('product-report', [UserProfileController::class, 'storeProductReport'])->name('product-report');
        Route::post('store-product-review', [UserProfileController::class, 'storeProductReview'])->name('store-product-review');
        Route::post('update-review/{id}', [UserProfileController::class, 'updateReview'])->name('update-review');

        Route::delete('remove-account', [UserProfileController::class, 'remove_account'])->name('remove-account');

        Route::get('message-with-seller', [MessageController::class, 'index'])->name('message-with-seller');
        Route::post('send-message-to-seller', [MessageController::class, 'send_message_to_seller'])->name('send-message-to-seller');
        Route::get('load-active-seller-message/{id}', [MessageController::class, 'laod_active_seller_message'])->name('load-active-seller-message');



        Route::get('chat-with-seller/{slug}', [MessageController::class, 'chatWithSeller'])->name('chat-with-seller');
        Route::get('message', [MessageController::class, 'index'])->name('message');
        Route::get('load-chat-box/{id}', [MessageController::class, 'loadChatBox'])->name('load-chat-box');
        Route::get('load-new-message/{id}', [MessageController::class, 'loadNewMessage'])->name('load-new-message');
        Route::get('send-message', [MessageController::class, 'sendMessage'])->name('send-message');

        Route::group(['as'=> 'checkout.', 'prefix' => 'checkout'],function (){
            Route::get('/', [CheckoutController::class, 'checkout'])->name('checkout');

            Route::post('/cash-on-delivery', [PaymentController::class, 'cashOnDelivery'])->name('cash-on-delivery');
            Route::post('/pay-with-stripe', [PaymentController::class, 'payWithStripe'])->name('pay-with-stripe');



            Route::post('/pay-with-bank', [PaymentController::class, 'payWithBank'])->name('pay-with-bank');
        });

        Route::get('state-by-country/{id}', [UserProfileController::class, 'stateByCountry'])->name('state-by-country');
        Route::get('city-by-state/{id}', [UserProfileController::class, 'cityByState'])->name('city-by-state');
    });


    Route::group(['as'=> 'seller.', 'prefix' => 'seller','middleware' => ['checkseller']],function (){
        Route::get('dashboard',[SellerDashboardController::class,'index'])->name('dashboard');
        Route::get('my-profile',[SellerProfileController::class,'index'])->name('my-profile');
        Route::get('state-by-country/{id}',[SellerProfileController::class,'stateByCountry'])->name('state-by-country');
        Route::get('city-by-state/{id}',[SellerProfileController::class,'cityByState'])->name('city-by-state');
        Route::post('update-seller-profile',[SellerProfileController::class,'updateSellerProfile'])->name('update-seller-profile');
        Route::get('change-password',[SellerProfileController::class,'changePassword'])->name('change-password');
        Route::put('password-update',[SellerProfileController::class,'updatePassword'])->name('password-update');
        Route::get('shop-profile',[SellerProfileController::class,'myShop'])->name('shop-profile');
        Route::post('update-seller-shop',[SellerProfileController::class,'updateSellerSop'])->name('update-seller-shop');
        Route::put('remove-seller-social-link/{id}',[SellerProfileController::class,'removeSellerSocialLink'])->name('remove-seller-social-link');
        Route::get('email-history',[SellerProfileController::class,'emailHistory'])->name('email-history');

        Route::resource('product', SellerProductController::class);
        Route::post('update-product/{id}', [SellerProductController::class, 'update'])->name('update-product');

        Route::put('product-status/{id}', [SellerProductController::class,'changeStatus'])->name('product.status');
        Route::put('removed-product-exist-specification/{id}', [SellerProductController::class,'removedProductExistSpecification'])->name('removed-product-exist-specification');

        Route::resource('brand', SellerBrandController::class);
        Route::put('brand-status/{id}', [SellerBrandController::class,'changeStatus'])->name('brand.status');
        Route::get('pending-product', [SellerProductController::class,'pendingProduct'])->name('pending-product');
        Route::get('product-highlight/{id}', [SellerProductController::class,'productHighlight'])->name('product-highlight');
        Route::put('update-product-highlight/{id}', [SellerProductController::class,'productHighlightUpdate'])->name('update-product-highlight');


        Route::get('subcategory-by-category/{id}', [SellerProductController::class,'getSubcategoryByCategory'])->name('subcategory-by-category');
        Route::get('childcategory-by-subcategory/{id}', [SellerProductController::class,'getChildcategoryBySubCategory'])->name('childcategory-by-subcategory');


        Route::get('product-variant/{id}', [SellerProductVariantController::class,'index'])->name('product-variant');
        Route::get('create-product-variant/{id}', [SellerProductVariantController::class,'create'])->name('create-product-variant');
        Route::post('store-product-variant', [SellerProductVariantController::class,'store'])->name('store-product-variant');
        Route::get('get-product-variant/{id}', [SellerProductVariantController::class,'show'])->name('get-product-variant');
        Route::get('edit-product-variant/{id}', [SellerProductVariantController::class,'edit'])->name('edit-product-variant');
        Route::put('update-product-variant/{id}', [SellerProductVariantController::class,'update'])->name('update-product-variant');
        Route::delete('delete-product-variant/{id}', [SellerProductVariantController::class,'destroy'])->name('delete-product-variant');
        Route::put('product-variant-status/{id}', [SellerProductVariantController::class,'changeStatus'])->name('product-variant.status');

        Route::get('product-variant-item', [SellerProductVariantItemController::class,'index'])->name('product-variant-item');
        Route::get('create-product-variant-item/{id}', [SellerProductVariantItemController::class,'create'])->name('create-product-variant-item');
        Route::post('store-product-variant-item', [SellerProductVariantItemController::class,'store'])->name('store-product-variant-item');
        Route::get('edit-product-variant-item/{id}', [SellerProductVariantItemController::class,'edit'])->name('edit-product-variant-item');

        Route::get('get-product-variant-item/{id}', [SellerProductVariantItemController::class,'show'])->name('egetdit-product-variant-item');

        Route::put('update-product-variant-item/{id}', [SellerProductVariantItemController::class,'update'])->name('update-product-variant-item');
        Route::delete('delete-product-variant-item/{id}', [SellerProductVariantItemController::class,'destroy'])->name('delete-product-variant-item');
        Route::put('product-variant-item-status/{id}', [SellerProductVariantItemController::class,'changeStatus'])->name('product-variant-item.status');

        Route::get('product-gallery/{id}', [SellerProductGalleryController::class,'index'])->name('product-gallery');
        Route::post('store-product-gallery', [SellerProductGalleryController::class,'store'])->name('store-product-gallery');
        Route::delete('delete-product-image/{id}', [SellerProductGalleryController::class,'destroy'])->name('delete-product-image');
        Route::put('product-gallery-status/{id}', [SellerProductGalleryController::class,'changeStatus'])->name('product-gallery.status');


        Route::get('product-review',[SellerProductReviewController::class,'index'])->name('product-review');
        Route::put('product-review-status/{id}',[SellerProductReviewController::class,'changeStatus'])->name('product-review-status');
        Route::get('show-product-review/{id}',[SellerProductReviewController::class,'show'])->name('show-product-review');


        Route::get('product-report',[SellerProductReportControler::class, 'index'])->name('product-report');
        Route::get('show-product-report/{id}',[SellerProductReportControler::class, 'show'])->name('show-product-report');

        Route::resource('my-withdraw', WithdrawController::class);
        Route::get('get-withdraw-account-info/{id}', [WithdrawController::class, 'getWithDrawAccountInfo'])->name('get-withdraw-account-info');

        Route::get('all-order', [SellerOrderController::class, 'index'])->name('all-order');
        Route::get('pending-order', [SellerOrderController::class, 'pendingOrder'])->name('pending-order');
        Route::get('progress-order', [SellerOrderController::class, 'progressOrder'])->name('progress-order');
        Route::get('delivered-order', [SellerOrderController::class, 'deliveredOrder'])->name('delivered-order');
        Route::get('completed-order', [SellerOrderController::class, 'completedOrder'])->name('completed-order');
        Route::get('declined-order', [SellerOrderController::class, 'declinedOrder'])->name('declined-order');
        Route::get('cash-on-delivery', [SellerOrderController::class, 'cashOnDelivery'])->name('cash-on-delivery');
        Route::get('order-show/{id}', [SellerOrderController::class, 'show'])->name('order-show');

        Route::get('message', [SellerMessageContoller::class, 'index'])->name('message');
        Route::get('load-chat-box/{id}', [SellerMessageContoller::class, 'loadChatBox'])->name('load-chat-box');
        Route::get('load-new-message/{id}', [SellerMessageContoller::class, 'loadNewMessage'])->name('load-new-message');
        Route::get('send-message', [SellerMessageContoller::class, 'sendMessage'])->name('send-message');
    });
});

/*
|--------------------------------------------------------------------------
| Seller Application APIs (Onboarding)
|--------------------------------------------------------------------------
*/

// Public APIs
Route::get('/store-categories', [\App\Http\Controllers\API\SellerApplicationController::class, 'getCategories']);
Route::post('/seller/apply', [\App\Http\Controllers\API\SellerApplicationController::class, 'apply']);
Route::get('/seller/application-status', [\App\Http\Controllers\API\SellerApplicationController::class, 'checkStatus']);

// Admin APIs for Seller Applications
Route::prefix('admin')->middleware('auth:admin-api')->group(function () {
    Route::get('/seller-applications', [\App\Http\Controllers\WEB\Admin\SellerApplicationController::class, 'apiIndex']);
    Route::get('/seller-applications/{applicationId}', [\App\Http\Controllers\WEB\Admin\SellerApplicationController::class, 'apiShow']);
    Route::patch('/seller-applications/{applicationId}/verify', [\App\Http\Controllers\WEB\Admin\SellerApplicationController::class, 'apiVerify']);
    Route::patch('/seller-applications/{applicationId}/approve', [\App\Http\Controllers\WEB\Admin\SellerApplicationController::class, 'apiApprove']);
    Route::patch('/seller-applications/{applicationId}/reject', [\App\Http\Controllers\WEB\Admin\SellerApplicationController::class, 'apiReject']);
});

/*
|--------------------------------------------------------------------------
| Store/Seller Panel APIs (New)
|--------------------------------------------------------------------------
| Kept in separate file to keep store module organized.
*/
require __DIR__.'/store_api.php';

    //delivery man routes
    Route::post('deliveryman/registration', [DeliveryManRegistrationController::class,'registration'])->name('delivery.man.registration');
    Route::post('deliveryman/login', [DeliveryManLoginController::class,'dashboardLogin'])->name('delivery.man.login');
    Route::get('deliveryman/logout',[DeliveryManLoginController::class,'logout'])->name('deliveryman.logout');

    Route::post('deliveryman/password/reset/email',[DeliveryManResetPasswordController::class,'passwrodResetEmail'])->name('deliveryman.password.reset.email');
    Route::put('deliveryman/password/update',[DeliveryManResetPasswordController::class,'passwrodUpdate'])->name('deliveryman.pasword.update');

    Route::group(['as'=> 'deliveryman.', 'prefix' => 'deliveryman', 'middleware'=>'deliverymanapi'],function (){
         Route::get('dashboard',[DeliveryManDashboardController::class,'index'])->name('dashboard');
         Route::get('my-profile',[DeliveryManProfileController::class,'index'])->name('my-profile');
         Route::get('edit-profile',[DeliveryManProfileController::class,'edit'])->name('edit-profile');
         Route::post('update-profile',[DeliveryManProfileController::class,'update'])->name('update-profile');
         Route::put('update-password',[DeliveryManProfileController::class,'updatePassword'])->name('update-password');
         Route::get('orders',[DeliveryManOrderController::class,'index'])->name('orders');
         Route::get('order-request',[DeliveryManOrderController::class,'orderRequest'])->name('order-request');
         Route::put('order-request-status/{id}',[DeliveryManOrderController::class,'orderRequestStatus'])->name('order-request-status');

         Route::get('completed-order',[DeliveryManOrderController::class,'completedOrder'])->name('completed-order');
         Route::get('order-show/{id}',[DeliveryManOrderController::class,'show'])->name('order-show');

         Route::put('update-order-status/{id}',[DeliveryManOrderController::class,'updateOrderStatus'])->name('update-order-status');

         Route::resource('withdraw', MyWithdrawController::class);

         Route::get('get-withdraw-account-info/{id}', [MyWithdrawController::class, 'getWithDrawAccountInfo'])->name('get-withdraw-account-info');

         Route::get('my-review', [MyReviewController::class, 'index'])->name('my-review');

        Route::get('message-with-customer/{order_id}', [DeliveryMessageController::class, 'message_with_customer'])->name('message-with-customer');
        Route::get('get-message-with-customer/{order_id}', [DeliveryMessageController::class, 'get_message_with_customer'])->name('get-message-with-customer');
        Route::post('sent-message-to-customer', [DeliveryMessageController::class, 'sent_message_to_customer'])->name('sent-message-to-customer');

        Route::get('logout',[DeliveryManLoginController::class,'logout'])->name('logout');
    });


    // API routes for mobile/frontend apps

Route::get('/image-items', [ImageItemController::class, 'apiIndex']);
Route::get('/image-items/by-type', [ImageItemController::class, 'apiByType']);


// start admin routes
Route::group(['as'=> 'admin.', 'prefix' => 'admin'],function (){

    // start auth route
    Route::get('login', [AdminLoginController::class,'adminLoginPage'])->name('login');
    Route::post('login', [AdminLoginController::class,'storeLogin'])->name('login');
    Route::post('logout', [AdminLoginController::class,'adminLogout'])->name('logout');
    Route::get('forget-password', [AdminForgotPasswordController::class,'forgetPassword'])->name('forget-password');
    Route::post('send-forget-password', [AdminForgotPasswordController::class,'sendForgetEmail'])->name('send.forget.password');
    Route::get('reset-password/{token}', [AdminForgotPasswordController::class,'resetPassword'])->name('reset.password');
    Route::post('password-store/{token}', [AdminForgotPasswordController::class,'storeResetData'])->name('store.reset.password');
    // end auth route

    Route::get('/', [DashboardController::class,'dashobard'])->name('dashboard');
    Route::get('dashboard', [DashboardController::class,'dashobard'])->name('dashboard');
    Route::get('profile', [AdminProfileController::class,'index'])->name('profile');
    Route::put('profile-update', [AdminProfileController::class,'update'])->name('profile.update');

    Route::resource('product-category', ProductCategoryController::class);
    Route::put('product-category-status/{id}', [ProductCategoryController::class,'changeStatus'])->name('product.category.status');

    Route::resource('product-sub-category', ProductSubCategoryController::class);
    Route::put('product-sub-category-status/{id}', [ProductSubCategoryController::class,'changeStatus'])->name('product.sub.category.status');

    Route::resource('product-child-category', ProductChildCategoryController::class);
    Route::put('product-child-category-status/{id}', [ProductChildCategoryController::class,'changeStatus'])->name('product.child.category.status');
    Route::get('subcategory-by-category/{id}', [ProductChildCategoryController::class,'getSubcategoryByCategory'])->name('subcategory-by-category');
    Route::get('childcategory-by-subcategory/{id}', [ProductChildCategoryController::class,'getChildcategoryBySubCategory'])->name('childcategory-by-subcategory');

    Route::resource('product-brand', ProductBrandController::class);
    Route::put('product-brand-status/{id}', [ProductBrandController::class,'changeStatus'])->name('product.brand.status');
    Route::put('product-brand-approval-status/{id}', [ProductBrandController::class,'changeApprovalStatus'])->name('product-brand-approval-status');

    Route::resource('specification-key', SpecificationKeyController::class);
    Route::put('specification-key-status/{id}', [SpecificationKeyController::class,'changeStatus'])->name('specification-key.status');

    Route::resource('testimonial', TestimonialController::class);
    Route::put('testimonial-status/{id}', [TestimonialController::class,'changeStatus'])->name('testimonial.status');

    Route::resource('product', ProductController::class);
    Route::get('create-product-info', [ProductController::class,'create'])->name('create-product-info');
    Route::put('product-status/{id}', [ProductController::class,'changeStatus'])->name('product.status');
    Route::put('product-approval-status/{id}', [ProductController::class,'changeApprovalStatus'])->name('product-approval-status');
    Route::put('removed-product-exist-specification/{id}', [ProductController::class,'removedProductExistSpecification'])->name('removed-product-exist-specification');
    Route::get('seller-product', [ProductController::class,'sellerProduct'])->name('seller-product');
    Route::get('seller-pending-product', [ProductController::class,'sellerPendingProduct'])->name('seller-pending-product');
    Route::get('product-highlight/{id}', [ProductController::class,'productHighlight'])->name('product-highlight');
    Route::put('update-product-highlight/{id}', [ProductController::class,'productHighlightUpdate'])->name('update-product-highlight');



    Route::get('product-variant/{id}', [ProductVariantController::class,'index'])->name('product-variant');
    Route::get('create-product-variant/{id}', [ProductVariantController::class,'create'])->name('create-product-variant');
    Route::post('store-product-variant', [ProductVariantController::class,'store'])->name('store-product-variant');
    Route::get('get-product-variant/{id}', [ProductVariantController::class,'show'])->name('get-product-variant');
    Route::put('update-product-variant/{id}', [ProductVariantController::class,'update'])->name('update-product-variant');
    Route::delete('delete-product-variant/{id}', [ProductVariantController::class,'destroy'])->name('delete-product-variant');
    Route::put('product-variant-status/{id}', [ProductVariantController::class,'changeStatus'])->name('product-variant.status');

    Route::get('product-variant-item', [ProductVariantItemController::class,'index'])->name('product-variant-item');
    Route::get('create-product-variant-item/{id}', [ProductVariantItemController::class,'create'])->name('create-product-variant-item');
    Route::post('store-product-variant-item', [ProductVariantItemController::class,'store'])->name('store-product-variant-item');
    Route::get('edit-product-variant-item/{id}', [ProductVariantItemController::class,'edit'])->name('edit-product-variant-item');
    Route::get('get-product-variant-item/{id}', [ProductVariantItemController::class,'show'])->name('egetdit-product-variant-item');

    Route::put('update-product-variant-item/{id}', [ProductVariantItemController::class,'update'])->name('update-product-variant-item');
    Route::delete('delete-product-variant-item/{id}', [ProductVariantItemController::class,'destroy'])->name('delete-product-variant-item');
    Route::put('product-variant-item-status/{id}', [ProductVariantItemController::class,'changeStatus'])->name('product-variant-item.status');


    Route::get('product-gallery/{id}', [ProductGalleryController::class,'index'])->name('product-gallery');
    Route::post('store-product-gallery', [ProductGalleryController::class,'store'])->name('store-product-gallery');
    Route::delete('delete-product-image/{id}', [ProductGalleryController::class,'destroy'])->name('delete-product-image');
    Route::put('product-gallery-status/{id}', [ProductGalleryController::class,'changeStatus'])->name('product-gallery.status');

    Route::resource('service', ServiceController::class);
    Route::put('service-status/{id}', [ServiceController::class,'changeStatus'])->name('service.status');

    Route::resource('about-us', AboutUsController::class);
    Route::resource('contact-us', ContactPageController::class);

    Route::resource('custom-page', CustomPageController::class);
    Route::put('custom-page-status/{id}', [CustomPageController::class,'changeStatus'])->name('custom-page.status');

    Route::resource('terms-and-condition', TermsAndConditionController::class);
    Route::resource('privacy-policy', PrivacyPolicyController::class);

    Route::resource('blog-category', BlogCategoryController::class);
    Route::put('blog-category-status/{id}', [BlogCategoryController::class,'changeStatus'])->name('blog.category.status');

    Route::resource('blog', BlogController::class);
    Route::put('blog-status/{id}', [BlogController::class,'changeStatus'])->name('blog.status');

    Route::resource('popular-blog', PopularBlogController::class);
    Route::put('popular-blog-status/{id}', [PopularBlogController::class,'changeStatus'])->name('popular-blog.status');

    Route::resource('blog-comment', BlogCommentController::class);
    Route::put('blog-comment-status/{id}', [BlogCommentController::class,'changeStatus'])->name('blog-comment.status');



    Route::get('clear-database',[SettingController::class,'showClearDatabasePage'])->name('clear-database');
    Route::delete('clear-database',[SettingController::class,'clearDatabase'])->name('clear-database');

    Route::get('subscriber',[SubscriberController::class,'index'])->name('subscriber');
    Route::delete('delete-subscriber/{id}',[SubscriberController::class,'destroy'])->name('delete-subscriber');
    Route::post('specification-subscriber-email/{id}',[SubscriberController::class,'specificationSubscriberEmail'])->name('specification-subscriber-email');
    Route::post('each-subscriber-email',[SubscriberController::class,'eachSubscriberEmail'])->name('each-subscriber-email');

    Route::get('contact-message',[ContactMessageController::class,'index'])->name('contact-message');
    Route::delete('delete-contact-message/{id}',[ContactMessageController::class,'destroy'])->name('delete-contact-message');
    Route::put('enable-save-contact-message',[ContactMessageController::class,'handleSaveContactMessage'])->name('enable-save-contact-message');

    Route::get('email-configuration',[EmailConfigurationController::class,'index'])->name('email-configuration');
    Route::put('update-email-configuraion',[EmailConfigurationController::class,'update'])->name('update-email-configuraion');

    Route::get('email-template',[EmailTemplateController::class,'index'])->name('email-template');
    Route::get('edit-email-template/{id}',[EmailTemplateController::class,'edit'])->name('edit-email-template');
    Route::put('update-email-template/{id}',[EmailTemplateController::class,'update'])->name('update-email-template');

    Route::get('general-setting',[SettingController::class,'index'])->name('general-setting');
    Route::put('update-general-setting',[SettingController::class,'updateGeneralSetting'])->name('update-general-setting');

    Route::put('update-theme-color',[SettingController::class,'updateThemeColor'])->name('update-theme-color');

    Route::put('update-logo-favicon',[SettingController::class,'updateLogoFavicon'])->name('update-logo-favicon');
    Route::put('update-cookie-consent',[SettingController::class,'updateCookieConset'])->name('update-cookie-consent');
    Route::put('update-google-recaptcha',[SettingController::class,'updateGoogleRecaptcha'])->name('update-google-recaptcha');
    Route::put('update-facebook-comment',[SettingController::class,'updateFacebookComment'])->name('update-facebook-comment');
    Route::put('update-tawk-chat',[SettingController::class,'updateTawkChat'])->name('update-tawk-chat');
    Route::put('update-google-analytic',[SettingController::class,'updateGoogleAnalytic'])->name('update-google-analytic');
    Route::put('update-custom-pagination',[SettingController::class,'updateCustomPagination'])->name('update-custom-pagination');
    Route::put('update-social-login',[SettingController::class,'updateSocialLogin'])->name('update-social-login');
    Route::put('update-facebook-pixel',[SettingController::class,'updateFacebookPixel'])->name('update-facebook-pixel');
    Route::put('update-pusher',[SettingController::class,'updatePusher'])->name('update-pusher');


    Route::resource('admin', AdminController::class);
    Route::put('admin-status/{id}', [AdminController::class,'changeStatus'])->name('admin-status');

    Route::resource('faq', FaqController::class);
    Route::put('faq-status/{id}', [FaqController::class,'changeStatus'])->name('faq-status');


    Route::get('product-review',[ProductReviewController::class,'index'])->name('product-review');
    Route::put('product-review-status/{id}',[ProductReviewController::class,'changeStatus'])->name('product-review-status');
    Route::get('show-product-review/{id}',[ProductReviewController::class,'show'])->name('show-product-review');
    Route::delete('delete-product-review/{id}',[ProductReviewController::class,'destroy'])->name('delete-product-review');

    Route::get('product-report',[ProductReportController::class, 'index'])->name('product-report');
    Route::get('show-product-report/{id}',[ProductReportController::class, 'show'])->name('show-product-report');
    Route::delete('delete-product-report/{id}',[ProductReportController::class, 'destroy'])->name('delete-product-report');
    Route::put('de-active-product/{id}',[ProductReportController::class, 'deactiveProduct'])->name('de-active-product');

    Route::get('customer-list',[CustomerController::class,'index'])->name('customer-list');
    Route::get('customer-show/{id}',[CustomerController::class,'show'])->name('customer-show');
    Route::put('customer-status/{id}',[CustomerController::class,'changeStatus'])->name('customer-status');
    Route::delete('customer-delete/{id}',[CustomerController::class,'destroy'])->name('customer-delete');
    Route::get('pending-customer-list',[CustomerController::class,'pendingCustomerList'])->name('pending-customer-list');
    Route::get('send-email-to-all-customer',[CustomerController::class,'sendEmailToAllUser'])->name('send-email-to-all-customer');
    Route::post('send-mail-to-all-user',[CustomerController::class,'sendMailToAllUser'])->name('send-mail-to-all-user');
    Route::post('send-mail-to-single-user/{id}',[CustomerController::class,'sendMailToSingleUser'])->name('send-mail-to-single-user');


    Route::get('seller-list',[SellerController::class,'index'])->name('seller-list');
    Route::get('seller-show/{id}',[SellerController::class,'show'])->name('seller-show');
    Route::put('seller-status/{id}',[SellerController::class,'changeStatus'])->name('seller-status');
    Route::delete('seller-delete/{id}',[SellerController::class,'destroy'])->name('seller-delete');
    Route::get('pending-seller-list',[SellerController::class,'pendingSellerList'])->name('pending-seller-list');
    Route::put('seller-update/{id}',[SellerController::class,'updateSeller'])->name('seller-update');
    Route::get('seller-shop-detail/{id}',[SellerController::class,'sellerShopDetail'])->name('seller-shop-detail');
    Route::put('remove-seller-social-link/{id}',[SellerController::class,'removeSellerSocialLink'])->name('remove-seller-social-link');


    Route::put('update-seller-shop/{id}',[SellerController::class,'updateSellerSop'])->name('update-seller-shop');
    Route::get('seller-reviews/{id}',[SellerController::class,'sellerReview'])->name('seller-reviews');
    Route::get('show-seller-review-details/{id}',[SellerController::class,'showSellerReviewDetails'])->name('show-seller-review-details');
    Route::get('send-email-to-seller/{id}',[SellerController::class,'sendEmailToSeller'])->name('send-email-to-seller');
    Route::post('send-mail-to-single-seller/{id}',[SellerController::class,'sendMailtoSingleSeller'])->name('send-mail-to-single-seller');
    Route::get('email-history/{id}',[SellerController::class,'emailHistory'])->name('email-history');
    Route::get('product-by-seller/{id}',[SellerController::class,'productBySaller'])->name('product-by-seller');
    Route::get('send-email-to-all-seller',[SellerController::class,'sendEmailToAllSeller'])->name('send-email-to-all-seller');
    Route::post('send-mail-to-all-seller',[SellerController::class,'sendMailToAllSeller'])->name('send-mail-to-all-seller');
    Route::get('withdraw-list/{id}',[SellerController::class,'sellerWithdrawList'])->name('withdraw-list');


    Route::get('state-by-country/{id}',[SellerController::class,'stateByCountry'])->name('state-by-country');
    Route::get('city-by-state/{id}',[SellerController::class,'cityByState'])->name('city-by-state');

    Route::resource('error-page', ErrorPageController::class);

    Route::get('maintainance-mode',[ContentController::class,'maintainanceMode'])->name('maintainance-mode');
    Route::put('maintainance-mode-update',[ContentController::class,'maintainanceModeUpdate'])->name('maintainance-mode-update');

    Route::get('announcement',[ContentController::class,'announcementModal'])->name('announcement');
    Route::post('announcement-update',[ContentController::class,'announcementModalUpdate'])->name('announcement-update');

    Route::get('topbar-contact', [ContentController::class, 'headerPhoneNumber'])->name('topbar-contact');
    Route::put('update-topbar-contact', [ContentController::class, 'updateHeaderPhoneNumber'])->name('update-topbar-contact');

    Route::get('product-quantity-progressbar', [ContentController::class, 'productProgressbar'])->name('product-quantity-progressbar');
    Route::put('update-product-quantity-progressbar', [ContentController::class, 'updateProductProgressbar'])->name('update-product-quantity-progressbar');

    Route::get('default-avatar', [ContentController::class, 'defaultAvatar'])->name('default-avatar');
    Route::post('update-default-avatar', [ContentController::class, 'updateDefaultAvatar'])->name('update-default-avatar');

    Route::get('seller-conditions', [ContentController::class, 'sellerCondition'])->name('seller-conditions');
    Route::put('update-seller-conditions', [ContentController::class, 'updatesellerCondition'])->name('update-seller-conditions');

    Route::get('subscription-banner', [ContentController::class, 'subscriptionBanner'])->name('subscription-banner');
    Route::post('update-subscription-banner', [ContentController::class, 'updatesubscriptionBanner'])->name('update-subscription-banner');




    Route::get('flash-sale', [FlashSaleController::class, 'index'])->name('flash-sale');
    Route::put('update-flash-sale', [FlashSaleController::class, 'update'])->name('update-flash-sale');
    Route::get('flash-sale-product', [FlashSaleController::class, 'flash_sale_product'])->name('flash-sale-product');
    Route::post('store-flash-sale-product', [FlashSaleController::class, 'store'])->name('store-flash-sale-product');
    Route::put('flash-sale-product-status/{id}', [FlashSaleController::class, 'changeStatus'])->name('flash-sale-product-status');
    Route::delete('delete-flash-sale-product/{id}', [FlashSaleController::class,'destroy'])->name('delete-flash-sale-product');


    Route::get('advertisement',[AdvertisementController::class, 'index'])->name('advertisement');

    Route::post('mega-menu-banner-update', [AdvertisementController::class, 'megaMenuBannerUpdate'])->name('mega-menu-banner-update');


    Route::post('slider-banner-one', [AdvertisementController::class, 'updateSliderBannerOne'])->name('slider-banner-one');

    Route::post('slider-banner-two', [AdvertisementController::class, 'updateSliderBannerTwo'])->name('slider-banner-two');

    Route::post('popular-category-sidebar', [AdvertisementController::class, 'updatePopularCategorySidebar'])->name('popular-category-sidebar');


    Route::post('homepage-two-col-first-banner', [AdvertisementController::class, 'homepageTwoColFirstBanner'])->name('homepage-two-col-first-banner');


    Route::post('homepage-two-col-second-banner', [AdvertisementController::class, 'homepageTwoColSecondBanner'])->name('homepage-two-col-second-banner');


    Route::post('homepage-single-first-banner', [AdvertisementController::class, 'homepageSinleFirstBanner'])->name('homepage-single-first-banner');

    Route::post('homepage-single-second-banner', [AdvertisementController::class, 'homepageSinleSecondBanner'])->name('homepage-single-second-banner');


    Route::post('homepage-flash-sale-sidebar-banner', [AdvertisementController::class, 'homepageFlashSaleSidebarBanner'])->name('homepage-flash-sale-sidebar-banner');


    Route::post('shop-page-center-banner', [AdvertisementController::class, 'shopPageCenterBanner'])->name('shop-page-center-banner');

    Route::post('shop-page-sidebar-banner', [AdvertisementController::class, 'shopPageSidebarBanner'])->name('shop-page-sidebar-banner');

    Route::get('login-page', [ContentController::class, 'loginPage'])->name('login-page');
    Route::post('update-login-page', [ContentController::class, 'updateloginPage'])->name('update-login-page');

    Route::get('shop-page',[ContentController::Class, 'shopPage'])->name('shop-page');
    Route::put('update-filter-price',[ContentController::Class, 'updateFilterPrice'])->name('update-filter-price');

    Route::get('seo-setup',[ContentController::Class, 'seoSetup'])->name('seo-setup');
    Route::put('update-seo-setup/{id}',[ContentController::Class, 'updateSeoSetup'])->name('update-seo-setup');
    Route::get('get-seo-setup/{id}',[ContentController::Class, 'getSeoSetup'])->name('get-seo-setup');



    Route::resource('country', CountryController::class);
    Route::put('country-status/{id}',[CountryController::class,'changeStatus'])->name('country-status');

    Route::resource('state', CountryStateController::class);
    Route::put('state-status/{id}',[CountryStateController::class,'changeStatus'])->name('state-status');

    Route::resource('city', CityController::class);
    Route::put('city-status/{id}',[CityController::class,'changeStatus'])->name('city-status');

    Route::get('payment-method',[PaymentMethodController::class,'index'])->name('payment-method');
    Route::put('update-paypal',[PaymentMethodController::class,'updatePaypal'])->name('update-paypal');
    Route::put('update-stripe',[PaymentMethodController::class,'updateStripe'])->name('update-stripe');
    Route::put('update-razorpay',[PaymentMethodController::class,'updateRazorpay'])->name('update-razorpay');
    Route::put('update-bank',[PaymentMethodController::class,'updateBank'])->name('update-bank');
    Route::put('update-mollie',[PaymentMethodController::class,'updateMollie'])->name('update-mollie');
    Route::put('update-paystack',[PaymentMethodController::class,'updatePayStack'])->name('update-paystack');
    Route::put('update-flutterwave',[PaymentMethodController::class,'updateflutterwave'])->name('update-flutterwave');
    Route::put('update-instamojo',[PaymentMethodController::class,'updateInstamojo'])->name('update-instamojo');
    Route::put('update-cash-on-delivery',[PaymentMethodController::class,'updateCashOnDelivery'])->name('update-cash-on-delivery');

    Route::resource('mega-menu-category', MegaMenuController::class);
    Route::put('mega-menu-category-status/{id}',[MegaMenuController::class,'changeStatus'])->name('mega-menu-category-status');

    Route::get('mega-menu-sub-category/{id}', [MegaMenuSubCategoryController::class, 'index'])->name('mega-menu-sub-category');
    Route::get('create-mega-menu-sub-category/{id}', [MegaMenuSubCategoryController::class, 'create'])->name('create-mega-menu-sub-category');
    Route::get('get-mega-menu-sub-category/{id}', [MegaMenuSubCategoryController::class, 'show'])->name('get-mega-menu-sub-category');
    Route::post('store-mega-menu-sub-category/{id}', [MegaMenuSubCategoryController::class, 'store'])->name('store-mega-menu-sub-category');
    Route::get('edit-mega-menu-sub-category/{id}', [MegaMenuSubCategoryController::class, 'edit'])->name('edit-mega-menu-sub-category');
    Route::put('update-mega-menu-sub-category/{id}', [MegaMenuSubCategoryController::class, 'update'])->name('update-mega-menu-sub-category');
    Route::delete('delete-mega-menu-sub-category/{id}', [MegaMenuSubCategoryController::class, 'destroy'])->name('delete-mega-menu-sub-category');
    Route::put('mega-menu-sub-category-status/{id}',[MegaMenuSubCategoryController::class,'changeStatus'])->name('mega-menu-sub-category-status');


    Route::resource('slider', SliderController::class);
    Route::put('slider-status/{id}',[SliderController::class,'changeStatus'])->name('slider-status');


    Route::get('popular-category', [HomePageController::class, 'popularCategory'])->name('popular-category');
    Route::post('store-popular-category', [HomePageController::class, 'storePopularCategory'])->name('store-popular-category');
    Route::delete('destroy-popular-category/{id}', [HomePageController::class, 'destroyPopularCategory'])->name('destroy-popular-category');

    Route::get('featured-category', [HomePageController::class, 'featuredCategory'])->name('featured-category');
    Route::post('store-featured-category', [HomePageController::class, 'storeFeaturedCategory'])->name('store-featured-category');
    Route::delete('destroy-featured-category/{id}', [HomePageController::class, 'destroyFeaturedCategory'])->name('destroy-featured-category');



    Route::get('homepage-visibility', [HomepageVisibilityController::class, 'index'])->name('homepage-visibility');
    Route::put('update-homepage-visibility', [HomepageVisibilityController::class, 'update'])->name('update-homepage-visibility');

    Route::get('menu-visibility', [MenuVisibilityController::class, 'index'])->name('menu-visibility');
    Route::put('update-menu-visibility/{id}', [MenuVisibilityController::class, 'update'])->name('update-menu-visibility');

    Route::resource('shipping', ShippingMethodController::class);
    Route::get('city-wise-shipping/{city_id}', [ShippingMethodController::class , 'cityWiseShipping'])->name('city-wise-shipping');

    Route::resource('withdraw-method', WithdrawMethodController::class);
    Route::put('withdraw-method-status/{id}',[WithdrawMethodController::class,'changeStatus'])->name('withdraw-method-status');

    Route::get('seller-withdraw', [SellerWithdrawController::class, 'index'])->name('seller-withdraw');
    Route::get('pending-seller-withdraw', [SellerWithdrawController::class, 'pendingSellerWithdraw'])->name('pending-seller-withdraw');

    Route::get('show-seller-withdraw/{id}', [SellerWithdrawController::class, 'show'])->name('show-seller-withdraw');
    Route::delete('delete-seller-withdraw/{id}', [SellerWithdrawController::class, 'destroy'])->name('delete-seller-withdraw');
    Route::put('approved-seller-withdraw/{id}', [SellerWithdrawController::class, 'approvedWithdraw'])->name('approved-seller-withdraw');

    Route::get('all-order', [OrderController::class, 'index'])->name('all-order');
    Route::get('pending-order', [OrderController::class, 'pendingOrder'])->name('pending-order');
    Route::get('progress-order', [OrderController::class, 'progressOrder'])->name('progress-order');
    Route::get('delivered-order', [OrderController::class, 'deliveredOrder'])->name('delivered-order');
    Route::get('completed-order', [OrderController::class, 'completedOrder'])->name('completed-order');
    Route::get('declined-order', [OrderController::class, 'declinedOrder'])->name('declined-order');
    Route::get('cash-on-delivery', [OrderController::class, 'cashOnDelivery'])->name('cash-on-delivery');
    Route::get('order-show/{id}', [OrderController::class, 'show'])->name('order-show');
    Route::delete('delete-order/{id}', [OrderController::class, 'destroy'])->name('delete-order');
    Route::put('update-order-status/{id}', [OrderController::class, 'updateOrderStatus'])->name('update-order-status');

    Route::resource('coupon', CouponController::class);
    Route::put('coupon-status/{id}',[CouponController::class,'changeStatus'])->name('coupon-status');

    Route::resource('banner-image', BreadcrumbController::class);

    Route::resource('footer', FooterController::class);
    Route::resource('social-link', FooterSocialLinkController::class);
    Route::resource('footer-link', FooterLinkController::class);
    Route::get('second-col-footer-link', [FooterLinkController::class, 'secondColFooterLink'])->name('second-col-footer-link');
    Route::get('third-col-footer-link', [FooterLinkController::class, 'thirdColFooterLink'])->name('third-col-footer-link');
    Route::put('update-col-title/{id}', [FooterLinkController::class, 'updateColTitle'])->name('update-col-title');


    Route::get('admin-language', [LanguageController::class, 'adminLnagugae'])->name('admin-language');
    Route::post('update-admin-language', [LanguageController::class, 'updateAdminLanguage'])->name('update-admin-language');

    Route::get('admin-validation-language', [LanguageController::class, 'adminValidationLnagugae'])->name('admin-validation-language');
    Route::post('update-admin-validation-language', [LanguageController::class, 'updateAdminValidationLnagugae'])->name('update-admin-validation-language');

    Route::get('website-language', [LanguageController::class, 'websiteLanguage'])->name('website-language');
    Route::post('update-language', [LanguageController::class, 'updateLanguage'])->name('update-language');

    Route::get('website-validation-language', [LanguageController::class, 'websiteValidationLanguage'])->name('website-validation-language');
    Route::post('update-validation-language', [LanguageController::class, 'updateValidationLanguage'])->name('update-validation-language');
});

});
