<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\IekModel\Version1_0;

class Login extends IekModel {

    protected $table = "tblLogins";
    protected $fillable = ['account', 'is_online', 'ip', 'browser'];
    protected $hidden = ['is_active', 'is_removed', 'updated_at', 'created_at'];

}
