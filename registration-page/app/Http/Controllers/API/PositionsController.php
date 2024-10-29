<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Position;

class PositionsController extends Controller
{
    public function index() {
        $response = [];
        $status = 200;

        $positions = Position::all()->toArray();
        if(sizeof($positions)) {
            $response['success'] = true;
            $response['positions'] = $positions;
        } else {
            $response['success'] = false;
            $response['message'] = 'Positions not found';

            $status = 404;
        }

        return response()->json($response, $status);
    }
}
