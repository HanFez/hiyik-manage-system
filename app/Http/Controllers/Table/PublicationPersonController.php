<?php

namespace App\Http\Controllers\Table;

use App\IekModel\Version1_0\Statistics\TotalPersonStatistic;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\PublicationPerson;
use App\IekModel\Version1_0\PublicationViewer;
use App\IekModel\Version1_0\PublicationLike;
use App\IekModel\Version1_0\PublicationComment;
use App\IekModel\Version1_0\PublicationOfficial;

class PublicationPersonController extends Controller
{
    /** get count with given personId
     * @param Request $request
     * @param personId
     * @return count
     */
    public static function getPersonPublicationStatistics($uid) {
        if(is_null($uid)) {
            return false;
        }
        $data = TotalPersonStatistic::where('uid', $uid)->first();
//
//        $likeTotal = PublicationPerson::getPersonPublicationTotal(PublicationLike::class, $uid);
//        $officialTotal = PublicationPerson::getPersonPublicationTotal(PublicationOfficial::class, $uid);
//        $viewTotal = PublicationPerson::getPersonPublicationTotal(PublicationViewer::class, $uid);
//        $commentTotal = PublicationPerson::getPersonPublicationTotal(PublicationComment::class, $uid);
//        $data = new \stdClass();
//        $data->like = $likeTotal;
//        $data->view = $viewTotal;
//        $data->comment = $commentTotal;
//        $data->official = $officialTotal;
//        dd($data);
        return $data;
    }

}
