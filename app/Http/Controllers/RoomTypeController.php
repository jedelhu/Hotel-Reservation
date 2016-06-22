<?php

namespace App\Http\Controllers;

use App\RoomType;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Response;
class RoomTypeController extends Controller
{
    public function index()
    {

        $room_types = RoomType::all();
        return $room_types;
    }

    public function store(Request $request)
    {

//        dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'short_name' => 'required',
            'base_price' => 'required|numeric|min:3',
            'base_availability' => 'required|numeric',
            'max_occupancy' => 'required|numeric',
        ]);

        if ($validator->fails()) {

            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()

            ), 400);

//            return $validator->messages();

        }else{
            $room_type=new RoomType($request->all());
            $room_type->save();

            return $room_type;
        }


    }
}
