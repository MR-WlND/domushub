<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('admin.tickets', function ($user) {
    return in_array($user->role, ['admin', 'manager', 'technician']);
});

Broadcast::channel('ticket.{id}', function ($user, $id) {
    // Both admin and the ticket owner can listen
    if (in_array($user->role, ['admin', 'manager', 'technician'])) {
        return true;
    }
    
    $ticket = \App\Models\Ticket::find($id);
    return $ticket && (int) $user->id === (int) $ticket->sender_id;
});

Broadcast::channel('chatbot.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('apartment.{id}', function ($user, $id) {
    // Resident in the apartment, or admin
    if (in_array($user->role, ['admin', 'manager'])) {
        return true;
    }
    return $user->residents()->where('apartment_id', $id)->whereNull('deleted_at')->exists();
});
