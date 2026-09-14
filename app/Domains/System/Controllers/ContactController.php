<?php

namespace App\Domains\System\Controllers;

use App\Domains\System\Models\ContactMessage;
use App\Http\Controllers\Controller;
use App\Jobs\Mail\SendContactMessageReceivedEmailJob;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:120'],
            'email'   => ['required', 'email', 'max:200'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $contactMessage = ContactMessage::create([
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status'  => 'unread',
        ]);

        SendContactMessageReceivedEmailJob::dispatch($contactMessage->id);

        return ApiResponse::success(null, 'Your message has been received. We will get back to you soon.');
    }
}
