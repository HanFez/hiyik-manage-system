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


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
* @author       Rich
*/
class FilterKeyword extends IekModel
{
    const CACHE_KEY = 'filterKeywords';
    
    /**
    * @var  id           INT8
    * @var  keyword      String
    * @var  replace_with String
    * @var  updated_at   timestamps
    * @var  created_at   timestamps
    * @var  hit          INT8
    * @var  is_active    Boolean
    * @var  is_removed   Boolean
    */

    public $primaryKey = 'id';
    protected $table = 'tblFilterWords';
    protected $fillable = array('word', 'replace_with');

    /**
     * Filter the content whether contain any filter keywords.
     * if contain any, add keyword count, and set isPassed to false, else set isPassed to true.
     * @param $content string|array The content need to filter.
     * @return \stdClass The filtered content and status.
     *                  bool $result->isPassed Indicator the filter status.
     *                  bool $result->isReplaced Indicator whether the keyword be replaced with special char.
     *                  string $result->content The string content replaced or not, indicate with isReplaced.
     *                  array $result->words The filtered words with count, [word => count,...].
     */
    static public function filter($content, $isReplaced = false) {
        $keywords = Cache::get(self::CACHE_KEY, function() {
            $words = self::where(IekModel::CONDITION)->get();
            return $words;
        });
//        $filter = $content.'%';
//        $keywords = self::where(['is_active' => true, 'is_removed' => false])
//            ->where('word', 'like', $filter)->increament('hit')->get();
        //Log::info('Words:', dump($keywords));
        $filterResult = array();
        foreach($keywords as $keyword) {
            $count = substr_count($content, $keyword->word);
            if($count > 0) {
                //$keyword->hit += $count;
                $keyword->increment('hit', $count);
                if($isReplaced) {
                    $content = str_ireplace($keyword->word, $keyword->replace_with, $content);
                }
                array_push($filterResult, [$keyword->word => $count]);
            }
        }
        $result =  new \stdClass();
        if(count($filterResult) > 0) {
            $result->isPassed = false;
        } else {
            $result->isPassed = true;
        }
        $result->isReplaced = $isReplaced;
        $result->content = $content;
        $result->words = $filterResult;
        return $result;
    }

}

?>
