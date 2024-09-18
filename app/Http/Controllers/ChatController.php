<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Dtos\Chat\ChatDto;
use Illuminate\Http\Request;
use App\Services\Chat\ChatService;
use App\Enums\Response\HttpResponseCode;
use Illuminate\Support\Facades\RateLimiter;

class ChatController extends Controller
{
    public function __construct(
        private readonly ChatService $chatService
    ){
    }

    public function list()
    {
        return $this->chatService->getChatUserList();
    }
    public function chatMessages(User $user)
    {

        $messages = $this->chatService->getUserChatMessage($user);
        $this->chatService->readAllMessages($user);
        return response()->json(
            $messages,
            HttpResponseCode::SUCCESS
        );
        // return $this->chatService->getChatUserList();
    }
    public function sendMessage(Request $request){

        $userId = $request->user()->id; // Assuming the user is authenticated

        $key = "send-message:$userId";
        cache()->put("$key", 1, 5);
        return RateLimiter::attempt($key, 5, function () use ($request) {
            $messageData = ChatDto::setFromRequest($request);
            $sendMessage = $this->chatService->sendMessage($messageData);
            return response()->json(
                $sendMessage,
                HttpResponseCode::SUCCESS
            );
        });
    }
}
