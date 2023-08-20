<?php

namespace App\Http\Repositories;

use ErrorException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;

class BookRepository
{

    public function __construct()
    {
    }

    /**
     * @throws ErrorException
     */
    public function addBook($uid)
    {
        try {
            DB::table('user_books')->insert([
                'user_id' => /*Auth::user()->getAuthIdentifier()*/ 1,
                'book_id' => $uid
            ]);
        } catch (ErrorException $exception) {
            throw new ErrorException("не удалось записать книгу");
        }

    }
}
