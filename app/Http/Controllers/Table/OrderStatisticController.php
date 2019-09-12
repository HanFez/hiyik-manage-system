<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/30
 * Time: 15:58
 */
namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Order;

class OrderStatisticController extends Controller
{
    /**
     * 订单统计
     * 每日订单量
     */
    public function statistics(){
        $err = new Error();
        $status = $this->orderType();
        $type = request()->input('type');
        $title = request()->input('title');
        $take = request()->input('take');
        $time = $this->defaultTime();
        $startTime = request()->input('startTime');
        $endTime = request()->input('endTime');
        if(is_null($startTime) && !is_null($endTime)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入开始时间','startTime');
        }
        //dd($startTime.'-'.$endTime);
        switch($type){
            case 'unpaid':
                if(is_null($startTime) && is_null($endTime)){
                    $startTime = $time['weekday'];
                    $endTime   = $time['now'];
                    $data = $this->unPaid($startTime,$endTime);
                    //dd($data);
                }elseif(!is_null($startTime) && is_null($endTime)){
                    $endTime = $time['now'];
                    $data = $this->unPaid($startTime,$endTime);
                    //dd($data);
                }else{
                    $data = $this->unPaid($startTime,$endTime);
                    //dd($data);
                }
                break;
            case 'paid':
                if(!is_null($startTime) && !is_null($endTime)){
                    $data = $this->paid($startTime,$endTime);
                }elseif(!is_null($startTime) && is_null($endTime)){
                    $endTime = $time['now'];
                    $data = $this->paid($startTime,$endTime);
                }else{
                    $startTime = $time['weekday'];
                    $endTime = $time['now'];
                    $data = $this->paid($startTime,$endTime);
                }
                break;
            case 'produce':
                if(is_null($startTime) && is_null($endTime)){
                    $startTime = $time['weekday'];
                    $endTime   = $time['now'];
                    $data = $this->producing($startTime,$endTime);
                }elseif(!is_null($startTime) && is_null($endTime)){
                    $endTime = $time['now'];
                    $data = $this->producing($startTime,$endTime);
                }else{
                    $data = $this->producing($startTime,$endTime);
                }
                break;
            //case 'accepts':
            case 'unDeliver':
                if(is_null($startTime) && is_null($endTime)){
                    $startTime = $time['weekday'];
                    $endTime   = $time['now'];
                    $data = $this->unDeliver($startTime,$endTime);
                }elseif(!is_null($startTime) && is_null($endTime)){
                    $endTime = $time['now'];
                    $data = $this->unDeliver($startTime,$endTime);
                }else{
                    $data = $this->unDeliver($startTime,$endTime);
                }
                break;
            case 'delivered':
                if(!is_null($startTime) && !is_null($endTime)){
                    $data = $this->delivered($startTime,$endTime);
                }elseif(!is_null($startTime) && is_null($endTime)){
                    $endTime = $time['now'];
                    $data = $this->delivered($startTime,$endTime);
                }else{
                    $startTime = $time['weekday'];
                    $endTime = $time['now'];
                    $data = $this->delivered($startTime,$endTime);
                }
                break;
            case 'accepts':
                if(!is_null($startTime) && !is_null($endTime)){
                    $data = $this->accepts($startTime,$endTime);
                }elseif(!is_null($startTime) && is_null($endTime)){
                    $endTime = $time['now'];
                    $data = $this->accepts($startTime,$endTime);
                }else{
                    $startTime = $time['weekday'];
                    $endTime = $time['now'];
                    $data = $this->accepts($startTime,$endTime);
                }
                break;
            case 'end':
                if(!is_null($startTime) && !is_null($endTime)){
                    $data = $this->close($startTime,$endTime);
                }elseif(!is_null($startTime) && is_null($endTime)){
                    $endTime = $time['now'];
                    $data = $this->close($startTime,$endTime);
                }else{
                    $startTime = $time['weekday'];
                    $endTime = $time['now'];
                    $data = $this->close($startTime,$endTime);
                }
                break;
            case 'finish':
                if(!is_null($startTime) && !is_null($endTime)){
                    $data = $this->finish($startTime,$endTime);
                }elseif(!is_null($startTime) && is_null($endTime)){
                    $endTime = $time['now'];
                    $data = $this->finish($startTime,$endTime);
                }else{
                    $startTime = $time['weekday'];
                    $endTime = $time['now'];
                    $data = $this->finish($startTime,$endTime);
                }
                break;
            default:
                $data = Order::orderBy(IekModel::CREATED,'desc')->get();
                break;
        }
        $total = $data->count;
        $err->setData($data->data);
        $err->status = $status;
        $err->take = $take;
        $err->total = $total;
        $err->type = $type;
        $err->search = $title;
        //dd($err);
        return view('admin.order.orderChart',['result'=>$err]);
    }
    /**
     * unpaid
     */
    public function unPaid($starttime , $endtime){
        $orders = Order::whereHas('orderStatus',
            function($q){
                $q->where(IekModel::CURRENT,true)
                    ->whereHas('status',function($q){
                        $q->where(IekModel::NAME,'waitPay');
                    });
            })
            ->select('created_at','currency','price')
            ->orderBy(IekModel::CREATED,'desc')
            ->where(IekModel::CONDITION)
            ->get();
        //查询出指定时间段的未支付状态的订单
        $orders = $orders->where(IekModel::CREATED,'>=',$starttime)
            ->where(IekModel::CREATED,'<=',$endtime);
        //每天有多少条订单数
        $count = 0;
        if(!$orders->isEmpty()){
            $orders = $orders->groupBy(function($item, $key) {
                    return $item->created_at->toDateString();
                });
            foreach($orders as $k=>$order){
                $count += count($order);
            }
        }
        $err = new \stdClass();
        $err->count = $count;
        $err->data = $orders;
        return $err;
    }

    /**
     * paid
     */
    public function paid($starttime ,$endtime){
        $orders = Order::whereHas('orderStatus',
            function($q){
                $q->where(IekModel::CURRENT,true)
                    ->whereHas('status',function($q){
                        $q->where(IekModel::NAME,'waitProduct');
                    });
            })
            ->orderBy(IekModel::CREATED,'desc')
            ->where(IekModel::CONDITION)
            ->get();
        $orders = $orders->where(IekModel::CREATED,'>=',$starttime)
            ->where(IekModel::CREATED,'<=',$endtime);
        $count = 0;
        if(!$orders->isEmpty()){
            $orders = $orders->groupBy(function($item, $key) {
                return $item->created_at->toDateString();
            });
            foreach($orders as $k=>$order){
                $count += count($order);
            }
        }
        $err = new \stdClass();
        $err->count = $count;
        $err->data = $orders;
        return $err;
    }
    /**
     * producing
     */
    public function producing($starttime ,$endtime){
        $orders = Order::whereHas('orderStatus',
            function($q){
                $q->where(IekModel::CURRENT,true)
                    ->whereHas('status',function($q){
                        $q->where(IekModel::NAME,'producing');
                    });
            })
            ->orderBy(IekModel::CREATED,'desc')
            ->where(IekModel::CONDITION)
            ->get();
        $orders = $orders->where(IekModel::CREATED,'>=',$starttime)
            ->where(IekModel::CREATED,'<=',$endtime);
        $count = 0;
        if(!$orders->isEmpty()){
            $orders = $orders->groupBy(function($item, $key) {
                return $item->created_at->toDateString();
            });
            foreach($orders as $k=>$order){
                $count += count($order);
            }
        }
        $err = new \stdClass();
        $err->count = $count;
        $err->data = $orders;
        return $err;
    }
    /**
     * unDeliver
     */
    public function unDeliver($starttime ,$endtime){
        $orders = Order::whereHas('orderStatus',
            function($q){
                $q->where(IekModel::CURRENT,true)
                    ->whereHas('status',function($q){
                        $q->where(IekModel::NAME,'waitSend');
                    });
            })
            ->orderBy(IekModel::CREATED,'desc')
            ->where(IekModel::CONDITION)
            ->get();
        $orders = $orders->where(IekModel::CREATED,'>=',$starttime)
            ->where(IekModel::CREATED,'<=',$endtime);
        $count = 0;
        if(!$orders->isEmpty()){
            $orders = $orders->groupBy(function($item, $key) {
                return $item->created_at->toDateString();
            });
            foreach($orders as $k=>$order){
                $count += count($order);
            }
        }
        $err = new \stdClass();
        $err->count = $count;
        $err->data = $orders;
        return $err;
    }
    /**
     * delivered
     */
    public function delivered($starttime ,$endtime){
        $orders = Order::whereHas('orderStatus',
            function($q){
                $q->where(IekModel::CURRENT,true)
                    ->whereHas('status',function($q){
                        $q->where(IekModel::NAME,'waitConfirm');
                    });
            })
            ->orderBy(IekModel::CREATED,'asc')
            ->where(IekModel::CONDITION)
            ->get();
        $orders = $orders->where(IekModel::CREATED,'>=',$starttime)
            ->where(IekModel::CREATED,'<=',$endtime);
        $count = 0;
        if(!$orders->isEmpty()){
            $orders = $orders->groupBy(function($item, $key) {
                return $item->created_at->toDateString();
            });
            foreach($orders as $k=>$order){
                $count += count($order);
            }
        }
        $err = new \stdClass();
        $err->count = $count;
        $err->data = $orders;
        return $err;
    }
    /**
     * accepts
     */
    public function accepts($starttime ,$endtime){
        $orders = Order::whereHas('orderStatus',
            function($q){
                $q->where(IekModel::CURRENT,true)
                    ->whereHas('status',function($q){
                        $q->where(IekModel::NAME,'waitAccept');
                    });
            })
            ->orderBy(IekModel::CREATED,'desc')
            ->where(IekModel::CONDITION)
            ->get();
        $orders = $orders->where(IekModel::CREATED,'>=',$starttime)
            ->where(IekModel::CREATED,'<=',$endtime);
        $count = 0;
        if(!$orders->isEmpty()){
            $orders = $orders->groupBy(function($item, $key) {
                return $item->created_at->toDateString();
            });
            foreach($orders as $k=>$order){
                $count += count($order);
            }
        }
        $err = new \stdClass();
        $err->count = $count;
        $err->data = $orders;
        return $err;
    }
    /**
     * close
     */
    public function close($starttime ,$endtime){
        $orders = Order::whereHas('orderStatus',
            function($q){
                $q->where(IekModel::CURRENT,true)
                    ->whereHas('status',function($q){
                        $q->where(IekModel::NAME,'close');
                    });
            })
            ->orderBy(IekModel::CREATED,'desc')
            ->where(IekModel::CONDITION)
            ->get();
        $orders = $orders->where(IekModel::CREATED,'>=',$starttime)
            ->where(IekModel::CREATED,'<=',$endtime);
        $count = 0;
        if(!$orders->isEmpty()){
            $orders = $orders->groupBy(function($item, $key) {
                return $item->created_at->toDateString();
            });
            foreach($orders as $k=>$order){
                $count += count($order);
            }
        }
        $err = new \stdClass();
        $err->count = $count;
        $err->data = $orders;
        return $err;
    }
    /**
     * finish
     */
    public function finish($starttime,$endtime){
        $orders = Order::whereHas('orderStatus',
            function($q){
                $q->where(IekModel::CURRENT,true)
                    ->whereHas('status',function($q){
                        $q->where(IekModel::NAME,'success');
                    });
            })
            ->orderBy(IekModel::CREATED,'desc')
            ->where(IekModel::CONDITION)
            ->get();
        $orders = $orders->where(IekModel::CREATED,'>=',$starttime)
            ->where(IekModel::CREATED,'<=',$endtime);
        $count = 0;
        if(!$orders->isEmpty()){
            $orders = $orders->groupBy(function($item, $key) {
                return $item->created_at->toDateString();
            });
            foreach($orders as $k=>$order){
                $count += count($order);
            }
        }
        $err = new \stdClass();
        $err->count = $count;
        $err->data = $orders;
        return $err;
    }

    /**
     * 订单状态
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
     * 默认时间
     */
    public function defaultTime(){
        //当日开始、结束时间
        $beginday = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $time['beginday'] = date("Y-m-d H:i:s",$beginday);
        $enday = mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        $time['enday'] = date("Y-m-d H:i:s",$enday);
        //近7天时间
        $time['weekday'] = date('Y-m-d H:i:s', mktime(0,0,0,date('m'),date('d')-7,date('Y')));
        //近30天时间
        $time['month'] = date('Y-m-d H:i:s', mktime(0,0,0,date('m'),date('d')-30,date('Y')));
        //当月开始、结束时间
        $beginmon = mktime(0,0,0,date('m'),1,date('Y'));
        $time['beginmonth'] = date("Y-m-d H:i:s",$beginmon);
        $endmon = mktime(23,59,59,date('m'),date('t'),date('Y'));
        $time['endmonth'] = date("Y-m-d H:i:s",$endmon);
        //当前时间
        $time['now'] = date("Y-m-d H:i:s",strtotime("+8 hour"));
        //dd($beginThismonth.'-'.$time);
        return $time;
    }
}