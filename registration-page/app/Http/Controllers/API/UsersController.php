<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    private $errors = [];

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

    public function getUsers(Request $request) {
        $response = [];
        $status = 200;

        $count = $request->count ?? '5';
        $page = $request->page ?? '1';

        if(!ctype_digit($count)) {
            $this->errors['count'] = ['The count must be an integer.'];
        } else if($count < 1) {
            $this->errors['count'] = ['The count must be at least 1.'];
        }

        if(!ctype_digit($page)) {
            $this->errors['page'] = ['The page must be an integer.'];
        } else if($page < 1) {
            $this->errors['page'] = ['The page must be at least 1.'];
        }

        if(sizeof($this->errors)) {
            $response['success'] = false;
            $response['message'] = 'Validation failed';
            $response['fails'] = $this->errors;

            $status = 422;
        } else {
            $offset = $count * ($page - 1);

            $users = User::with('position')->limit($count)->offset($offset)->get()->toArray();
            $totalUsers = User::count();
            $totalPages = (int)ceil($totalUsers/$count);
            $nextUrl = null;
            $prevUrl = null;

            $linkForGetUsers = 'https://' . $request->header('host') . $request->getBasePath() . '/' . $request->path();
            $linkParams = [];
            $linkParams['count'] = 'count=' . $count;

            if($page < $totalPages) {
                $linkParams['page'] = 'page=' . (string)((int)$page + 1);
                $nextUrl = $linkForGetUsers . '?' . join('&', $linkParams);
            }

            if($page > 1) {
                $linkParams['page'] = 'page=' . (string)((int)$page - 1);
                $prevUrl = $linkForGetUsers . '?' . join('&', $linkParams);
            }

            foreach ($users as $key => $user) {
                $users[$key]['registration_timestamp'] = strtotime($user['created_at']);
                $users[$key]['position'] = $user['position']['position'];

                unset($users[$key]['created_at']);
                unset($users[$key]['updated_at']);
            }

            if(!sizeof($users)) {
                $response['success'] = false;
                $response['message'] = 'Page not found';

                $status = 404;
            } else {
                $response['success'] = true;
                $response['page'] = (int)$page;
                $response['total_pages'] = $totalPages;
                $response['total_users'] = $totalUsers;
                $response['count'] = (int)$count;
                $response['links'] = [
                    'next_url' => $nextUrl,
                    'prev_url' => $prevUrl,
                ];
                $response['users'] = $users;
            }
        }

        return response()->json($response, $status);
    }
}
