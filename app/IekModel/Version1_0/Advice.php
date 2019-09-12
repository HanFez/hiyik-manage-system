<?php
//
// +------------------------------------------------------------------------+
// | PHP Version 5                                                          |
// +------------------------------------------------------------------------+
// | Copyright (c) All rights reserved.                                     |
// +------------------------------------------------------------------------+
// | File:                                                                  |
// +------------------------------------------------------------------------+
// | Author:                                                                |
// +------------------------------------------------------------------------+
//
// $Id$
//

namespace App\IekModel\Version1_0;


/**
 * @author       Rich
 */
class Advice extends IekModel
{

    /**
     * @var  advice_id    INT4
     * @var  person_id    INT4
     * @var  title        String
     * @var  content      String
     * @var  created_at   timestamps
     * @var  updated_at   timestamps
     * @var  memo         String
     */

    // public $timestamps = false;
    // public $incrementing = false;
    public $primaryKey = 'id';
    protected $table = 'tblAdvices';
    protected $fillable = ['id','content','email'];

    public function adviceHandle() {
        return $this->hasOne(self::$NAME_SPACE.'\AdviceHandle', self::ADVICE_ID, self::ID);
    }
    
    public function person() {
        return $this->belongsTo(self::$NAME_SPACE.'\Person', self::UID,self::ID);
    }

    public function isExist($id){
        $count = self::where(self::ID,$id)
            ->count();
        return $count == 0 ? false : true;
    }

}

?>
