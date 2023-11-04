<?php

namespace App\Http\Controllers\Web;

use App\Events\StreamAnswer;
use App\Events\StreamOffer;
use App\Http\Controllers\Controller;
use App\Jobs\MergeVideo;
use App\Models\Participation;
use App\Models\Raffle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class WebrtcStreamingController extends Controller
{

    // public function index()
    // {
    //     return view('video-broadcast', ['type' => 'broadcaster', 'id' => Auth::id()]);
    // }

    public function consumer(Request $request, $streamId, $userId)
    {
        return view("stream", ['stream_id' => $streamId, 'user_id' => $userId]);
    }

    public function notifyUsers(Request $request, Raffle $streamId)
    {
        $streamId->started_at = Carbon::now();
        $streamId->save();

        $participations = Participation::where('sweepstake_id', $streamId->sweepstake_id)->pluck('mobile_app_user_id')->unique()->toarray();
        // foreach ($participations as $user_id) {
        //     $url = route('streaming.view', ['streamId' => $streamId->id, 'userId' => $user_id]);
        //     // broadcast(new BroadcastNotification($user_id, $url));
        //     $this->publishBroadcastUrl($url, $user_id, $streamId->id);

        //     // return response(['user'=>$user_id], 200);
        // }

        $url = route('streaming.view', ['streamId' => $streamId->id, 'userId' => random_int(4, 6)]);

        $this->publishBroadcastUrlForAllUser($url, $streamId->id);

        return response([
            'status'    =>  true,
            'participations'    =>  $participations
        ], 200);
    }

    public function makeStreamOffer(Request $request)
    {
        $data['broadcaster'] = $request->broadcaster;
        $data['receiver'] = $request->receiver;
        $data['offer'] = $request->offer;
        event(new StreamOffer($data));
    }

    public function makeStreamAnswer(Request $request)
    {
        $data['broadcaster'] = $request->broadcaster;
        $data['answer'] = $request->answer;
        $data['user'] = $request->user;

        event(new StreamAnswer($data));
    }

    public function storeVideo(Request $request, $raffle)
    {
        $streamId = $request->streamId;
        $video_type = $request->video_type;
        $name = "raffle_draw_$streamId";

        $temp_path = "temp/raffle_$streamId";

        if ($request->last_byte) {
            $raffle = Raffle::findOrFail($raffle);
            $save_path = sprintf("raffle/%s.%s", uniqid(), 'webm');

            MergeVideo::dispatch($raffle, $temp_path, $save_path);


            $url = route('streaming.view', ['streamId' => $raffle->id, 'userId' => random_int(4, 6)]);
            $this->publishBroadcastUrlForAllUser($url, $raffle->id, 0);

            $raffle->update([
                'stopped_at'        =>  Carbon::now(),
                'video_src_path'    =>  $save_path,
            ]);

            return response(['status' => 'completely uploaded', 'file_name' => $save_path], 200);
        }

        $extension = $request->file('chunk')->extension();

        $request->file('chunk')->storeAs($temp_path,  sprintf("%s-%s.%s", $name, uniqid(), $extension));

        return response(['status' => 'creating complete', 'file_name' => "$video_type"], 200);
    }

    /**
     * Publish Story Like & Comment update event
     *
     * @return void
     */
    public function publishBroadcastUrl(string $url, int $user_id, $streamId, $stream_status = 1)
    {
        $data = [
            'event'     =>  'BroadcastNotification',
            'data'      =>  [
                'url' => $url,
                'stream_status' => $stream_status,
                'stream_id' => $streamId,
            ]
        ];
        Redis::publish('broadcast-notification-' . $user_id, json_encode($data));

        $data = [
            'event'     =>  'BroadcastNotificationStatic',
            'data'      =>  [
                'url' => $url,
                'stream_status' => $stream_status,
                'stream_id' => $streamId,
                'user_id'   =>  $user_id
            ]
        ];

        Redis::publish('broadcast-notification-static', json_encode($data));



        // $data2 = [
        //     'event'     =>  'BroadcastStart',
        //     'data'      =>  [
        //         'url' => $url,
        //         'stream_status' => $stream_status,
        //         'stream_id' => $streamId,
        //         'user_id'   =>  $user_id
        //     ]
        // ];

        // Redis::publish('story-update-channel', json_encode($data2));



        //     $data = [
        //         'event'     =>  'StoryUpdate',
        //         'data'      =>  [
        //             'id'            =>     0,
        //             'like_count'    =>      0,
        //             'comment_count' =>      0,
        //             'type'          =>      'live_streaming',
        //             'is_like_by_me' =>       0,

        //             'url' => $url,
        //             'stream_status' => $stream_status,
        //             'stream_id' => $streamId,
        //         ]
        //     ];

        // // dump($data);

        //     Redis::publish('story-update-channel', json_encode($data));



        $data = [
            'event'     =>  'UrlSend',
            'data'      =>  [
                'url' => $url,
                'stream_status' => $stream_status,
                'stream_id' => $streamId,
            ]
        ];
        Redis::publish('url', json_encode($data));
    }




    public function publishBroadcastUrlForAllUser(string $url, $streamId, $stream_status = 1)
    {
        $data = [
            'event'     =>  'BroadcastNotification',
            'data'      =>  [
                'url' => $url,
                'stream_status' => $stream_status,
                'stream_id' => $streamId,
            ]
        ];

        Redis::publish('broadcast-notification', json_encode($data));
    }


    public function startLiveStream(Request $request, Raffle $raffle)
    {
        $request->validate([
            'playback_url'      =>  ['required'],
            // 'file_url'          =>  ['required'],
        ]);

        $raffle->update([
            'started_at' => Carbon::now(),
            'playback_url'  =>  $request->playback_url
            // 'video_src_path'    =>  $request->file_url
        ]);

        $this->publishBroadcastUrlForAllUser($request->playback_url, $raffle->id, 1);

        return response()->json([
            'status'        =>  true,
        ]);
    }


    public function completeLiveStream(Request $request, Raffle $raffle)
    {
        $request->validate([
            'playback_url'      =>  ['required'],
            'file_url'          =>  ['required'],
        ]);

        $raffle->update([
            'stopped_at' => Carbon::now(),
            'video_src_path'    =>  $request->file_url,
            'playback_url'      =>  null
        ]);

        $this->publishBroadcastUrlForAllUser($request->playback_url, $raffle->id, 0);

        return response()->json([
            'status'        =>  true,
        ]);
    }
}
