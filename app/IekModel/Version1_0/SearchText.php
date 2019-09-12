<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-9-2
 * Time: 上午9:12
 */

namespace App\IekModel\Version1_0;


class SearchText extends IekModel {
    protected $table = 'tblSearchTexts';

    public static function insertRow($contentId, $content) {
        $err = new Error();
        $condition = self::CONDITION;
        array_push($condition, [self::CONTENT_ID, '=', $contentId]);
        array_push($condition, [self::CONTENT, '=', $content]);
        $text = self::where($condition)->first();
        if(is_null($text)) {
            $row = new self();
            $row->{self::CONTENT_ID} = $contentId;
            $row->{self::CONTENT} = $content;
            $row->save();
            $err->data = $row;
        } else {
            $err->setError('Exist');
        }
        return $err;
    }
}