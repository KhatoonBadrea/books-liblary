<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;

class BookController extends Controller
{
    use ApiResponseTrait;
    
    protected $bookService;

    /**
     * constractur to inject Movie Service Class
     * @param BookService $bookService 
     */
    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }
    /**
     * Display a listing of the resource.  
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $books = $this->bookService->getAllBooks();
        return $this->successResponse('this is all book', $books, 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreBookRequest $request
     * @return @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreBookRequest $request)
    {

        $validationdata = $request->validated();
        $book = $this->bookService->create_book($validationdata);
        return $this->successResponse('successefuly added the book', $book, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateBookRequest $request
     * @param Book $book
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateBookRequest $request, Book $book)
    {


        $validatedRequest = $request->validated();
        $updatedBookResource = $this->bookService->updateBook($book, $validatedRequest);
        return $this->successResponse($updatedBookResource, 'Book updated successfully.', 200);
    }

    /** 
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->bookService->deleteBook($id);
            return $this->successResponse([], 'Book deleted successfully.', 200);
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred: ' . $e->getMessage(), 500);
        }
    }
}
