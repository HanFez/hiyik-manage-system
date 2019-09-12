<?php

namespace App\IekModel\Version1_0;

use Illuminate\Database\Eloquent\Model;

class PersonCareer extends IekModel
{
    protected $table = 'tblPersonCareers';
    protected $fillable = ['person_id', 'career_id', 'is_active', 'is_removed', 'is_publish', 'updated_at', 'created_at','id'];
    
    protected $hidden = ['is_active', 'is_removed', 'is_publish', 'updated_at', 'created_at'];
}
