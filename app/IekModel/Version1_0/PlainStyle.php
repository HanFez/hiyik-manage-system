<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/7/28
 * Time: 10:02
 */
namespace App\IekModel\Version1_0;

class PlainStyle extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblPlainStyles';

    public function description(){
        return $this->belongsTo(self::$NAME_SPACE.'\Description','description_id','id');
    }

    public function styleText(){
        return $this->belongsTo(self::$NAME_SPACE.'\StyleText','style_text_id','id');
    }

    public function manageLog(){
        return $this->hasMany(self::$NAME_SPACE.'\ManageLogs', 'row_id', 'id');
    }


    public function publicationDescription() {
        return $this->belongsTo(self::$NAME_SPACE.'\PublicationDescription', 'id', 'content_id')
            ->where(IekModel::CONDITION);
    }

    public function publicationTitle() {
        return $this->belongsTo(self::$NAME_SPACE.'\PublicationTitle', 'id', 'content_id')
            ->where(IekModel::CONDITION);
    }

    public function publicationImage(){
        return $this->belongsTo(self::$NAME_SPACE.'\PublicationImage', 'id', 'title_id')
            ->where(IekModel::CONDITION);
    }

    public function folderTitle(){
        return $this->belongsTo(self::$NAME_SPACE.'\FolderTitle','id','title_id')
            ->where(IekModel::CONDITION);
    }

    public function folderDescription(){
        return $this->belongsTo(self::$NAME_SPACE.'\FolderDescription','id','description_id')
            ->where(IekModel::CONDITION);
    }

    public function iwallDescription(){
        return $this->belongsTo(self::$NAME_SPACE.'\IwallDescription','id','content_id')
            ->where(IekModel::CONDITION);
    }

    public function iwallTitle(){
        return $this->belongsTo(self::$NAME_SPACE.'\IwallTitle','id','content_id')
            ->where(IekModel::CONDITION);
    }
}