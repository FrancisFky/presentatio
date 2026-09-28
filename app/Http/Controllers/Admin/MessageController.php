<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

/** Messages du formulaire de contact */
class MessageController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');

        $messages = Message::query()
            ->when($request->filled('search'), fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('subject', 'like', '%' . $request->search . '%')))
            // Sans filtre : la boîte de réception, sans les archivés
            ->when($status, fn ($q) => $q->where('status', $status), fn ($q) => $q->where('status', '!=', Message::STATUS_ARCHIVED))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.messages.index', ['current' => 'messages', 'messages' => $messages]);
    }

    public function show(Message $message)
    {
        $message->markAsRead();

        return view('admin.messages.show', ['current' => 'messages', 'message' => $message]);
    }

    public function archive(Message $message)
    {
        $archived = $message->status !== Message::STATUS_ARCHIVED;
        $message->update(['status' => $archived ? Message::STATUS_ARCHIVED : Message::STATUS_READ]);

        return redirect()->route('messages.index')->with('success', $archived ? 'Message archivé.' : 'Message remis dans la boîte de réception.');
    }

    public function destroy(Message $message)
    {
        $message->delete();

        return redirect()->route('messages.index')->with('success', 'Message supprimé.');
    }
}
