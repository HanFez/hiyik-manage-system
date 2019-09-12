<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\IekModel\Version1_0;

use Illuminate\Database\Eloquent\Model;

class PublicationSearchContent extends Model {

    protected $table = "tblPublicationSearchContents";
    protected $fillable = ['publication_id', 'content'];
    protected $hidden = ['is_active', 'is_removed', 'is_publish', 'updated_at', 'created_at'];

}
