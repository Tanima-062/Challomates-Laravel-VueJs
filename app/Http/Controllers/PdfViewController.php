<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PdfViewController extends Controller
{


    /**
     * View privacy policy pdf file
     *
     * @return void
     */
    public function privacyPolicy()
    {
        return response()->file('pdfs/privacy_policy.pdf');
    }


    /**
     * View terms and conditions pdf file
     *
     * @return void
     */
    public function termsAndCondition()
    {
        return response()->file('pdfs/terms_and_conditions.pdf');
    }
    /**
     * View terms and conditions for participation sweepstakes pdf file
     *
     * @return void
     */
    public function termsAndConditionForParticipationSweepstakes()
    {
        return response()->file('pdfs/terms_and_conditions_of_participation_sweepstakes.pdf');
    }
}
