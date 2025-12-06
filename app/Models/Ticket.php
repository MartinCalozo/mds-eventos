<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = ['invitation_redemption_id','code','used','validated_by','validated_at'];

    public function redemption()
    {
        return $this->belongsTo(InvitationRedemption::class, 'invitation_redemption_id');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function logs()
    {
        return $this->hasMany(TicketValidationLog::class);
    }
}

