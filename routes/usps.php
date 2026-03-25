<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Johnpaulmedina\Usps\Http\Controllers\AddressController;
use Johnpaulmedina\Usps\Http\Controllers\CarrierPickupController;
use Johnpaulmedina\Usps\Http\Controllers\ContainerController;
use Johnpaulmedina\Usps\Http\Controllers\DomesticPriceController;
use Johnpaulmedina\Usps\Http\Controllers\InternationalLabelController;
use Johnpaulmedina\Usps\Http\Controllers\InternationalPriceController;
use Johnpaulmedina\Usps\Http\Controllers\LabelController;
use Johnpaulmedina\Usps\Http\Controllers\LocationController;
use Johnpaulmedina\Usps\Http\Controllers\PaymentController;
use Johnpaulmedina\Usps\Http\Controllers\ScanFormController;
use Johnpaulmedina\Usps\Http\Controllers\ServiceStandardController;
use Johnpaulmedina\Usps\Http\Controllers\ShippingOptionController;
use Johnpaulmedina\Usps\Http\Controllers\TrackingController;

/*
|--------------------------------------------------------------------------
| USPS API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the UspsServiceProvider. They can be published
| to your application's routes directory for customization:
|
|   php artisan vendor:publish --tag=usps-routes
|
*/

Route::prefix('api/usps')
    ->middleware(config('usps.route_middleware', ['api']))
    ->name('usps.')
    ->group(function (): void {

    // Address endpoints
    Route::prefix('address')->name('address.')->group(function (): void {
        Route::get('validate', [AddressController::class, 'validate'])->name('validate');
        Route::get('city-state', [AddressController::class, 'cityState'])->name('city-state');
        Route::get('zipcode', [AddressController::class, 'zipcode'])->name('zipcode');
    });

    // Tracking endpoints
    Route::prefix('tracking')->name('tracking.')->group(function (): void {
        Route::post('/', [TrackingController::class, 'store'])->name('store');
        Route::post('{trackingNumber}/notifications', [TrackingController::class, 'notifications'])->name('notifications');
        Route::post('{trackingNumber}/proof-of-delivery', [TrackingController::class, 'proofOfDelivery'])->name('proof-of-delivery');
    });

    // Domestic label endpoints
    Route::prefix('labels')->name('labels.')->group(function (): void {
        Route::post('/', [LabelController::class, 'store'])->name('store');
        Route::post('return', [LabelController::class, 'storeReturn'])->name('return');
        Route::delete('{trackingNumber}', [LabelController::class, 'destroy'])->name('destroy');
    });

    // International label endpoints
    Route::post('international-labels', [InternationalLabelController::class, 'store'])->name('international-labels.store');

    // Domestic price endpoints
    Route::prefix('prices')->name('prices.')->group(function (): void {
        Route::post('base-rates', [DomesticPriceController::class, 'baseRates'])->name('base-rates');
        Route::post('extra-services', [DomesticPriceController::class, 'extraServices'])->name('extra-services');
        Route::post('total-rates', [DomesticPriceController::class, 'totalRates'])->name('total-rates');
    });

    // International price endpoints
    Route::prefix('international-prices')->name('international-prices.')->group(function (): void {
        Route::post('base-rates', [InternationalPriceController::class, 'baseRates'])->name('base-rates');
        Route::post('total-rates', [InternationalPriceController::class, 'totalRates'])->name('total-rates');
    });

    // Service standards endpoints
    Route::prefix('service-standards')->name('service-standards.')->group(function (): void {
        Route::get('estimates', [ServiceStandardController::class, 'estimates'])->name('estimates');
        Route::get('standards', [ServiceStandardController::class, 'standards'])->name('standards');
    });

    // Location endpoints
    Route::prefix('locations')->name('locations.')->group(function (): void {
        Route::get('post-offices', [LocationController::class, 'postOffices'])->name('post-offices');
        Route::get('dropoff', [LocationController::class, 'dropoff'])->name('dropoff');
        Route::get('parcel-lockers', [LocationController::class, 'parcelLockers'])->name('parcel-lockers');
    });

    // Carrier pickup endpoints
    Route::prefix('carrier-pickup')->name('carrier-pickup.')->group(function (): void {
        Route::post('/', [CarrierPickupController::class, 'store'])->name('store');
        Route::get('{confirmationNumber}', [CarrierPickupController::class, 'show'])->name('show');
        Route::put('{confirmationNumber}', [CarrierPickupController::class, 'update'])->name('update');
        Route::delete('{confirmationNumber}', [CarrierPickupController::class, 'destroy'])->name('destroy');
    });

    // Container endpoints
    Route::post('containers', [ContainerController::class, 'store'])->name('containers.store');

    // Payment endpoints
    Route::post('payments/authorize', [PaymentController::class, 'authorize'])->name('payments.authorize');

    // Shipping options endpoints
    Route::post('shipping-options/search', [ShippingOptionController::class, 'search'])->name('shipping-options.search');

    // SCAN form endpoints
    Route::post('scan-forms', [ScanFormController::class, 'store'])->name('scan-forms.store');
});
