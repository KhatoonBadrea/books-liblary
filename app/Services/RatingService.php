<?php

namespace App\Services;

use App\Models\Rating;
use App\Models\Borrow_record;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\ApiResponseTrait;

class RatingService
{
    use ApiResponseTrait;

    public function getUserRatings(int $userId)
    {
        return Rating::where('user_id', $userId)->get();
    }






    /**
     * Create a new rating.
     *
     * @param array $data
     * @return Rating|null
     */
    public function createRating(array $data): ?Rating
    {
        // الحصول على المستخدم المصدق عليه
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            throw new \Exception('User not authenticated.');
        }

        // تحقق من أن المستخدم قد استعار الكتاب
        $borrowedRecord = Borrow_record::where('user_id', $user->id)
            ->where('book_id', $data['book_id'])
            ->whereNotNull('borrowed_at')
            ->exists();

        if (!$borrowedRecord) {
            throw new \Exception('You can only rate books you have borrowed.');
        }

        // إنشاء التقييم
        return Rating::create([
            'user_id' => $user->id,
            'book_id' => $data['book_id'],
            'rating' => $data['rating'],
            'review' => $data['review'],
        ]);
    }

    public function updateRating(Rating $rating, array $data)
    {
        // dd($rating->user_id);
        $user = JWTAuth::parseToken()->authenticate();
        // dd($data['book_id'] == $rating->book_id);
        // dd($user->id == $rating->user_id);
        // dd($user->id == $rating->user_id && $data['book_id'] == $rating->book_id);
        if ($user->id == $rating->user_id && $data['book_id'] == $rating->book_id) {
            // $record = Rating::where('book_id', $data['book_id'])->where('user_id', $user->id)->get();

            // if ($record->isNotEmpty()) {
            // dd($rating);

            $rating->update([
                'book_id' => $data['book_id'] ?? $rating->book_id,
                'rating' => $data['rating'] ?? $rating->rating,
                'review' => $data['review'] ?? $rating->review,

            ]);

            return $rating;
            // } else {
            //     return "you";
            // }
        } else {
            return response()->json('error');
        }
        // $user===$rating->user_id?return $rating->user_id:return 0;

        // $borrowRecord = Borrow_record::where('user_id', $user->id)->get();

    }

    public function deleteRating(Rating $rating)
    {
        $user = JWTAuth::parseToken()->authenticate();

        // Find the borrow record associated with this rating
        $borrowRecord = Rating::where('user_id', $user->id)
            ->get();
        try {

            // Allow only if the borrow record exists and belongs to the authenticated user
            if ($borrowRecord->isNotEmpty()) {

                $rating->delete();
                return true;
            } else {
                throw new \Exception('you can not delete this ratin');
            }
        } catch (\Exception $e) {
            Log::error('Error in RatingService@deleteRating: ' . $e->getMessage());
            return $this->errorResponse('An error occurred: ' . 'there is an error in the server', 500);
        }
    }
}
