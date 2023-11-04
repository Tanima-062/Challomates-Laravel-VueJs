<?php

namespace App\Http\Requests\SweepStakes;

use App\Models\Sweepstake;
use App\Rules\InArrayWhen;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class SweepStakesCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:30'],
            'type' => ['required', 'in:sweepstake,jackpot'],
            'runtime_from' => ['required', 'date', 'after:now', function($attr, $value, $fail) {
                if ( Sweepstake::where('runtime_to', '>=', $value)->count() ) {
                    $arr = array(
                        'runtime_from.exist' => array(
                            'title' => 'Gewinnspiel Erstellung nicht möglich',
                            'message' => 'Für die eingegebene Gewinnspiel Laufzeit besteht bereits ein <br>oder mehrere Gewinnspiele mit der Art "Gewinnspiel" für ein,<br>mehrere oder alle Tage des eingegebenen Zeitraums.<br>Bitte prüfen Sie die bestehenden Gewinnspiele und erfassen <br>Sie eine Gewinnspiel Laufzeit die keine Daten enthält, welche<br> bereits Teil von anderen vorhandenen Gewinnspielen mit <br>der Art "Gewinnspiel" sind.'
                        ),
                    );
                    $fail($arr);
                }
            }],
            'runtime_to' => ['required', 'date', 'after:runtime_from'],
            'raffle_time' => ['required', 'date', 'after:runtime_to'],
            'price' => ['required_if:type,sweepstake', 'string', 'max:40'],
            "number_of_winners" => ['required_if:type,in:sweepstake', 'nullable', 'integer'],
            "total_sweepstake_number_position" =>  "required|integer",
            "winning_number_position_from" => "required|lte:winning_number_position_to",
            "winning_number_position_to" => "required|gte:winning_number_position_from",
            "number_of_coins_for_participation" => "required|regex:/^\d+(\.\d{1,2})?$/i",
            "published" => 'required|boolean'
        ];
    }

    /**
     * @return array
     */
    public function messages() {
        return [
            'runtime_from.date' => 'Das Gewinnspiel kann nicht publiziert werden, da der <br>Start-Zeitpunkt (Laufzeit “Von”) in der Vergangenheit liegt. <br>Bitte erfassen Sie ein Datum, welches aktuell in der Zukunft liegt, um dieses Gewinnspiel publizieren zu können.',
            'runtime_from.after' => 'Das Gewinnspiel kann nicht publiziert werden, da der <br>Start-Zeitpunkt (Laufzeit “Von”) in der Vergangenheit liegt. <br>Bitte erfassen Sie ein Datum, welches aktuell in der Zukunft liegt, um dieses Gewinnspiel publizieren zu können.',
            'raffle_time.date' => 'Für die eingegebene Gewinnspiel Laufzeit besteht bereits ein oder mehrere Gewinnspiele mit der Art “Gewinnspiel” für ein, mehrere oder alle Tage des eingegebenen Zeitraums.
Bitte prüfen Sie die bestehenden Gewinnspiele und erfassen
Sie eine Gewinnspiel Laufzeit die keine Daten enthält, welche bereits Teil von anderen vorhandenen Gewinnspielen mit
der Art “Gewinnspiel” sind.',
            'raffle_time.after' => 'Für die eingegebene Gewinnspiel Laufzeit besteht bereits ein oder mehrere Gewinnspiele mit der Art “Gewinnspiel” für ein, mehrere oder alle Tage des eingegebenen Zeitraums.
Bitte prüfen Sie die bestehenden Gewinnspiele und erfassen
Sie eine Gewinnspiel Laufzeit die keine Daten enthält, welche bereits Teil von anderen vorhandenen Gewinnspielen mit
der Art “Gewinnspiel” sind.'
        ];
    }
}
