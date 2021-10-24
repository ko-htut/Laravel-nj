<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Featured extends Model
{
     
        protected $fillable = ['featured_id','title', 'type', 'poster_path', 'genre'];


}
