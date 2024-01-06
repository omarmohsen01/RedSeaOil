<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestReqRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'test_well_id'=>'exists:test_wells,id',
            'description'=>'required|string|max:1025'
        ];
    }
}
