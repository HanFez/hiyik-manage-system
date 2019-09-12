<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-6-20
 * Time: 下午1:31
 */

namespace App\IekModel\Version1_0;


class Career extends IekModel
{

    /** table columns:
     * @var id                  INT8
     * @var begin_at            Timestamp
     * @var end_at              Timestamp
     * @var department          String
     * @var duty                String
     * @var content             String
     * @var achieve             String
     * @var company_id          INT8
     * @var created_at          Timestamp
     * @var updated_at          Timestamp
     * @var is_active           Boolean
     * @var is_removed          Boolean
     */
    public $table = 'tblCareers';
    protected $fillable=['id','begin_at','end_at','department','duty','content','achieve','company_id'];
    protected $hidden=['is_active', 'is_removed', 'updated_at', 'created_at'];

    public function company() {
        return $this->belongsTo(self::$NAME_SPACE.'\Company', 'company_id', 'id');
    }
}