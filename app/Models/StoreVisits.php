<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class StoreVisits extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_partner_id', 'jackpot_share', 'mobile_app_user_id', 'check_in_time', 'check_out_time', 'turnover', 'coin', 'posting_coins', 'receipt', 'checkout_type', 'sent_time', 'sent_count', 'contract_id', 'is_limit_over', 'challomate_marketing_ag_share', 'sales_partner_share', 'consultant_share', 'challomate_share', 'senior_partner_share'
    ];

    protected $appends = ['check_out_prefix_id', 'check_in_prefix_id'];

    protected $casts = [
        'turnover' => 'decimal:2',
        'is_limit_over' => 'boolean',
        // 'check_in_time' =>  'datetime'
    ];


    public function salesPartner(): BelongsTo
    {
        return $this->belongsTo(SalesPartner::class);
    }

    public function mobileAppUser(): BelongsTo
    {
        return $this->belongsTo(MobileAppUser::class);
    }

    public function stories()
    {
        return $this->hasMany(Story::class, 'check_in_id');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    //local scopes
    public function scopeSearch($q, $search)
    {
        return $q->whereHas('mobileAppUser', fn ($q) => $q->whereRaw("CONCAT(mobile_app_users.first_name, ' ', mobile_app_users.last_name) LIKE ?", ["%$search%"]))
            ->orWhereHas('salesPartner', fn ($q) => $q->where("company_name", "LIKE", "%$search%"));
    }

    /**
     * @param Illuminate\Database\Query\Builder $q
     * @param String $IDs comma separated
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeMobileAppUserIn($q, $IDs)
    {

        if ($IDs) {
            $IDs = explode(',', $IDs);
            return $q->whereHas('mobileAppUser', fn ($q) => $q->whereIn("mobile_app_users.id", $IDs));
        }
        return $q;
    }

    /**
     * @param Illuminate\Database\Query\Builder $q
     * @param String $IDs comma separated
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeMobileAppUserWithCondition($q, $IDs)
    {

        if ($IDs == 'select_all') {
            return $q;
        }

        if ($IDs == 'active') {
            return $q->whereHas('mobileAppUser', fn ($q) => $q->where("status", 'active'));
        }

        if ($IDs == 'inactive') {
            return $q->whereHas('mobileAppUser', fn ($q) => $q->where("status", 'inactive'));
        }

        if ($IDs) {
            $IDs = explode(',', $IDs);
            return $q->whereHas('mobileAppUser', fn ($q) => $q->whereIn("mobile_app_users.id", $IDs));
        }
        return $q;
    }

    /**
     * @param Illuminate\Database\Query\Builder $q
     * @param  String $IDs comma separated
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeSalesPartnerIn($q, $IDs)
    {
        if ($IDs) {
            $IDs = explode(',', $IDs);
            return $q->whereHas('salesPartner', fn ($q) => $q->whereIn("sales_partners.id", $IDs));
        }
        return $q;
    }

    /**
     * @param Illuminate\Database\Query\Builder $q
     * @param  String $IDs comma separated
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeConsultantIn($q, $IDs)
    {
        if ($IDs) {
            $IDs = explode(',', $IDs);
            return $q->whereHas('salesPartner', fn ($q) => $q->whereIn('sales_partners.consultant_id', $IDs));
        }
        return $q;
    }

    /**
     * Sort By Columns and relations
     * @param Illuminate\Database\Query\Builder $q
     * @param String $column
     * @param String $direction ASC, DESC
     */
    public function scopeSortByColumns($q, $column = 'check_in_time', $direction = 'DESC')
    {
        $sortable_columns = ['mobile_app_user_id', 'sales_partner_id', 'check_in_time', 'check_out_time', 'turnover', 'coin', 'posting_coins', 'total_coins'];
        $available_direction = ['ASC', "DESC"];
        if (!in_array($direction, $available_direction) || !in_array($column, $sortable_columns)) {
            return $q;
        }

        if ($column == 'mobile_app_user_id') {
            return $q->orderBy(MobileAppUser::selectRaw("CONCAT(mobile_app_users.first_name, ' ', mobile_app_users.last_name)")->whereColumn('mobile_app_users.id', 'store_visits.mobile_app_user_id'), $direction);
        }

        if ($column == 'sales_partner_id') {
            // dd($q->orderBy(SalesPartner::select("company_name")->whereColumn('sales_partners.id', 'store_visits.sales_partner_id'), $direction)->toSql());
            return $q->orderBy(SalesPartner::select("company_name")->whereColumn('sales_partners.id', 'store_visits.sales_partner_id'), $direction);
        }
        return $q->orderBy($column, $direction);
    }

    public function scopeCheckedInOrOutBetween($q, $startDate = null, $endDate = null)
    {
        if ($startDate && $endDate) {
            return $q->where(function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::parse($startDate)->format('Y-m-d');
                $endDate = Carbon::parse($endDate)->format('Y-m-d');
                $query->where(fn ($q) => $q->whereRaw("date(check_in_time) >=  date('$startDate')")->whereRaw("date(check_in_time) <= date('$endDate')"))
                    ->orWhere(fn ($q) => $q->whereRaw("date(check_out_time) >=  date('$startDate')")->whereRaw("date(check_out_time) <= date('$endDate')"));
            });
        }
        return $q;
    }

    public function scopeCreatedAt($q, $startDate = null, $endDate = null)
    {
        if ($startDate && $endDate) {
            return $q->whereBetween('created_at', [$startDate, $endDate]);
        }
        return $q;
    }

    public function scopeCreatedAtBetween($q, $start_date, $end_date, $date_selection)
    {
        if ($date_selection == 'last_week') {
            $start_date = Carbon::now()->subWeek()->startOfWeek()->toDate();
            $end_date = Carbon::now()->subWeek()->endOfWeek()->toDate();

            return $q->whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date);
        }
        if ($date_selection == 'last_month') {
            $start_date = Carbon::now()->subMonth()->startOfMonth()->toDate();
            $end_date = Carbon::now()->subMonth()->endOfMonth()->toDate();

            return $q->whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date);
        }

        if (!$start_date || !$end_date) {
            return $q->whereDate('created_at', '>=', Carbon::now())->whereDate('created_at', '<=', Carbon::now());
        }
        return $q->whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date);
    }

    //getter

    public function checkOutPrefixId(): Attribute
    {
        return Attribute::make(get: function () {
            if (!$this->check_out_time)
                return null;

            return "CO" . $this->prefix_id;
        });
    }

    public function checkInPrefixId(): Attribute
    {
        return Attribute::make(get: function () {
            return "CI" . $this->prefix_id;
        });
    }

    public function getReceiptUrlAttribute()
    {
        if (is_null($this->receipt)) {
            return null;
        }

        if (preg_match("@http@", $this->receipt)) {
            return $this->receipt;
        }

        if(config('filesystems.default')  == 'exoscale' ||  env('FILESYSTEM_DISK') == 'exoscale' ){
            return Storage::disk(env('FILESYSTEM_DISK'))->publicUrl($this->receipt);
        }

        return Storage::url($this->receipt);
    }

    //observers
    protected static function booted()
    {
        static::creating(function ($store_visit) {
            //set prefix id
            $store_visit->prefix_id = self::getNextPrefixID();
        });
    }

    public static function getNextPrefixID()
    {
        $prefix_id = self::orderBy('id', 'desc')->first()?->prefix_id;
        $prefix_id = ($prefix_id ?? 0) + 1;
        return sprintf("%05d",  $prefix_id);
    }


    public function scopeTimeBetween($query, string $column = 'created_at')
    {
        $start_date = Carbon::parse(request('start_date'))->startOfDay();
        $end_date = Carbon::parse(request('end_date'))->endOfDay();

        $query->where($column, '>=', $start_date)
            ->where($column, '<=', $end_date);
    }
}
