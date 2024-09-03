<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Borrow_record;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReturnRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Services\BorrowRecordService;
use App\Http\Requests\StoreBorrow_recordRequest;
use App\Http\Requests\UpdateBorrow_recordRequest;

class BorrowRecordController extends Controller
{
    use ApiResponseTrait;

    protected $BorrowRecordService;


    /**
     * constractur to inject Movie Service Class
     * @param BorrowRecordService $BorrowRecordService
     */
    public function __construct(BorrowRecordService $BorrowRecordService)
    {
        $this->BorrowRecordService = $BorrowRecordService;
    }




    /** 
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $records = $this->BorrowRecordService->getAllRecord();
        return $this->successResponse('This is all Borrow Records', $records, 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreBorrow_recordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreBorrow_recordRequest $request)
    {
        $validationdata = $request->validated();
        // dd($validationdata);
        $borrowRecord = $this->BorrowRecordService->create_Borrow_Record($validationdata, $validationdata['book_id']);
        return $this->successResponse($borrowRecord, 'Borrow record created successfully.', 201);
    }



    /**
     * Display the specified resource.
     */
    public function show(Borrow_record $borrow_record)
    {
        //
    }


    // التحقق من صلاحيات الأدمن داخل طريقة update
    public function update(UpdateBorrow_recordRequest $request, Borrow_record $borrow_record)
    {


        $validatedRequest = $request->validated();
        $updatedBorrowRecord = $this->BorrowRecordService->updateBorrowRecord($borrow_record, $validatedRequest);
        return $this->successResponse($updatedBorrowRecord, 'Borrow record updated successfully.', 200);
    }




    /**
     * Update the specified resource in storage.
     * @param UpdateBorrow_recordRequest $request
     * @param Borrow_record $borrow_record
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        // $user = User::where('email', 'admin@gmail.com')->first();
        // dd($user->roles); // تأكد من أن هناك دور "admin"
        try {
            $user = JWTAuth::parseToken()->authenticate();
            Log::info('Authenticated User: ', ['user' => $user]);
            $user = User::where('email', 'admin@gmail.com')->first();
            dd($user->roles); // تأكد من أن هناك دور "admin"
            if ($user && $user->role == 'admin') {
                $this->BorrowRecordService->deleteBorrowRecord($id);
                return $this->successResponse([], 'Record deleted successfully.', 200);
            } else {


                return $this->Unauthorized('You do not have permission to perform this action.');
            }
        } catch (\Exception $e) {
            Log::error('Error in destroy: ' . $e->getMessage());
            return $this->errorResponse('An error occurred: ' . $e->getMessage(), 500);
        }
    }
}
