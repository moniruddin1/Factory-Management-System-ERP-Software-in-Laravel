<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class UnitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // যেহেতু আমরা মিডলওয়্যার ব্যবহার করছি, তাই এখানে true করে দিন
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
            'name' => 'required|string|max:255|unique:units,name,' . $this->route('unit'),
            'short_name' => 'required|string|max:50',
        ];
    }

    /**
     * Custom messages for validation
     */
    public function messages(): array
    {
        return [
            'name.required' => 'ইউনিটের নাম দেওয়া বাধ্যতামূলক।',
            'name.unique' => 'এই নামের ইউনিট অলরেডি আছে।',
            'short_name.required' => 'ইউনিটের সংক্ষিপ্ত নাম (যেমন: KG) দিন।',
        ];
    }
}
