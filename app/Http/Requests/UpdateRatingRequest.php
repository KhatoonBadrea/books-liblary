<?php

namespace App\Http\Requests;

use App\Models\Borrow_record;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRatingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Get the authenticated user
        $user = JWTAuth::parseToken()->authenticate();

        // Find the borrow record associated with this rating
        $borrowRecord = Borrow_record::where('user_id', $user->id)
            
            ->first();

        // Allow only if the borrow record exists and belongs to the authenticated user
        return $borrowRecord !== null;
        // return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'book_id' => 'nullable|exists:books,id',
            'rating' => 'nullable|integer|between:1,5',
            'review' => 'nullable|string|max:255',
        ];
    }
}
