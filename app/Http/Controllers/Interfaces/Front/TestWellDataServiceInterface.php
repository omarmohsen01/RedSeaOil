<?php

namespace App\Http\Controllers\Interfaces\Front;

interface TestWellDataServiceInterface
{
    public function publishNewTestWell($request,$published);
    public function publishOldTestWell($request,$published);
    public function requestToEdit($request,$published,$wellRequest);
}
