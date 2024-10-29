<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PositionsController extends Controller
{
    public function index() {
        $response = [];
        $positions = Position::all()->toArray();
        if(sizeof($positions)) {
            $response['success'] = true;
            $response['positions'] = $positions;
        } else {
            $response['success'] = false;
            $response['message'] = 'Positions not found';
        }

        return response()->json($response);
    }
}
