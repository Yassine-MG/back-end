<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $validator = $request->validate([
            'receiver_id' => 'required|integer',
            'content' => 'required',
        ]);
        
        $senderId = Auth::id();
        $receiverId = intval($validator['receiver_id']);
        $content = $validator['content'];
        
        $message = new Message();
        $message->sender_id = $senderId;
        $message->receiver_id = $receiverId;
        $message->content = $content;
        $message->seen = false;
        $message->save();
        
        return response()->json(['message' => 'Message sent successfully'], 200);
    }
    public function index(Request $request, $receiverId)
    {
        $senderId = Auth::id();

        // Fetch messages where the sender is the authenticated user and the receiver is the specified receiverId
        $sentMessages = Message::where('sender_id', $senderId)
            ->where('receiver_id', $receiverId)
            ->get();
    
        // Fetch messages where the sender is the specified receiverId and the receiver is the authenticated user
        $receivedMessages = Message::where('sender_id', $receiverId)
            ->where('receiver_id', $senderId)
            ->get();
    
        // Combine sent and received messages into a single collection
        $messages = $sentMessages->concat($receivedMessages);
    
        // Sort the messages by the date of creation in ascending order
        $sortedMessages = $messages->sortBy('created_at')->values();
    
        return response()->json(['messages' => $sortedMessages , "auth" =>$senderId], 200);
    }

    public function chat()
    {
        $userId = Auth::id();

        // Fetch all messages where the sender is the authenticated user or the receiver is the authenticated user
        $messages = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->get();
    
        // Get unique user IDs from the messages
        $userIds = $messages->pluck('sender_id')
            ->concat($messages->pluck('receiver_id'))
            ->unique();
    
        // Exclude the authenticated user ID from the list of user IDs
        $userIds = $userIds->reject(function ($userId) {
            return $userId == Auth::id();
        });
    
        // Fetch user information for the remaining user IDs
        $users = User::whereIn('id', $userIds)->get();
    
        return response()->json(['users' => $users], 200);
    }


    public function getMessages(Request $request, $otherUserId)
    {
        $senderId = Auth::id();
        
        // Fetch messages where the sender is the authenticated user and the receiver is the specified otherUserId
        $sentMessages = Message::where('sender_id', $senderId)
            ->where('receiver_id', $otherUserId)
            ->with('sender')
            ->get();
    
        // Fetch messages where the sender is the specified otherUserId and the receiver is the authenticated user
        $receivedMessages = Message::where('sender_id', $otherUserId)
            ->where('receiver_id', $senderId)
            ->with('sender')
            ->get();
    
        // Combine sent and received messages into a single collection
        $messages = $sentMessages->concat($receivedMessages);
    
        // Sort the messages by the date of creation in ascending order
        $sortedMessages = $messages->sortBy('created_at')->values();
    
        return response()->json([
            'messages' => $sortedMessages,
        ], 200);
    }


    public function getLastMessage(Request $request, $otherUserId)
    {
        $senderId = Auth::id();
        
        // Fetch the last message where the sender is the authenticated user and the receiver is the specified otherUserId
        $lastSentMessage = Message::where('sender_id', $senderId)
            ->where('receiver_id', $otherUserId)
            ->orderBy('created_at', 'desc')
            ->first();
    
        // Fetch the last message where the sender is the specified otherUserId and the receiver is the authenticated user
        $lastReceivedMessage = Message::where('sender_id', $otherUserId)
            ->where('receiver_id', $senderId)
            ->orderBy('created_at', 'desc')
            ->first();
    
        // Determine the last message by comparing the creation dates of the last sent and received messages
        $lastMessage = $lastSentMessage;
    
        if ($lastReceivedMessage && (!$lastSentMessage || $lastReceivedMessage->created_at > $lastSentMessage->created_at)) {
            $lastMessage = $lastReceivedMessage;
        }
    
        return response()->json(['message' => $lastMessage]);
    }
    
}
