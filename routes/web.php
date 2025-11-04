<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;


use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\User\AddressCotroller;
use App\Http\Controllers\User\PaypalController;
use App\Http\Controllers\User\MessageController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\WEB\Admin\FaqController;
use App\Http\Controllers\WEB\Admin\BlogController;
use App\Http\Controllers\WEB\Admin\CityController;
use App\Http\Controllers\WEB\Admin\AdminController;
use App\Http\Controllers\WEB\Admin\OrderController;
use App\Http\Controllers\User\UserProfileController;
use App\Http\Controllers\WEB\Admin\CouponController;
use App\Http\Controllers\WEB\Admin\FooterController;
use App\Http\Controllers\WEB\Admin\SellerController;
use App\Http\Controllers\WEB\Admin\SliderController;
use App\Http\Controllers\WEB\Admin\AboutUsController;
use App\Http\Controllers\WEB\Admin\ContentController;
use App\Http\Controllers\WEB\Admin\CountryController;
use App\Http\Controllers\WEB\Admin\ProductController;
use App\Http\Controllers\WEB\Admin\ServiceController;
use App\Http\Controllers\WEB\Admin\SettingController;
use App\Http\Controllers\WEB\Admin\CustomerController;
use App\Http\Controllers\WEB\Admin\HomePageController;
use App\Http\Controllers\WEB\Admin\LanguageController;
use App\Http\Controllers\WEB\Admin\MegaMenuController;
use App\Http\Controllers\WEB\Admin\DashboardController;
use App\Http\Controllers\WEB\Admin\ErrorPageController;
use App\Http\Controllers\WEB\Admin\FlashSaleController;
use App\Http\Controllers\WEB\Admin\InventoryController;
use App\Http\Controllers\WEB\Seller\WithdrawController;
// use App\Http\Controllers\WEB\Admin\BreadcrumbController;
use App\Http\Controllers\WEB\Admin\NewBreadcrumbController;
use App\Http\Controllers\WEB\Admin\CustomPageController;
use App\Http\Controllers\WEB\Admin\FooterLinkController;
use App\Http\Controllers\WEB\Admin\SubscriberController;
use App\Http\Controllers\WEB\Admin\BlogCommentController;
use App\Http\Controllers\WEB\Admin\ContactPageController;
use App\Http\Controllers\WEB\Admin\DeliveryManController;
use App\Http\Controllers\WEB\Admin\PopularBlogController;
use App\Http\Controllers\WEB\Admin\TestimonialController;
use App\Http\Controllers\WEB\Admin\AdminProfileController;
use App\Http\Controllers\WEB\Admin\BlogCategoryController;
use App\Http\Controllers\WEB\Admin\CountryStateController;
use App\Http\Controllers\WEB\Admin\NotificationController;
use App\Http\Controllers\WEB\Admin\ProductBrandController;
use App\Http\Controllers\WEB\Seller\SellerOrderController;
use App\Http\Controllers\WEB\Admin\AdvertisementController;
use App\Http\Controllers\WEB\Admin\EmailTemplateController;
use App\Http\Controllers\WEB\Admin\PaymentMethodController;
use App\Http\Controllers\WEB\Admin\PrivacyPolicyController;
use App\Http\Controllers\WEB\Admin\ProductReportController;
use App\Http\Controllers\WEB\Admin\ProductReviewController;
use App\Http\Controllers\WEB\Seller\SellerMessageContoller;
use App\Http\Controllers\WEB\Admin\ContactMessageController;
use App\Http\Controllers\WEB\Admin\MenuVisibilityController;
use App\Http\Controllers\WEB\Admin\NewMenuVisibilityController;
use App\Http\Controllers\WEB\Admin\ProductGalleryController;
use App\Http\Controllers\WEB\Admin\ProductVariantController;
use App\Http\Controllers\WEB\Admin\SellerWithdrawController;
use App\Http\Controllers\WEB\Admin\CurrencyController;
use App\Http\Controllers\Admin\ImageItemController;
use App\Http\Controllers\Admin\ImageTypeController;

use App\Http\Controllers\WEB\Admin\ShippingMethodController;
use App\Http\Controllers\WEB\Admin\WithdrawMethodController;
use App\Http\Controllers\WEB\Deliveryman\MyReviewController;
use App\Http\Controllers\WEB\Seller\SellerProductController;
use App\Http\Controllers\WEB\Seller\SellerProfileController;
use App\Http\Controllers\WEB\Admin\Auth\AdminLoginController;
use App\Http\Controllers\WEB\Admin\ProductCategoryController;
use App\Http\Controllers\WEB\Admin\FooterSocialLinkController;
use App\Http\Controllers\WEB\Admin\SpecificationKeyController;
use App\Http\Controllers\WEB\Deliveryman\MyWithdrawController;
use App\Http\Controllers\WEB\Seller\SellerDashboardController;
use App\Http\Controllers\WEB\Admin\TermsAndConditionController;

use App\Http\Controllers\WEB\Seller\Auth\SellerLoginController;
use App\Http\Controllers\WEB\Admin\EmailConfigurationController;
// use App\Http\Controllers\WEB\Admin\HomepageVisibilityController;
use App\Http\Controllers\WEB\Admin\GenHomepageVisibilityController;
use App\Http\Controllers\WEB\Admin\ProductSubCategoryController;
use App\Http\Controllers\WEB\Admin\ProductVariantItemController;
use App\Http\Controllers\WEB\Admin\DeliveryManWithdrawController;
use App\Http\Controllers\WEB\Admin\MegaMenuSubCategoryController;
use App\Http\Controllers\WEB\Seller\SellerProductReportControler;



use App\Http\Controllers\WEB\Admin\ProductChildCategoryController;
use App\Http\Controllers\WEB\Admin\PosController;
use App\Http\Controllers\WEB\Seller\SellerProductReviewController;

use App\Http\Controllers\WEB\Deliveryman\DeliveryMessageController;
use App\Http\Controllers\WEB\Seller\SellerProductGalleryController;
use App\Http\Controllers\WEB\Seller\SellerProductVariantController;
use App\Http\Controllers\WEB\Admin\DeliveryManOrderAmountController;
use App\Http\Controllers\WEB\Deliveryman\DeliveryManOrderController;
use App\Http\Controllers\WEB\Admin\Auth\NewAdminForgotPasswordController;
use App\Http\Controllers\WEB\Admin\Auth\WebAdminForgotPasswordController;
use App\Http\Controllers\WEB\Deliveryman\DeliveryManProfileController;
use App\Http\Controllers\WEB\Admin\DeliveryManWithdrawMethodController;
use App\Http\Controllers\WEB\Seller\SellerProductVariantItemController;
use App\Http\Controllers\WEB\Deliveryman\DeliveryManDashboardController;
use App\Http\Controllers\WEB\Seller\Auth\SellerForgotPasswordController;
use App\Http\Controllers\WEB\Deliveryman\Auth\DeliveryManLoginController;
use App\Http\Controllers\WEB\Deliveryman\Auth\DeliveryManResetPasswordController;
use App\Http\Controllers\WEB\Seller\InventoryController as SellerInventoryController;
use App\Http\Controllers\WEB\Admin\CoinCampaignController;
use App\Http\Controllers\WEB\Admin\BasePlanController;
use App\Http\Controllers\WEB\Admin\WinnerController;
use App\Http\Controllers\CouponCampaignController;
use App\Http\Controllers\CouponTicketController;


// Broadcast::routes(['middleware' => ['auth:web']]);

Broadcast::routes(['prefix' => 'seller','middleware' => 'auth:web']);

Broadcast::routes(['prefix' => 'api', 'middleware' => 'auth:api']);

Route::group([
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);

});

Route::group(['as'=> 'user.', 'prefix' => 'user'],function (){
    Route::group(['as'=> 'checkout.', 'prefix' => 'checkout'],function (){

        Route::get('/paypal-web-view', [PaypalController::class, 'paypalWebView'])->name('paypal-web-view');

        Route::get('/pay-with-paypal', [PaypalController::class, 'payWithPaypal'])->name('pay-with-paypal');

        Route::get('/paypal-payment-success', [PaypalController::class, 'paypalPaymentSuccess'])->name('paypal-payment-success');

        Route::get('/paypal-payment-cancled', [PaypalController::class, 'paypalPaymentCancled'])->name('paypal-payment-cancled');


        Route::get('/paypal-react-web-view', [PaypalController::class, 'paypalReactWebView'])->name('paypal-react-web-view');
        Route::get('/pay-with-paypal-from-react', [PaypalController::class, 'payWithPaypalForReactJs'])->name('pay-with-paypal-from-react');
        Route::get('/paypal-payment-success-from-react', [PaypalController::class, 'paypalPaymentSuccessFromReact'])->name('paypal-payment-success-from-react');
        Route::get('/paypal-payment-cancled-from-react', [PaypalController::class, 'paypalPaymentCancledFromReact'])->name('paypal-payment-cancled-from-react');


        Route::get('/razorpay-order', [PaymentController::class, 'razorpayOrder'])->name('razorpay-order');
        Route::get('/razorpay-web-view', [PaymentController::class, 'razorpayWebView'])->name('razorpay-web-view');
        Route::post('razorpay/pay/verify', [PaymentController::class, 'razorpayVerify'])->name('razorpay-pay-verify');


        Route::get('/flutterwave-web-view', [PaymentController::class, 'flutterwaveWebView'])->name('flutterwave-web-view');
        Route::post('/pay-with-flutterwave', [PaymentController::class, 'payWithFlutterwave'])->name('pay-with-flutterwave');

        Route::get('/pay-with-mollie', [PaymentController::class, 'payWithMollie'])->name('pay-with-mollie');
        Route::get('/mollie-payment-success', [PaymentController::class, 'molliePaymentSuccess'])->name('mollie-payment-success');

        Route::get('/pay-with-instamojo', [PaymentController::class, 'payWithInstamojo'])->name('pay-with-instamojo');
        Route::get('/instamojo-response', [PaymentController::class, 'instamojoResponse'])->name('instamojo-response');


        Route::get('/paystack-web-view', [PaymentController::class, 'paystackWebView'])->name('paystack-web-view');
        Route::post('/pay-with-paystack', [PaymentController::class, 'payWithPayStack'])->name('pay-with-paystack');


        Route::get('/sslcommerz-web-view', [PaymentController::class,   'sslcommerzWebView'])->name('sslcommerz-web-view');
        Route::post('/sslcommerz-pay',     [PaymentController::class,   'sslcommerz'])->name('sslcommerz-pay');
        Route::post('/sslcommerz-success', [PaymentController::class,   'sslcommerz_success'])->name('sslcommerz-success');

        Route::post('/sslcommerz-failed', [PaymentController::class,   'sslcommerz_failed'])->name('sslcommerz-failed');
        Route::post('/sslcommerz-cancel', [PaymentController::class,   'sslcommerz_failed'])->name('sslcommerz-cancel');


        Route::get('/myfatoorah-webview', [PaymentController::class, 'myfatoorah_webview'])->name('myfatoorah-webview');
        Route::get('/myfatoorah-webview-callback', [PaymentController::class, 'myfatoorah_webview_callback'])->name('myfatoorah-webview-callback');



        Route::get('order-success-url-for-mobile-app', function(){
            return response()->json(['message' => 'order success']);
        })->name('order-success-url-for-mobile-app');

        Route::get('order-fail-url-for-mobile-app', function(){
            return response()->json(['message' => 'order faild']);
        })->name('order-fail-url-for-mobile-app');




    });
});

// seller auth related (no login required)
// Seller forgot password (before login)
Route::prefix('seller')->name('seller.')->group(function () {
    Route::get('forgot-password', [SellerForgotPasswordController::class, 'forgetPassword'])
        ->name('forgot-password');
    Route::post('forgot-password', [SellerForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('forgot-password.email');

          Route::get('forget-password', [SellerForgotPasswordController::class,'forgetPassword'])->name('forget-password');
    Route::post('send-forget-password', [SellerForgotPasswordController::class,'sendForgetPassword'])->name('send.forget.password');
    Route::get('reset-password/{token}', [SellerForgotPasswordController::class,'resetPassword'])->name('reset.password');
    Route::post('password-store/{token}', [SellerForgotPasswordController::class,'storeResetData'])->name('store.reset.password');
});



Route::group(['middleware' => ['demo','XSS']], function () {

Route::group(['middleware' => ['maintainance']], function () {
    Route::get('/', function(){
        return redirect()->route('admin.login');
    })->name('home');

    Route::get('seller/login', [SellerLoginController::class,'sellerLoginPage'])->name('seller.login');
    Route::post('seller/login', [SellerLoginController::class,'storeLogin'])->name('seller.login');
    Route::get('seller/logout', [SellerLoginController::class,'adminLogout'])->name('seller.logout');


    Route::group(['as'=> 'seller.', 'prefix' => 'seller'],function (){
        Route::get('dashboard',[SellerDashboardController::class,'index'])->name('dashboard');
        Route::get('my-profile',[SellerProfileController::class,'index'])->name('my-profile');
        Route::get('state-by-country/{id}',[SellerProfileController::class,'stateByCountry'])->name('state-by-country');
        Route::get('city-by-state/{id}',[SellerProfileController::class,'cityByState'])->name('city-by-state');
        Route::put('update-seller-profile',[SellerProfileController::class,'updateSellerProfile'])->name('update-seller-profile');
        // Route::get('change-password',[SellerProfileController::class,'changePassword'])->name('change-password');
        // Route::put('password-update',[SellerProfileController::class,'updatePassword'])->name('password-update');
        Route::get('shop-profile',[SellerProfileController::class,'myShop'])->name('shop-profile');
        Route::put('update-seller-shop',[SellerProfileController::class,'updateSellerSop'])->name('update-seller-shop');
        Route::put('remove-seller-social-link/{id}',[SellerProfileController::class,'removeSellerSocialLink'])->name('remove-seller-social-link');
        Route::get('email-history',[SellerProfileController::class,'emailHistory'])->name('email-history');
 



        Route::resource('product', SellerProductController::class);
        Route::get('stockout-product', [SellerProductController::class,'stockoutProduct'])->name('stockout-product');
        Route::put('product-status/{id}', [SellerProductController::class,'changeStatus'])->name('product-status');
        Route::put('removed-product-exist-specification/{id}', [SellerProductController::class,'removedProductExistSpecification'])->name('removed-product-exist-specification');
        Route::get('pending-product', [SellerProductController::class,'pendingProduct'])->name('pending-product');
        Route::get('product-highlight/{id}', [SellerProductController::class,'productHighlight'])->name('product-highlight');
        Route::put('update-product-highlight/{id}', [SellerProductController::class,'productHighlightUpdate'])->name('update-product-highlight');

        Route::get('product-import-page',[SellerProductController::class,'product_import_page'])->name('product-import-page');
        Route::get('product-export',[SellerProductController::class,'product_export'])->name('product-export');
        Route::get('product-demo-export',[SellerProductController::class,'demo_product_export'])->name('product-demo-export');
        Route::post('product-import',[SellerProductController::class,'product_import'])->name('product-import');


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
        Route::get('message-customer-list', [SellerMessageContoller::class, 'existing_customer_list'])->name('message-customer-list');

        Route::post('send-message-to-customer', [SellerMessageContoller::class, 'send_message_to_customer'])->name('send-message-to-customer');

        Route::get('load-active-user-message/{id}', [SellerMessageContoller::class, 'laod_active_user_message'])->name('load-active-user-message');



        Route::get('load-chat-box/{id}', [SellerMessageContoller::class, 'loadChatBox'])->name('load-chat-box');
        Route::get('load-new-message/{id}', [SellerMessageContoller::class, 'loadNewMessage'])->name('load-new-message');
        Route::get('send-message', [SellerMessageContoller::class, 'sendMessage'])->name('send-message');


        Route::get('inventory', [SellerInventoryController::class, 'index'])->name('inventory');
        Route::get('stock-history/{id}', [SellerInventoryController::class, 'show_inventory'])->name('stock-history');
        Route::post('add-stock', [SellerInventoryController::class, 'add_stock'])->name('add-stock');
        Route::delete('delete-stock/{id}', [SellerInventoryController::class, 'delete_stock'])->name('delete-stock');

    });


});



    //delivery man routes
    Route::get('deliveryman/login', [DeliveryManLoginController::class,'LoginPage'])->name('delivery.man.login');
    Route::post('deliveryman/login', [DeliveryManLoginController::class,'dashboardLogin'])->name('delivery.man.login');
    Route::get('deliveryman/logout',[DeliveryManLoginController::class,'logout'])->name('deliveryman.logout');

    Route::get('deliveryman/password/reset',[DeliveryManResetPasswordController::class,'passwordReset'])->name('deliveryman.password.reset');
    Route::post('deliveryman/password/reset/email',[DeliveryManResetPasswordController::class,'passwrodResetEmail'])->name('deliveryman.password.reset.email');
    Route::get('deliveryman/password/reset/{token}',[DeliveryManResetPasswordController::class,'passwordResetPage'])->name('deliveryman.password.reset.page');
    Route::post('deliveryman/password/update',[DeliveryManResetPasswordController::class,'passwrodUpdate'])->name('deliveryman.pasword.update');



    Route::group(['as'=> 'deliveryman.', 'prefix' => 'deliveryman', 'middleware'=>'deliveryman'],function (){
        Route::get('dashboard',[DeliveryManDashboardController::class,'index'])->name('dashboard');
        Route::get('my-profile',[DeliveryManProfileController::class,'index'])->name('my-profile');
        Route::get('edit-profile',[DeliveryManProfileController::class,'edit'])->name('edit-profile');
        Route::put('update-profile',[DeliveryManProfileController::class,'update'])->name('update-profile');
        Route::get('edit-password',[DeliveryManProfileController::class,'password'])->name('edit-password');
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
        Route::get('sent-message-to-customer', [DeliveryMessageController::class, 'sent_message_to_customer'])->name('sent-message-to-customer');

        Route::get('logout',[DeliveryManLoginController::class,'logout'])->name('logout');

    });


// start admin routes
Route::group(['as'=> 'admin.', 'prefix' => 'admin'],function (){


        Route::resource('image-types', ImageTypeController::class);
    Route::resource('image-items', ImageItemController::class);
    // Campaign routes
    Route::get('/campaigns', [CouponCampaignController::class, 'index'])->name('campaigns.index');
    Route::get('/campaigns/create', [CouponCampaignController::class, 'create'])->name('campaigns.create');
    Route::post('/campaigns', [CouponCampaignController::class, 'store'])->name('campaigns.store');
    Route::get('/campaigns/{id}', [CouponCampaignController::class, 'show'])->name('campaigns.show');
    Route::get('/campaigns/{id}/edit', [CouponCampaignController::class, 'edit'])->name('campaigns.edit');
    Route::put('/campaigns/{id}', [CouponCampaignController::class, 'update'])->name('campaigns.update');
    Route::delete('/campaigns/{id}', [CouponCampaignController::class, 'destroy'])->name('campaigns.destroy');

    // Ticket routes (optional admin usage)
    Route::get('/campaigns/{campaign}/tickets', [CouponTicketController::class, 'index'])->name('tickets.index');
    Route::post('/campaigns/{campaign}/tickets/generate', [CouponTicketController::class, 'generate'])->name('tickets.generate');
    
    
    Route::get('all-prize-campaigns', [CoinCampaignController::class, 'prizeindex'])->name('all-prize-campaigns');
    Route::get('/winners/create/{camp_id}', [WinnerController::class, 'create'])->name('winners.create');
    Route::post('/winners/store', [WinnerController::class, 'store'])->name('winners.store');
    Route::get('/winners/index', [WinnerController::class, 'index'])->name('winners.index');

    Route::delete('/winners/delete/{id}', [WinnerController::class, 'delete'])->name('winner.delete');


    Route::get('userstatements', [CoinCampaignController::class, 'statementsindex'])->name('statementsindex');

    
    Route::get('all-coin-campaigns', [CoinCampaignController::class, 'index'])->name('all-coin-campaigns');
    Route::get('view-coin-campaign/{id}', [CoinCampaignController::class, 'show'])->name('view-coin-campaign');
    Route::get('create-coin-campaign', [CoinCampaignController::class, 'create'])->name('create-coin-campaign');
    Route::post('store-coin-campaign', [CoinCampaignController::class, 'store'])->name('store-coin-campaign');
    Route::get('edit-coin-campaign/{id}', [CoinCampaignController::class, 'edit'])->name('edit-coin-campaign');
    Route::post('update-coin-campaign/{id}', [CoinCampaignController::class, 'update'])->name('update-coin-campaign');
    Route::delete('delete-coin-campaign/{id}', [CoinCampaignController::class, 'destroy'])->name('delete-coin-campaign');
    Route::put('coin-campaign-status/{id}', [CoinCampaignController::class,'changeStatus'])->name('coin-campaign-status');



    Route::get('all-baseplans', [BasePlanController::class, 'index'])->name('all-baseplans');
    Route::get('view-cbaseplanss/{id}', [BasePlanController::class, 'show'])->name('view-baseplans');
    Route::get('create-baseplans', [BasePlanController::class, 'create'])->name('create-baseplans');
    Route::post('store-baseplans', [BasePlanController::class, 'store'])->name('store-baseplans');
    Route::get('edit-baseplans/{id}', [BasePlanController::class, 'edit'])->name('edit-baseplans');
    Route::post('update-baseplans/{id}', [BasePlanController::class, 'update'])->name('update-baseplans');
    Route::delete('delete-baseplans/{id}', [BasePlanController::class, 'destroy'])->name('delete-baseplans');
    Route::put('coin-baseplans/{id}', [BasePlanController::class,'changeStatus'])->name('coin-baseplans');
    Route::put('base-campaign-status/{id}', [BasePlanController::class,'changeStatus'])->name('base-campaign-status');



    Route::get('coin-campaign-orders', [CoinCampaignController::class, 'purchasedindex'])->name('coin-campaign-orders');
    Route::get('/admin/purchase/{id}', [CoinCampaignController::class, 'showPurchaseDetails'])->name('purchasedetails');


    // start auth route
    Route::get('login', [AdminLoginController::class,'adminLoginPage'])->name('login');
    Route::post('login', [AdminLoginController::class,'storeLogin'])->name('login');
    Route::post('logout', [AdminLoginController::class,'adminLogout'])->name('logout');
    Route::get('forget-password', [NewAdminForgotPasswordController::class,'forgetPassword'])->name('forget-password');
    Route::post('send-forget-password', [NewAdminForgotPasswordController::class,'sendForgetEmail'])->name('send.forget.password');
    Route::get('reset-password/{token}', [NewAdminForgotPasswordController::class,'resetPassword'])->name('reset.password');
    Route::post('password-store/{token}', [NewAdminForgotPasswordController::class,'storeResetData'])->name('store.reset.password');
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

    Route::resource('specification-key', SpecificationKeyController::class);
    Route::put('specification-key-status/{id}', [SpecificationKeyController::class,'changeStatus'])->name('specification-key.status');

    Route::resource('testimonial', TestimonialController::class);
    Route::put('testimonial-status/{id}', [TestimonialController::class,'changeStatus'])->name('testimonial.status');

    Route::resource('product', ProductController::class);
    Route::get('create-product-info', [ProductController::class,'create'])->name('create-product-info');
    Route::put('product-status/{id}', [ProductController::class,'changeStatus'])->name('product.status');
    Route::put('product-approved/{id}', [ProductController::class,'productApproved'])->name('product-approved');
    Route::put('removed-product-exist-specification/{id}', [ProductController::class,'removedProductExistSpecification'])->name('removed-product-exist-specification');
    Route::get('seller-product', [ProductController::class,'sellerProduct'])->name('seller-product');
    Route::get('seller-pending-product', [ProductController::class,'sellerPendingProduct'])->name('seller-pending-product');
    Route::get('stockout-product', [ProductController::class,'stockoutProduct'])->name('stockout-product');

    Route::get('product-import-page',[ProductController::class,'product_import_page'])->name('product-import-page');
    Route::get('product-export',[ProductController::class,'product_export'])->name('product-export');
    Route::get('product-demo-export',[ProductController::class,'demo_product_export'])->name('product-demo-export');
    Route::post('product-import',[ProductController::class,'product_import'])->name('product-import');



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



    Route::put('update-database',[SettingController::class,'update_database'])->name('update-database');


    Route::get('clear-database',[SettingController::class,'showClearDatabasePage'])->name('clear-database');
    Route::delete('clear-database',[SettingController::class,'clearDatabase'])->name('clear-database');

    Route::get('subscriber',[SubscriberController::class,'index'])->name('subscriber');
    Route::delete('delete-subscriber/{id}',[SubscriberController::class,'destroy'])->name('delete-subscriber');
    Route::post('specification-subscriber-email/{id}',[SubscriberController::class,'specificationSubscriberEmail'])->name('specification-subscriber-email');
    Route::post('each-subscriber-email',[SubscriberController::class,'eachSubscriberEmail'])->name('each-subscriber-email');

    Route::get('contact-message',[ContactMessageController::class,'index'])->name('contact-message');
    Route::get('show-contact-message/{id}',[ContactMessageController::class,'show'])->name('show-contact-message');
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

    Route::get('image-content', [ContentController::class, 'image_content'])->name('image-content');
    Route::post('update-image-content', [ContentController::class, 'updateImageContent'])->name('update-image-content');

    Route::get('shop-page',[ContentController::class, 'shopPage'])->name('shop-page');
    Route::put('update-filter-price',[ContentController::class, 'updateFilterPrice'])->name('update-filter-price');

    Route::get('seo-setup',[ContentController::class, 'seoSetup'])->name('seo-setup');
    Route::put('update-seo-setup/{id}',[ContentController::class, 'updateSeoSetup'])->name('update-seo-setup');
    Route::get('get-seo-setup/{id}',[ContentController::class, 'getSeoSetup'])->name('get-seo-setup');



    Route::resource('country', CountryController::class);
    Route::put('country-status/{id}',[CountryController::class,'changeStatus'])->name('country-status');

    Route::get('country-import-page',[CountryController::class,'country_import_page'])->name('country-import-page');
    Route::get('country-export',[CountryController::class,'country_export'])->name('country-export');
    Route::get('country-demo-export',[CountryController::class,'demo_country_export'])->name('country-demo-export');
    Route::post('country-import',[CountryController::class,'country_import'])->name('country-import');

    Route::resource('state', CountryStateController::class);
    Route::put('state-status/{id}',[CountryStateController::class,'changeStatus'])->name('state-status');

    Route::get('state-import-page',[CountryStateController::class,'state_import_page'])->name('state-import-page');
    Route::get('state-export',[CountryStateController::class,'state_export'])->name('state-export');
    Route::get('state-demo-export',[CountryStateController::class,'demo_state_export'])->name('state-demo-export');
    Route::post('state-import',[CountryStateController::class,'state_import'])->name('state-import');

    Route::resource('city', CityController::class);
    Route::put('city-status/{id}',[CityController::class,'changeStatus'])->name('city-status');

    Route::get('city-import-page',[CityController::class,'city_import_page'])->name('city-import-page');
    Route::get('city-export',[CityController::class,'city_export'])->name('city-export');
    Route::get('city-demo-export',[CityController::class,'demo_city_export'])->name('city-demo-export');
    Route::post('city-import',[CityController::class,'city_import'])->name('city-import');

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
    Route::put('update-sslcommerz',[PaymentMethodController::class,'updateSslcommerz'])->name('update-sslcommerz');
    Route::put('update-myfatoorah',[PaymentMethodController::class,'update_myfatoorah'])->name('update-myfatoorah');

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

    Route::put('popular-category-banner', [HomePageController::class, 'bannerPopularCategory'])->name('popular-category-banner');

    Route::put('featured-category-banner', [HomePageController::class, 'bannerFeaturedCategory'])->name('featured-category-banner');

    Route::get('featured-category', [HomePageController::class, 'featuredCategory'])->name('featured-category');
    Route::post('store-featured-category', [HomePageController::class, 'storeFeaturedCategory'])->name('store-featured-category');
    Route::delete('destroy-featured-category/{id}', [HomePageController::class, 'destroyFeaturedCategory'])->name('destroy-featured-category');


    Route::get('homepage-section-title', [HomePageController::class, 'homepage_section_content'])->name('homepage-section-title');
    Route::post('update-homepage-section-title', [HomePageController::class, 'update_homepage_section_content'])->name('update-homepage-section-title');



    Route::get('homepage-visibility', [GenHomepageVisibilityController::class, 'index'])->name('homepage-visibility');
    Route::put('update-homepage-visibility', [GenHomepageVisibilityController::class, 'update'])->name('update-homepage-visibility');

    Route::get('menu-visibility', [NewMenuVisibilityController::class, 'index'])->name('menu-visibility');
    Route::put('update-menu-visibility/{id}', [NewMenuVisibilityController::class, 'update'])->name('update-menu-visibility');

    Route::resource('shipping', ShippingMethodController::class);
    Route::get('city-wise-shipping/{city_id}', [ShippingMethodController::class , 'cityWiseShipping'])->name('city-wise-shipping');

    Route::get('shipping-import-page',[ShippingMethodController::class,'shipping_import_page'])->name('shipping-import-page');
    Route::get('shipping-export',[ShippingMethodController::class,'shipping_export'])->name('shipping-export');
    Route::get('shipping-demo-export',[ShippingMethodController::class,'demo_shipping_export'])->name('shipping-demo-export');
    Route::post('shipping-import',[ShippingMethodController::class,'shipping_import'])->name('shipping-import');

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

    Route::resource('currency', CurrencyController::class);
    Route::put('currency-status/{id}', [CurrencyController::class,'changeStatus'])->name('coupon.status');

    Route::resource('banner-image', NewBreadcrumbController::class);

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


    Route::get('sms-notification', [NotificationController::class, 'twilio_sms'])->name('sms-notification');
    Route::put('update-twilio-configuration', [NotificationController::class, 'update_twilio_sms'])->name('update-twilio-configuration');
    Route::put('update-biztech-configuration', [NotificationController::class, 'update_biztech_sms'])->name('update-biztech-configuration');

    Route::get('sms-template', [NotificationController::class, 'sms_template'])->name('sms-template');
    Route::get('edit-sms-template/{id}', [NotificationController::class, 'edit_sms_template'])->name('edit-sms-template');
    Route::put('update-sms-template/{id}', [NotificationController::class, 'update_sms_template'])->name('update-sms-template');

    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::get('stock-history/{id}', [InventoryController::class, 'show_inventory'])->name('stock-history');
    Route::post('add-stock', [InventoryController::class, 'add_stock'])->name('add-stock');
    Route::delete('delete-stock/{id}', [InventoryController::class, 'delete_stock'])->name('delete-stock');

    //Delivery man route
    Route::resource('delivery-man', DeliveryManController::class);
    Route::put('delivery-man-status/{id}',[DeliveryManController::class,'changeStatus'])->name('delivery-man-status');

    Route::get('delivery-man-review', [DeliveryManController::class, 'review'])->name('delivery-man-review');
    Route::put('delivery-man-review-status/{id}', [DeliveryManController::class, 'changeReviewStatus'])->name('delivery-man-review-status');
    Route::delete('delete-delivery-man-review/{id}', [DeliveryManController::class, 'deleteReview'])->name('delete-delivery-man-review');

    Route::resource('delivery-man-withdraw-method', DeliveryManWithdrawMethodController::class);
    Route::put('delivery-man-withdraw-method-status/{id}', [DeliveryManWithdrawMethodController::class,'changeStatus']);

    Route::get('delivery-man-withdraw', [DeliveryManWithdrawController::class, 'index'])->name('delivery-man-withdraw');
    Route::get('pending-delivery-man-withdraw', [DeliveryManWithdrawController::class, 'pendingDeliveryManWithdraw'])->name('pending-delivery-man-withdraw');

    Route::get('show-delivery-man-withdraw/{id}', [DeliveryManWithdrawController::class, 'show'])->name('show-delivery-man-withdraw');
    Route::delete('delete-delivery-man-withdraw/{id}', [DeliveryManWithdrawController::class, 'destroy'])->name('delete-delivery-man-withdraw');
    Route::put('approved-delivery-man-withdraw/{id}', [DeliveryManWithdrawController::class, 'approvedWithdraw'])->name('approved-delivery-man-withdraw');
    Route::get('delivery-man-withdraw-list/{id}', [DeliveryManWithdrawController::class, 'withdrawList'])->name('delivery-man-withdraw-list');


    Route::get('delivery-man-order-amount', [DeliveryManOrderAmountController::class, 'index'])->name('delivery-man-order-amount');
    Route::get('get-deliveryman-account-info/{id}', [DeliveryManOrderAmountController::class, 'getWithDeliveryManAccountInfo'])->name('get-deliveryman-account-info');

    Route::get('delivery-man-order-amount/create', [DeliveryManOrderAmountController::class, 'create'])->name('delivery-man-order-amount.create');
    Route::post('delivery-man-order-amount', [DeliveryManOrderAmountController::class, 'store'])->name('delivery-man-order-amount.store');

    Route::delete('delete-delivery-order-amount/{id}', [DeliveryManOrderAmountController::class, 'destroy'])->name('delivery-man-order-amount.delete');

    // Pos Routes........
    Route::get('/pos', [PosController::class, 'Index'])->name('pos.index');
    Route::get('/pos/category/{id}', [PosController::class, 'categoryIndex'])->name('pos.category.index');
    Route::get('/products/search', [PosController::class, 'search'])->name('pos.product.search');
    Route::get('/pos/add/product/{id}', [PosController::class, 'AddProduct'])->name('pos.add.product');
    Route::get('/pos/product/delete/{id}', [PosController::class, 'Destroy'])->name('pos.destroy.product');
    Route::get('/pos/product/cart/increment/{id}', [PosController::class, 'cartIncremet'])->name('pos.cart.increment.product');
    Route::get('/pos/product/cart/decrement/delete/{id}', [PosController::class, 'cartDecrement'])->name('pos.cart.decrement.product');
    Route::get('/pos/product/cart/clear', [PosController::class, 'clearCart'])->name('pos.cart.clear.product');
    Route::post('/pos/add/customer', [PosController::class, 'addCustomer'])->name('pos.add.customer');
    Route::get('/pos/apply/cupon', [PosController::class, 'applyCupon'])->name('pos.apply.cupon');
    Route::post('/pos/order/submit', [PosController::class, 'orderSubmit'])->name('pos.order.submit');
    Route::get('/pos/bulk/order', [PosController::class, 'bulkOrder'])->name('pos.bulk.order');
    Route::get('/pos/bulk/order/serch', [PosController::class, 'bulkOrderSerch'])->name('pos.bulk.order.serch');
    Route::put('/pos/bulk/order/status/change', [PosController::class, 'updateOrderStatus'])->name('pos.bulk.order.status.change');
    Route::put('/pos/update/cart/product', [PosController::class, 'updatePosCart'])->name('pos.update.cart.order');
    Route::post('/pos/add/product/with/detils/{id}', [PosController::class, 'AddProductWithDetils'])->name('pos.cart.order.detils');


    Route::post('/add-new-product-in-order/{id}', [OrderController::class, 'addNewProduct'])->name('add-new-product-in-order');
    Route::get('/increment-order-quantity/{id}/{order_id}', [OrderController::class, 'incrementOrderQuantity'])->name('order-quantity-increment');
    Route::get('/decrement-order-quantity/{id}/{order_id}', [OrderController::class, 'decrementOrderQuantity'])->name('order-quantity-decrement');
    Route::delete('/delete-order-product/{id}/{order_id}', [OrderController::class, 'deleteOrderProduct'])->name('delete-order-product');


});

});









