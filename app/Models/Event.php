<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['name', 'date', 'sector'];

    public function redemptions()
    {
        return $this->hasMany(InvitationRedemption::class);
    }
}
