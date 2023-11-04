<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\StoreVisits;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Booster;

class BoosterPostStore extends Command
{


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booster-post:store';

    /**
     * The console command description.
     *
     * @var string
     */

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Booster post has been saved successfully!');
        $start = now();
        $end = now();

        $start_time =  $start->startOfMinute()->subSeconds()->toDateTimeString();
        $end_time  =  $end->startOfMinute()->addMinute()->toDateTimeString();

        // dump(now()->toDateTimeString(), $start_time, $end_time);

        // $oneTimeBoosters = Booster::where('type','One Time')->where('start', date('Y-m-d'))->get();
        $oneTimeBoosters = Booster::query()
                ->without(['salesPartner','contract', 'boosterBoosterTypes'])
                ->where(function($q){
                    // $q->where('status', 'new');
                })
                ->where('type','One Time')
                ->whereDate('start', now())
                ->whereTime('posting_time', '<', $end_time)
                ->whereTime('posting_time', '>', $start_time)
                // ->count()
                // ->pluck('id')
                // ->toArray()
                ->get()
                // ->pluck('posting_time')
                ;
        // dd($oneTimeBoosters);
        // dump($oneTimeBoosters->count());
        // info($oneTimeBoosters->count());
        // info('booster posted');
        // info($oneTimeBoosters->pluck('id'));

        $one_time_posts = [];

        foreach($oneTimeBoosters as $booster){
            $posting_time = Carbon::parse($booster->posting_time)->toTimeString();
            $one_time_posts[] = [
                'booster_id'=> $booster->id,
                'sales_partner_id' => $booster->sales_partner_id,
                'title' => $booster->title,
                'body' => $booster->body,
                'file' => $booster->file,
                'file_name' => $booster->file_name,
                'range' => $booster->range,
                'posting_date' => $booster->start,
                'posting_time' => $posting_time,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('booster_posts')->insert($one_time_posts);

        $recurringBoosters = Booster::query()
            ->without(['salesPartner','contract'])
            ->where('type','Recurring')
            ->whereRaw('"'.date('Y-m-d').'" between `start` and `end`')
            ->get()
        ;

        $booster_posts = [];

        foreach($recurringBoosters as $booster){
            $today = today();
            $currentWeekOfMonth = $today->weekOfMonth;
            $currentDayOfWeek = $today->dayName;

            $weekNumber = $this->getWeek($currentWeekOfMonth);

            foreach($booster->boosterBoosterTypes as $boosterType){
                if($boosterType->week == $weekNumber && $boosterType->weekday == $currentDayOfWeek) {
                    $boosterTypeTime = Carbon::parse($boosterType->time);

                    $start = now();
                    $end = now();

                    $start_time =  $start->startOfMinute()->subSeconds()->toDateTimeString();
                    $end_time  =  $end->startOfMinute()->addMinute()->toDateTimeString();

                    if($boosterTypeTime->lessThan($end_time) && $boosterTypeTime->greaterThan($start_time) ){
                        $booster_posts[]= [
                            'booster_id'=> $booster->id,
                            'sales_partner_id' => $booster->sales_partner_id,
                            'title' => $booster->title,
                            'body' => $booster->body,
                            'file' => $booster->file,
                            'file_name' => $booster->file_name,
                            'range' => $booster->range,
                            'posting_date' => today(),
                            'posting_time' => $boosterType->time,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }
        }

        // foreach($recurringBoosters as $booster){

        //     $dates = array();
        //     $current = ($booster->start < date('Y-m-d')) ? strtotime(date('Y-m-d')) : strtotime($booster->start);
        //     $last = strtotime($booster->end);

        //     while( $current <= $last ) {
        //         $dates[] = date('Y-m-d', $current);
        //         $current = strtotime('+1 day', $current);
        //     }
        //     foreach($booster->boosterBoosterTypes as $booster_type){
        //         foreach($dates as $date){
        //             $week = $this->getWeek(date('w', strtotime($date)));
        //             $day = date('D', strtotime($date)) . 'day';

        //             if($week == $booster_type->week && $day == $booster_type->weekday && $booster_type->time > Carbon::parse($start_time)->toTimeString() && $booster_type->time < Carbon::parse($end_time)->toTimeString()){

        //                 array_push($booster_posts,[
        //                     'booster_id'=> $booster->id,
        //                     'sales_partner_id' => $booster->sales_partner_id,
        //                     'title' => $booster->title,
        //                     'body' => $booster->body,
        //                     'file' => $booster->file,
        //                     'file_name' => $booster->file_name,
        //                     'range' => $booster->range,
        //                     'posting_date' => $date,
        //                     'posting_time' => $booster_type->time,
        //                     'created_at' => now(),
        //                     'updated_at' => now(),
        //                 ]);
        //             }
        //         }
        //     }
        // }


        DB::table('booster_posts')->insert($booster_posts);
    }

    public function getWeek($week){
        if($week == '1'){
            return "1st";
        }elseif($week == '2'){
            return "2nd";
        }
        elseif($week == '3'){
            return "3rd";
        }
        elseif($week == '4'){
            return "4th";
        }
        elseif($week == '5'){
            return "last";
        }


    }

}
