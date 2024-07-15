<?php

namespace App\Http\Requests;

use App\Traits\GlobalTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class LoginRequest extends FormRequest
{
    use GlobalTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'username' => 'required',
            'password' => 'required'
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'username.required' => 'Username harus diisi.',
            'password.required' => 'Password harus diisi.'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $messages = $this->formatRequestValidation($validator->errors()->getMessages());
        throw new HttpResponseException(response()->error($messages, 400));
    }
}
