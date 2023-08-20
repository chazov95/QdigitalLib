<?php

namespace App\Http\Controllers;

use App\Models\UserBooks;
use ErrorException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserBooksController extends Controller
{
    public function showBookList(Request $request)
    {
        $user = $this->getUserByToken($request);

        $userBooks = DB::table('user_books')
            ->join('books', 'books.id', '=', 'user_books.book_id')
            ->select('books.title', 'books.description', 'books.authors', 'user_books.favorite')
            ->where('user_id', $user->id)
            ->get();

        return new JsonResponse($userBooks);
    }

    public function deleteBook(Request $request, $bookId)
    {
        if (false === $this->checkUserHasBook($request, $bookId)) {
            return new JsonResponse(["message" => "книга не принадлежит пользователю"]);
        }

        $deleted = DB::table('user_books')
            ->where('book_id', $bookId)->delete();
        if ($deleted === 0) {
            return new JsonResponse(["message" => "такая книга не найдена"]);
        }
            return new JsonResponse(["message" => "книга удалена"]);
    }

    /**
     * @throws ErrorException
     */
    public function makeFavorite(Request $request, $bookId)
    {
        if (false === $this->checkUserHasBook($request, $bookId)) {
            return new JsonResponse(["message" => "книга не принадлежит пользователю"]);
        }

       $update = DB::table('user_books')
            ->where('book_id', $bookId)
            ->update(['favorite' => true]);

        if ($update === 0) {
            return new JsonResponse(["message" => "такая книга не найдена"]);
        }
        return new JsonResponse(["message" => "книга добавлена в раздел 'люимые книги'"]);
    }

    /**
     * @throws ErrorException
     */
    public function getInfo(Request $request, $bookId)
    {
        if (false === $this->checkUserHasBook($request, $bookId)) {
            return new JsonResponse(["message" => "книга не принадлежит пользователю"]);
        }
        $response = DB::table('books')
            ->select('description', 'title', 'authors')
            ->where('id', $bookId)
            ->get();

        return $response;
    }

    /**
     * @throws ErrorException
     */
    private function checkUserHasBook(Request $request, $bookId)
    {
        $user = $this->getUserByToken($request);

        $bookExistence = DB::table('user_books')
            ->where('book_id', $bookId)
            ->where('user_id', $user->id)
            ->exists();

    }

    private function getUserByToken(Request $request)
    {
        $token = $request->bearerToken();

        $user = DB::table("users")
            ->where("remember_token", "=", $token)->first();

        return $user;
    }
}
