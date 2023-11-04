<?php

namespace App\Models;

use App\Traits\HasPermission;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Propaganistas\LaravelPhone\PhoneNumber;

class User extends Authenticatable
{
    use HasFactory, Notifiable, LogsActivity, SoftDeletes, HasApiTokens, HasPermission;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_PENDING = 'pending';
    public const STATUS_NEW = 'new';

    const SYSTEM_ADMIN = 'system_admin';
    const CHALLO_MATES_ADMIN = 'challo_mates_admin';
    const COMPANY_CONSULTANT = 'company_consultant';
    const SALES_PARTNERS = 'sales_partners';
    const MOBILE_APP_USER = 'mobile_app_user';

    protected static $recordEvents = ['updated'];

    protected static $logAttributes = [];

    public function getActivitylogOptions(): LogOptions
    {
        $logOnly = [];

        if ($this->type == 'employee') {
            $logOnly[] = 'status';
        }

        return LogOptions::defaults()
            ->logOnly($logOnly)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
        // Chain fluent methods for configuration options
    }
    /**
     * Company Sales Admin User Type
     *
     * @var String
     */
    public static $sales_company_admin     = "sales_company_admin";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'type',
        'language_id',
        'status',
        'user_medium',
        'phone_number',
        'country_iso_code',
        'verification_token',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        // 'status' => 'boolean',
    ];

    protected $appends = ['name', 'user_type'];

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getUserTypeAttribute()
    {
        $type_translations = ['challo_mates_admin' => 'CHalloMates Admin'];

        return $type_translations[$this->type] ?? ucwords(str_replace(['_', '-'], " ", $this->type));
    }

    public function getAvatarUrlAttribute()
    {

        if (is_null($this->avatar)) {
            return asset("images/avatar.png");
        }

        if (preg_match("@http@", $this->avatar)) {
            return $this->avatar;
        }

        if(config('filesystems.default')  == 'exoscale' ||  env('FILESYSTEM_DISK') == 'exoscale' ){
            return Storage::disk(env('FILESYSTEM_DISK'))->publicUrl($this->avatar);
        }

        return Storage::url($this->avatar);
    }

    public function getActiveStatusAttribute()
    {
        if (!is_null($this->verification_token)) {
            return User::STATUS_PENDING;
        }

        return $this->status ? User::STATUS_ACTIVE : User::STATUS_INACTIVE;
    }

    public static function getNextPrefixID($type)
    {
        $prefix_id = User::where('type', $type)->orderBy('id', 'desc')->take(1)->first()?->prefix_id;
        $prefix_id = ($prefix_id ? preg_replace('/[^0-9.]/', '', $prefix_id) : 0) + 1;
        return sprintf("%05d",  $prefix_id);
    }

    //relations

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id');
    }


    public function routeNotificationForTwilio()
    {
        return $this->full_phone_number;
    }

    public function resetToken()
    {
        $column_name = $this->user_medium == "phone" ? "phone_number" : "email";
        return $this->hasOne(PasswordReset::class, $column_name, $column_name);
    }


    //local scopes

    public function scopeIsChalloMatesAdmin($q)
    {
        return $q->where('type', 'challo_mates_admin');
    }

    public function scopeIsConsultant($q)
    {
        return $q->where('type', 'company_consultant');
    }


    //observers
    protected static function booted()
    {
        static::creating(function ($user) {
            //set prefix id
            $user->prefix_id = getUserPrefix($user->type) . User::getNextPrefixID($user->type);

            // set full phone number
            try {
                $user->full_phone_number = PhoneNumber::make($user->phone_number, $user->country_iso_code)->formatE164();
            } catch (\Throwable $th) {
                $user->full_phone_number = '+' . getCountryCodeByAbbreviation($user->country_iso_code) . $user->phone_number;
            }
        });

        static::updating(function ($user) {

            // set full phone number
            try {
                $user->full_phone_number = PhoneNumber::make($user->phone_number, $user->country_iso_code)->formatE164();
            } catch (\Throwable $th) {
                $user->full_phone_number = '+' . getCountryCodeByAbbreviation($user->country_iso_code) . $user->phone_number;
            }
        });
    }
}
