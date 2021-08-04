<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CardRegisterRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'title' => 'required:max:255',
            'description' => 'required',
        ];
    }
}
