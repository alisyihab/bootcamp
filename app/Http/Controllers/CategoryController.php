<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $category = Category::select("title", "created_at");

        if (!empty($request->q)) {
            $category = $category->where('title', 'LIKE', '%' . $request->q . '%');
        }

        $category = $category->orderBy('created_at', 'DESC')->paginate(10);

        return response()->json(['status' => 200, "content" => $category], 200);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'title' => 'required|string'
        ]);

        if ($validate->fails()) {
            return response()->json([
                "status" => 422,
                "error" => $validate->errors()
            ]);
        }

        Category::create($request->all());

        return response()->json([
            "status" => 201,
            "message" => "Category $request->title berhasil dibuat"
        ]);
    }

    public function edit($id)
    {
        return response()->json(["status" => 200, "data" => Category::find($id)]);
    }

    public function update(Request $request, $id)
    {
        $product = Category::find($id);
        $product->update($request->all());

        return response()->json([
            "status" => 201,
            "message" => "Category $request->title berhasil diubah"
        ]);
    }

    public function destroy($id)
    {
        Category::destroy($id);
        return response()->json(['status' => 200, "message" => "data berhasil dihapus"]);
    }
}
