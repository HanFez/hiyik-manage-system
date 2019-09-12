<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/4
 * Time: 17:02
 */
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Product\Material;
use App\IekModel\Version1_0\Product\MaterialSection;
use Illuminate\Support\Facades\DB;

class MaterialSectionController extends Controller
{
/**
     * MaterialSection's add page
     */
    public function add(){
        $materials = Material::where(IekModel::CONDITION)->get();
        return view('product.produce.materialSection',['materials'=>$materials]);
    }
    /**
     * MaterialSection's add data deal
     */
    public function create(){
        $req = request()->all();
        //验证
        $verify = $this->verify($req,null);
        if($verify) return $verify;
        if(isset($req['arr'])){
            $bezier = $req['arr']['bezier'];
            $viewportNew = $req['arr']['viewportNew'];
        }else{
            $err = new Error();
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage("请输入图形数据");
            return view('message.formResult',['err'=>$err]);
        }
        try{
            DB::beginTransaction();
            $ms = new MaterialSection();
            $ms->material_id = $req['material'];
            $ms->bezier = $bezier;
            $ms->viewport = $viewportNew;
            $ms->phy_width = $req['phy_width'];
            $ms->phy_height = $req['phy_height'];
            $ms->position = $req['position'];
            $ms->perspective = $req['perspective'];
            $ms->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"保存成功","保存失败",$ms);
    }
    /**
     * MaterialSection's all data list
     */
    public function listMaterialSection(){
        $model = new MaterialSection();
        $type = 'material-section';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * MaterialSection's data detail
     */
    public function showMaterialSection(){
        //
    }
    /**
     * MaterialSection's edit page
     */
    public function edit($id){
        $materials = Material::where(IekModel::CONDITION)->get();
        $ms = MaterialSection::with('material')->find($id);
        $action = 'edit';
        return view('product.produce.materialSection',compact('materials','ms','action'));
    }
    /**
     * MaterialSection's edit data deal
     */
    public function update($id){
        $req_u = request()->all();
        $verify = $this->verify($req_u,$id);
        if($verify) return $verify;
        try{
            DB::beginTransaction();
            $ms = MaterialSection::where(IekModel::ID,$id)
                ->update([
                    IekModel::MATERIAL_ID => $req_u['material'],
                    IekModel::BEZIER => $req_u['bezier'],
                    IekModel::VIEWPORT => $req_u['viewport'],
                    IekModel::PHY_WIDTH => $req_u['phy_width'],
                    IekModel::PHY_HEIGHT => $req_u['phy_height'],
                    IekModel::POSITION => $req_u['position'],
                    IekModel::PERSP => $req_u['perspective']
                ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"修改成功","修改失败",$ms);
    }
    /**
     * delete MaterialSection's record
     */
    public function del(){
        $model = new MaterialSection();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover MaterialSection's record
     */
    public function cover(){
        $model = new MaterialSection();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
    /**
     * add canvas
     * return view
     */
    public function paperView(){
        return view('product.produce.getBezier');
    }
    /**
     *return bezier data
     */
    public function getBezier(){
        $req = request()->all();
        $err = new Error();
        if(count($req) !== 4){
            $err->setError(Errors::FAILED);
            $err->setMessage('数据格式不对的');
            return response()->json($err);
        }
        $str = $req['str'];
        $str = str_replace('fill="none"','',$str);
        /*preg_match('/<g .*?>/',$str,$str1);
        preg_match('/<path .*?\/><\/g>/',$str,$str2);*/
        //$svg = array_merge($str1,$str2);
        //$svg = implode($svg);
        $points = $req['points'];
        $viewport = $req['viewport'];
        $viewportNew = new \stdClass();
        $viewportNew->x = 0;
        $viewportNew->y = 0;
        $viewportNew->width = intval($viewport['width']);
        $viewportNew->height = intval($viewport['height']);
        $bezier = [];
        foreach($points as $k=> $point){
            $bezier[$k]['x'] = abs(intval($point['x']) - intval($viewport['x']));
            $bezier[$k]['y'] = abs(intval($point['y']) - intval($viewport['y']));
        }
        $arr = [];
        $arr['bezier'] = json_encode($bezier);
        $arr['viewportNew'] = json_encode($viewportNew);
        $arr['svg'] = $str;
        if(!is_null($arr)){
            $err->setError(Errors::OK);
            $err->setMessage("提交成功");
            $err->setData($arr);
            return response()->json($err);
        }
        //dd($bezier);
    }
    /**
     * validate
     */
    public function verify($req,$id){
        $mid = $req['material'];
        //$bezier = $req['bezier'];
        //$viewport = $req['viewport'];
        $width = $req['phy_width'];
        $height = $req['phy_height'];
        $position = $req['position'];
        if(is_null($mid)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择材料",$mid);
        }
        if(is_null($width)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请添加实际宽度",$width);
        }
        if(is_null($height)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请添加实际高度",$height);
        }
        if(is_null($position)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择摆放方位",$position);
        }
        $section = MaterialSection::where(IekModel::MATERIAL_ID,$mid)
            ->where(IekModel::PHY_WIDTH,$width)
            ->where(IekModel::PHY_HEIGHT,$height)
            ->where(IekModel::POSITION,$position)
            ->where(IekModel::PERSP,$req['perspective'])
            ->first();
        if(is_null($id)){
            if(!is_null($section)){
                return $this->viewReturn(Errors::EXIST,"此记录已存在",$mid);
            }
        }else{
            if(!is_null($section) && $section->id != $id){
                return $this->viewReturn(Errors::EXIST,"此记录已存在",$mid);
            }
        }
    }
}
?>