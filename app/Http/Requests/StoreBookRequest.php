<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $dateString = $this->input('publiched_at');

        $formattedDate = date('Y-m-d', strtotime($dateString));

        $this->merge([
            'publiched_at' => $formattedDate,
        ]);
    }
 

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    { 
        return [
            'title' => 'required|string|min:3|max:100|unique:books,title',
            'author' => 'required|string|min:3|max:100',
            'description' => 'required|string|max:500',
            'category' => 'required|string|max:255',
            'publiched_at' => 'required|date|after_or_equal:1400-01-01|before_or_equal:2024-12-31',


        ];
    }


    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'يرجى التأكد من المدخلات',
            'errors' => $validator->errors(),

        ]));
    }

    protected function passedValidation()
    {
        //
    }

    public function attributes()
    {
        return [
            'title' => 'اسم الكتاب',
            'author' => 'اسم الكاتب',
            'description' => 'توصيف الكتاب',
            'category' => 'تصنيف الكتاب',
            'publiched_at' => 'تاريخ النشر',
        ];
    }

    public function messages()
    {
        return [
            'required' => ' :attribute مطلوب',
            'min' => ':attribute يجب أن يكون طوله على الأقل :min أحرف.',
            'max' => ':attribute يجب ألا يزيد طوله عن :max أحرف.',
            'unique' => ':attribute موجود بالفعل. يرجى اختيار اسم آخر.',
            'date' => ':attribute يجب أن يكون تاريخًا صالحًا.',
            'after_or_equal' => ':attribute يجب أن يكون بعد أو يساوي :date.',
        ];
    }
}
