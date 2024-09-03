<?php

namespace App\Services;

use App\Models\Book;
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
    public function getAllBooks()
    {
        try {

            $book = Book::all();

            //check if the book is not empty
            if ($book->isNotEmpty()) {

                $book = BookResource::collection($book);

                return $book;
            } else
                return $this->notFound('there are not any book here');
        } catch (\Exception $e) {
            Log::error('Error in BookController@store' . $e->getMessage());
            return $this->errorResponse('An error occurred: ' . 'There is an error on the server', [], 500);
        }
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
}
