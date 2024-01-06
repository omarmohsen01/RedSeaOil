<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Interfaces\Dashboard\StructureServiceInterface;
use App\Http\Controllers\Interfaces\Dashboard\TestStructureServiceInterface;
use App\Http\Requests\StructureDescRequest;
use App\Models\Option;
use App\Models\Structure;
use App\Models\Test;
use App\Models\TestStructure;
use Illuminate\Http\Request;
use Throwable;

class TestStructureController extends Controller
{
    protected $structService;
    public function __construct(TestStructureServiceInterface $structService)
    {
        $this->structService=$structService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $test=$request->test;
        return view('dashboard.structure.create',compact('test'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StructureDescRequest $request)
    {
        $this->structService->structStore($request);
        return redirect()->route('tests.index')
            ->with('success','Structure Created Successfully');
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
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Test $test)
    {
        abort(404);
    }
    public function deleteStruct(string $id)
    {
        $structure=TestStructure::find($id);
        return view('dashboard.structure.delete',compact('structure'));
    }
}
