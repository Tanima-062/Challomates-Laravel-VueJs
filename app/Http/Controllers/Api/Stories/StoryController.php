<?php

namespace App\Http\Controllers\Api\Stories;

use App\Events\StoryCommentEvent;
use App\Events\StoryLikeCommentUpdateEvent;
use App\Events\StoryLikeEvent;
use App\Models\Story;
use App\Models\Follower;
use App\Traits\UploadAble;
use Illuminate\Http\Request;
use App\Models\MobileAppUser;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Story\StoryResource;
use App\Http\Resources\Story\CommentResource;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Stories\CreateStoryRequest;
use App\Http\Resources\Story\StoryLikeUserResource;
use App\Models\Comment;
use App\Models\Raffle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class StoryController extends Controller
{
    use UploadAble;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $blocked_users_id = $user->blockedMobileAppUsers->pluck('id')->merge($user->blockedByMobileAppUsers->pluck('id'));

        $myStories = Story::query()
            ->select('id')
            ->where('mobile_app_user_id', $user->id)
            ->pluck('id')
            ->toArray();

        // return $myStories;

        $folloWingsUsers = Follower::UserFollowings()
            ->accept()
            ->WhereFollower($user->id)
            ->whereHas('followable',  fn ($q) => $q->where('status', MobileAppUser::STATUS_ACTIVE))
            ->select('followable_id')
            ->pluck('followable_id')
            ->toArray();
        // return $folloWingsUsers;

        $taggedPosts = DB::table('story_tag')
            ->select('story_id')
            ->where('mobile_app_user_id', $user->id)
            ->pluck('story_id')
            ->toArray();

        // return $taggedPosts;

        $stories = Story::query()
            ->orderBy('created_at', 'DESC')
            ->with('creator', 'media')
            ->withCount([
                'comments' => fn ($q) => $q->whereHas('user', fn ($q) => $q->where('status', MobileAppUser::STATUS_ACTIVE)->whereNotIn('id', $blocked_users_id)),
                'likes' => fn ($q) => $q->whereHas('user', fn ($q) => $q->where('status', MobileAppUser::STATUS_ACTIVE)->whereNotIn('id', $blocked_users_id))
            ])
            ->isLike($user)
            ->isTagged($user)
            ->excludeReportedPosts($user->id)
            ->where(function ($q) use ($myStories, $folloWingsUsers, $taggedPosts) {
                // $q->whereIn('id', $myStories);
                $q->whereIn('id', [...$myStories])
                    ->orWhereIn('id', [...$taggedPosts])
                    ->orWhereIn('mobile_app_user_id', $folloWingsUsers);;
            })
            ->whereNotIn('mobile_app_user_id', $blocked_users_id)
            ->paginate(request('per_page', 10));

        //current running stream;
        $stream = Raffle::where('started_at', '>=', Carbon::now()->subMinutes(10))
            ->whereNotNull('playback_url')
            ->whereNull('stopped_at')
            ->first();
        // $url = $stream ? route('streaming.view', ['streamId' => $stream->id, 'userId' => $user->id]) : null;

        // $url = $stream ? route('streaming.view', ['streamId' => $stream->id, 'userId' => $user->id]) : null;

        // return $stream;
        $url = $stream ? $stream->playback_url : null;
        // if ($stream && $stream->video_src_path) {
        //     $stream_name = pathinfo($stream->video_src_path, PATHINFO_FILENAME);

        //     $url = sprintf('%s/play.html?name=%s&autoplay=true&mute=false', env('STREAM_SERVER_URL', 'https://strm.challomates.com:5443/WebRTCAppEE'),  $stream_name);
        // }



        return StoryResource::collection($stories)->additional([
            'status' =>  true,
            'running_stream' => $url,
        ]);

        // $stories = $user->allStories()
        //     ->orderBy('created_at', 'DESC')
        //     ->with('creator', 'media')
        //     ->withCount(['comments'=> function($q){
        //         $q->whereHas('user', function($q){
        //             $q->where('status', MobileAppUser::STATUS_ACTIVE);
        //         });
        //     }, 'likes'=>function($q){
        //         $q->whereHas('user', function($q){
        //             $q->where('status', MobileAppUser::STATUS_ACTIVE);
        //         });
        //     }])
        //     ->isLike($user)
        //     ->isTagged($user)
        //     ->paginate(request('per_page', 10));

        // return StoryResource::collection($stories)->additional([
        //     'status' =>  true
        // ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateStoryRequest $request)
    {
        $user = $request->user();
        $tagged_users = explode(',', $request->tagged);
        if ($total = count($tagged_users)) {
            for ($i = 0; $i < $total; $i++) {
                if ($tagged_users[$i] != '') {
                    $mobile_app_user = MobileAppUser::where('id', $tagged_users[$i])->where('status', MobileAppUser::STATUS_ACTIVE)->first();
                    if (!$mobile_app_user) {
                        throw ValidationException::withMessages(['tagged' => 'The user with id ' . $tagged_users[$i] . ' Not found']);
                    }
                }
            }
        }

        $story =  $user->stories()->create($request->only(['sales_partner_id', 'title', 'check_in_id']));

        foreach ($request->media as $media) {
            $extension = $media->getClientOriginalExtension();

            $media_type = '';
            if (in_array(strtolower($extension), ['png', 'jpg', 'jpeg', 'heic'])) {
                $media_type = 'image';
            } else if (in_array(strtolower($extension), ['mp4', 'ogx', 'oga', 'ogv', 'ogg', 'webm', 'mov', 'avi', 'mkv', 'flv', 'wmv', 'mpg', 'mpeg', 'qt', 'm4v', 'm4p', 'mp2', 'mpe', 'mpv'])) {
                $media_type = 'video';
            }

            $story->media()->create([
                'media_path'        =>  $this->uploadOne($media, 'stories', env('FILESYSTEM_DISK', 'public')),
                'media_type'        =>  $media_type
            ]);
        }

        // $tagged_users = $request->tagged;

        if ($request->tagged && count($tagged_users)) {
            $story->tagged()
                ->syncWithPivotValues($tagged_users, ['creator_id' => $story->mobile_app_user_id]);;
        }

        // if($request->tagged){
        //     $story->storyUsers()->sync([$user->id, ...$tagged_users]);
        // }


        return response([
            'message' => 'Story create successfully.',
            'data' => new StoryResource($story),
            'status' => true,
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $storyId)
    {
        $user = $request->user();
        $blocked_users_id = $user->blockedMobileAppUsers->pluck('id')->merge($user->blockedByMobileAppUsers->pluck('id'));
        $story = Story::query()
            ->where('id', $storyId)
            ->with('creator', 'media')
            ->withCount([
                'comments' => fn ($q) => $q->whereHas('user', fn ($q) => $q->whereNotIn('id', $blocked_users_id)),
                 'likes' => fn ($q) => $q->whereHas('user', fn ($q) => $q->whereNotIn('id', $blocked_users_id))
                 ])
            ->isLike($user)
            ->isTagged($user)
            ->firstOrFail();

        return (new StoryResource($story))->additional([
            'status'    =>  true
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Story $story)
    {
        return response(['message' => 'Update API not ready.']);
        $this->authorize('update', $story);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Story $story)
    {
        $this->authorize('delete', $story);

        $story->delete();

        return response([
            'data'  =>  null,
            'status'    =>  true,
        ], Response::HTTP_OK);
    }

    public function removeTag(Request $request,  Story $story)
    {
        $user = $request->user();

        $tag = DB::table('story_tag')
            ->where('story_id', $story->id)
            ->where('mobile_app_user_id', $user->id)
            ->delete()
            // ->get()
        ;
        // $storyUser = DB::table('story_user')->where('story_id', $story->id)->where('mobile_app_user_id', $user->id )->delete();

        return response([
            'success'   =>  true,
        ]);
    }

    /**
     * Like Story
     *
     * @param Request $request
     * @param Story $story
     * @return void
     */
    public function toggleLIke(Request $request, $story)
    {
        $request->validate([
            'type'      =>  ['required', 'in:post,booster_post']
        ]);

        $user = $request->user();

        $type = $request->type;

        $like = DB::table('likes')
            ->where('story_id', $story)
            ->where('mobile_app_user_id', $user->id)
            ->where('post_type', $type)
            ->first();

        if ($like) {
            DB::table('likes')
                ->where('story_id', $story)
                ->where('mobile_app_user_id', $user->id)
                ->where('post_type', $type)
                ->delete();

            $like_count =  DB::table('likes')
                ->where('story_id', $story)
                ->where('mobile_app_user_id', $user->id)
                ->where('post_type', $type)
                ->count();

            $this->publishLikeEvent($story, $type);

            return response([
                'total'  =>  $like_count,
                'is_like_by_me' => false,
                'status'    =>  true,
            ], Response::HTTP_CREATED);
        } else {
            DB::table('likes')
                ->insert([
                    'story_id'              =>  $story,
                    'mobile_app_user_id'    =>  $user->id,
                    'post_type'             =>  $type,
                    'created_at'            =>  now(),
                    'updated_at'            =>  now(),
                ]);

            $like_count =  DB::table('likes')
                ->where('story_id', $story)
                ->where('mobile_app_user_id', $user->id)
                ->where('post_type', $type)
                ->count();


            $this->publishLikeEvent($story, $type);


            return response([
                'total'  =>   $like_count,
                'is_like_by_me' => true,
                'status'    =>  true,
            ], Response::HTTP_CREATED);
        }
    }

    public function likeUserList(Request $request, $story)
    {
        $request->validate([
            'type'      =>  ['required', 'in:post,booster_post']
        ]);


        $user = $request->user();
        $blocked_users_id = $user->blockedMobileAppUsers->pluck('id')->merge($user->blockedByMobileAppUsers->pluck('id'));

        // $like_ids =  $story->likes->pluck('mobile_app_user_id')->toArray();

        $type  = $request->type;
        $like_ids = DB::table('likes')
            ->where('story_id', $story)
            ->where('post_type', $type)
            ->pluck('mobile_app_user_id')
            ->toArray();

        $mobile_app_users = MobileAppUser::query()
            ->whereIn('id', $like_ids)
            ->FollowByMe($user->id)
            ->whereNotIn('id', $blocked_users_id)
            ->get();

        // return $mobile_app_users;
        return StoryLikeUserResource::collection($mobile_app_users);
    }


    /**
     * Get all comments by story
     *
     * @param Request $request
     * @param Story $story
     * @return void
     */
    public function comments(Request $request, $story)
    {
        $request->validate([
            'type'      =>  ['required', 'in:post,booster_post']
        ]);
        $blocked_users_id = $request->user()->blockedMobileAppUsers->pluck('id')->merge($request->user()->blockedByMobileAppUsers->pluck('id'));

        // $comments = $story->comments()
        //     ->with('user:id,first_name,last_name,username,photo')
        //     ->orderBy('created_at', 'DESC')
        //     ->paginate(request('per_page', 10))
        //     ;

        $comments = Comment::query()
            ->where('story_id', $story)
            ->whereNotIn('mobile_app_user_id', $blocked_users_id)
            ->with('user:id,first_name,last_name,username,photo')
            ->orderBy('created_at', 'DESC')
            ->paginate(request('per_page', 10));


        return CommentResource::collection($comments)->additional([
            'status'    =>  true,
        ]);
    }

    /**
     * Get all comments by story
     *
     * @param Request $request
     * @param Story $story
     * @return void
     */
    public function postComment(Request $request, $story)
    {
        $user = $request->user();

        $request->validate([
            'body'      =>  ['required'],
            'type'      =>  ['required', 'in:post,booster_post']
        ]);

        $type = $request->type;

        $comment = Comment::create([
            'story_id'              =>  $story,
            'mobile_app_user_id'    =>  $user->id,
            'body'                  =>  $request->body,
            'comment_type'          =>  $type,
        ]);

        // $comment = $story->comments()
        //     ->create([
        //         'mobile_app_user_id'    => $user->id,
        //         'body'  =>  $request->body
        //     ])
        //     ;

        // broadcast(new StoryCommentEvent($story, $story->comments->count()));
        // broadcast(new StoryLikeCommentUpdateEvent($story));

        $this->publishLikeEvent($story, $type);


        return (new CommentResource($comment->load('user')))->additional([
            'status'    =>  true,
        ]);
    }

    public function storyReport(Request $request, Story $story)
    {
        $story->reports()->syncWithoutDetaching($request->user()->id);
        return ['status'  => true];
    }



    /**
     * Publish Story Like & Comment update event
     *
     * @return void
     */
    public function publishLikeEvent($story, $type)
    {
        $likes_count =  DB::table('likes')
            ->where('story_id', $story)
            ->where('post_type', $type)
            ->count();

        $comments_count =  DB::table('comments')
            ->where('story_id', $story)
            ->where('comment_type', $type)
            ->count();

        $auth_user = auth()->user();

        $is_like_by_me = DB::table('likes')
            ->where('story_id', $story)
            ->where('post_type', $type)
            ->where('mobile_app_user_id', $auth_user->id)
            ->first();



        $data = [
            'event'     =>  'StoryUpdate',
            'data'      =>  [
                'id'            =>      $story,
                'like_count'    =>      $likes_count,
                'comment_count' =>      $comments_count,
                'type'          =>      $type,
                'is_like_by_me' =>      $is_like_by_me ? 1 : 0,
                'test_key'      =>  'Test Value'
            ]
        ];

        // dump($data);

        Redis::publish('story-update-channel', json_encode($data));
    }
}
