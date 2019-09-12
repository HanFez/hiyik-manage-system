<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/11/21
 * Time: 15:41
 */
namespace App\IekModel\Version1_0;

class FolderTitle extends IekModel
{
    protected $table = 'tblFolderTitles';

    public function title() {
        return $this->belongsTo(self::$NAME_SPACE.'\PlainStyle', self::TID, self::ID);
    }

    public function personFolder(){
        return $this->belongsTo(self::$NAME_SPACE.'\PersonFolder','folder_id','folder_id');
    }
}
?>