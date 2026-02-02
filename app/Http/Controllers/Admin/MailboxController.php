<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Models\MailboxMessage;
use App\Models\MessageParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MailboxController extends Controller
{
    use MediaUploadingTrait;
    public function index()
    {
        $search = request('search');
        if (!in_array(request('type'), ['inbox', 'sent', 'important', 'starred', 'trash', 'archive'])) {
            return redirect()->route('admin.mailbox.index', ['type' => 'inbox']);
        }
        $messages = MailboxMessage::with(['participants.user.media','sender'])
            ->where('parent_id', null)
            ->forUser(auth()->id(), request('type'))
            ->when($search, function ($query) use ($search) {
                $query->where('subject', 'like', '%' . $search . '%')
                    ->orWhereHas('participants.user', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhere('content', 'like', '%' . $search . '%');
            })
            ->latest('updated_at')
            ->paginate(25);
            
        $totalMessages = MessageParticipant::select(DB::raw('
            COUNT(CASE WHEN user_id = '.auth()->id().' AND is_deleted = 0 AND is_archived = 0 AND role = "receiver" THEN 1 END) as inbox,
            COUNT(CASE WHEN user_id = '.auth()->id().' AND is_deleted = 0 AND is_archived = 0 AND role = "sender" THEN 1 END) as sent,
            COUNT(CASE WHEN user_id = '.auth()->id().' AND is_deleted = 0 AND is_archived = 0 AND is_starred = 1 THEN 1 END) as starred,
            COUNT(CASE WHEN user_id = '.auth()->id().' AND is_deleted = 0 AND is_archived = 0 AND is_important = 1 THEN 1 END) as important,
            COUNT(CASE WHEN user_id = '.auth()->id().' AND is_deleted = 0 AND is_archived = 1 THEN 1 END) as archive,
            COUNT(CASE WHEN user_id = '.auth()->id().' AND is_deleted = 1 THEN 1 END) as trash, 
            COUNT(*) as total
        '))->first(); 
        return view('admin.mailbox.index', compact('messages', 'totalMessages'));
    }

    public function store(Request $request)
    {
        foreach ($request->receivers as $receiverId) {
            $mailboxMessage = MailboxMessage::create([
                'subject' => $request->subject,
                'content' => $request->content,
                'sender_id' => auth()->user()->id,
            ]);
            MessageParticipant::insert([
                [
                    'message_id' => $mailboxMessage->id,
                    'user_id' => $receiverId,
                    'role' => 'receiver',
                    'is_read' => false,
                ],
                [
                    'message_id' => $mailboxMessage->id,
                    'user_id' => auth()->user()->id,
                    'role' => 'sender',
                    'is_read' => true,
                ],
            ]);
        }

        foreach ($request->input('attachments', []) as $file) {
            $mailboxMessage->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('attachments');
        }
        return redirect()->route('admin.mailbox.index');
    }

    public function star(Request $request)
    {
        $message = MailboxMessage::find($request->message_id);
        $participant = $message->participants->where('user_id', auth()->id())->first();
        $participant->update(['is_starred' => !$participant->is_starred]);
        return response()->json(['success' => true]);
    }

    public function show(Request $request)
    {
        $message = MailboxMessage::find($request->id);
        $participant = $message->participants->where('user_id', auth()->id())->first();
        if($participant->is_read == false){
            $participant->update(['is_read' => true]);
        }
        $replies = MailboxMessage::where('thread_id', $message->thread_id)->whereNotNull('parent_id')->get();
        $html = view('admin.mailbox.show', compact('message', 'replies'))->render();
        return response()->json([
            'html' => $html,  
        ]);
    }

    public function important(Request $request)
    {
        $message = MailboxMessage::find($request->message_id);
        $participant = $message->participants->where('user_id', auth()->id())->first();
        $participant->update(['is_important' => !$participant->is_important]);
        return response()->json(['success' => true]);
    }
    public function archive(Request $request)
    {
        $message = MailboxMessage::find($request->message_id);
        $participant = $message->participants->where('user_id', auth()->id())->first();
        $participant->update(['is_archived' => !$participant->is_archived]);
        return response()->json(['success' => true]);
    }

    public function destroy(MailboxMessage $message)
    {
        $participant = $message->participants->where('user_id', auth()->id())->first();
        $participant->update(['is_deleted' => true]);
        return response()->json(['success' => true]);
    }

    public function reply(Request $request)
    {
        $message = MailboxMessage::find($request->message_id);
        if($message->parent_id == null && $message->thread_id == null){
            $message->thread_id = $message->id;
        }
        $message->updated_at = now();
        $message->save();
        
        $participant = $message->participants->where('user_id','!=', auth()->id())->first(); 
        $participant->update(['is_read' => false]);
        
        $parentId = $message->id;
        $threadId = $message->thread_id;
        MailboxMessage::create([
            'subject' => 'Re: ' . $message->subject,
            'content' => $request->content,
            'sender_id' => auth()->user()->id,
            'parent_id' => $parentId,
            'thread_id' => $threadId,
        ]);
        return redirect()->route('admin.mailbox.index');
    }

    public function loadMore(Request $request)
    {
        $search = $request->search;
        $type = $request->type;
        $page = $request->page ?? 2;
        
        if (!in_array($type, ['inbox', 'sent', 'important', 'starred', 'trash', 'archive'])) {
            return response()->json(['error' => 'Invalid type'], 400);
        }
        
        $messages = MailboxMessage::with(['participants.user.media','sender'])
            ->forUser(auth()->id(), $type)
            ->when($search, function ($query) use ($search) {
                $query->where('subject', 'like', '%' . $search . '%')
                    ->orWhereHas('participants.user', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhere('content', 'like', '%' . $search . '%');
            })
            ->latest('updated_at')
            ->paginate(25, ['*'], 'page', $page);
            
        $html = view('admin.mailbox.partials.messages', compact('messages'))->render();
        
        return response()->json([
            'html' => $html,
            'hasMorePages' => $messages->hasMorePages(),
            'currentPage' => $messages->currentPage(),
            'lastPage' => $messages->lastPage()
        ]);
    }
}
