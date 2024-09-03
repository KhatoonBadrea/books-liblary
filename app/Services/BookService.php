<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Models\Book;
use App\Models\Borrow_record;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\BookResource;
use App\Http\Traits\ApiResponseTrait;

class BookService
{
    use ApiResponseTrait;

    /**
     * fetch the all book from DB
     * @return Book $book
     */
    public function getAllBooks($filterBy = null, $filterValue = null, $isBorrowed = null)
    {
        //make a query for book
        $query = Book::query();

        //filtering the book
        if ($filterBy && $filterValue) {
            $query->where($filterBy, $filterValue);
        }

        if ($isBorrowed !== null) {
            //check if the book borrowed
            $borrowedBooksIds = Borrow_Record::whereNull('due_date')
                ->orWhere('due_date', '=<', today())
                ->pluck('book_id')
                ->toArray();
                //apply the condition :if the book borrowed or not
            $query->where(function ($subQuery) use ($borrowedBooksIds, $isBorrowed) {
                if (!$isBorrowed) {
                    //featch the borrowed book just
                    $subQuery->whereIn('id', $borrowedBooksIds);
                } else {
                    //featch the book not borrowed & not in the Borrow_Record
                    $subQuery->whereNotIn('id', $borrowedBooksIds);
                }
            });
        }

        $books = $query->get();
        return $books;
    }






    /**
     * create new book
     * @param array $data
     * @return Book $book
     */

    public function create_book(array $data)
    {

        // Create a new book using the provided data
        try {

            $book = Book::create([
                'title' => $data['title'],
                'author' => $data['author'],
                'description' => $data['description'],
                'category' => $data['category'],
                'publiched_at' => $data['publiched_at'],
            ]);

            // Check if the book was created successfully
            if (!$book) {
                throw new \Exception('Failed to create the book.');
            }

            // Return the created book as a resource
            return BookResource::make($book)->toArray(request());
        } catch (\Exception $e) {
            Log::error('Error in BookController@store' . $e->getMessage());
            return $this->errorResponse('An error occurred: ' . 'There is an error on the server', [], 500);
        }
    }
    /**
     * update the book
     * @param Book $book
     * @param array $data
     * @return Book $book 
     */


    public function updateBook(Book $book, array $data)
    {
        try {
            // dd($book);

            if (!$book->exists) {
                return $this->notFound('Book not found.');
            }
            //   Update only the fields that are provided in the data array
            $book->update(array_filter([
                'title' => $data['title'] ?? $book->title,
                'author' => $data['author'] ?? $book->director,
                'description' => $data['description'] ?? $book->description,
                'category' => $data['category'] ?? $book->category,
                'publiched_at' => $data['publiched_at'] ?? $book->genre,
            ]));

            // Return the updated book as a resource
            return BookResource::make($book)->toArray(request());
        } catch (\Exception $e) {
            Log::error('Error in BookController@update' . $e->getMessage());
            return $this->errorResponse('An error occurred: ' . 'there is an error in the server', [], 500);
        }
    }
    /**
     * delete the book
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function deleteBook(int $id)
    {
        try {
            $book = Book::find($id);
            if (!$book) {
                throw new \Exception('Book not found.');
            }

            $book->delete();
        } catch (\Exception $e) {
            Log::error('Error in BookController@deleteBook: ' . $e->getMessage());
            throw $e;
        }
    }
    public function getBookDetails(Book $id)
    {
        try {
            // dd($id->ratings());
            // تحميل الكتاب مع التقييمات
            // dd($id);

            // $book->load('ratings.user');
            dd(Borrow_record::with('ratings')->find($id));
            // $book = Book::with('ratings')->find($id);

            // $ratings = $book->ratings()->avg('ratings');
            // dd($ratings);
            // تحقق من حالة الاستعارة
            // $isAvailable = !$book->borrow_records()->whereNull('returned_at')->exists();

            // بناء البيانات المراد إرجاعها
            // return [
            //     'id' => $book->id,
            //     'title' => $book->title,
            //     'author' => $book->author,
            //     'description' => $book->description,
            //     'category' => $book->category,
            //     'published_at' => $book->published_at,
            //     // 'average_rating' => $book->ratings()->avg('rating') ?? 'لا يوجد تقييمات',
            //     // 'is_available' => $isAvailable,
            //     'ratings' => $book->ratings,
            // ];
            // dd($book);
        } catch (Exception $e) {
            Log::error('Error in BookService@getBookDetails: ' . $e->getMessage());
            throw new Exception('An error occurred while retrieving book details.');
        }
    }
}
