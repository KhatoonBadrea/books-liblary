<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Events\BookReturned;
use App\Models\Borrow_record;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Resources\Borrow_recordResource;

class BorrowRecordService
{
    use ApiResponseTrait;
    /**
     * fetch the borrow record
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllRecord()
    {
        try {
            $records = Borrow_record::with(['user', 'book'])->get();
            if ($records->isNotEmpty()) {
                return Borrow_recordResource::collection($records);
            } else {
                return $this->notFound('There are no records here');
            }
        } catch (\Exception $e) {
            Log::error('Error in BorrowRecordController@getAllRecord: ' . $e->getMessage());
            return $this->errorResponse('An error occurred: ' . 'there is an error in the server', [], 500);
        }
    }

    /**
     * create a new borrow record , the user can borrow th book
     * @param array $data
     * @param $bookId
     * @return Borrow_Record $record
     */

    public function create_Borrow_Record(array $data, $bookId)
    {
        try {
            // dd($bookId);
            // get the user (jwt auth)
            $user = JWTAuth::parseToken()->authenticate();



            if (!$user) {
            }

            //check if the book is borrow 
            $isBorrowed = Borrow_record::where('book_id', $bookId)
                ->where(function ($query) {
                    $query->where('due_date', '<=', Carbon::today())
                        ->orWhereNull('due_date');
                })
                ->get();
            // dd($isBorrowed);

            if ($isBorrowed->isNotEmpty()) {
                return $this->errorResponse('This book is currently borrowed and not yet returned.', 400);
            } else {

                // create the borrow_record
                $record = Borrow_record::create([
                    'user_id' => $user->id, // fetch the id for the auth user
                    'book_id' => $bookId,
                    'borrowed_at' => Carbon::now(),
                    'due_date' => null,
                    'returned_at' => Carbon::now()->addDays(14),
                ]);


                if (!$record) {
                    throw new \Exception('Failed to create the record.');
                }

                return $record;
            }
        } catch (\Exception $e) {
            Log::error('Error in BorrowRecordService@create_Borrow_Record: ' . $e->getMessage());
            return $this->errorResponse('An error occurred: ' . 'there is an error in the server', 500);
        }
    }

    /**
     * return the book that the user borrow it
     * @param array $data
     */
    public function returnBook(array $data)
    {
        try {
            //the id of the book that the user input it in the field
            $bookId = $data['book_id'];

            //the auth user 
            $user = JWTAuth::parseToken()->authenticate();
            /**
             *  جلب سجلات الكتب 
             * المستعارة بحيث يكون اليوزر الذي يريد اعادة الكتاب هو نفسة الذي قام باستعارته
             *  وايضا معرف الكتاب هو المعرف الذي يدخله اليوزر وايضا 
             *التأكد من انه لم يتم اعدة الكتاب
             */
            $borrowRecord = Borrow_record::where('user_id', $user->id)
                ->where('book_id', $bookId)
                ->whereNull('due_date')
                ->firstOrFail();

            // target the event 
            event(new BookReturned($borrowRecord));

            return response()->json(['status' => 'success', 'message' => 'Book returned successfully.']);
        } catch (\Exception $e) {
            Log::error('Error in returning book: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'this book is not for you'], 500);
        }
    }





    public function updateBorrowRecord(Borrow_record $Borrow_record, array $data)
    {
        dd($Borrow_record);
        try {
            // تحقق مما إذا كان السجل موجودًا
            if (!$Borrow_record->exists) {
                return $this->notFound('Borrow record not found.');
            }
            // تحديث الحقول التي تم توفيرها فقط في مصفوفة البيانات
            $Borrow_record->update([
                'user_id' => $data['user_id'] ?? $Borrow_record->user_id,
                'book_id' => $data['book_id'] ?? $Borrow_record->book_id,
                'borrowed_at' => $data['borrowed_at'] ?? $Borrow_record->borrowed_at,
                'due_date' => $data['due_date'] ?? $Borrow_record->due_date,
                'returned_at' => $data['returned_at'] ?? $Borrow_record->returned_at,
            ]);

            return Borrow_recordResource::make($Borrow_record)->toArray(request());
        } catch (\Exception $e) {
            Log::error('Error in updateBorrowRecord: ' . $e->getMessage());
            return $this->errorResponse('An error occurred: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * dalate the borrow record
     * @param int $id
     */

    public function deleteBorrowRecord(int $id)
    {
        $user = User::where('email', 'admin@gmail.com')->first();
        dd($user->roles); // تأكد من أن هناك دور "admin"

        try {
            $record = Borrow_record::find($id);
            if (!$record) {
                throw new \Exception('Book not found.');
            }
            $record->delete();
        } catch (\Exception $e) {
            Log::error('Error in BookController@deleteBook: ' . $e->getMessage());
            throw $e;
        }
    }
}
