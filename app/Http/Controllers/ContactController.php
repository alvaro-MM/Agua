<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactFormSubmitted;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(ContactRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'email', 'phone', 'message']);
        $data['ip_address'] = $request->ip();

        $message = ContactMessage::create($data);

        $notifyEmail = config('site.contact.notify_email');

        if ($notifyEmail) {
            Mail::to($notifyEmail)->send(new ContactFormSubmitted($message));
        }

        return redirect()
            ->route('contact')
            ->with('success', '¡Gracias! Hemos recibido tu mensaje y te responderemos lo antes posible.');
    }
}
