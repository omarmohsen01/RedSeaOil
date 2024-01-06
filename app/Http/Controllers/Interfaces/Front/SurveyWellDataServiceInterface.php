<?php

namespace App\Http\Controllers\Interfaces\Front;

interface SurveyWellDataServiceInterface
{
    public function publishNewSurveyWell($request,$published);
    public function publishOldSurveyWell($request,$published);
    public function requestToEdit($request,$published,$wellRequest);
}
