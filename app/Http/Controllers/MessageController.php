<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Ad;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MessageController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $allMessages = Message::query()
            ->with([
                'sender:id,name',
                'receiver:id,name',
                'ad:id,title,slug',
            ])
            ->where(function ($query) use ($user): void {
                $query
                    ->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->latest()
            ->get();

        $conversations = $allMessages
            ->groupBy(function (Message $message) use ($user): string {
                $partnerId = $message->sender_id === $user->id
                    ? $message->receiver_id
                    : $message->sender_id;

                return $message->ad_id.'-'.$partnerId;
            })
            ->map(function ($messages) use ($user): array {
                /** @var \App\Models\Message $latest */
                $latest = $messages->first();
                $partner = $latest->sender_id === $user->id ? $latest->receiver : $latest->sender;

                $unreadCount = $messages
                    ->where('receiver_id', $user->id)
                    ->whereNull('read_at')
                    ->count();

                return [
                    'ad' => [
                        'id' => $latest->ad?->id,
                        'title' => $latest->ad?->title,
                        'slug' => $latest->ad?->slug,
                    ],
                    'partner' => [
                        'id' => $partner?->id,
                        'name' => $partner?->name,
                    ],
                    'latest_message' => [
                        'id' => $latest->id,
                        'body' => $latest->body,
                        'created_at' => $latest->created_at,
                    ],
                    'unread_count' => $unreadCount,
                ];
            })
            ->sortByDesc(fn (array $conversation) => $conversation['latest_message']['created_at'])
            ->values();

        $activeAdId = (int) $request->integer('ad');
        $activeUserId = (int) $request->integer('with');
        $thread = collect();
        $activeAd = null;
        $activeUser = null;

        if ($activeAdId > 0 && $activeUserId > 0) {
            $activeAd = Ad::query()->find($activeAdId);
            $activeUser = User::query()->select('id', 'name')->find($activeUserId);

            $thread = Message::query()
                ->with(['sender:id,name', 'receiver:id,name'])
                ->where('ad_id', $activeAdId)
                ->where(function ($query) use ($user, $activeUserId): void {
                    $query
                        ->where(fn ($inner) => $inner->where('sender_id', $user->id)->where('receiver_id', $activeUserId))
                        ->orWhere(fn ($inner) => $inner->where('sender_id', $activeUserId)->where('receiver_id', $user->id));
                })
                ->orderBy('created_at')
                ->get();

            Message::query()
                ->where('ad_id', $activeAdId)
                ->where('sender_id', $activeUserId)
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return Inertia::render('Messages/Index', [
            'conversations' => $conversations,
            'activeAd' => $activeAd ? [
                'id' => $activeAd->id,
                'title' => $activeAd->title,
                'slug' => $activeAd->slug,
            ] : null,
            'activeUser' => $activeUser,
            'messages' => MessageResource::collection($thread)->resolve(),
        ]);
    }

    public function store(StoreMessageRequest $request): RedirectResponse
    {
        $this->authorize('create', Message::class);

        $validated = $request->validated();
        $sender = $request->user();
        $ad = Ad::query()->findOrFail((int) $validated['ad_id']);
        $receiverId = (int) $validated['receiver_id'];

        if ($receiverId === $sender->id) {
            return back()->with('error', 'لا يمكنك إرسال رسالة لنفسك.');
        }

        if ($sender->id !== $ad->user_id && $ad->status !== 'approved') {
            return back()->with('error', 'لا يمكن مراسلة إعلان غير منشور.');
        }

        if ($sender->id !== $ad->user_id && $receiverId !== $ad->user_id) {
            return back()->with('error', 'لا يمكن بدء محادثة خارج إطار صاحب الإعلان.');
        }

        Message::query()->create([
            'ad_id' => $ad->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiverId,
            'body' => $validated['body'],
        ]);

        return to_route('messages.index', [
            'ad' => $ad->id,
            'with' => $receiverId,
        ])->with('success', 'تم إرسال الرسالة.');
    }
}
