<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyUsersCount extends Model
{
    use HasFactory;

    protected $table = 'daily_users_count';
    protected $fillable = ['mobile_app_users_count', 'sales_partner_id', 'consultant_id', 'type', 'created_at'];



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

    public function scopeSalesPartnersIn($q, $sales_partners)
    {
        if (!$sales_partners) {
            return $q;
        }

        return $q->whereIn('sales_partner_id', explode(',', $sales_partners));
    }

    public function scopeConsultantsIn($q, $consultants)
    {
        if (!$consultants) {
            return $q;
        }

        return $q->whereIn('consultant_id', explode(',', $consultants));
    }
}
