<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class AttachedFile extends Model
{
    use Notifiable, SoftDeletes;

//    protected $guard = 'admin';
    protected $table = 'attached_files';
    protected $fillable = [
        'path_to_file',
        'created_at',
        'updated_at',
        'deleted_at',
        'user_id',
        'mailing_id',
        'file_name',
        ];
    public function mailing(){
        return $this->belongsTo('App\Models\Mailing','mailing_id','id');
    }
}
