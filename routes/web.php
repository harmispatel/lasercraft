<?php

use App\Http\Controllers\{AuthController, CartController, CategoryController,DashboardController, CustomerController, CustomerQuoteController, CustomPageController, DesignController ,ItemsController, ItemsReviewsController, LanguageController,OptionController,OrderController,PaymentController,PaypalController, ShopBannerController, ShopScheduleController, StatisticsController,TagsController,UserController, FrontendController};
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



// Cache Routes
Route::get('config-clear', function ()
{
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');

    return redirect()->route('home')->with('success', 'Cache has been Clear SuccessFully...');
});


// Frontend Routes
Route::get('/',[FrontendController::class,'index'])->name('home');
Route::get('page/{page_slug}',[FrontendController::class,'customPageView'])->name('custom.page.view');
Route::get('/collections/{cat_id}',[FrontendController::class,'collectionByCategory'])->name('categories.collections');
Route::get('/product-details/{item_id}',[FrontendController::class,'productDetails'])->name('product.deatails');
Route::get('/contact-us',[FrontendController::class,'contactUS'])->name('contact.us');
Route::get('/prints/{page_id}',[FrontendController::class,'printsPage'])->name('prints.page');
Route::post('/send-item-review',[FrontendController::class,'sendItemReview'])->name('send.item.review');
Route::post('/products-search',[FrontendController::class,'searchProducts'])->name('products.search');
Route::post('/submit-contact-us',[FrontendController::class,'submitContactUS'])->name('submit.contact.us');
Route::get('/customer-verify/{userID}',[FrontendController::class,'customerVerify'])->name('customer.verify');
Route::post('/customer-verify',[FrontendController::class,'customerVerifyPost'])->name('customer.verify.post');
Route::get('/profile',[FrontendController::class,'profile'])->name('customer.profile');
Route::get('/edit-profile/{id}',[FrontendController::class,'editProfile'])->name('customer.profile.edit');
Route::post('/update-profile',[FrontendController::class,'updateProfile'])->name('customer.profile.update');
Route::get('/my-orders',[FrontendController::class,'orders'])->name('customer.orders');
Route::get('/order-details/{id}',[FrontendController::class,'ordersDetails'])->name('customer.orders.details');

// Auth Routes for All Users
Route::get('/login', [AuthController::class,'showLogin'])->name('login');
Route::post('/login', [AuthController::class,'login'])->name('doLogin');
Route::get('/logout', [AuthController::class,'logout'])->name('logout');
Route::get('/resgister', [AuthController::class,'showRegister'])->name('register');
Route::post('/resgister', [AuthController::class,'register'])->name('doRegister');

// Customer Routes
Route::group(['prefix' => 'user'], function(){

    // Cart
    Route::get('/my-cart',[CartController::class,'cartList'])->name('cart.list');
    Route::post('/store-cart-item', [CartController::class, 'addToCart'])->name('cart.store');
    Route::post('/update-cart', [CartController::class, 'updateCart'])->name('cart.update');
    Route::get('/remove-cart-item/{id}', [CartController::class, 'removeCart'])->name('cart.remove');
    Route::get('/cart-checkout',[CartController::class,'cartCheckout'])->name('cart.checkout');
    Route::post('/cart-checkout-post',[CartController::class,'cartCheckoutPost'])->name('cart.checkout.post');
    Route::get('/cart-checkout-suceess/{id}',[CartController::class,'cartCheckoutSuccess'])->name('cart.checkout.success');
    Route::post('/user-check-order-status',[CartController::class,'checkOrderStatus'])->name('check.order.status.user');
    Route::post('/set-checkout-type',[CartController::class,'setCartCheckoutType'])->name('cart.set.checkout.type');
    Route::post('/set-delivery-address',[CartController::class,'setDeliveryAddress'])->name('cart.set.delivery.address');
    // Paypal Payment
    Route::get('/paypal/payment/',[PaypalController::class,'payWithpaypal'])->name('paypal.payment');
    Route::get('/paypal/payment/status',[PaypalController::class,'getPaymentStatus'])->name('paypal.payment.status');
    Route::post('/paypal/payment/process',[PaypalController::class,'getPaymentProcess'])->name('paypal.payment.process');
    Route::get('/paypal/payment/cancel',[PaypalController::class,'paymentCancel'])->name('paypal.payment.cancel');

    // Route::group(['middleware' => ['auth','is_customer']], function (){

    // });

});


// Client Routes
Route::group(['prefix' => 'admin'], function()
{
    // If Auth Login
    Route::group(['middleware' => ['auth','is_admin']], function ()
    {
        // Client Dashboard
        Route::get('dashboard', [DashboardController::class,'clientDashboard'])->name('client.dashboard');

        // Categories
        Route::get('categories/{cat_id?}',[CategoryController::class,'index'])->name('categories');
        Route::post('store-categories',[CategoryController::class,'store'])->name('categories.store');
        Route::post('delete-categories',[CategoryController::class,'destroy'])->name('categories.delete');
        Route::post('edit-categories',[CategoryController::class,'edit'])->name('categories.edit');
        Route::post('update-categories',[CategoryController::class,'update'])->name('categories.update');
        Route::post('update-categories-by-lang',[CategoryController::class,'updateByLangCode'])->name('categories.update.by.lang');
        Route::post('status-categories',[CategoryController::class,'status'])->name('categories.status');
        Route::post('search-categories',[CategoryController::class,'searchCategories'])->name('categories.search');
        Route::post('sorting-categories',[CategoryController::class,'sorting'])->name('categories.sorting');
        Route::post('delete-categories-images',[CategoryController::class,'deleteCategoryImages'])->name('categories.delete.images');
        Route::get('delete-categories-image/{id}',[CategoryController::class,'deleteCategoryImage'])->name('categories.delete.image');

        // Items
        Route::get('items/{id?}',[ItemsController::class,'index'])->name('items');
        Route::post('store-items',[ItemsController::class,'store'])->name('items.store');
        Route::post('delete-items',[ItemsController::class,'destroy'])->name('items.delete');
        Route::post('status-items',[ItemsController::class,'status'])->name('items.status');
        Route::post('search-items',[ItemsController::class,'searchItems'])->name('items.search');
        Route::post('edit-items',[ItemsController::class,'edit'])->name('items.edit');
        Route::post('update-items',[ItemsController::class,'update'])->name('items.update');
        Route::post('update-items-by-lang',[ItemsController::class,'updateByLangCode'])->name('items.update.by.lang');
        Route::post('sorting-items',[ItemsController::class,'sorting'])->name('items.sorting');
        Route::post('delete-price-items',[ItemsController::class,'deleteItemPrice'])->name('items.delete.price');
        Route::post('delete-items-image',[ItemsController::class,'deleteItemImage'])->name('items.delete.image');

        // Options
        Route::get('options',[OptionController::class,'index'])->name('options');
        Route::post('store-options',[OptionController::class,'store'])->name('options.store');
        Route::post('delete-options',[OptionController::class,'destroy'])->name('options.delete');
        Route::post('edit-options',[OptionController::class,'edit'])->name('options.edit');
        Route::post('update-options-by-lang',[OptionController::class,'updateByLangCode'])->name('options.update-by-lang');
        Route::post('update-options',[OptionController::class,'update'])->name('options.update');
        Route::post('delete-price-options',[OptionController::class,'deleteOptionPrice'])->name('options.price.delete');

        // Designs
        Route::get('/design-logo', [DesignController::class,'logo'])->name('design.logo');
        Route::post('/design-logo-upload', [DesignController::class,'logoUpload'])->name('design.logo.upload');
        Route::get('/design-logo-delete', [DesignController::class,'deleteLogo'])->name('design.logo.delete');

        Route::post('/design-intro-status', [DesignController::class,'introStatus'])->name('design.intro.status');
        Route::post('/design-intro-icon', [DesignController::class,'introIconUpload'])->name('design.intro.icon');
        Route::post('/design-intro-duration', [DesignController::class,'introDuration'])->name('design.intro.duration');

        Route::get('/design-cover', [DesignController::class,'cover'])->name('design.cover');
        Route::get('/design-cover-delete', [DesignController::class,'deleteCover'])->name('design.cover.delete');

        Route::get('/banners', [ShopBannerController::class,'index'])->name('banners');
        Route::post('/banners-store', [ShopBannerController::class,'store'])->name('banners.store');
        Route::post('/banners-delete', [ShopBannerController::class,'destroy'])->name('banners.delete');
        Route::post('/banners-edit', [ShopBannerController::class,'edit'])->name('banners.edit');
        Route::post('/banners-update', [ShopBannerController::class,'update'])->name('banners.update');
        Route::post('/banners-image-delete', [ShopBannerController::class,'deleteBanner'])->name('banners.delete.image');
        Route::post('update-banners-by-lang',[ShopBannerController::class,'updateByLangCode'])->name('banners.update-by-lang');
        Route::post('banners-sorting',[ShopBannerController::class,'sorting'])->name('banners.sorting');

        Route::get('/design-general-info', [DesignController::class,'generalInfo'])->name('design.general-info');
        Route::get('/design-mail-forms', [DesignController::class,'MailForms'])->name('design.mail.forms');
        Route::post('/design-generalInfoUpdate', [DesignController::class,'generalInfoUpdate'])->name('design.generalInfoUpdate');
        Route::post('/design-mailFormUpdate', [DesignController::class,'mailFormUpdate'])->name('design.mailFormUpdate');

        // Custom Pages
        Route::get('/custom-pages', [CustomPageController::class, 'index'])->name('custom.pages');
        Route::get('/custom-pages/create', [CustomPageController::class, 'create'])->name('custom.pages.create');
        Route::post('/custom-pages/store', [CustomPageController::class, 'store'])->name('custom.pages.store');
        Route::get('/custom-pages/edit/{id}', [CustomPageController::class, 'edit'])->name('custom.pages.edit');
        Route::post('/custom-pages/update', [CustomPageController::class, 'update'])->name('custom.pages.update');
        Route::post('/custom-pages/status', [CustomPageController::class, 'status'])->name('custom.pages.status');
        Route::post('/custom-pages/destroy', [CustomPageController::class, 'destroy'])->name('custom.pages.destroy');

        // Languages
        Route::get('/languages', [LanguageController::class,'index'])->name('languages');
        Route::post('/language-set-primary', [LanguageController::class,'setPrimaryLanguage'])->name('language.set-primary');
        Route::post('/language-set-additional', [LanguageController::class,'setAdditionalLanguages'])->name('language.set-additional');
        Route::post('/language-delete-additional', [LanguageController::class,'deleteAdditionalLanguage'])->name('language.delete-additional');
        Route::post('/language-change-status', [LanguageController::class,'changeLanguageStatus'])->name('language.changeStatus');
        Route::post('/language-categorydetails', [LanguageController::class,'getCategoryDetails'])->name('language.categorydetails');
        Route::post('/language-update-catdetails', [LanguageController::class,'updateCategoryDetails'])->name('language.update-category-details');
        Route::post('/language-itemdetails', [LanguageController::class,'getItemDetails'])->name('language.itemdetails');
        Route::post('/language-update-itemdetails', [LanguageController::class,'updateItemDetails'])->name('language.update-item-details');
        Route::post('/language-google-translate', [LanguageController::class,'setGoogleTranslate'])->name('language.google.translate');

        // ClientProfile
        Route::get('/my-profile/{id}',[UserController::class,'myProfile'])->name('client.profile.view');
        Route::get('/edit-profile/{id}',[UserController::class,'editProfile'])->name('client.profile.edit');
        Route::post('/update-profile',[UserController::class,'updateProfile'])->name('client.profile.update');
        Route::get('/delete-profile-picture',[UserController::class,'deleteProfilePicture'])->name('client.delete.profile.picture');

        // Tags
        Route::get('tags',[TagsController::class,'index'])->name('tags');
        Route::post('store-tags',[TagsController::class,'store'])->name('tags.store');
        Route::post('delete-tags',[TagsController::class,'destroy'])->name('tags.destroy');
        Route::post('edit-tags',[TagsController::class,'edit'])->name('tags.edit');
        Route::post('update-tags',[TagsController::class,'update'])->name('tags.update');
        Route::post('sorting-tags',[TagsController::class,'sorting'])->name('tags.sorting');
        Route::post('update-tags-by-lang',[TagsController::class,'updateByLangCode'])->name('tags.update-by-lang');


        // Statistic
        Route::get('/statistics/{key?}',[StatisticsController::class,'index'])->name('statistics');

        // Themes
        // Route::get('/design-theme', [ThemeController::class,'index'])->name('design.theme');
        // Route::get('/design-theme-preview/{id}', [ThemeController::class,'themePrview'])->name('design.theme-preview');
        // Route::get('/design-create-theme', [ThemeController::class,'create'])->name('design.theme-create');
        // Route::post('/design-store-theme', [ThemeController::class,'store'])->name('design.theme-store');
        // Route::post('/design-update-theme', [ThemeController::class,'update'])->name('design.theme-update');
        // Route::post('/change-theme', [ThemeController::class,'changeTheme'])->name('theme.change');
        // Route::get('/delete-theme/{id}', [ThemeController::class,'destroy'])->name('theme.delete');
        // Route::get('/clone-theme/{id}', [ThemeController::class,'cloneView'])->name('theme.clone');


        // Orders
        Route::get('/orders-settings',[OrderController::class,'OrderSettings'])->name('order.settings');
        Route::post('/orders-settings-update',[OrderController::class,'UpdateOrderSettings'])->name('update.order.settings');
        Route::get('/orders',[OrderController::class,'index'])->name('client.orders');
        Route::match(['get','post'],'orders-history',[OrderController::class,'ordersHistory'])->name('client.orders.history');
        Route::post('/orders-change-estimate',[OrderController::class,'changeOrderEstimate'])->name('change.order.estimate');
        Route::post('/accept-order',[OrderController::class,'acceptOrder'])->name('accept.order');
        Route::post('/reject-order',[OrderController::class,'rejectOrder'])->name('reject.order');
        Route::post('/finalized-order',[OrderController::class,'finalizedOrder'])->name('finalized.order');
        Route::get('/order-view/{id}',[OrderController::class,'viewOrder'])->name('view.order');
        Route::get('/clear-delivey-range',[OrderController::class,'clearDeliveryRangeSettings'])->name('remove.delivery.range');
        Route::post('/order-notification',[OrderController::class,'orderNotification'])->name('order.notification');
        Route::get('/new-orders',[OrderController::class,'getNewOrders'])->name('new.orders');
        Route::post('orders-history-export',[OrderController::class,'exportOrderHistory'])->name('orders.history.export');

        // Payment
        Route::get('/payment-settings',[PaymentController::class,'paymentSettings'])->name('payment.settings');
        Route::post('/payment-settings-update',[PaymentController::class,'UpdatePaymentSettings'])->name('update.payment.settings');

        // Item Reviews
        Route::get('/items-reviews',[ItemsReviewsController::class,'index'])->name('items.reviews');
        Route::post('/items-reviews-destroy',[ItemsReviewsController::class,'destroy'])->name('items.reviews.destroy');

        Route::post('/verify/client/password',[UserController::class,'verifyClientPassword'])->name('verify.client.password');

        // Shop Schedule
        Route::get('/shop-schedule',[ShopScheduleController::class,'index'])->name('shop.schedule');
        Route::post('/shop-schedule-update',[ShopScheduleController::class,'updateShopSchedule'])->name('update.shop.schedule');

        // Customer Quotes
        Route::get('/customer-quotes',[CustomerQuoteController::class,'index'])->name('customer.quotes');
        Route::post('/customer-quote-details',[CustomerQuoteController::class,'quoteDetails'])->name('customer.quote.details');
        Route::post('/customer-invoice-reply',[CustomerQuoteController::class,'invoiceSent'])->name('customer.invoice.sent');
        Route::post('/customer-quote-reply',[CustomerQuoteController::class,'quoteReply'])->name('customer.quote.reply');
        Route::post('/customer-quote-reply-edit',[CustomerQuoteController::class,'quoteReplyEdit'])->name('customer.quote.reply.edit');

        // Customers
        Route::get('/customers',[CustomerController::class,'index'])->name('customers');
        Route::post('/customers-clients',[CustomerController::class,'changeStatus'])->name('customers.status');
        Route::post('/customers-delete',[CustomerController::class,'destroy'])->name('customers.destroy');
    });
});


// Change Backend Language
Route::post('/change-backend-language', [DashboardController::class, 'changeBackendLanguage'])->name('change.backend.language');
