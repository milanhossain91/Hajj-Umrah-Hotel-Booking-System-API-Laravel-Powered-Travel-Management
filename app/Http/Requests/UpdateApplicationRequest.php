<?php

namespace App\Http\Requests;

class UpdateApplicationRequest extends StoreApplicationRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'thana' => 'required|string|max:255',
            'village' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'photo' => 'nullable|image|max:2048',
            'children' => 'nullable|json',
            'children.*.name' => 'required|string|max:255',
            'children.*.registration_number' => 'nullable|string|max:255',
            'children.*.thana' => 'nullable|string|max:255',
            'children.*.photo' => 'nullable|image|max:2048',
            'payments' => 'nullable|json',
            'payments.*.payment_method' => 'required|string|max:255',
            'payments.*.amount' => 'nullable|numeric',
            'payments.*.date' => 'nullable|date',
            'email' => 'required|email',
            'password' => 'nullable|string|min:8|confirmed',
        ];
    }
}
