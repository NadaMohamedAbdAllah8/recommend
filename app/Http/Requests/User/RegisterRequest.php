<?php

namespace Http\Requests\User;

class RegisterRequest extends FormRequest
{

    public function rules()
    {
        return [
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
        ];
    }

}
