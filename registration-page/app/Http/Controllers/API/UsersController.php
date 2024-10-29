<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    public function getUser(Request $request) {
        $response = [];
        $status = 200;

        if(!ctype_digit($request->id)) {
            $response['success'] = false;
            $response['message'] = 'The user with the requestedid does not exist';
            $response['fails'] = [
                'userId' => [
                    'The user must be an integer.'
                ]
            ];

            $status = 400;
        } else {
            $user = User::where('id', $request->id)->with('position')->first();
            if(!$user) {
                $response['success'] = false;
                $response['message'] = 'User not found';

                $status = 404;
            } else {
                $user = $user->toArray();
                $user['position'] = $user['position']['position'];
                unset($user['created_at']);
                unset($user['updated_at']);

                $response['success'] = true;
                $response['user'] = $user;
            }
        }

        return response()->json($response, $status);
    }
}
