<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\Troubleshoot;
use App\Models\TroubleshootWell;
use App\Models\Well;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;

class TroubleshootWellController extends Controller
{
    public $well;
    public function __construct(TroubleshootWell $well)
    {
        $this->well = $well;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $wells=$this->well->with('user')->filter($request->query())->paginate(5);
        return view('dashboard.troubleshootwell.index',compact('wells'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function generatePDF($id)
    {
        $options=Troubleshoot::all();
        $well = $this->well->with('Structure_descriptions')->find($id);


        $data=[
            'well'=>$well,
            'structureDescriptions'=>$well->Structure_descriptions,
            'options'=>$options
        ];

        return view('dashboard.well.well_pdf')->with($data);

        // $pdf = Pdf::loadView('dashboard.well.well_pdf', $data);
        // return $pdf->download('RedSeaOil.pdf');

        // Queue::push(function ($job) use ($data) {
        //     $pdf = Pdf::loadView('dashboard.well.well_pdf', $data);
        //     $pdf->download('RedSeaOil.pdf');
        //     $job->delete();
        // });

    }
}
