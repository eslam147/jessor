<?php

namespace App\Dtos\Chat;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

readonly class ChatDto
{
    public function __construct(
        public string $message,
        public ?array $files = [],
        public User $sender,
        public User $receiver
    ) {
    }
    public static function setFromRequest(Request $request): self
    {
        return new self(
            $request->message,
            $request->file,
            auth()->user(),
            User::findOrFail($request->receiver_id)
        );
    }

}