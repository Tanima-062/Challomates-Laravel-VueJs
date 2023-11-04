<?php

namespace App\Http\Controllers\Api\Stories;

use App\Models\Story;
use App\Models\Follower;
use Illuminate\Http\Request;
use App\Models\MobileAppUser;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\Story\StoryResource;
use Illuminate\Support\Facades\Auth;

class MyStoryController extends Controller
{

      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, MobileAppUser $mobile_app_user)
    {
        $user = $mobile_app_user;
        $blocked_users_id = $request->user()->blockedMobileAppUsers->pluck('id')->merge($user->blockedByMobileAppUsers->pluck('id'));
        $stories = $user->stories()
        ->excludeReportedPosts(Auth::user()->id)
        ->orderBy('created_at', 'DESC')
        ->with('creator', 'media')
        ->withCount([
            'comments'=> fn($q) => $q->whereHas('user',  fn($q)=> $q->active()->whereNotIn('id', $blocked_users_id)),
            'likes'=> fn($q) => $q->whereHas('user',  fn($q)=> $q->active()->whereNotIn('id', $blocked_users_id))
        ])
        ->isLike($user)
        ->isTagged($user)
        ->paginate(request('per_page', 10));
        ;

        return StoryResource::collection($stories)->additional([
            'status' =>  true
        ]);
    }


     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getMateStories(Request $request)
    {
        $user = $request->user();
        $blocked_users_id = $user->blockedMobileAppUsers->pluck('id')->merge($user->blockedByMobileAppUsers->pluck('id'));
        if($request->has('user_id') && !is_null($request->user_id)){
            if(!$user = MobileAppUser::active()->where('id', $request->user_id)->first()){
                return StoryResource::collection([])->additional([
                    'status' =>  true
                ]);
            }
        }

        $followers = Follower::query()
                        ->select('follower_id')
                        ->UserFollowings()
                        ->WhereFollowabble($user->id)
                        ->UserFollower()
                        ->Accept()
                        ->whereHas('followerable', fn($q)=>$q->active())
                    ->pluck('follower_id')
                    ->toArray()
                        // ->with('followerable')
                        // ->get()
                    ;
        $followings = Follower::query()
                        ->select('followable_id')
                        ->UserFollowings()
                        ->WhereFollower($user->id)
                        ->UserFollower()
                        ->Accept()
                        ->whereHas('followable', fn($q)=>$q->active())
                    ->pluck('followable_id')
                    ->toArray()
                    ;

        $stories = Story::query()
            ->whereIn('mobile_app_user_id', [...$followers, ...$followings])
            ->whereNotIn('mobile_app_user_id', $blocked_users_id)
            ->excludeReportedPosts(Auth::user()->id)
        ->orderBy('created_at', 'DESC')
        ->with('creator', 'media')
        ->withCount([
            'comments'=> fn($q) => $q->whereHas('user',  fn($q)=> $q->active()->whereNotIn('id', $blocked_users_id)),
            'likes'=> fn($q) => $q->whereHas('user',  fn($q)=> $q->active()->whereNotIn('id', $blocked_users_id))
        ])
        ->isLike($user)
        ->isTagged($user)
        ->paginate(request('per_page', 10));
        ;
        return StoryResource::collection($stories)->additional([
            'status' =>  true
        ]);
    }



    public function getTaggedStories(Request $request)
    {
        $user = $request->user();
        $blocked_users_id = $user->blockedMobileAppUsers->pluck('id')->merge($user->blockedByMobileAppUsers->pluck('id'));

        if($request->has('user_id') && !is_null($request->user_id)){
            if(!$user = MobileAppUser::active()->where('id', $request->user_id)->first()){
                return StoryResource::collection([])->additional([
                    'status' =>  true
                ]);
            }
        }

        $taggedPosts = DB::table('story_tag')
            ->select('story_id')
            ->where('mobile_app_user_id', $user->id)
            ->pluck('story_id')
            ->toArray();

        // return $taggedPosts;

         $stories = Story::query()
            ->orderBy('created_at', 'DESC')
            ->whereNotIn('mobile_app_user_id', $blocked_users_id)
            ->excludeReportedPosts(Auth::user()->id)
            ->with('creator', 'media')
            ->withCount([
                'comments'=> fn($q)=> $q->whereHas('user', fn($q)=> $q->where('status', MobileAppUser::STATUS_ACTIVE)->whereNotIn('id', $blocked_users_id)),
                'likes'=> fn($q)=> $q->whereHas('user', fn($q)=> $q->where('status', MobileAppUser::STATUS_ACTIVE)->whereNotIn('id', $blocked_users_id))
            ])
            ->isLike($user)
            ->isTagged($user)
            ->where(function($q) use($taggedPosts){
                    $q->whereIn('id', [...$taggedPosts])
                    ;
                ;
            })
            ->paginate(request('per_page', 10));

        return StoryResource::collection($stories)->additional([
            'status' =>  true
        ]);
    }
}
