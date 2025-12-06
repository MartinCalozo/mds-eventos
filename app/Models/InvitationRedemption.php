<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvitationRedemption extends Model
{
    protected $fillable = ['invitation_id', 'hash', 'event_id', 'guest_count'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
