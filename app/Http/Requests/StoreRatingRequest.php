<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Borrow_record;


class StoreRatingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // $borrowRecord = $this->route('borrow_record');

        // // تحقق من أن المستخدم الحالي هو من قام باستعارة الكتاب
        // return $borrowRecord && $this->user()->id === $borrowRecord->user_id;
        $user = JWTAuth::parseToken()->authenticate();
        $borrowRecord = Borrow_record::where('user_id', $user->id)->get();
        if ($borrowRecord->isNotEmpty()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'book_id' => 'required|exists:books,id',
            'rating' => 'required|integer|between:1,5',
            'review' => 'nullable|string|max:255',
        ];
    }
}
