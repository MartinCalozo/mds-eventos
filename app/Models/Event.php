<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'date',
        'sector'
    ];

    public function redemptions()
    {
        return $this->hasMany(InvitationRedemption::class);
    }
}
