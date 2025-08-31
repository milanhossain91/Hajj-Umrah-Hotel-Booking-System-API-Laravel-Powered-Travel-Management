<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
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

            'children' => 'nullable|array',
            'children.*.name' => 'required|string|max:255',
            'children.*.registration_number' => 'nullable|string|max:255',
            'children.*.thana' => 'nullable|string|max:255',
            'children.*.photo_path' => 'nullable|image|max:2048',

            'payments' => 'nullable|array',
            'payments.*.payment_method' => 'required|string|max:255',
            'payments.*.amount' => 'nullable|numeric',
            'payments.*.date' => 'nullable|date',

            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ];
    }
}
