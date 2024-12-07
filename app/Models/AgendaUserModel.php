<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgendaUserModel extends Model
{
    protected $table = 't_agenda_user';
    
    protected $fillable = [
        'agenda_id',
        'user_id'
    ];

    public function agenda()
    {
        return $this->belongsTo(AgendaModel::class, 'agenda_id', 'agenda_id');
    }

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
}