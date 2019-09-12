<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/2/16
 * Time: 16:23
 */
namespace App\Http\Controllers\Table;


use AliSdk\ShowapiRequest;
use App\Events\NotifyEvent;
use App\Http\Controllers\Controller;
use App\IekModel\EventArguments\NotifyEventArguments;
use App\IekModel\Version1_0\Company;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\OrderCommentImage;
use App\IekModel\Version1_0\OfficialPerson;
use App\IekModel\Version1_0\Order;
use App\IekModel\Version1_0\OrderComment;
use App\IekModel\Version1_0\OrderCommentImageNorm;
use App\IekModel\Version1_0\OrderCommentText;
use App\IekModel\Version1_0\OrderPlatformMemo;
use App\IekModel\Version1_0\OrderReply;
use App\IekModel\Version1_0\OrderReplyContent;
use App\IekModel\Version1_0\OrderShip;
use App\IekModel\Version1_0\Product;
use App\IekModel\Version1_0\Reason;
use App\IekModel\Version1_0\ShipMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public static $ImageFormats = [
        'JPEG' => 'jpeg',
        'JPG' => 'jpg',
        'BMP' => 'bmp',
        'GIF' => 'gif',
        'ICO' => 'ico',
        'PSD' => 'psd'
    ];

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * order list
     */
    public function orderList(){
        $result = new Error();
        $orderType = $this->orderType();
        $type = request()->input('type');
        $take = request()->input('take');
        $skip = request()->input('skip');
        $title = request()->input('title');
        if(!is_null($title)){
            $no = $this->getOrderByName($title);
        }
        switch($type){
            case 'unpaid':
                $order = $this->unPaid();
                if(!is_null($title)){
                    $order = $order->whereIn(IekModel::ORDER_NO,$no);
                }
                break;
            case 'paid':
                $param = request()->input('param');
                if(is_null($param)){
                    $order = $this->paid();
                    /*$ono = [];//已申请退款的订单id盒子
                    foreach($order as $or){
                        $re = RefundRequest::with('refundRequestHandle')
                            ->where(IekModel::ORDER_ID,$or->id)
                            ->first();
                        if(!is_null($re)){
                            $ono[] = $or->id;
                        }
                    }
                    $result->ono = $ono;*/
                }
                if($param == 'no_material'){
                    $order = $this->noMaterial();
                    $result->params = $param;
                }
                if($param == 'already_refund_request'){
                    $order = $this->alreadyRequestRefund();
                    $result->params = $param;
                }
                if($param == 'urge'){
                    $order = $this->urge();
                    $result->params = $param;
                }
                if(!is_null($title)){
                    $order = $order->whereIn(IekModel::ORDER_NO,$no);
                }
                break;
            case 'produce':
                $order = $this->producing();
                if(!is_null($title)){
                    $order = $order->whereIn(IekModel::ORDER_NO,$no);
                }
                break;
            case 'accepts':
                $order = $this->accept();
                if(!is_null($title)){
                    $order = $order->whereIn(IekModel::ORDER_NO,$no);
                }
                break;
            case 'unDeliver':
                $order = $this->unDeliver();
                if(!is_null($title)){
                    $order = $order->whereIn(IekModel::ORDER_NO,$no);
                }
                break;
            case 'delivered':
                $order = $this->delivered();
                if(!is_null($title)){
                    $order = $order->whereIn(IekModel::ORDER_NO,$no);
                }
                break;
            case 'end':
                $order = $this->close();
                if(!is_null($title)){
                    $order = $order->whereIn(IekModel::ORDER_NO,$no);
                }
                break;
            case 'finish':
                $param = request()->input('param');
                if(is_null($param)){
                    $order = $this->finish();
                }
                if($param == 'have_comment'){
                    $order = $this->haveComment();
                }
                if($param == 'no_comment'){
                    $order = $this->noComment();
                }
                if($param == 'have_reason'){
                    $order = $this->haveReason();
                }
                if($param == 'no_reason'){
                    $order = $this->noReason();
                }
                if($param == 'already_reply'){
                    $order = $this->alreadyReply();
                }
                if($param == 'no_reply'){
                    $order = $this->noReply();
                }
                if(!is_null($title)){
                    $order = $order->whereIn(IekModel::ORDER_NO,$no);
                }
                break;
            default:
                $order = Order::orderBy(IekModel::UPDATED,'desc')->get();
                break;
        }
        $total = $order->count();
        if($take != null && $skip != null){
            $order = $order->slice($skip,$take);
        }
        $result->setData($order);
        $result->orderType = $orderType;
        $result->take = $take;
        $result->skip = $skip;
        $result->total = $total;
        $result->type = $type;
        $result->search = $title;
        return view('admin.order.orderList',compact('result'));
    }

    /**
     * @param $no
     * @return mixed
     * search order use order no
     */
    public function getOrderByName($no){
        $orderName = Order::where(IekModel::ORDER_NO,'like','%'.$no.'%')
            ->where(IekModel::ACTIVE,true)
            ->pluck(IekModel::ORDER_NO);
        return $orderName;
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * order detail
     */
    public function order($id){
        $err = new Error();
        $order = Order::with('platformMemo')
            ->with('score')
            ->with('orderShip.ship.company')
            ->with('personOrder.person.personNick.nick')
            ->with('orderProducts.orderProductVoucher.personVoucher.voucher')
            ->with('orderPersonVoucher.personVoucher.voucher')
            ->with(['orderReceiveInformation.receiveInformation'=> function($query){
                $query->with('name')
                    ->with('address.city')
                    ->with('phone');
            }])
            ->with(['orderStatus'=>function($query){
                $query->with('status')
                    ->with('reason');
            }])
            ->with(['orderProducts.products'=>function($query){
                $query->with('productDefine')
                    ->with('border.materialDefine')
                    ->with(['core'=>function($q){
                        $q->with('materialDefine')
                            ->with('coreHandle')
                            ->with('coreContent');
                    }])
                    ->with('frame.materialDefine')
                    ->with('front.materialDefine')
                    ->with('back.materialDefine')
                    ->with('backFacade.materialDefine')
                    ->with(['show'=>function($query){
                        $query->with('material')
                            ->with('show');
                    }])
                    ->with('productThumb.thumb.norm')
                    ->with('postMaker.maker')
                    ->with('person.personNick.nick');
            }])
            ->with(['orderPay' =>function($q){
                $q->with('thirdPay')
                    ->with('wealthPay')
                    ->with('orderReturnPay');
            }])
            ->with(['orderComment.comment'=>function($query){
                $query->with('text')
                    ->with('image.norms');
            }])
            ->with(['refundRequest'=>function($query){
                $query->with('refundRequestHandle.handleResult')
                    ->orderBy(IekModel::CREATED,'desc');
            }])
            ->find($id);
        $err->setData($order);
        $reason = Reason::getReason();
        $err -> reasons = $reason;

        return view('admin.order.order',compact('err','accessory'));
    }
    /**
     * reply view
     */
    public function replyView(){
        $err = new Error();
        $comments = OrderComment::with(['comment'=>function($query){
            $query->with('text','image');
        }])
            ->get();
        return view('admin.order.replyComment',['result'=>$err]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * reply order comment
     */
    public function reply($id,Request $request){
        $err = new Error();
        $uid = session('login.id');
        $req = request()->all();
        $orderComment = OrderComment::find($id);
        if(is_null($orderComment)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'订单未评论','reply');
        }
        if($req['content'] == '0' && $req['fileName'] == '1'){
            $err->setError(Errors::NOT_EMPTY);
            $err->setMessage('请输入文字或图片');
            return response()->json($err);
        }
        DB::beginTransaction();
        try{
            $reply = new OrderReply();
            $reply->comment_id = $id;
            $reply->comment_at = $orderComment->comment_at;
            $reply->operator_id = $uid;
            $reply->save();

            if($req['content'] != '0'){
                $commentText = new OrderCommentText();
                $haveText = $commentText->where(IekModel::CONTENT,$req['content'])->first();
                if(!is_null($haveText)){
                    $commentText = $haveText;
                }else{
                    $commentText->content = $req['content'];
                    $commentText->save();
                }
                $replyContent = new OrderReplyContent();
                $replyContent->reply_id = $reply->id;
                $replyContent->content_type = 0;
                $replyContent->content_id = $commentText->id;
                $replyContent->comment_at = $orderComment->comment_at;
                $re = $replyContent->save();
            }

            if($req['fileName'] != '1'){
                $image = $this->replyImage($request);
                /*$folder    = 'files/orderComment/reply/';
                $extension = $file->getClientOriginalExtension();
                $md5       = hash('md5',File::get($file));//给文件一个校验码
                $width     = getimagesize($file->getRealPath())[0];
                $height    = getimagesize($file->getRealPath())[1];
                $length    = $file->getClientSize();
                $file_name = $file->getClientOriginalName();
                $uri       = $folder.$md5.'.'.$extension;
                $image     = new OrderCommentImage();
                $image -> extension = $extension;
                $image -> md5       = $md5;
                $image -> width     = $width;
                $image -> height    = $height;
                $image -> length    = $length;
                $image -> file_name = $file_name;
                $image -> uri       = $uri;
                $image->save();*/
                $replyContent = new OrderReplyContent();
                $replyContent->reply_id = $reply->id;
                $replyContent->content_type = 1;
                $replyContent->content_id = $image->data->id;
                $replyContent->comment_at = $orderComment->comment_at;
                $re = $replyContent->save();
            }

            //通知
            $params = new \stdClass();
            $params->action = 'replied';
            $params->lang = 'order comment replied';
            $params->fromId = OfficialPerson::notifier();
            $params->toId = $orderComment->person_id;
            $params->targetId = $reply->id;
            $args = new NotifyEventArguments(null, \App\IekModel\Version1_0\Notify\OrderCommentReply::class, $params);
            event(new NotifyEvent($args));

            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        if($re){
            $err->setError(Errors::OK);
            $err->setMessage('回复成功');
        }else{
            $err->setError(Errors::FAILED);
            $err->setMessage('回复失败');
        }
        return response()->json($err);
        //return $this->curd(Errors::OK,Errors::FAILED,'回复成功','回复失败',$re);
    }

    /**
     * @param Request $request
     * @return Error|\Illuminate\Http\JsonResponse
     * reply comment to image
     */
    public function replyImage(Request $request){
        $imageFile = new ImageFileController();
        $dir = 'files/orderComment/reply/';
        $params = $imageFile->getUploadFile($request);
        if(!($params->isOk() || $params->isExist())) {
            return response()->json($params);
        }
        $status = new Error();
        if (is_null($dir)) {
            $status->setError(Errors::UNKNOWN_LOCATION);
            return $status;
        }
        if(!$params->isOk() && !$params->isExist()) {
            if(!is_null($params->data) && !is_null($params->data->content)) {
                unset($params->data->content);
            }
            return $params;
        }
        $fileParams = $params->data;
        $md5 = $fileParams->md5;
        if(is_null($md5)) {
            $md5 = hash('md5', $fileParams->content);
        }
        $ext = strtolower($fileParams->extension);
        if($imageFile->isEndWith($dir,'/')) {
            $destFile = $dir . $md5 . '.' . $ext;
        } else {
            $destFile = $dir . '/' . $md5 . '.' . $ext;
        }
        if (!is_null($ext) && in_array($ext, $imageFile::$ImageFormats)) {
            $img = null;
            try {
                $img = new \Imagick();
                $img->readImageBlob($fileParams->content);
                $width = $img->getImageWidth();
                $height = $img->getImageHeight();
            } catch (\Exception $ex) {
                Log::info('image exception: '.$ex->getMessage());
                Log::info('Trace: '.$ex->getTraceAsString());
                $status->setError(Errors::UNKNOWN_IMAGE);
                return $status;
            }
            if (!Storage::exists($destFile)) {
                Storage::put($destFile, $fileParams->content);
            } else {
                $status->setError(Errors::EXIST);
            }
            $native = new \stdClass();
            $native->width = $width;
            $native->height = $height;
            $native->uri = $destFile;
            $native->md5 = $md5;
            $native->name = $fileParams->name;
            $native->length = $fileParams->length;
            $native->content = $img->getImageBlob();
            $native->extension = $ext;
            $status->data = $native;
            if(!is_null($img)) {
                $img->destroy();
            }
        } else {
            $status->setError(Errors::UNKNOWN_IMAGE);
        }
        unset($fileParams->content);
        if($status->isOk() || $status->isExist()) {
            $md5 = $status->data->md5;
            $image = null;
            $norms = null;
            if(!OrderCommentImage::isHashExist($md5)) {
                $image = $imageFile->insertImageRelation(OrderCommentImage::class,$status->data);
                $norms = $imageFile->makeImageNorms(OrderCommentImageNorm::class, $image->id, $status->data->content);
                $norms = $norms->data;
            } else {
                $image = OrderCommentImage::getImageByHash($md5);
            }
            if(!is_null($image)) {
                if(is_null($norms)) {
                    if (OrderCommentImageNorm::isImageExists($image->id)) {
                        $norms = $image->norms()
                            ->where(IekModel::CONDITION)
                            ->get()->each(function($item, $key) {
                                $norm = $item->norm;
                                unset($item->norm);
                                $item->name = $norm->width.'_'.$norm->height;
                            });
                    } else {
                        $content = $status->data->content;
                        if (!is_null($content)) {
                            $norms = $imageFile->makeImageNorms(new OrderCommentImageNorm(), $image->id, $content);
                            $norms = $norms->data;
                            unset($content);
                        } else {
                            $norms = null;
                        }
                    }
                }
                //$image->norms = $norms;
                $status->data = $image;
            } else {
                $status->setError(Errors::NOT_FOUND);
            }
        } else {
            $status->setError(Errors::FAILED);
        }
        return $status;
    }

    /**
     * @return mixed
     * wait pay money
     */
    public function unPaid(){
        $orders = Order::whereHas('orderStatus',
            function($q){
                $q->where(IekModel::CURRENT,true)
                    ->whereHas('status',function($q){
                        $q->where(IekModel::NAME,'waitPay');
                    });
            })
            ->with('orderStatus.status')
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $orders;
    }

    /**
     * @return mixed
     * wait product
     */
    public function paid(){
        $orders = Order::whereHas('orderStatus',
            function($q){
                $q->where(IekModel::CURRENT,true)
                    ->whereHas('status',function($q){
                        $q->where(IekModel::NAME,'waitProduct');
                    });
            })
            ->with('orderStatus.status')
            ->with(['orderProducts.products'=>function($q) {
                $q->with('border.materialDefine')
                    ->with('core.materialDefine')
                    ->with('frame.materialDefine')
                    ->with('front.materialDefine')
                    ->with('back.materialDefine')
                    ->with('backFacade.materialDefine');
            }])
            ->with(['refundRequest'=>function($query){
                $query->with('refundRequestHandle.handleResult')
                    ->orderBy(IekModel::CREATED,'desc');
            }])
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $orders;
    }

    /**
     * @return mixed
     * order material no enough
     */
    public function noMaterial(){
        $orders = Order::whereHas('orderStatus', function($q){
                $q->where(IekModel::CURRENT,true)
                    ->whereHas('status',function($q){
                        $q->where(IekModel::NAME,'waitProduct');
                    });
                })
            ->with('orderStatus.status')
            ->whereHas('orderProducts.products',function($q) {
                $q->whereHas('border.materialDefine', function($q){
                    $q->where(IekModel::REMOVED,true);
                })
                    ->orwhereHas('front.materialDefine',
                        function($q){
                            $q->where(IekModel::REMOVED,true);
                        })
                    ->orwhereHas('frame.materialDefine',
                        function($q){
                            $q->where(IekModel::REMOVED,true);
                        })
                    ->orwhereHas('back.materialDefine',
                        function($q){
                            $q->where(IekModel::REMOVED,true);
                        })
                    ->orwhereHas('backFacade.materialDefine',
                        function($q){
                            $q->where(IekModel::REMOVED,true);
                        })
                    ->orwhereHas('core.materialDefine',
                        function($q){
                            $q->where(IekModel::REMOVED,true);
                        });
            })
            ->with(['orderProducts.products'=>function($q) {
                $q->with('border.materialDefine')
                    ->with('core.materialDefine')
                    ->with('frame.materialDefine')
                    ->with('front.materialDefine')
                    ->with('back.materialDefine')
                    ->with('backFacade.materialDefine');
            }])
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $orders;
    }

    /**
     * @return mixed
     * this order request refund
     */
    public function alreadyRequestRefund(){
        $orders = Order::whereHas('orderStatus',
            function($q){
                $q->where(IekModel::CURRENT,true)
                    ->whereHas('status',function($q){
                        $q->where(IekModel::NAME,'waitProduct');
                    });
            })
            ->with('orderStatus.status')
            ->with(['orderProducts.products'=>function($q) {
                $q->with('border.materialDefine')
                    ->with('core.materialDefine')
                    ->with('frame.materialDefine')
                    ->with('front.materialDefine')
                    ->with('back.materialDefine')
                    ->with('backFacade.materialDefine');
            }])
            ->whereHas('refundRequest')
            ->with(['refundRequest'=>function($query){
                $query->with('refundRequestHandle.handleResult')
                    ->orderBy(IekModel::CREATED,'desc');
            }])
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $orders;
    }

    public function urge(){
        $orders = Order::whereHas('urge')
            ->whereHas('orderStatus',function($query){
                $query->where(IekModel::CURRENT,true)
                    ->whereHas('status',function($q){
                        $q->where(IekModel::NAME,'waitProduct');
                    });
            })
            ->with('urge')
            ->with('orderStatus.status')
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $orders;
    }

    /**
     * @return mixed
     * this order producing
     */
    public function producing(){
        $orders = Order::whereHas('orderStatus',
            function($q){
                $q->where(IekModel::CURRENT,true)
                    ->whereHas('status',function($q){
                        $q->where(IekModel::NAME,'producing');
                    });
            })
            ->with('orderStatus.status')
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $orders;
    }

    /**
     * @return mixed
     * the order wait send
     */
    public function unDeliver(){
        $orders = Order::whereHas('orderStatus',
            function($q){
                $q->where(IekModel::CURRENT,true)
                    ->whereHas('status',function($q){
                        $q->where(IekModel::NAME,'waitSend');
                    });
            })
            ->with('orderStatus.status')
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $orders;
    }
    /**
     * logistics information
     * 查询物流信息
     */
    public function logisticsInfo($id){
        $err = new Error();
        $orderShip = OrderShip::whereHas('ship.shipMessage')
            ->with('ship.shipMessage')
            ->where(IekModel::CONDITION)
            ->where(IekModel::ORDER_ID,$id)
            ->first();
        if(!is_null($orderShip)){
            $err->setData($orderShip);
        }else{
            $err->setMessage('该订单还未查询物流信息！');
            $orderShip = OrderShip::with('ship')
                ->where(IekModel::CONDITION)
                ->where(IekModel::ORDER_ID,$id)
                ->first();
            if(is_null($orderShip->ship)){
                $err->setError(Errors::INVALID_PARAMS);
                $err->setMessage('该订单还没有填写发货信息');
                return response()->json($err);
            }
            $no = $orderShip->ship->no;
            $err->orderShip = $orderShip;
            $err->no = $no;
        }
        $company = Company::where(IekModel::CONDITION)->get();
        $err->company = $company;
        return view('admin.order.logistics',['result'=>$err]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * query api
     */
    public function queryLog(){
        $err = new Error();
        $appKey = 'b6da9b4f53a541f981ba54fc4e8d5def';
        $url = "https://ali-deliver.showapi.com/showapi_expInfo?";
        $shipNum = request()->input('no');
        $shipName = request()->input('shipName');
        $url = $url.'com='.$shipName.'&nu='.$shipNum;

        $shipMessage = new ShipMessage();
        $have_message = $shipMessage->where(IekModel::SHIP_NO,$shipNum)->get();
        if(!$have_message->isEmpty()){
            $current_time = time()+8*60*60;
            $last_time = strtotime($have_message[0]->updated_at);
            if(($current_time-$last_time)/3600 >= 2){
                $ship = new ShowapiRequest($url , $appKey);
                $ship = $ship->get();//也可以换成->post()
                $ship = $ship->getBody();
                $ship = json_decode($ship);
                if(is_null($ship)){
                    $err->setError(Errors::INVALID_PARAMS);
                    $err->setMessage('查询失败');
                    return view('message.formResult',['result'=>$err]);
                }
                $result = $shipMessage->where(IekModel::SHIP_NO,$shipNum)
                    ->update([
                        IekModel::MESSAGE => json_encode($ship)
                    ]);
            }else{
                foreach($have_message as $item){
                    $msg = $item->message;
                }
                $result = json_decode($msg);
            }
            return response()->json($result);
        }else{
            $ship = new ShowapiRequest($url , $appKey);
            $ship = $ship->get();//也可以换成->post()
            $ship = $ship->getBody();
            $ship = json_decode($ship);
            if(is_null($ship)){
                $err->setError(Errors::INVALID_PARAMS);
                $err->setMessage('查询失败');
                return view('message.formResult',['result'=>$err]);
            }
            $shipMessage->ship_no = $shipNum;
            $shipMessage->message = json_encode($ship);
            $shipMessage->save();
            return response()->json($shipMessage->message);
        }
    }

    /**
     * @return mixed
     * order wait confirm
     */
    public function delivered(){
        $orders = Order::whereHas('orderStatus',
            function($q){
                $q->where(IekModel::CURRENT,true)
                    ->whereHas('status',function($q){
                        $q->where(IekModel::NAME,'waitConfirm');
                    });
            })
            ->with('orderStatus.status')
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        /*$ids = [];
        //dd($data);
        foreach($orders as $data){
            if(!is_null($data)){
                $ids[] = $data->id;
                //dd($ids);
                foreach($data->orderStatus as $dt){
                    if($dt->status->name == 'waitConfirm'){
                        $begin = strtotime($dt->created_at);
                        $end = strtotime($dt->created_at."+ 10 days");
                        $diff = $end - $begin;
                        $now = time();
                        if(date("d",$diff) == 10 || $now > $end){
                            OrderStatus::whereIn('order_id',$ids)
                                ->where('is_current',true)
                                ->update([
                                    'is_current' => false
                                ]);
                            foreach($ids as $id) {
                                $reason = new Reason();
                                $reason->reason = '发货后，超过规定时间平台自动确认收货';
                                $reason->type = 'status';
                                $reason->save();
                                $orderStatus = new OrderStatus();
                                $orderStatus->order_id = $id;
                                $orderStatus->status_id = '11de361c-381b-4a2c-b344-318326fcd887';
                                $orderStatus->is_current = true;
                                $orderStatus->operation_id = $this->manager();
                                $orderStatus->reason_id = $reason->id;
                                $orderStatus->save();
                            }
                        }
                    }
                }
            }
        }*/
        return $orders;
    }

    /**
     * @return mixed
     * order wait accept
     */
    public function accept(){
        $orders = Order::whereHas('orderStatus',
            function($q){
                $q->where(IekModel::CURRENT,true)
                    ->whereHas('status',function($q){
                        $q->where(IekModel::NAME,'waitAccept');
                    });
            })
            ->with('orderStatus.status')
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $orders;
    }

    /**
     * @return mixed
     * the order close
     */
    public function close(){
        $orders = Order::whereHas('orderStatus',
            function($q){
                $q->where(IekModel::CURRENT,true)
                    ->whereHas('status',function($q){
                        $q->where(IekModel::NAME,'close');
                    });
            })
            ->with('orderStatus.status')
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $orders;
    }

    /**
     * @return mixed
     * the order finish
     */
    public function finish(){
        $orders = Order::whereHas('orderStatus',
            function($q){
                $q->where(IekModel::CURRENT,true)
                    ->whereHas('status',function($q){
                        $q->where(IekModel::NAME,'success');
                    });
            })
            ->with('orderStatus.status')
            ->with('orderComment.reply')
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $orders;
    }

    /**
     * @return mixed
     * finish order have comment
     */
    public function haveComment(){
        $orders = Order::whereHas('orderStatus',
            function($q){
                $q->where(IekModel::CURRENT,true)
                    ->whereHas('status',function($q){
                        $q->where(IekModel::NAME,'success');
                    });
            })
            ->with('orderStatus.status')
            ->whereHas('orderComment')
            ->with('orderComment')
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $orders;
    }

    /**
     * @return mixed
     * finish order no comment
     */
    public function noComment(){
        $orders = Order::whereHas('orderStatus',
            function($q){
                $q->where(IekModel::CURRENT,true)
                    ->whereHas('status',function($q){
                        $q->where(IekModel::NAME,'success');
                    });
            })
            ->with('orderStatus.status')
            ->doesntHave('orderComment')
            ->with('orderComment')
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $orders;
    }

    /**
     * @return mixed
     * finish order have score reason
     */
    public function haveReason(){
        $orders = Order::whereHas('orderStatus',
            function($q){
                $q->where(IekModel::CURRENT,true)
                    ->whereHas('status',function($q){
                        $q->where(IekModel::NAME,'success');
                    });
            })
            ->with('orderStatus.status')
            ->whereHas('score.reason')
            ->with('score.reason')
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $orders;
    }

    /**
     * @return mixed
     * finish order have score reason
     */
    public function noReason(){
        $orders = Order::whereHas('orderStatus',
            function ($q) {
                $q->where(IekModel::CURRENT, true)
                    ->whereHas('status', function ($q) {
                        $q->where(IekModel::NAME, 'success');
                    });
            })
            ->with('orderStatus.status')
            ->doesntHave('score.reason')
            ->with('score.reason')
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED, 'desc')
            ->get();
        return $orders;
    }

    /**
     * @return mixed
     * finish order have comment and reply
     */
    public function alreadyReply(){
        $orders = Order::whereHas('orderStatus',
            function ($q) {
                $q->where(IekModel::CURRENT, true)
                    ->whereHas('status', function ($q) {
                        $q->where(IekModel::NAME, 'success');
                    });
            })
            ->with('orderStatus.status')
            ->whereHas('orderComment.reply')
            ->with('orderComment.reply')
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED, 'desc')
            ->get();
        return $orders;
    }

    /**
     * @return mixed
     * finish order have comment and no reply
     */
    public function noReply(){
        $orders = Order::whereHas('orderStatus',
            function ($q) {
                $q->where(IekModel::CURRENT, true)
                    ->whereHas('status', function ($q) {
                        $q->where(IekModel::NAME, 'success');
                    });
            })
            ->with('orderStatus.status')
            ->doesntHave('orderComment.reply')
            ->with('orderComment.reply')
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED, 'desc')
            ->get();
        return $orders;
    }
    /**
     * 订单类型
     */
    public function orderType(){
        $type = [
            'unpaid',
            'paid',
            'produce',
            'accepts',
            'unDeliver',
            'delivered',
            'end',
            'finish',
        ];
        return $type;
    }
    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * add official memo
     */
    public function addOfficialMemo($id){
        $err = new Error();
        $memo = request()->input('memo');

        DB::beginTransaction();
        try{
            $platformMemo = new OrderPlatformMemo();
            $platformMemo->order_id = $id;
            $platformMemo->operator_id = session('login.id');
            $platformMemo->memo = $memo;
            $re = $platformMemo->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'添加成功','添加失败',$re);
    }
}