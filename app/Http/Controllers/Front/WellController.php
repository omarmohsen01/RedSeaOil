<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\Well;
use App\Models\Well_data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
class WellController extends Controller
{
    public $well;
    public $well_data;
    public function __construct(Well $well,Well_data $well_data)
    {
        $this->well=$well;
        $this->well_data=$well_data;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $well=$this->well->with('user')->get();
        if(!$well){
            return response()->json(['message' => 'Well Not Found'], 404);
        }
        return response()->json($well,200);
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
        $well=$this->well->with('user')->findOrFail($id);
        if(!$well){
            return response()->json(['message'=> 'Well Not Found'],404);
        }
        return response()->json($well,200);
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

    public function userWell()
    {
        $user=Auth::guard('sanctum')->user();
        $well=$this->well->where('user_id',$user->id)->get();
        return $well;
    }

    public function generatePDF(string $id)
    {
        $options = Option::all();
        $well = $this->well->with('Structure_descriptions')->find($id);

        $data = [
            'well' => $well,
            'structureDescriptions' => $well->Structure_descriptions,
            'options' => $options
        ];

        $htmlContent = View::make('dashboard.well.well_pdf', $data)->render();

        // You can store the HTML content as a temporary file or in a database
        // For simplicity, we'll store it in a temporary file in the public directory
        $fileName = 'temp_pdf_' . uniqid() . '.html';
        $filePath = public_path($fileName);
        file_put_contents($filePath, $htmlContent);

        // Return JSON response with HTML content and URL
        return response()->json([
            // 'html' => $htmlContent,
            'url' => url($fileName), // This is the URL to access the temporary HTML file
        ]);
    }
}
