<?php

namespace App\Http\Controllers\Global;

use App\Events\ConversationSeenEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Models\User;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\ChatService;

class ChatController extends Controller
{
    use MediaUploadingTrait;
    protected $chatService;
    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }
    public function index(Request $request)
    {
        $users = User::where('id', '!=', auth()->user()->id)->get();
        $conversations = $request->user()->conversations()
            ->with(['lastMessage', 'users:id,name'])
            ->orderBy(function ($query) {
                $query->select('created_at')
                    ->from('messages')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->latest()
                    ->limit(1);
            }, 'desc')
            ->get()
            ->map(function ($conversation) {
                $conversation->other_user = $conversation->users->firstWhere('id', '!=', auth()->id());
                return $conversation;
            });
        return view('admin.chats.index', compact('users', 'conversations'));
    }

    public function startChat(Request $request)
    {
        $conversation = $this->chatService->findOneToOneConversation(
            auth()->id(),
            $request->input('user_id')
        );
        $this->chatService->sendMessage(
            auth()->user(),
            $conversation,
            $request->input('message'),
            $request->input('attachments', [])
        );

        return redirect()->route('admin.chats.index');
    }

    public function markAsRead(Request $request)
    {
        $conversation = $request->user()
            ->conversations()
            ->find($request->input('conversation_id'));

        $conversation->markAsSeen(auth()->id());
    }

    public function sendMessage(Request $request)
    {
        $conversation = $request->user()
            ->conversations()
            ->find($request->input('conversation_id'));

        $html = $this->chatService->sendMessage(
            auth()->user(),
            $conversation,
            $request->input('message'),
            $request->input('attachments', [])
        );

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    public function loadConversation(Request $request): JsonResponse
    {
        try {
            $conversationId = $request->input('conversation_id');

            // Get the conversation with latest 200 messages and users
            $conversation = $request->user()->conversations()
                ->with(['messages' => function ($query) {
                    $query->with('sender')
                        ->orderBy('id', 'desc')
                        ->limit(200);
                }, 'users:id,name'])
                ->findOrFail($conversationId);

            // Get the other participant
            $otherParticipant = $conversation->otherParticipant(auth()->id());

            $conversation->markAsSeen(auth()->id());

            // Render the chat box view
            $html = view('admin.chats.chat-box', compact('conversation', 'otherParticipant'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'conversation_id' => $conversationId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading conversation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function loadMoreMessages(Request $request): JsonResponse
    {
        try {
            $conversationId = $request->input('conversation_id');
            $page = $request->input('page', 2);
            $perPage = 200;

            // Get the conversation
            $conversation = $request->user()->conversations()
                ->with(['users:id,name'])
                ->findOrFail($conversationId);

            // Get the other participant
            $otherParticipant = $conversation->otherParticipant(auth()->id());

            // Get messages with pagination
            $messages = $conversation->messages()
                ->with('sender')
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            if ($messages->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'html' => '',
                    'hasMorePages' => false,
                    'currentPage' => $page,
                    'lastPage' => $messages->lastPage()
                ]);
            }

            // Render the messages
            $html = '';
            foreach ($messages->reverse() as $message) {
                $html .= view('admin.chats.message', [
                    'message' => $message, 
                    'otherParticipant' => $otherParticipant, 
                    'authUser' => auth()->user()
                ])->render();
            }

            return response()->json([
                'success' => true,
                'html' => $html,
                'hasMorePages' => $messages->hasMorePages(),
                'currentPage' => $messages->currentPage(),
                'lastPage' => $messages->lastPage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading messages: ' . $e->getMessage()
            ], 500);
        }
    }
}
