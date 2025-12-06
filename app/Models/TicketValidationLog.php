<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketValidationLog extends Model
{
    protected $fillable = ['ticket_id', 'validated_by', 'result'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
