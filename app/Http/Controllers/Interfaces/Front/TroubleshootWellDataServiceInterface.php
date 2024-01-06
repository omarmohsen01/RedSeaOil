<?php

namespace App\Http\Controllers\Interfaces\Front;

interface TroubleshootWellDataServiceInterface
{
    public function publishNewTroubleshootWell($request,$published);
    public function publishOldTroubleshootWell($request,$published);
    public function requestToEdit($request,$published,$wellRequest);
}
