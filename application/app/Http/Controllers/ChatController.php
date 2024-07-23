<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\ChatMessageSent;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $data = $request->data;
        
        $message = $request->message;
        $username = $request->username;

        // Broadcast the chat message
        try{
            broadcast(new ChatMessageSent( $data, $message, $username))->toOthers();

        }catch(\Exception $e){
            return response()->json(['status' => 'Error', 'message' => $e->getMessage()]);
        }
       

        return response()->json(['status' => 'Message sent!', 'message' => $message]);
    }
}
