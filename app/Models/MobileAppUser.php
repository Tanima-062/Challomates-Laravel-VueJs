<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Propaganistas\LaravelPhone\PhoneNumber;
use Illuminate\Database\Eloquent\SoftDeletes;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\SpatialBuilder;
use Illuminate\Database\Eloquent\Casts\Attribute;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class MobileAppUser extends Authenticatable
{
    use HasFactory, HasApiTokens, SoftDeletes, Notifiable;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_PENDING = 'pending';
    public const STATUS_NEW = 'new';

    const PREFIX = 'MAB';

    const DIRECT_CONSUMER = 'direct_consumer';
    const DISTRIBUTION_CONSUMER = 'distribution_consumer';

    protected $fillable = [
        'prefix_id', 'photo', 'username', 'first_name', 'last_name', 'date_of_birth', 'email', 'street', 'house_number', 'zip_code', 'city', 'country', 'country_iso_code', 'phone_number', 'full_phone_number', 'type', 'privacy', 'password', 'email_verified_at', 'verification_token', 'language_id', 'status', 'sales_partner_id', 'coin', 'location'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'deleted_at',
    ];


    protected $casts = [
        'privacy'       =>  'boolean',
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'location' => Point::class,
    ];


    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->prefix_id = nextId('mobile_app_users', self::PREFIX);
            // $user->status = self::STATUS_PENDING;
            // $user->verification_token = mt_rand(11111,999999);

            // set full phone number
            try {
                $user->full_phone_number = PhoneNumber::make($user->phone_number, $user->country_iso_code)->formatE164();
            } catch (\Throwable $th) {
                $user->full_phone_number = null;
            }
        });

        static::updating(function ($user) {
            // set full phone number
            try {
                $user->full_phone_number = PhoneNumber::make($user->phone_number, $user->country_iso_code)->formatE164();
            } catch (\Throwable $th) {
                $user->full_phone_number = null;
            }
        });

        static::deleting(function ($user) {

            // DB::table('activity_log')->where('subject_id', $user->id)->where('subject_type', MobileAppUser::class)->delete();

            DB::table('fcm_tokens')->where('mobile_app_user_id', $user->id)->delete();
            DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->where('tokenable_type', MobileAppUser::class)->delete();
            DB::table('mobile_password_resets')->where('mobile_app_user_id', $user->id)->delete();
            DB::table('mobile_user_deletes')->where('mobile_app_user_id', $user->id)->delete();
            DB::table('mobile_email_verification')->where('mobile_app_user_id', $user->id)->delete();


            DB::table('raffle_winners')->where('mobile_app_user_id', $user->id)->delete();
            DB::table('participations')->where('mobile_app_user_id', $user->id)->delete();


            DB::table('followers')->where('followable_id', $user->id)->where('followable_type', MobileAppUser::class)->delete();
            DB::table('followers')->where('follower_id', $user->id)->where('follower_type', MobileAppUser::class)->delete();

            DB::table('comments')->where('mobile_app_user_id', $user->id)->delete();
            DB::table('likes')->where('mobile_app_user_id', $user->id)->delete();
            DB::table('block_user')->where('mobile_app_user_id', $user->id)->delete();
            DB::table('block_user')->where('blocked_id', $user->id)->delete();
            DB::table('story_user_report')->where('mobile_app_user_id', $user->id)->delete();
            DB::table('story_tag')->where('mobile_app_user_id', $user->id)->delete();
            DB::table('story_user')->where('mobile_app_user_id', $user->id)->delete();


            Story::query()->where('mobile_app_user_id', $user->id)->get()->each(fn($item)=>$item->delete());

            DB::table('store_visits')->where('mobile_app_user_id', $user->id)->delete();
        });
    }

    public function newEloquentBuilder($query): SpatialBuilder
    {
        return new SpatialBuilder($query);
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getPhotoUrlAttribute()
    {
        if (is_null($this->photo)) {
            return asset("images/photo.png");
        }

        if (preg_match("@http@", $this->photo)) {
            return $this->photo;
        }

        if(config('filesystems.default')  == 'exoscale' ||  env('FILESYSTEM_DISK') == 'exoscale' ){
            return Storage::disk(env('FILESYSTEM_DISK'))->publicUrl($this->photo);
        }

        return Storage::disk(env('FILESYSTEM_DISK'))->url($this->photo);
    }


    //relations
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id');
    }

    public function storeVisits()
    {
        return $this->hasMany(StoreVisits::class, 'mobile_app_user_id');
    }

    public function blockedMobileAppUsers()
    {
        return $this->belongsToMany(MobileAppUser::class, 'block_user', 'mobile_app_user_id', 'blocked_id');
    }

    public function blockedByMobileAppUsers()
    {
        return $this->belongsToMany(MobileAppUser::class, 'block_user', 'blocked_id', 'mobile_app_user_id');
    }

    //local scopes
    public function ScopeSearch($q, $search)
    {
        if (!is_null($search)) {
            return $q->where(function ($query) use ($search) {
                $query->where('first_name', 'LIKE', "%$search%")
                    ->orWhere('last_name', "LIKE", "%$search%")
                    ->orWhere('username', "LIKE", "%$search%")
                    ->orWhereRaw("CONCAT(`first_name`, ' ', `last_name`) LIKE ?", ["%$search%"])
                    ->orWhere('email', "LIKE", "%$search%");
            });
        }
        return $q;
    }

    public function ScopeCreatedAtBetween($q, $start_date, $end_date)
    {
        if (!is_null($start_date) && !is_null($end_date)) {
            return $q->whereDate('created_at', '>=', $start_date)->whereDate('created_at', "<=", $end_date);
        }
        return $q;
    }

    public function ScopeStatusIn($q, $status)
    {
        if (!is_null($status)) {
            return $q->whereIn('status', explode(",", $status));
        }
        return $q;
    }

    public function ScopeSortByOwnTableColumns($q, $column = 'created_at', $direction = 'DESC')
    {
        $sortable_columns = ['created_at', 'username', 'type', 'email', 'status'];
        $available_direction = ['ASC', "DESC"];

        if (!in_array($direction, $available_direction)) {
            return $q;
        }

        if ($column == 'name' && $direction) {
            return $q->orderByRaw("CONCAT(first_name, ' ', last_name) $direction");
        }

        if ($column && $direction && in_array($column, $sortable_columns)) {
            return $q->orderBy($column, $direction);
        }
        return $q;
    }


    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }


    //Twilio
    public function routeNotificationForTwilio()
    {
        return $this->full_phone_number;
    }

    public function latitude(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->location?->latitude;
        });
    }

    public function longitude(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->location?->longitude;
        });
    }

    public function fcmTokens()
    {
        return $this->hasMany(FcmToken::class);
    }


    /**
     * Own stories
     *
     * @return void
     */
    public function stories()
    {
        return $this->hasMany(Story::class);
    }


    /**
     * Tagged stories
     *
     * @return void
     */
    public function taggedStories()
    {
        return $this->belongsToMany(Story::class, 'story_tag', 'mobile_app_user_id', 'story_id');
    }

    // All stories own, tagged
    public function allStories()
    {
        return $this->belongsToMany(Story::class, 'story_user', 'mobile_app_user_id', 'story_id');
    }




    public function follow()
    {
        return $this->belongsTo(Follower::class, 'follower_id')
            ->accept()
            ->UserFollowings()
            ->UserFollower()

        ;
    }


    public function scopeWithUserFollow($query, $userId)
    {
        $query->addSelect(['follower_id'=> Follower::select(['id'])
            ->whereColumn('followable_id', 'mobile_app_users.id')
            ->where('follower_id', $userId)
            // ->latest()
            ->take(1)
        ])
        // ->with('follow')
        ;
    }


    public function scopeFollowByMe($query, $userId){
        $query->addSelect(['follow_by_me'=> Follower::select(['status'])
            ->whereColumn('followable_id', 'mobile_app_users.id')
            ->where('follower_id', $userId)
            // ->latest()
            ->take(1)
        ]);
    }


    public function scopeFollowByMate($query, $userId){
        $query->addSelect(['follow_by_mate'=> Follower::select(['status'])
            ->whereColumn('follower_id', 'mobile_app_users.id')
            ->where('followable_id', $userId)
            // ->latest()
            ->take(1)
        ]);
    }


    public function scopeCheckInNow($query){
        $query->addSelect(['check_in_now'=> StoreVisits::select(['id'])
            ->whereColumn('mobile_app_user_id', 'mobile_app_users.id')
            ->whereNull('check_out_time')
            // ->where('follower_id', $userId)
            ->latest()
            ->take(1)
        ]);
    }


    public function scopeCheckInVisits($query){
        $query->addSelect(['check_in_now'=> StoreVisits::select(['check_out_time'])
            ->whereColumn('mobile_app_user_id', 'mobile_app_users.id')
            // ->whereNull('check_out_time')
            // ->where('follower_id', $userId)
            ->latest()
            ->take(1)
        ]);
    }


    public function participations()
    {
        return $this->hasMany(Participation::class);
    }


    /**
     * Get all followings
     *
     * @return void
     */
    public function followings()
    {
        return $this->belongsToMany(MobileAppUser::class, 'followers', 'follower_id', 'followable_id')
            ->withPivot(['followable_type', 'follower_type', 'id', 'status', 'accept_at']);
    }



    /**
     * Get all Followers
     *
     * @return void
     */
    public function followers()
    {
        return $this->belongsToMany(MobileAppUser::class, 'followers',  'followable_id', 'follower_id')
            ->withPivot(['followable_type', 'follower_type', 'id', 'status', 'accept_at']);
    }



    /**
     * Get all followers by follow type mobile_app_user active
     *
     * @param [type] $query
     * @return void
     */
    public function scopeUserFollowers($query)
    {
        return $query->where('followable_type', MobileAppUser::class)
            ->where('mobile_app_users.status', 'active');
    }


    /**
     * Get all followings by follow type mobile_app_user active
     *
     * @param [type] $query
     * @return void
     */
    public function scopeUserFollowings($query)
    {
        return $query->where('followable_type', MobileAppUser::class)
            ->where('mobile_app_users.status', 'active');
    }


    /**
     * Get only accepted followers
     *
     * @param [type] $query
     * @return void
     */
    public function scopeAcceptedFollowers($query)
    {
        return $this->followers()->wherePivot('status', Follower::ACCEPT);
    }



    /**
     * Get only pending followers
     *
     * @param [type] $query
     * @return void
     */
    public function scopePendingFollowers($query)
    {
        return $this->followers()->wherePivot('status', Follower::PENDING);
    }

    /**
     * Exclude Blocked users
     *
     */
    public function scopeExcludeBlockedUsers($query)
    {
        return $query->whereDoesNotHave('blockedMobileAppUsers');
    }

    /**
     * Get only pending followers
     *
     * @param [type] $query
     * @return void
     */
    public function scopePendingAndAcceptFollowers($query)
    {
        return $this->followers()
            ->wherePivot('status', Follower::PENDING)
            ->orWherePivot('status', Follower::ACCEPT)
            // ->orderBy('created_at')
            ;
    }


    /**
     * Get only accepted followings
     *
     * @param [type] $query
     * @return void
     */
    public function scopeAcceptedFollowings($query)
    {
        return $this->followings()->wherePivot('status', Follower::ACCEPT);
    }


    /**
     * Get only pending followings
     *
     * @param [type] $query
     * @return void
     */
    public function scopePendingFollowings($query)
    {
        return $this->followings()->wherePivot('status', Follower::PENDING);
    }

    //getters

    public function photoUrl(): Attribute
    {

        return Attribute::make(get: function () {
            if (is_null($this->photo)) {
                return asset("images/avatar.png");
            }

            if (preg_match("@http@", $this->photo)) {
                return $this->photo;
            }

            if(config('filesystems.default')  == 'exoscale' ||  env('FILESYSTEM_DISK') == 'exoscale' ){
                return Storage::disk(env('FILESYSTEM_DISK'))->publicUrl($this->photo);
            }

            return Storage::disk('public')->url($this->photo);
        });
    }
}
