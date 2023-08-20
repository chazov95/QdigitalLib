<?php

namespace App\Http\Controllers;

use App\Models\UserBooks;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserBooksController extends Controller
{
    public function showBookList()
    {

        $userBooks = DB::table('user_books')
            ->join('books', 'books.id', '=', 'user_books.book_id')
            ->select('books.title', 'books.description', 'books.authors', 'user_books.favorite')
            ->where('user_id', 1)
            ->get();
        dd($userBooks);
    }

    public function deleteBook(Request $request)
    {
        $bookId = $request->get("bookId");
        $this->checkUserHasBook($bookId);

        $deleted = DB::table('user_books')
            ->where('book_id', $bookId)->delete();
    }

    /**
     * @throws ErrorException
     */
    public function makeFavorite(Request $request)
    {
        $bookId = $request->get("bookId");
        $this->checkUserHasBook($bookId);

        DB::table('user_books')
            ->where('book_id', $bookId)
            ->update(['favorite' => true]);
    }

    /**
     * @throws ErrorException
     */
    public function getInfo(Request $request)
    {
        $bookId = $request->get("bookId");
        $this->checkUserHasBook($bookId);

        $response = DB::table('books')
            ->select('description')
            ->where('id', $bookId)
            ->get();
        dd($response);
        return $response;
    }

    /**
     * @throws ErrorException
     */
    private function checkUserHasBook($bookId)
    {
        $bookExistence = DB::table('user_books')
            ->where('book_id', $bookId)
            ->where('user_id', 1 /*Auth::user()->getAuthIdentifier()*/)
            ->exists();

        if (!$bookExistence) {
            throw new ErrorException("Выбранной книги нет в вашем списке");
        }
    }
}
