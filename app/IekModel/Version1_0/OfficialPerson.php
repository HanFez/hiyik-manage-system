<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/1/18
 * Time: 15:26
 */
namespace App\IekModel\Version1_0;

class OfficialPerson extends IekModel
{
    protected $table = 'tblOfficialPersons';

    public static function notifier(){
        $person = OfficialPerson::where(IekModel::CONDITION)
            ->where('group_name','HIYIK')
            ->value('person_id');
        return $person;
    }
}
?>