<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = ["prefix_id", "name", "contract_term_from", "contract_term_to", "sales_partner_id", "package_id", "marketing_fee_id", "status"];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_NEW = 'new';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_EXPIRED = 'expired';


    // Side Effects

    protected static function booted()
    {
        static::creating(function ($sales_partner) {
            $sales_partner->prefix_id = static::getNextPrefixId();
        });
    }

    public function salesPartner(): BelongsTo
    {
        return $this->belongsTo(SalesPartner::class, 'sales_partner_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function marketingFee(): BelongsTo
    {
        return $this->belongsTo(MarketingFee::class, 'marketing_fee_id');
    }

    //local scopes
    public function scopeSearch($q, $search)
    {
        if (!is_null($search)) {
            return $q->where(function ($query) use ($search) {
                $query
                    ->where('prefix_id', "LIKE", "%$search%")
                    ->orWhere('name', "LIKE", "%$search%")
                    ->orWhereHas('salesPartner', fn ($q) => $q->where('company_name', "LIKE", "%$search%"));
            });
        }
    }

    public function scopeCreatedAtBetween($q, $start_date, $end_date)
    {
        if (!is_null($start_date) && !is_null($end_date)) {
            return $q->whereDate('created_at', '>=', $start_date)->whereDate('created_at', "<=", $end_date);
        }
    }

    public function scopeContractTermBetween($query, $start_date, $end_date)
    {
        if (!is_null($start_date) && !is_null($end_date)) {
            $start_date = Carbon::parse($start_date);
            $end_date = Carbon::parse($end_date);

            return $query->where(fn ($q) => $q->whereBetween('contract_term_from', [$start_date, $end_date])->orWhereBetween('contract_term_to', [$start_date, $end_date]));
        }
    }

    public function scopeSalesPartnerIn($q, $sales_partner)
    {
        if (is_null($sales_partner))
            return $q;

        return $q->whereHas('salesPartner', fn ($q) => $q->whereIn('id', explode(',', $sales_partner)));
    }

    public function scopePackageIn($q, $sales_partner)
    {
        if (is_null($sales_partner))
            return $q;

        return $q->whereHas('package', fn ($q) => $q->whereIn('id', explode(',', $sales_partner)));
    }

    public function scopeMarketingFeeIn($q, $sales_partner)
    {
        if (is_null($sales_partner))
            return $q;

        return $q->whereHas('marketingFee', fn ($q) => $q->whereIn('id', explode(',', $sales_partner)));
    }

    public function scopeStatusIs($q, $status)
    {
        if (!is_null($status)) {
            return $q->having('current_status', $status);
        }
    }

    public function scopeSortByColumns($q, $column = 'created_at', $direction = 'DESC')
    {
        $sortable_columns = ['created_at', 'name', 'status', 'sales_partner',  'contract_term_period'];
        $available_direction = ['ASC', "DESC"];

        if (!in_array($direction, $available_direction) || !in_array($column, $sortable_columns)) {
            return $q;
        }

        if ($column == 'sales_partner') {
            return $q->orderBy(SalesPartner::select('company_name')->whereColumn('sales_partners.id', 'contracts.sales_partner_id'), $direction);
        }

        if ($column == 'contract_term_period') {
            return $q->orderBy('contract_term_from', $direction);
        }

        if ($column == 'status') {
            return $q->orderBy('current_status', $direction);
        }

        return $q->orderBy($column, $direction);
    }

    public static function getNextPrefixId()
    {
        $last_id = (self::orderBy('id', 'desc')->first()?->id ?? 0) + 1;
        return sprintf("V%05d",  $last_id);
    }

    public function currentStatus(): Attribute
    {
        return Attribute::make(get: function ($value) {
            $now = Carbon::now()->format('Y-m-d');

            if ($this->status == Contract::STATUS_CANCELED) {
                return Contract::STATUS_CANCELED;
            }
            if ($now >= $this->contract_term_from && $this->contract_term_to >= $now)
                return Contract::STATUS_ACTIVE;

            if ($now < $this->contract_term_from)
                return Contract::STATUS_NEW;

            if ($now > $this->contract_term_to)
                return Contract::STATUS_EXPIRED;

            return $this->status;
        });
    }
}
