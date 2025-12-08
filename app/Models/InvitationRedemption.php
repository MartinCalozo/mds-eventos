<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvitationRedemption extends Model
{
    use HasFactory;
    protected $fillable = [
        'invitation_id',
        'hash',
        'event_id',
        'guest_count'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
