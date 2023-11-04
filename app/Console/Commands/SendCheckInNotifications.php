<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\StoreVisits;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
class SendCheckInNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkin-notifications:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Check-In Notifications to Mobile App Users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        info('send notification command run');
        // return 0;
        $this->info('Check in notification send successfully!');

        $now = now()->addSeconds(30);

        $start = now();
        $end = now();

        $start_time =  $start->startOfMinute()->toDateTimeString();
        $end_time  =  $end->endOfMinute()->toDateTimeString();

        dump($start_time, $end_time);

        $query = StoreVisits::query()
            ->whereNull('check_out_time')
            ->whereDate('check_in_time', Carbon::today()->toDateString())
            // ->whereTime('check_in_time', '<', now()->subHours(3)->subMinute())
            // ->whereTime('check_in_time', '<', now()->subMinutes(10)->subMinute())
            // ->whereTime('sent_time', '<', $now)
            ->whereTime('sent_time', '<=', $end_time)
            ->whereTime('sent_time', '>=', $start_time)
            ->where('sent_count', '<', 3)
            ;

        info('store_visits');
        // info($query)
        $storeVisits = clone $query;

        $nextTime = now()->addHours(3);
        // $nextTime = now()->addMinutes(5);


        $mobileAppUsersId = $storeVisits->get()
        ->pluck('mobile_app_user_id')
        ->unique()
        ->values()
        ->toArray();
        ;

        if(count($mobileAppUsersId)){
            $query->increment('sent_count', 1, ['sent_time' =>$nextTime]);
            dump($mobileAppUsersId);
            info('mobile_app_users');
            info($mobileAppUsersId);

            $fcm_tokens = DB::table('fcm_tokens')
                ->whereIn('mobile_app_user_id', $mobileAppUsersId)
                ->pluck('fcm_token')
                ->unique()
                ->values()
                ->toArray();
                ;
            info('fcm_tokens');
            info($fcm_tokens);
            dump($fcm_tokens);


            if(count($fcm_tokens)){
                $this->sendNotification($fcm_tokens);
            }

        }else {
            dump($now->toDateTimeLocalString());
            dump('no user found');
            info('No user found');
        }

    }

      /**
     * Send push notification
     *
     * @param array $tokens
     * @return void
     */
    private function sendNotification(array $tokens)
    {
        info("inside send notificatio method");
        $data = [
            "registration_ids" => $tokens,
            "notification" => [
                "title" => 'Erinnerung',
                "body" => 'Wir möchten Dich nur kurz daran erinnern, dass Du noch in einem Store eingecheckt bist. Solltest Du noch da sein, geniesse Deinen Aufenthalt einfach weiter :-). Ansonsten checke Dich bitte aus.',
            ],
            "data"  => [
                "title"=> "Something"
            ]
        ];


        $dataString = json_encode($data);

        $firebaseKey =env('FIREBASE_SERVER_KEY');

        $headers = [
            'Authorization: key=' . $firebaseKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);
        info($response);

        dump($response);
    }
}
