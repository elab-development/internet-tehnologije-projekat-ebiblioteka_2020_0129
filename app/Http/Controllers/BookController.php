<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookResource;
use App\Http\Resources\BooksCollection;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = $request->query('page', 0);
        $size = $request->query('size', 10);
        $name = $request->query('name', null);
        $genreId = $request->query('genre_id', null);
        $where = [];
        if ($name != null) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if ($genreId != null) {
            $where[] = ['genre_id', '=', $genreId];
        }
        $result = Book::where($where)->paginate($size, ['*'], 'page', $page);
        return response()->json(new BooksCollection($result));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user->admin) {
            return response()->json(["error" => "Missing permissions"], 403);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'isbn' => 'required|string',
            'description' => 'required|string',
            'genre_id' => 'required|integer',
            'pages' => 'required|integer',
            'published_year' => 'required|integer',
            'preview' => 'required|string',
            'content' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $book = Book::create($request->all());
        return response()->json(new BookResource($book));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        return response()->json(new BookResource($book));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        $user = $request->user();
        if (!$user->admin) {
            return response()->json(["error" => "Missing permissions"], 403);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'isbn' => 'string',
            'description' => 'string',
            'genre_id' => 'integer',
            'pages' => 'integer',
            'published_year' => 'integer',
            'preview' => 'string',
            'content' => 'string'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $book->update($request->all());
        return response()->json(new BookResource($book));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Book $book)
    {
        $user = $request->user();
        if (!$user->admin) {
            return response()->json(["error" => "Missing permissions"], 403);
        }
        $book->delete();
        return response()->noContent();
    }
}
