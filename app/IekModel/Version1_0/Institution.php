<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\IekModel\Version1_0;

class Institution extends IekModel {

    protected $table = "tblInstitutions";
    protected $fillable = ['name'];
    protected $hidden = ['is_active', 'is_removed', 'updated_at', 'created_at'];

}
