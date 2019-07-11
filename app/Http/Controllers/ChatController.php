<?php

namespace App\Http\Controllers;

use App\Message;
use App\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $users = User::select('id', 'name')->where('id', '!=', \Auth::id())->get();
        $messages = Message::select('body', 'date', 'user_from', 'user_to', 'path', 'type')
            ->where('user_to', '=', '*')
            ->orWhere('user_to', '=', \Auth::id())
            ->orWhere('user_from', '=', \Auth::id())->get();

        foreach ($messages as $message) {
            if ($message["path"]) {
                $message['path'] = asset('/storage/' . $message['path']);
            }
        }

        return view('room', [
            'users' => $users,
            'databaseMessages' => $messages
        ]);
    }

    public function sendMessage(Request $request) {
        $this->validate($request, [
            'body' => 'required',
            'date' => 'required',
            'user_from' => 'required',
            'user_to' => 'required',
        ]);

        $data = $request->all();
        $message = new Message();
        $message->fill($data);
        $message->save();

        \App\Events\PrivateChat::dispatch($data);
    }
}
