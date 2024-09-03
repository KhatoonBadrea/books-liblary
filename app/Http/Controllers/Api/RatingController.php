<?php

namespace App\Http\Controllers\Api;

use App\Models\Rating;
use Illuminate\Http\Request;
use App\Models\Borrow_record;
use App\Services\RatingService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Requests\StoreRatingRequest;
use App\Http\Requests\UpdateRatingRequest;
use App\Http\Resources\RatingResource;

class RatingController extends Controller
{
    use ApiResponseTrait;

    protected $ratingService;

    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    }

    /** 
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // الحصول على المستخدم المصدق عليه
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['message' => 'User not authenticated.'], 401);
            }

            // استخدام RatingService لجلب تقييمات المستخدم مع تحميل العلاقات
            $ratings = $this->ratingService->getUserRatings($user->id)->load(['user', 'book']);
            $ratings = RatingResource::collection($ratings);
            return $this->successResponse('successefuly added the book', $ratings, 200);
        } catch (\Exception $e) {
            Log::error('Error in RatingController@index: ' . $e->getMessage());
            return $this->errorResponse('An error occurred: ' . 'There is an error on the server', [], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRatingRequest $request)
    {
        try {
            $rating = $this->ratingService->createRating($request->validated());

            return response()->json(['message' => 'Rating added successfully.', 'data' => $rating], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRatingRequest $request,  Rating $rating)
    {

        // dd($rating);
        $updatedRating = $this->ratingService->updateRating($rating, $request->only(['book_id', 'rating', 'review']));

        return response()->json(['message' => 'Rating updated successfully.', 'data' => $updatedRating], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rating $rating)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if ($rating->user_id !== $user->id) {
                return $this->errorResponse('Unauthorized action.', 403);
            }
            $this->ratingService->deleteRating($rating);

            return $this->successResponse('successefuly deleted the rating', $rating, 201);
        } catch (\Exception $e) {
            Log::error('Error in RatingController@destroy: ' . $e->getMessage());
            return $this->errorResponse('An error occurred: ' . 'there is an error in the server', 500);
        }
    }
}
