<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateBorrow_recordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->role !== 'admin') {
            return false;
        } else {

            return true;
        }
    }


    public function prepareForValidation()
    {

        $this->merge([
            'borrowed_at' => $this->input('borrowed_at') ?? Carbon::now()->format('Y-m-d'),
            'due_date' => $this->input('due_date') ?? Carbon::now()->addDays(14)->format('Y-m-d'),
            'returned_at' => $this->input('returned_at') ?? Carbon::now()->addDays(14)->format('Y-m-d'),
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
            'user_id' => 'integer|exists:users,id',
            'book_id' => 'nullable|integer|exists:books,id',
            'borrowed_at' => 'nullable|date',
            'due_date' => 'nullable|date|after:borrowed_at',
            'returned_at' => 'nullable|date|after:borrowed_at',
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

    public function attributes()
    {
        return [
            'user_id' => ' المستخدم',
            'book_id' => ' الكتاب',
            'borrowed_at' => 'تاريخ الاستعارة',
            'due_date' => 'تاريخ الاعادة',
            'returned_at' => 'تاريخ الارجاع'
        ];
    }

    public function messages()
    {
        return [

            'date' => ':attribute يجب أن يكون تاريخاً صالحاً.',
            'exists' => ':attribute غير موجود',
            'after' => 'يجب ان يكون :attributeبعد تاريخ الاستعارة',
        ];
    }
}
