<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{

    public function uploadFile(Request $request)
    {
        $user = $request->user();
        if (!$user->admin) {
            return response()->json(["error" => "Missing permissions"], 403);
        }
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:doc,docx,pdf,txt,csv',
        ]);

        if ($validator->fails()) {

            return response()->json(['error' => $validator->errors()], 400);
        }


        $fileName = $request->file('file')->store('local');
        return response()->json(['fileName' => $fileName]);
    }

    public function getBookFile(Request $request, $bookId)
    {
        $book = Book::find($bookId);
        $user = $request->user();
        $fileName = $book->content;
        if ($user->admin) {
            return response(Storage::disk('local')->get($fileName));
        }
        $now = time();
        $subscription = Subscription::where('book_id', $bookId)->where('user_id', $user->id)
            ->where('status', 'accepted')
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->first();
        if (!$subscription) {
            return response()->json(["error" => "Missing subscription"], 403);
        }
        return response(Storage::disk('local')->get($fileName));
    }
}
