<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Ad;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MessageApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        $messages = Message::query()
            ->with(['sender:id,name', 'receiver:id,name', 'ad:id,title,slug'])
            ->where(function ($query) use ($user): void {
                $query
                    ->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->when(
                $request->filled('ad_id'),
                fn ($query) => $query->where('ad_id', (int) $request->integer('ad_id'))
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return MessageResource::collection($messages);
    }

    public function store(StoreMessageRequest $request): MessageResource
    {
        $this->authorize('create', Message::class);

        $validated = $request->validated();
        $sender = $request->user();
        $ad = Ad::query()->findOrFail((int) $validated['ad_id']);
        $receiverId = (int) $validated['receiver_id'];

        abort_if($receiverId === $sender->id, 422, 'لا يمكنك إرسال رسالة لنفسك.');
        abort_if($sender->id !== $ad->user_id && $receiverId !== $ad->user_id, 422, 'محادثة غير صالحة.');

        $message = Message::query()->create([
            'ad_id' => $ad->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiverId,
            'body' => $validated['body'],
        ]);

        $message->load(['sender:id,name', 'receiver:id,name', 'ad:id,title,slug']);

        return new MessageResource($message);
    }
}

