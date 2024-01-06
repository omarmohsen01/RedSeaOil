<?php

namespace App\Http\Requests;

use App\Models\Structure_description;
use Illuminate\Foundation\Http\FormRequest;

class WellDataRequest extends FormRequest
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
        $rules= [
            'name'=>'required|string|max:255|unique:wells,name',
            'from'=>'required|date',
            'to'=>'required|date',
            'well_data'=>'required|array',
            'well_data.*.structure_description_id'=>'exists:structure_descriptions,id',
            'well_data.*.structure_id'=>'exists:structures,id'
        ];

        $wellDataInputs = $this->json('well_data');
        foreach ($wellDataInputs as $key => $wellDataInput) {
            if (isset($wellDataInput['structure_description_id'])) {
                $structure_desc = Structure_description::findOrFail($wellDataInput['structure_description_id']);
                if ($structure_desc->is_require == 'Required') {
                    $rules["well_data.{$key}.data"] = 'required';
                }
            }
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'The Well name is required.',
            'from.required' => 'The from field is required.',
            'from.date' => 'The from field must be a valid date.',
            'to.required' => 'The to field is required.',
            'to.date' => 'The to field must be a valid date.',
            'well_data.required' => 'The well data field is required.',
            'well_data.array' => 'The well data field must be an array.',
            'well_data.*.structure_description_id.exists' => 'The selected structure description is invalid.',
            'well_data.*.structure_id.exists' => 'The selected structure is invalid.',
            'well_data.*.data.required' => 'The data field is required.',
        ];
    }

}
