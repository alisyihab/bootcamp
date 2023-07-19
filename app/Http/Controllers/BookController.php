<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role !== "2") {
            return response()->json(["message" => "access denied"], 400);
        }

        $book = Book::with(['author', 'Book'])->paginate(20);

        return response()->json(['status' => 200, "content" => $book]);
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== "2") {
            return response()->json(["message" => "access denied"], 400);
        }

        $validate = Validator::make($request->all(), [
            'title' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validate->fails()) {
            return response()->json([
                "status" => 422,
                "error" => $validate->errors()
            ]);
        }

        $input = $request->all();

        $path = '/uploads/books';
        $input['image'] = env('APP_URL') . $path . '/' .time() . '.' . $request->image->extension();

        if (!File::exists($path)) {
            File::makeDirectory($path, 0775, true, true);
        }

        $request->image->move(public_path('/uploads/books'), $input['image']);

        $input['user_id'] = $request->user()->id;

        Book::create($input);

        return response()->json([
            "status" => 201,
            "message" => "Book $request->title berhasil dibuat"
        ]);
    }

    public function edit($id)
    {
        return response()->json(["status" => 200, "data" => Book::find($id)]);
    }

    public function update(Request $request, $id)
    {
        $product = Book::find($id);
        $product->update($request->all());

        return response()->json([
            "status" => 201,
            "message" => "Book $request->title berhasil diubah"
        ]);
    }

    public function destroy($id)
    {
        Book::destroy($id);
        return response()->json(['status' => 200, "message" => "data berhasil dihapus"]);
    }
}
