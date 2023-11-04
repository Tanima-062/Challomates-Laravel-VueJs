<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\Map\MapController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\Coins\CoinController;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\DeleteProfileController;
use App\Http\Controllers\Api\Stories\StoryController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\Stories\TaggedController;
use App\Http\Controllers\Api\Follow\FollowerController;
use App\Http\Controllers\Api\Stories\MyStoryController;
use App\Http\Controllers\Api\Follow\FollowingController;
use App\Http\Controllers\Api\Stories\BoosterPostController;
use App\Http\Controllers\Api\StoreVisit\StoreVisitController;
use App\Http\Controllers\Api\SweepStake\SweepStakeController;
use App\Http\Controllers\Api\SalesPartner\SalesPartnerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1'], function () {
    Route::group(['middleware' => ['guest', 'throttle:60,1'] ], function () {
        Route::post('login', LoginController::class);

        //Forgot password
        Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLink']);
        Route::post('forgot-password/verify-otp', [ForgotPasswordController::class, 'verifyOtp']);
        // Route::post('forgot-password/resend-otp', [ForgotPasswordController::class, 'resendOtp']);
        Route::post('forgot-password/update-password', [ForgotPasswordController::class, 'updatePassword']);

        //Register
        Route::post('register', [RegistrationController::class, 'store']);
        Route::post('register-check-store', [RegistrationController::class, 'checkStore']);
        Route::post('/check-user', [RegistrationController::class, 'checkUser']);
        Route::post('verify-otp', [RegistrationController::class, 'verifyOtp']);
        Route::post('resend-otp', [RegistrationController::class, 'resendOtp']);
        Route::put('registration-complete', [RegistrationController::class, 'registrationComplete']);

        Route::get('store/check-qr/{salesPartner}', [SalesPartnerController::class, 'checkQr']);
    });

    Route::group(['middleware' =>    ['auth:employee']], function () {
        Route::get('me', [UserController::class, 'me']);
        Route::post('logout', LogoutController::class);
        Route::put('update-profile', [UserController::class, 'update']);

        Route::put('verify-email/{token}', [UserController::class, 'verifyEmail']);
        Route::put('/resend/verify-email-token', [UserController::class, 'resendEmailVerificationToken']);

        Route::post('reset-password', [UserController::class, 'resetPassword']);
        Route::post('reset-password/verify-otp', [UserController::class, 'verifyResendPasswordOtp']);
        Route::post('reset-password/resend-otp', [UserController::class, 'resendResendPasswordOtp']);
        Route::post('update-password', [UserController::class, 'updatePassword']);
        Route::post('user/{user}/block', [UserController::class, 'blockUser']);

        Route::get('search-users', [SearchController::class, 'serachUserAndStore']);
        Route::get('user-profile/{user:id}', [ProfileController::class, 'show']);


        //Get authenticated user stories
        // Route::get('stories', [UserController::class, 'getStories']);
        Route::post('check-in', [SalesPartnerController::class, 'checkIn']);
        Route::post('check-out', [SalesPartnerController::class, 'checkOut']);

        //Store
        Route::get('store/{salesPartner}', [SalesPartnerController::class, 'show']);
        Route::get('nearest-store', [SalesPartnerController::class, 'getNearestStore']);
        Route::get('store/check-in-now/{salesPartner}',[ SalesPartnerController::class, 'getCheckInNow']);
        Route::post('store/get-receipt-data', [SalesPartnerController::class, 'getReceiptData']);
        Route::get('stores/{salesPartner}/follower-mates', [SalesPartnerController::class, 'getFollowerMates']);
        Route::get('stores/{salesPartner}/mates-live-on-site', [SalesPartnerController::class, 'getMatesLiveOnSite']);
        Route::get('stores/{salesPartner}/all-mates', [SalesPartnerController::class, 'getAllMates']);
        Route::post('stores/{salesPartner}/follow', [SalesPartnerController::class, 'follow']);
        Route::delete('stores/{salesPartner}/remove', [SalesPartnerController::class, 'remove']);

        Route::get('store/{salesPartner}/stories', [SalesPartnerController::class, 'getStories']);


        //
        Route::apiResource('stories', StoryController::class);
        Route::get('profile-stories/{mobile_app_user}', [MyStoryController::class, 'index']);
        Route::get('profile-mates-stories', [MyStoryController::class, 'getMateStories']);
        Route::get('profile-tagged-stories', [MyStoryController::class, 'getTaggedStories']);
        Route::get('booster-posts', [BoosterPostController::class, 'index']);

        //Like
        Route::post('stories/{story}/like', [StoryController::class, 'toggleLIke']);
        Route::post('stories/{story}/report', [StoryController::class, 'storyReport']);
        Route::get('stories/{story}/like-list', [StoryController::class, 'likeUserList']);

        Route::post('stories/{story}/remove-tag', [StoryController::class, 'removeTag']);
        // Route::delete('stores/{story}/like', [StoryController::class, 'dislike']);

        //Comments
        Route::get('stories/{story}/comments', [StoryController::class, 'comments']);
        Route::post('stories/{story}/comments', [StoryController::class, 'postComment']);

        Route::get('stories/{story}/tagged', [TaggedController::class, 'index']);


        //All Followers
        Route::get('followers', [FollowerController::class, 'index']);
        Route::get('followers-count', [FollowerController::class, 'count']);
        Route::post('follow/{mobile_app_user}', [FollowerController::class, 'follow']);
        Route::post('follow/{mobile_app_user}/accept', [FollowerController::class, 'accept']);
        Route::post('follow/{mobile_app_user}/reject', [FollowerController::class, 'reject']);
        Route::delete('follow/{mobile_app_user}/remove', [FollowerController::class, 'remove']);



        //Accepted Followers
        Route::get('accepted-followers', [FollowerController::class, 'getAcceptedFollowers']);

        //Pending Followers
        Route::get('pending-followers', [FollowerController::class, 'getPendingFollowers']);

        //All Followers
        Route::get('followings', [FollowingController::class, 'index']);
        Route::get('followings-count', [FollowingController::class, 'count']);
        Route::delete('following/{mobile_app_user}/remove', [FollowingController::class, 'remove']);

        Route::get('map', [MapController::class, 'index']);
        Route::get('map-search', [MapController::class, 'search']);
        Route::get('map-search-branch', [MapController::class, 'searchBranch']);

        Route::get('coins/credit', [CoinController::class, 'credit']);
        Route::get('coins/debit', [CoinController::class, 'debit']);
        Route::get('coins/reedem-sweepstake', [CoinController::class, 'getReedemSweepstake']);
        Route::post('coins/reedem', [CoinController::class, 'reedem']);

        Route::get('store-visits', [StoreVisitController::class, 'index']);

        Route::apiResource('sweepstakes', SweepStakeController::class)->only(['index', 'show']);
        Route::get('sweepstakes-filter-data', [SweepStakeController::class, 'getFilterData']);
        Route::get('sweepstakes-my-participations', [SweepStakeController::class, 'getMyPerticipations']);
        Route::get('sweepstakes-my-participationsfilter-data', [SweepStakeController::class, 'getMyPerticipationsFilterData']);


        //Delete Account

        Route::delete('delete-request', [DeleteProfileController::class, 'deleteRequest']);
        Route::post('delete-request/verify-otp', [DeleteProfileController::class, 'verifyOtp']);
        Route::post('delete-request/resend-otp', [DeleteProfileController::class, 'resendOtp']);
        // Route::delete('delete-account', [DeleteProfileController::class, 'deleteAccount']);

    });



    //Guest & Authenticated API
    Route::post('check-login', [UserController::class, 'isLogin']);

});
