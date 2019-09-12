<?php

namespace App\IekModel\Version1_0;



class PersonEducation extends IekModel
{
    protected $table = 'tblPersonEducations';
    protected $fillable = ['person_id', 'education_id', 'is_active', 'is_removed', 'is_publish', 'updated_at', 'created_at','id'];
    
    protected $hidden = ['is_active', 'is_removed', 'is_publish', 'updated_at', 'created_at'];
}
