<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\IekModel\Version1_0;

class Education extends IekModel {

    protected $table = "tblEducations";
    protected $fillable = ['id', 'begin_at', 'end_at', 'department', 'major', 'achieve', 'institution_id'];
    protected $hidden = ['is_active', 'is_removed', 'updated_at', 'created_at'];

}
