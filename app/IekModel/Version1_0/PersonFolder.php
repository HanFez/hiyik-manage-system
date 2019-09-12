<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/11/21
 * Time: 15:55
 */
namespace App\IekModel\Version1_0;

class PersonFolder extends IekModel
{
    protected $table = 'tblPersonFolders';

    public function person() {
        return $this->belongsTo(self::$NAME_SPACE.'\Person', 'person_id', 'id');
    }
}
?>