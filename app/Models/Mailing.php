<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Mailing extends Model
{
    use Notifiable, SoftDeletes;

//    protected $guard = 'admin';
    protected $table = 'mailings';
    protected $fillable = [
        'mailing_name', 'mode', 'email_address','list_of_emails','sender','greetings','message','status','subject','signature'
    ];
    protected $hidden = [
        'remember_token',
        'sender',
    ];

    public function attached_file(){
        return $this->hasMany('App\Models\AttachedFile','mailing_id');
    }

}
