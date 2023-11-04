<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Participation extends Model
{
    use HasFactory;

    protected $fillable = array(
        'winning_number', 'mobile_app_user_id', 'sweepstake_id', 'participation_id'
    );

    protected $appends = ['prefix_id'];


    //relations
    public function mobileAppUser()
    {
        return $this->belongsTo(MobileAppUser::class, 'mobile_app_user_id');
    }

    public function sweepstake()
    {
        return $this->belongsTo(Sweepstake::class, 'sweepstake_id');
    }

    //scopes

    /**
     * Search by sweepstake name, mobile app user user, first or last name
     * @param Illuminate\Database\Query\Builder $q
     * @param String $search
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeSearch($q, $search)
    {
        return $q->whereHas('mobileAppUser', fn ($q) => $q->whereRaw("CONCAT(mobile_app_users.first_name, ' ', mobile_app_users.last_name) LIKE ?", ["%$search%"]))
            ->orWhereHas('sweepstake', fn ($q) => $q->where("name", "LIKE", "%$search%"));
    }

    /**
     * Sort By Columns and relations
     * @param Illuminate\Database\Query\Builder $q
     * @param String $column
     * @param String $direction ASC, DESC
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeSortByColumns($q, $column = 'created_at', $direction = 'DESC')
    {
        $sortable_columns = ['created_at', 'mobile_app_user', 'sweepstake', 'total_coins', 'description'];
        $available_direction = ['ASC', "DESC"];

        if (!in_array($direction, $available_direction) || !in_array($column, $sortable_columns)) {
            return $q;
        }

        if ($column == 'mobile_app_user') {
            return $q->orderBy(MobileAppUser::selectRaw("CONCAT(mobile_app_users.first_name, ' ', mobile_app_users.last_name)")->whereColumn('mobile_app_users.id', 'participations.mobile_app_user_id'), $direction);
        }

        if ($column == 'sweepstake') {
            return $q->orderBy(Sweepstake::select("name")->whereColumn('sweepstakes.id', 'participations.sweepstake_id'), $direction);
        }

        if($column == 'description') {
            return $q->orderBy(Sweepstake::selectRaw("CASE WHEN  sweepstakes.type = 'sweepstake' THEN 'Jackpot- und Zusatz-Gewinnspiel Teilnahme' ELSE 'Jackpot-Gewinnspiel-Teilnahme' END AS description")->whereColumn('sweepstakes.id', 'participations.sweepstake_id'), $direction);
        }

        return $q->orderBy($column, $direction);
    }

    /**
     * @param Illuminate\Database\Query\Builder $q
     * @param String $IDs comma separated
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeMobileAppUserIs($q, $id)
    {
        if ($id) {
            return $q->whereHas('mobileAppUser', fn ($q) => $q->where("mobile_app_users.id", $id));
        }
        return $q;
    }

    /**
     * @param Illuminate\Database\Query\Builder $q
     * @param  String $IDs comma separated
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeSweepstakeIn($q, $IDs)
    {
        if ($IDs) {
            $IDs = explode(',', $IDs);
            return $q->whereHas('sweepstake', fn ($q) => $q->whereIn("sweepstakes.id", $IDs));
        }
        return $q;
    }

    public function scopeParticipatedAt($q, $startDate = null, $endDate = null)
    {
        if ($startDate && $endDate) {
            return $q->whereBetween(DB::raw('date_format(participations.created_at, \'%Y-%m-%d\')'), [$startDate, $endDate]);
        }
        return $q;
    }

    //getter

    public function prefixId(): Attribute
    {
        return Attribute::make(get: fn ($q) => sprintf("ID%05d", $this->id));
    }
}
