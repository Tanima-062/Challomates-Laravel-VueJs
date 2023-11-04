<?php

namespace App\Http\Controllers\Api\Stories;

use App\Models\BoosterPost;
use Illuminate\Http\Request;
use App\Models\MobileAppUser;
use App\Http\Controllers\Controller;
use MatanYadaev\EloquentSpatial\Objects\Point;
use App\Http\Resources\Story\BoosterPostResource;

class BoosterPostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'lat'               =>  ['nullable', 'numeric'],
            'lon'               =>  ['nullable', 'numeric'],
            'store_id'          =>  ['nullable', 'numeric'],
        ]);


        if($request->store_id){
            $boosterPosts = BoosterPost::query()
            ->with('salesPartner:id,company_name,profile_picture,website,street,house_number,zip_code,city,coordinates,country')
            ->where('sales_partner_id', $request->store_id)
            ->where(function($q){
                $q->whereDate('created_at', '<=', now())
                    ->whereDate('created_at', '>=', now()->subHours(24))
                    // ->whereDate('created_at', '>=', now()->subMinutes(10))
                ;
            })
            ->withCount([
                'comments'=> fn($q)=> $q->whereHas('user', fn($q)=> $q->where('status', MobileAppUser::STATUS_ACTIVE)),
                'likes'=> fn($q)=> $q->whereHas('user', fn($q)=> $q->where('status', MobileAppUser::STATUS_ACTIVE))
            ])
            ->orderBy('id', 'DESC')
            ->isLike($user)
            ->get()
            ;
            return BoosterPostResource::collection($boosterPosts)->additional([
                'status'        =>  true,
            ]);
        }

        $boosterPosts = BoosterPost::query()
            ->with('salesPartner:id,company_name,profile_picture,website,street,house_number,zip_code,city,coordinates,country')
            // ->where()
            ->where(function($q){
                $q->whereDate('created_at', '<=', now())
                    ->whereDate('created_at', '>=', now()->subHours(24))
                    // ->whereDate('created_at', '>=', now()->subMinutes(10))
                ;
            })
            ->withCount([
                'comments'=> fn($q)=> $q->whereHas('user', fn($q)=> $q->where('status', MobileAppUser::STATUS_ACTIVE)),
                'likes'=> fn($q)=> $q->whereHas('user', fn($q)=> $q->where('status', MobileAppUser::STATUS_ACTIVE))
            ])
            ->isLike($user)
            ->orderBy('id', 'DESC')
            ->get()
            ->filter(function($item){
                $latitude = $item->salesPartner->latitude;
                $longitude = $item->salesPartner->longitude;
                // $lat =  46.818188;
                // $lon =  8.227512;
                $lat = request('lat');
                $lon =  request('lon');

                $distance = $this->distance($latitude, $longitude, $lat, $lon, 'K');

                return $distance <= $item->range;
            })
            ->values()
        ;

        // $userLocation = new Point($request->lat, $request->lon);
        // $radious = request('radius', 1);//km
        //     $boosterPosts = BoosterPost::query()
        //     ->with('salesPartner:id,company_name,profile_picture,website,street,house_number,zip_code,city,coordinates')
        //     // ->where()
        //     ->where(function($q){
        //         $q->whereDate('created_at', '<=', now())
        //             ->whereDate('created_at', '>=', now()->subHours(24))
        //             // ->whereDate('created_at', '>=', now()->subMinutes(10))
        //         ;
        //     })
        //     ->whereHas('salesPartner', function($q) use($userLocation, $radious){
        //         $q->whereDistanceSphere('coordinates',  $userLocation, '<=', ($radious * 1000));
        //     })
        //     ->get()
        // ;

        // return $boosterPosts;

        return BoosterPostResource::collection($boosterPosts)->additional([
            'status'        =>  true,
        ]);

        // return $boosterPosts;
        // return $boosterPosts->count();
    }


    function distance($lat1, $lon1, $lat2, $lon2, $unit) {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
      }

}
