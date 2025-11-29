<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    protected function prepareForValidation()
    {
        $mapping = [

        'i' => 'id',

        'n' => 'name',

        'd' => 'description',
        
        's' => 'status',
        
        'q' => 'quantity',
        
        'im' => 'image',
        
        'se' => 'seller',

        ];
        $data = [];

        foreach($this->all() as $key => $value)
        {
            $newKey=$mapping[$key] ?? $key;
            $data[$newKey] = $value;
        }
        $this->merge($data);
    }
    public function rules(): array
    {
        return [
            'name' => 'required',
            'description' => 'required',
            'status' => 'required',
            'quantity' => 'required',
            'image' => 'nullable',
            'seller' => 'required'
        ];
    }
}
