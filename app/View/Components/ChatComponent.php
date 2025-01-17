<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Services\Chat\ChatService;

class ChatComponent extends Component
{
    public $content = [];
    public function __construct(
        private readonly ChatService $chatService
    ) {
    }

    public function render()
    {
        $chatUsers = $this->chatService->getChatUserList();
        $this->content = $chatUsers;

        return view('components.chat-component');
    }
}
