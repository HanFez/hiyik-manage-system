<?php

namespace App\Http\Controllers\Admin;

use App\IekModel\Version1_0\Avatar;
use App\IekModel\Version1_0\Comment;
use App\IekModel\Version1_0\Description;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Images;
use App\IekModel\Version1_0\Message;
use App\IekModel\Version1_0\Name;
use App\IekModel\Version1_0\Nick;
use App\IekModel\Version1_0\OrderComment;
use App\IekModel\Version1_0\OrderCommentImage;
use App\IekModel\Version1_0\OrderCommentText;
use App\IekModel\Version1_0\PlainStyle;
use App\IekModel\Version1_0\Signature;
use App\IekModel\Version1_0\Tag;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SeeReasonController extends Controller
{
    /**
     * 作品标题、作品描述、收藏夹的被禁原因
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function seeTitle($id){
        $data = PlainStyle::with(['manageLog'=>
            function($query) {
                $query->orderBy(IekModel::CREATED,'desc')
                    ->with('reason')
                    ->with('operator');
            }])
            ->with('description')
            ->with('styleText')
            ->find($id);
        return view('reason.plain',compact('data'));
    }

    /**
     *作品标签的被禁原因
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function seeTag($id){
        $data = Tag::with(['manageLog'=>
            function($query){
                $query->orderBy(IekModel::CREATED,'desc')
                    ->with('reason')
                    ->with('operator');
            }])
            ->find($id);
        return view('reason.tag',compact('data'));
    }

    /**
     * 作品图片的被禁原因
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function seeImage($id){
        $data = Images::with(['manageLog'=>
            function($query){
                $query->orderBy(IekModel::CREATED,'desc')
                    ->with('reason')
                    ->with('operator');
            }])
            ->find($id);
        return view('reason.image',compact('data'));
    }

    /**
     * 用户昵称的被禁原因
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function seeNick($id){
        $data = Nick::with(['manageLog'=>
            function($query){
                $query->orderBy(IekModel::CREATED,'desc')
                    ->with('reason')
                    ->with('operator');
            }])
            ->find($id);
        return view('reason.nick',compact('data'));
    }

    /**
     * 用户头像的被禁原因
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function seeAvatar($id){
        $data = Avatar::with(['manageLog'=>
            function($query){
                $query->orderBy(IekModel::CREATED,'desc')
                    ->with('reason')
                    ->with('operator');
            }])
            ->find($id);
        return view('reason.avatar',compact('data'));
    }

    /**
     * 用户签名签名的被禁原因
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function seeSignature($id){
        $data = Signature::with(['manageLog'=>
            function($query){
                $query->orderBy(IekModel::CREATED,'desc')
                    ->with('reason')
                    ->with('operator');
            }])
            ->find($id);
        return view('reason.signature',compact('data'));
    }

    /**
     * 评论举报的被禁原因
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function seeComment($id){
        $data = Comment::with(['manageLog'=>
            function($query){
                $query->orderBy(IekModel::CREATED,'desc')
                    ->with('reason')
                    ->with('operator');
            }])
            ->find($id);
        return view('reason.comment',compact('data'));
    }

    /**
     * 订单评论的被禁原因
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function seeCommentText($id){
        $data = OrderCommentText::with(['manageLog'=>
            function($query){
                $query->orderBy(IekModel::CREATED,'desc')
                    ->with('reason')
                    ->with('operator');
            }])
            ->find($id);
        return view('reason.orderComment',compact('data'));
    }
    /**
     * 订单评论图片的被禁原因
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function seeCommentImage($id){
        $data = OrderCommentImage::with(['manageLog'=>
            function($query){
                $query->orderBy(IekModel::CREATED,'desc')
                    ->with('reason')
                    ->with('operator');
            }])
            ->find($id);
        return view('reason.image',compact('data'));
    }
}
