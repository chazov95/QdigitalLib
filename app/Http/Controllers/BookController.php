<?php

namespace App\Http\Controllers;

use App\Http\Repositories\BookRepository;
use App\Models\Book;
use ErrorException;
use Illuminate\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use MongoDB\Driver\Session;

class BookController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $booksByArray = $this->getBooks($request->get("query"));

        try {
            $items = array_map(
                function ($item) {
                    return [
                        'uid' => $item['id'],
                        'title' => $item['volumeInfo']['title']
                    ];
                },
                $booksByArray['items']
            );
        } catch (\ErrorException $exception) {
            return new JsonResponse(['error' => 'ошибка'], 404);
        }

        return new JsonResponse($items);
    }

    private function getBooks($query)
    {
        $query = trim(htmlspecialchars($query));
        return json_decode(
            Http::acceptJson()->get("https://www.googleapis.com/books/v1/volumes?q=" . $query),
            true
        );
    }

    /**
     * @throws ErrorException
     */
    public function addBook(Request $request)
    {
        /* @var $book Book */
        $book = $this->addBookToBookTable($request->get("uid"));

        $this->addBookToUserBookTable($book);

        return new JsonResponse();
    }

    public function addBookToBookTable($uid)
    {
        $bookInfo = $this->getBooks($uid);
        $bookData = $this->getBookData($bookInfo);

        return Book::firstOrCreate(
            ['uid' => $uid],
            $bookData[0]
        );
        /*$bookRepository = new BookRepository;
        $bookRepository->addBook($request->get("uid"));*/

    }

    private function getBookData($bookInfo): array
    {
        return array_map(function ($item) {
            return [
                'title' => $item['volumeInfo']['title'],
                'authors' => implode(',', $item['volumeInfo']['authors']),
                'description' => $item['volumeInfo']['description']
            ];
        }, $bookInfo['items']);
    }

    private function addBookToUserBookTable(Book $book)
    {
        //todo проверка на наличе книги в таблице user_books
        try {
            DB::table('user_books')->insert([
                'user_id' => /*Auth::user()->getAuthIdentifier()*/ 1,
                'book_id' => $book->id
            ]);
        } catch (ErrorException $exception) {
            throw new ErrorException("не удалось добавить книгу");
        }
    }
}
