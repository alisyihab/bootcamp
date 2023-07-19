<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request) {
        $fields = $request->all();

        $validate = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 422, 
                "error" => $validate->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'status' => '201',
            'message' => "User $request->name berhasil dibuat",
        ];

        return response($response, 201);
    }

    public function login(Request $request) {
        $validate = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validate->fails()) {
            return response()->json([
                "status" => 422,
                "error" => $validate->errors()
            ], 422);
        }
        // Check email
        $user = User::where('email', $request->email)->first();

        // Check password
        if(!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'Bad creds'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout(Request $request) {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out'
        ];
    }

    public function bookList(Request $request)
    {
        $book = Book::with(['author', 'category']);

        if (!empty($request->title)) {
            $book = $book->where('title', 'Like', '%' . $request->title . '%');
        }

        if (!empty($request->author)) {
            $book = $book->where('user_id', $request->author);
        }

        $book = $book->paginate(10);
        
        return response()->json(['status' => 200, "content" => $book]);
    }

    public function listAuthor()
    {
        $data = User::where('role', 1)->get();

        return response()->json(['status' => 200, 'content' => $data]);
    }
}
