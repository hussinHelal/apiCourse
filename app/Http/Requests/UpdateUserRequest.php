<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User as usr;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $mapping = [
            'i' => 'id',
            'n' => 'name',
            'e' => 'email',
            'v' => 'verified',
            'a' => 'admin',
            'created' => 'created_at',
            'updated' => 'updated_at',
        ];

        $data=[];
        foreach($this->all() as $key => $value)
        {
            $newkey = $mapping[$key] ?? $key;
            $data[$newkey] = $value;
        }
        $this->merge($data);
    }
    public function rules(): array
    {
        $userId = $this->route('id');
        return [
            'email' => 'email|unique:users,email,' . $userId ,
            'password' => 'min:6|confirmed',
            'admin' => 'in:' . Usr::ADMIN_USER . ',' . Usr::REGULAR_USER ,
        ];
    }
}
