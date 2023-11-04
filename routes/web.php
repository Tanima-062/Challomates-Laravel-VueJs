<?php

use App\Mail\TestMail;
use App\Events\TestEvent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Web\Raffles\RaffleController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\PdfViewController;
use App\Http\Controllers\Web\Coins\CoinsDebitController;
use App\Http\Controllers\Web\Packages\PackageController;
use App\Http\Controllers\Web\Boosters\BoostersController;
use App\Http\Controllers\Web\Coins\CoinsCreditController;
use App\Http\Controllers\Web\Contract\ContractController;
use App\Http\Controllers\Web\StoreVisits\StoreVisitsController;
use App\Http\Controllers\Web\SweepStakes\SweepStakesController;
use App\Http\Controllers\Web\SalesPartner\SalesPartnerController;
use App\Http\Controllers\Web\ChalloMate\ChalloMatesAdminController;
use App\Http\Controllers\Web\Jackpot\JackpotContributionController;
use App\Http\Controllers\Web\MarketingFees\MarketingFeesController;
use App\Http\Controllers\Web\MobileAppUser\MobileAppUserController;
use App\Http\Controllers\Web\Participation\ParticipationController;
use App\Http\Controllers\Web\CompanyConsultants\CompanyConsultantsController;
use App\Http\Controllers\Web\WebrtcStreamingController;
use Illuminate\Auth\GenericUser;
use Illuminate\Support\Facades\Broadcast;

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

Route::group(['middleware' => ['guest']], function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Route::get('/password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
    // Route::post('/password/confirm', [ConfirmPasswordController::class, 'confirm']);

    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');


    Route::get('/password/request', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::get('password/reset', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'complete']);



    // require_once('mobile.php');
});

Route::group(['middleware' => 'auth'], function () {
    Route::redirect('/', '/dashboard');
    Route::redirect('/home', '/dashboard');
    Route::get('dashboard',[ DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/filter/data', [DashboardController::class, 'getFilterableData'])->name('dashboard.filter.data');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::resource('challo-mates-admins', ChalloMatesAdminController::class);
    Route::put('challao-mates-admins/{challo_mates_admin}/toggle-status', [ChalloMatesAdminController::class, 'toggleStatus'])->name('challo-mates-admins.toggle-status');
    Route::put('challao-mates-admins/{challo_mates_admin}/resend-invitaion', [ChalloMatesAdminController::class, 'resendInvitation'])->name('challo-mates-admins.resend-invitaion');
    Route::get('challao-mates-admins/filter/data', [ChalloMatesAdminController::class, 'getFilterableData'])->name('challo-mates-admins.filter.data');

    //Packages
    Route::get('/package/filter-data', [PackageController::class, 'filterData'])->name('package.filter-data');
    Route::resource('package', PackageController::class)->except(['destroy']);
    Route::put('/package/{package}/toggle-status', [PackageController::class, 'toggleStatus'])->name('package.toggle-status');
    Route::get('/package/show/multiple', [PackageController::class, 'showMultiple'])->name('package.show.multiple');


    //Sweepstakes
    Route::get('sweepstakes/filter-data', [SweepStakesController::class, 'filterData'])->name('sweepstakes.filter-data');
    Route::resource('sweepstakes', SweepStakesController::class)->except(['destroy']);
    Route::put('sweepstakes/{sweepstake}/publish', [SweepStakesController::class, 'publish'])->name('sweepstakes.publish');
    Route::put('sweepstakes/{sweepstake}/cancel', [SweepStakesController::class, 'cancel'])->name('sweepstakes.cancel');

    //Participation
    Route::get('participation/filter-data', [ParticipationController::class, 'filterData'])->name('participation.filter-data');
    Route::resource('participation', ParticipationController::class)->except(['destroy']);

    //Raffles
    Route::get('raffles/websocket', [RaffleController::class, 'webSocket'])->name('raffles.websocket');


    //Route::get('raffles/streaming', [RaffleController::class, 'streaming'] )->name( 'raffles.streaming' );
    // Route::get('raffles/streaming/{streamId}', [RaffleController::class, 'consuming'])->name('raffles.consuming');
    // Route::get('raffles/stream-offer', [RaffleController::class, 'makeStreamOffer'])->name('raffles.stream-offer');
    // Route::get('raffles/stream-answer', [RaffleController::class, 'makeStreamAnswer'])->name('raffles.stream-answer');

    Route::get('raffles/play-video', [RaffleController::class, 'videoStream'])->name('raffles.play-video');
    Route::get('raffles/filter-data', [RaffleController::class, 'filterData'])->name('raffles.filter-data');
    Route::resource('raffles', RaffleController::class)->except(['destroy']);
    Route::put('raffles/{raffle_id}/captureWinner', [RaffleController::class, 'captureWinner'])->name('raffles.capture-winner');



    //Jackpot Contribution
    Route::get('jackpot/filter-data', [JackpotContributionController::class, 'filterData'])->name('jackpot.filter-data');
    Route::resource('jackpot', JackpotContributionController::class)->only(['index', 'show']);

    //mobile app users
    Route::resource('mobile-app-users', MobileAppUserController::class);
    Route::put('mobile-app-users/{mobile_app_user}/toggle-status', [MobileAppUserController::class, 'toggleStatus'])->name('mobile-app-users.toggle-status');
    Route::get('mobile-app-users/filter/data', [MobileAppUserController::class, 'getFilterableData'])->name('mobile-app-users.filter.data');

    //marketing fees
    Route::get('/marketing-fees/filter-data', [MarketingFeesController::class, 'filterData'])->name('marketing-fees.filter-data');
    Route::resource('marketing-fees', MarketingFeesController::class);
    Route::put('/marketing-fees/{marketing_fee}/toggle-status', [MarketingFeesController::class, 'toggleStatus'])->name('marketing-fees.toggle-status');

    //company consultants
    Route::get('/company-consultants/filter-data', [CompanyConsultantsController::class, 'filterData'])->name('company-consultants.filter-data');
    Route::resource('company-consultants', CompanyConsultantsController::class);
    Route::put('/company-consultants/{consultant}/toggle-status', [CompanyConsultantsController::class, 'toggleStatus'])->name('company-consultants.toggle-status');

    //Boosters
    Route::get('/boosters/filter-data', [BoostersController::class, 'filterData'])->name('boosters.filter-data');
    Route::resource('boosters', BoostersController::class);
    Route::put('/boosters/{booster}/toggle-status', [BoostersController::class, 'toggleStatus'])->name('boosters.toggle-status');
    Route::get('/getContracts/{sales_partner_id}', [BoostersController::class, 'getContracts'])->name('boosters.get-contracts');
    Route::get('/booster-posts', [BoostersController::class, 'storeBoosterPosts']);

    //sales partners
    // Route::get('sales-partner/edit/{sales_partner?}', [SalesPartnerController::class, 'edit'])->name('sales-partner.edit');
    Route::resource('sales-partner', SalesPartnerController::class)->except(['destroy']);
    Route::get('sales-partner/filter/data', [SalesPartnerController::class, 'getFilterableData'])->name('sales-partner.filter.data');
    Route::put('sales-partner/assign/consultant', [SalesPartnerController::class, 'assignConsultant'])->name('sales-partner.assign.consultant');
    Route::put('sales-partner/update-status/{sales_partner}', [SalesPartnerController::class, 'updateStatus'])->name('sales-partner.update-status');
    Route::delete('sales-partner/profile-picture/delete/{sales_partner}', [SalesPartnerController::class, 'deleteProfilePicture'])->name('sales-partner.profile-picture.delete');
    Route::post('sales-partner/set-session/redirect/{to_edit?}', [SalesPartnerController::class, 'setSalesPartnerSessionAndRedirect'])->name('sales-partner.set-session.redirect');
    Route::post('sales-partner/email-qr-code', [SalesPartnerController::class, 'emailQrCode'])->name('sales-partner.email-qr-code');

    // contract
    // Route::get('contract/edit/{contract?}', [ContractController::class, 'edit'])->name('contract.edit');
    Route::resource('contract', ContractController::class)->except(['destroy']);
    Route::get('contract/filter/data', [ContractController::class, 'getFilterableData'])->name('contract.filter.data');
    Route::put('contract/cancel/{contract}', [ContractController::class, 'cancel'])->name('contract.cancel');
    Route::post('contract/set-session/redirect/{to_edit?}', [ContractController::class, 'setContractSessionAndRedirect'])->name('contract.set-session.redirect');

    //store visits
    Route::get('store-visits', [StoreVisitsController::class, 'index'])->name('store-visits.index');
    Route::get('store-visits/{store_visit}', [StoreVisitsController::class, 'show'])->name('store-visits.show');
    Route::get('store-visits/filter/data', [StoreVisitsController::class, 'getFilterableData'])->name('store-visits.filter.data');

    //coins
    Route::resource('coins-credit', CoinsCreditController::class)->only(['index', 'show']);
    Route::get('coins-credit/filter/data', [CoinsCreditController::class, 'getFilterableData'])->name('coins-credit.filter.data');
    Route::resource('coins-debit', CoinsDebitController::class)->only(['index', 'show']);
    Route::get('coins-debit/filter/data', [CoinsDebitController::class, 'getFilterableData'])->name('coins-debit.filter.data');
});


Route::get('/broadcast', function () {
    broadcast(new TestEvent());
});

Route::get('test', function () {

    // return asset('images/logo_email.png');
    // Mail::to('user@mail.com')->send(new TestMail);

    // Mail::to('yelac16230@oceore.com')->send(new TestMail);
    // Mail::to('jysuzoja@ema-sofia.eu')->send(new TestMail);
    // Mail::to('dukkenegnu@vusra.com')->send(new TestMail);
    // Mail::to('zidjyvrbw@scpulse.com')->send(new TestMail);
    // Mail::to('zjo6aypl5a@paperpapyrus.com')->send(new TestMail);
    // Mail::to('mithunhalderrptc@gmail.com')->send(new TestMail);
    // Mail::to('bishalalam50@gmail.com')->send(new TestMail);
    // Mail::to('mithun@infodigita.com')->send(new TestMail);
    // Mail::to('mithunrptc@gmail.com')->send(new TestMail);

    // Mail::to('mithunrptc@outlook.com')->send(new TestMail);
    // return view('mail.auth.reset-password');
    return (new TestMail)->render();
});

Broadcast::routes();

Route::post('/public/presence/auth/{id}', function ($id) {
    $user = new GenericUser(['id' => $id]);
    request()->setUserResolver(function () use ($user) {
        return $user;
    });
    return Broadcast::auth(request());
});



// Route::get('/streaming/{streamId}/user/{userId}', [WebrtcStreamingController::class, 'consumer'])->name('streaming.view');
// Route::post('/stream-answer', [WebrtcStreamingController::class, 'makeStreamAnswer']);
// Route::group(['middleware' => 'auth'], function () {
//     Route::get('/streaming/{streamId}/notifiy', [WebrtcStreamingController::class, 'notifyUsers'])->name('streaming.notify-users');
//     Route::post('/stream-offer', [WebrtcStreamingController::class, 'makeStreamOffer']);
//     Route::post('/streaming/store-video/{raffle}', [WebrtcStreamingController::class, 'storeVideo'])->name('streaming.store-video');
// });


// Route::get('/privacy-policy', [PdfViewController::class, 'privacyPolicy']);
// Route::get('/terms-and-conditions', [PdfViewController::class, 'termsAndCondition']);
// Route::get('/terms-and-conditions-of-participation-sweepstakes', [PdfViewController::class, 'termsAndConditionForParticipationSweepstakes']);


Route::post('/streaming/start-stream/{raffle}', [WebrtcStreamingController::class, 'startLiveStream'])->name('start.live.strem');
Route::post('/streaming/complete/{raffle}', [WebrtcStreamingController::class, 'completeLiveStream'])->name('complete.live.strem');
