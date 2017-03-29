<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    //
    /*protected $fillable = [];
    protected $guarded = [];*/
    public function questions()
    {
        return $this->belongsToMany(Question::class);
    }
}
