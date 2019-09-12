<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/11/21
 * Time: 15:42
 */
namespace App\IekModel\Version1_0;

class FolderDescription extends IekModel
{
    protected $table = 'tblFolderDescriptions';

    public function description() {
        return $this->belongsTo(self::$NAME_SPACE.'\PlainStyle', self::DID, self::ID);
    }

    public function personFolder(){
        return $this->belongsTo(self::$NAME_SPACE.'\PersonFolder','folder_id','folder_id');
    }
}
?>