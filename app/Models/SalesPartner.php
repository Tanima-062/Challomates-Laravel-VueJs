<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\SalesPartnerFollower;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Propaganistas\LaravelPhone\PhoneNumber;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\SpatialBuilder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalesPartner extends Model
{
    use HasFactory, SalesPartnerFollower;

    const PREFIX_ID = "VP";

    protected $fillable = ['prefix_id', 'status', 'company_name', 'profile_picture', 'receipt_template', 'receipt_template_name', 'website', 'street', 'house_number', 'zip_code', 'city', 'country', 'no_information', 'contact_person_first_name', 'contact_person_last_name', 'contact_person_email', 'contact_person_country_iso_code', 'contact_person_phone_number', 'contact_person_full_phone_number', 'branch_id', 'branch_category_id', 'consultant_id', 'challo_mates_admin_id', 'map_address', 'coordinates'];
    protected $appends = ['latitude', 'longitude'];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_NEW = 'new';
    public const STATUS_INACTIVE = 'inactive';


    protected $casts = [
        'coordinates' => Point::class,
    ];

    // Side Effects

    protected static function booted()
    {
        static::creating(function ($sales_partner) {
            $sales_partner->prefix_id = static::getNextPrefixId();
            // set full phone number
            try {
                $sales_partner->contact_person_full_phone_number = PhoneNumber::make($sales_partner->contact_person_phone_number, $sales_partner->contact_person_country_iso_code)->formatE164();
            } catch (\Throwable $th) {
                $sales_partner->contact_person_full_phone_number = '+' . getCountryCodeByAbbreviation($sales_partner->contact_person_country_iso_code) . $sales_partner->contact_person_phone_number;
            }
        });


        static::updating(function ($sales_partner) {
            // set full phone number
            try {
                $sales_partner->contact_person_full_phone_number = PhoneNumber::make($sales_partner->contact_person_phone_number, $sales_partner->contact_person_country_iso_code)->formatE164();
            } catch (\Throwable $th) {
                $sales_partner->contact_person_full_phone_number = null;
            }
        });
    }

    public function newEloquentBuilder($query): SpatialBuilder
    {
        return new SpatialBuilder($query);
    }

    // public function getProfilePictureUrlAttribute()
    // {
    //     if (is_null($this->profile_picture)) {
    //         return null;
    //     }

    //     if (preg_match("@http@", $this->profile_picture)) {
    //         return $this->profile_picture;
    //     }

    //     return Storage::disk('public')->url($this->profile_picture);
    // }


    //relations

    public function boosterPost()
    {
        return $this->hasMany(BoosterPost::class, 'sales_partner_id');
    }


    public function stories()
    {
        return $this->hasMany(Story::class, 'sales_partner_id');
    }



    public function branchCategory(): BelongsTo
    {
        return $this->belongsTo(BranchCategory::class, 'branch_category_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function consultant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'consultant_id')->where('type', 'company_consultant');
    }

    public function mobileAppUsers(): HasMany
    {
        return $this->hasMany(MobileAppUser::class, 'sales_partner_id');
    }

    public function openingHours(): HasMany
    {
        $day = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $orederByQuery = "FIELD(day,'" . implode("', '", $day) .  "')";
        return $this->hasMany(SalesPartnerOpeningHours::class, 'sales_partner_id')->orderByRaw($orederByQuery);
    }

    public function storeVisits()
    {
        return $this->hasMany(StoreVisits::class);
    }


    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'sales_partner_id');
    }

    //local scopes

    public function scopeSearch($q, $search)
    {
        if (!is_null($search)) {
            return $q->where(function ($query) use ($search) {
                $query
                    ->where('prefix_id', "LIKE", "%$search%")
                    ->orWhere('company_name', "LIKE", "%$search%")
                    ->orWhere('contact_person_first_name', "LIKE", "%$search%")
                    ->orWhere('contact_person_last_name', "LIKE", "%$search%")
                    ->orWhereRaw("CONCAT(contact_person_first_name,' ', contact_person_last_name) LIKE ?", ["%$search%"])
                    ->orWhereHas('consultant', fn ($q) => $q->whereRaw("CONCAT(`first_name`,' ', `last_name`) LIKE ?", ["%$search%"]));
            });
        }
    }

    public function scopeCreatedAtBetween($q, $start_date, $end_date)
    {
        if (!is_null($start_date) && !is_null($end_date)) {
            return $q->whereDate('created_at', '>=', $start_date)->whereDate('created_at', "<=", $end_date);
        }
    }

    public function scopeCompanyConsultantIn($q, $consultants)
    {
        if (is_null($consultants))
            return $q;

        return $q->whereIn('consultant_id', explode(',', $consultants));
    }

    public function scopeBranchIn($q, $branches)
    {
        if (is_null($branches))
            return $q;

        return $q->whereHas('branch', fn ($q) => $q->whereIn('id', explode(',', $branches)));
    }

    public function scopeBranchCategoryIn($q, $branch_catagories)
    {
        if (is_null($branch_catagories))
            return $q;

        return $q->whereHas('branchCategory', fn ($q) => $q->whereIn('id', explode(',', $branch_catagories)));
    }

    public function scopeCityIn($q, $cities)
    {
        if (is_null($cities))
            return $q;

        return $q->whereIn('city', explode(',', $cities));
    }

    public function scopeStatusIn($q, $status = "")
    {
        if (is_null($status)) {
            return $q;
        }
        return $q->whereIn('status', explode(",", $status));
    }


    public function scopeSortByColumns($q, $column = 'created_at', $direction = 'DESC')
    {
        $sortable_columns = ['created_at', 'company_name', 'status', 'company_consultant', 'branch_category', 'contract_term_period'];
        $available_direction = ['ASC', "DESC"];

        if (!in_array($direction, $available_direction) || !in_array($column, $sortable_columns)) {
            return $q;
        }

        if ($column == 'company_consultant') {
            return $q->orderBy(User::select('first_name')->isConsultant()->whereColumn('users.id', 'sales_partners.consultant_id'), $direction);
        }

        if ($column == 'branch_category') {
            return $q->orderBy(BranchCategory::select('name')->whereColumn('branch_category_id', 'branch_categories.id'), $direction);
        }

        if ($column == 'contract_term_period') {
            $now = Carbon::now()->format("Y-m-d");
            return $q->orderBy(Contract::select('contract_term_from')
                ->whereColumn('sales_partners.id', 'contracts.sales_partner_id')
                ->where(fn ($q) => $q->whereDate('contracts.contract_term_from', '<=', $now)->where('contracts.contract_term_to', '>=', $now)->orWhereDate('contracts.contract_term_from', '>=', $now))
                ->orderBy('contracts.contract_term_from', 'ASC')->limit(1), $direction);
        }

        return $q->orderBy($column, $direction);
    }

    function currentOpeningHour(): Attribute
    {
        return Attribute::make(get: function ($value) {
            $openingAt = [];
            foreach ($this->openingHours as $key => $openingHour) {
                $first_start_time = Carbon::parse($openingHour->day . $openingHour->first_start_time);
                $first_end_time = Carbon::parse($openingHour->day . $openingHour->first_end_time);
                $last_start_time = Carbon::parse($openingHour->day . $openingHour->last_start_time);
                $last_end_time = Carbon::parse($openingHour->day . $openingHour->last_end_time);
                $current_time = Carbon::now();

                if ($current_time->lessThanOrEqualTo($first_end_time)) {
                    $openingAt[] = ["start_time" => $first_start_time, "end_time" => $first_end_time];
                };

                if ($current_time->lessThanOrEqualTo($last_end_time)) {
                    $openingAt[] = ["start_time" => $last_start_time, "end_time" => $last_end_time];
                };
            }
            usort($openingAt, fn ($a, $b) => $a['start_time'] <=> $b['start_time']);
            return $openingAt[0] ?? null;
        });
    }

    public function currentContract(): Attribute
    {
        return Attribute::make(get: function () {
            $now = Carbon::now()->format("Y-m-d");
            return $this->contracts->where('contract_term_from', '<=', $now)->where('contract_term_to', '>=', $now)->first() ?? $this->contracts->where('contract_term_from', '>=', $now)->first();
        });
    }

    public function profilePictureUrl(): Attribute
    {

        return Attribute::make(get: function () {
            if (is_null($this->profile_picture)) {
                return null;
            }

            if (preg_match("@http@", $this->profile_picture)) {
                return $this->profile_picture;
            }

            if(config('filesystems.default')  == 'exoscale' ||  env('FILESYSTEM_DISK') == 'exoscale' ){
                return Storage::disk(env('FILESYSTEM_DISK'))->publicUrl($this->profile_picture);
            }

            return Storage::disk('public')->url($this->profile_picture);
        });
    }

    public function receiptTemplateUrl(): Attribute
    {
        return Attribute::make(get: function () {
            if (is_null($this->receipt_template)) {
                return null;
            }

            if (preg_match("@http@", $this->receipt_template)) {
                return $this->profile_picture;
            }

            if(config('filesystems.default')  == 'exoscale' ||  env('FILESYSTEM_DISK') == 'exoscale' ){
                return Storage::disk(env('FILESYSTEM_DISK'))->publicUrl($this->receipt_template);
            }

            return Storage::disk('public')->url($this->receipt_template);
        });
    }

    public function latitude(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->coordinates?->latitude;
        });
    }

    public function longitude(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->coordinates?->longitude;
        });
    }



    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }


    public static function getNextPrefixId()
    {
        $last_id = (self::orderBy('id', 'desc')->first()?->id ?? 0) + 1;
        return sprintf("VP%05d",  $last_id);
    }
}
