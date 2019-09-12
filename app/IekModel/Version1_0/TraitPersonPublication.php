<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 16-9-16
 * Time: 下午11:04
 */

namespace app\IekModel\Version1_0;


trait TraitPersonPublication {
    public static $queryOrderFormat = '
          select uid, case when total_count is null then 0 else total_count end as total 
          from 
          (select person_pubs.uid, 
            (select sum(pub_total.cnt) from 
              (select pid, pub_count.cnt
               from unnest(person_pubs.pids) as pid
                left join
                 (select pa.publication_id, count(pa.publication_id) as cnt
                  from "%s" as pa
                   where pa.is_active=\'t\' and pa.is_removed=\'f\'
                    group by pa.publication_id) as pub_count
                on pid=pub_count.publication_id 
                order by pub_count.cnt) as pub_total
          ) as total_count
          from
            (select uid, case 
             when count(pp.publication_id)>0 
             then array_agg(pp.publication_id) 
             else null end as pids
             from unnest(array[%s]) as uid 
             left join (select pu.publication_id, pu.person_id
			 from "tblPublicationPersons" as pu, "tblPublications" as p 
			 where p.is_publish=\'t\' 
			 and p.is_active=\'t\' 
			 and p.is_removed=\'f\' 
			 and pu.is_active=\'t\' 
			 and pu.is_removed=\'f\' 
			 and pu.publication_id = p.id) as pp 
             on uid=pp.person_id 
             group by uid) 
            as person_pubs) as person_views order by total desc';

    /** To get query order string for persons according to their publications.
     * Be used view, like, comment currently. The query returns [{uid, total}]
     * @param IekModel $model Supported model are PublicationViewer, PublicationLike, PublicationComment.
     * @param string $ids The person ids generator via implode(',', IdArray[]).
     * @return string
     */
    public static function getPersonOrderQuery($model, $ids) {
        $tableName = $model::getDataTable();
        return sprintf(self::$queryOrderFormat, $tableName, $ids);
    }
}