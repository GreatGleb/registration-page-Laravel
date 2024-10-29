<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TokenController extends Controller
{
    public function index() {
        $user = User::first();
        
//      Creating a token without scopes...
        return response()->json($user->createToken('Token Name'));
    }
}
