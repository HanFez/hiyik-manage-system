<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-7-25
 * Time: 下午12:25
 */

namespace App\IekModel\Version1_0;
use Illuminate\Support\Facades\DB;

class ImageTone extends IekModel{

    protected $table = 'tblImageTones';
    protected $sql = <<<sql
            SELECT image_id FROM "tblImageTones" t, jsonb_array_elements(t.tone->'values') obj 
            WHERE obj->>'color'='greenYellow' ORDER BY  obj->>'p' desc
sql;

    static protected function searchTone($tone) {
        $sql = <<<sql
            SELECT image_id FROM "tblImageTones" t, jsonb_array_elements(t.tone->'values') obj 
            WHERE obj->>'color'='$tone' AND obj->>'p' >='0.0625' ORDER BY  obj->>'p' desc
sql;
        $rows = DB::select(DB::raw($sql));
        return $rows;
    }

    public function image() {
        return $this->belongsTo(self::$NAME_SPACE.'\Images', 'image_id', 'id');
    }

    public static function hasImageTone($imageId) {
        if(is_null($imageId)) {
            return false;
        }
        $count = self::where(self::IID, $imageId)
            ->where(self::CONDITION)
            ->count();
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }
}