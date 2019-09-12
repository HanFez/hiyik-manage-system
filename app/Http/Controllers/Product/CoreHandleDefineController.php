<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/14
 * Time: 12:08
 */
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Product\Handle;
use App\IekModel\Version1_0\Product\HCategory;
use App\IekModel\Version1_0\Product\PosterCoreHandleDefine;
use Illuminate\Support\Facades\DB;

class CoreHandleDefineController extends Controller
{
/**
     * CoreHandleDefine's add page
     */
    public function add(){
        $handles = Handle::where(IekModel::CONDITION)->get();
        $hcs = HCategory::where(IekModel::CONDITION)->get();
        return view('product.define.coreHandleDefine',['handles'=>$handles,'hcs'=>$hcs]);
    }
    /**
     * CoreHandleDefine's add data deal
     */
    public function create(){
        $req = request()->all();
        //验证
        $verify = $this->verify($req,null);
        if($verify) return $verify;
        try{
            DB::beginTransaction();
            $core = new PosterCoreHandleDefine();
            $core->name = $req['name'];
            $core->description = $req['des'];
            $core->handle_id = $req['handleId'];
            $core->category_id = $req['categoryId'];
            $core->phy_width_max = $req['phyWidthMax'];
            $core->phy_width_min = $req['phyWidthMin'];
            $core->phy_height_max = $req['phyHeightMax'];
            $core->phy_height_min = $req['phyHeightMin'];
            $core->phy_depth_max = $req['phyDepthMax'];
            $core->phy_depth_min = $req['phyDepthMin'];
            $core->dpi_max = $req['dpiMax'];
            $core->dpi_min = $req['dpiMin'];
            $core->price = $req['price'];
            $core->price_unit = $req['priceUnit'];
            $core->is_full_color = $req['isFullColor'];
            $core->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"保存成功","保存失败",$core);
    }
    /**
     * CoreHandleDefine's all data list
     */
    public function listCHD(){
        $model = new PosterCoreHandleDefine();
        $type = 'core-handle-define';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * CoreHandleDefine's data detail
     */
    public function showCHD(){
        //
    }
    /**
     * CoreHandleDefine's edit page
     */
    public function edit($id){
        $handles = Handle::where(IekModel::CONDITION)->get();
        $hcs = HCategory::where(IekModel::CONDITION)->get();
        $action = 'edit';
        $chd = PosterCoreHandleDefine::find($id);
        return view('product.define.coreHandleDefine',compact('handles','action','chd','hcs'));
    }
    /**
     * CoreHandleDefine's edit data deal
     */
    public function update($id){
        $req_u = request()->all();
        //验证
        $verify = $this->verify($req_u,$id);
        if($verify) return $verify;
        try{
            DB::beginTransaction();
            $core = PosterCoreHandleDefine::where(IekModel::ID,$id)
                ->update([
                    IekModel::NAME => $req_u['name'],
                    IekModel::DESC => $req_u['des'],
                    IekModel::HANDLE_ID => $req_u['handleId'],
                    IekModel::CATEGORY_ID => $req_u['categoryId'],
                    IekModel::WIDTH_MAX => $req_u['phyWidthMax'],
                    IekModel::WIDTH_MIN => $req_u['phyWidthMin'],
                    IekModel::HEIGHT_MAX => $req_u['phyHeightMax'],
                    IekModel::HEIGHT_MIN => $req_u['phyHeightMin'],
                    IekModel::DEPTH_MAX => $req_u['phyDepthMax'],
                    IekModel::DEPTH_MIN => $req_u['phyDepthMin'],
                    IekModel::DPI_MAX => $req_u['dpiMax'],
                    IekModel::DPI_MIN => $req_u['dpiMin'],
                    IekModel::PRICE => $req_u['price'],
                    IekModel::PRICE_UNIT => $req_u['priceUnit'],
                    IekModel::IS_FULL_COLOR => $req_u['isFullColor']
                ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"修改成功","修改失败",$core);
    }
    /**
     * delete CoreHandleDefine's record
     */
    public function del(){
        $model = new PosterCoreHandleDefine();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover CoreHandleDefine's record
     */
    public function cover(){
        $model = new PosterCoreHandleDefine();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
    /**
     * validate
     */
    public function verify($req,$id){
        $hid = $req['handleId'];
        $cid = $req['categoryId'];
        $unit = $req['priceUnit'];
        if(is_null($hid)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择工艺",$hid);
        }
        if(is_null($cid)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择工艺分类",$cid);
        }
        if(is_null($unit)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请添加单位",$unit);
        }
        $has = PosterCoreHandleDefine::where(IekModel::NAME,$req['name'])
            ->where(IekModel::HANDLE_ID,$hid)
            ->where(IekModel::CATEGORY_ID,$cid)
            ->where(IekModel::IS_FULL_COLOR,$req['isFullColor'])
            ->first();
        if(!is_null($has)){
            if(is_null($id)){
                return $this->viewReturn(Errors::EXIST,'此记录已添加',$req['name']);
            }else{
                if($has->id != $id){
                    return $this->viewReturn(Errors::EXIST,'此记录已添加',$req['name']);
                }
            }
        }
    }
}
?>