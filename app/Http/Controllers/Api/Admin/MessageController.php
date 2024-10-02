<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $read=Message::where('isread',1)->get();
        $unread=Message::where('isread',0)->get();
        return response()->json([
            "readMessages" => MessageResource::collection($read),
            "unReadMessages" => MessageResource::collection($unread),
        ], 200);
    }
    public function read(Message $message){
        if($message->isread==0){
            $message->update([
                'isread'=> 1,
            ]);
        }
        return response()->json([
            "success" => "Message was read successfull!",
            "message"=>new MessageResource($message),
        ], 200);
    }
    public function destroy(Message $message){
        $message->delete();
        return response()->json([
            "success" => "Message was deleted successfull!",
        ], 200);
    }

}
