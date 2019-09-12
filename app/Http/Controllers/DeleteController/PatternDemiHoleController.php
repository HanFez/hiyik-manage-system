<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/5/8
 * Time: 9:40
 */
namespace App\Http\Controllers\Table;

use App\Http\Controllers\Admin\PrivilegeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\DemiFrame;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Pattern;
use App\IekModel\Version1_0\PatternDemiHole;
use App\IekModel\Version1_0\PatternDemiShape;
use App\IekModel\Version1_0\Shape;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatternDemiHoleController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 添加页面
     */
    public function add(){
        $err = new Error();
        $pattern = Pattern::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        if(is_null($pattern)){
            $pattern = null;
        }
        $demiHole = DemiFrame::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $shape = Shape::where(IekModel::CONDITION)
            ->where(IekModel::LEVEL,1)
            ->where(IekModel::OFFICIAL,true)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $data = new \stdClass();
        $data -> pattern = $pattern;
        $data -> demiHole = $demiHole;
        $data -> shape = $shape;
        $err->setData($data);
        return view('admin.systemSetting.product.patternDemiHole',['result'=>$err]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 保存数据
     */
    public function create(Request $request){
        $err = new Error();
        DB::beginTransaction();
        try{
            $err = $this->createParam();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
        }
        return view('message.formResult',['result'=>$err]);
    }
    /**
     * 保存数据
     */
    public function createParam(){
        $err = new Error();
        $demiHoles = request()->except('_token');
//        dd($demiHoles);
        $patternDemiHole = new PatternDemiHole();
        $patternDemiHole->pattern_id = $demiHoles['patternId'];
        foreach($demiHoles['demiHole'] as $demiHole){
            $patternDemiHole->demi_frame_id = $demiHole['demiHoleId'];
            $patternDemiHole->price = $demiHole['price'];
            $patternDemiHole->currency = $demiHole['currency'];
            $patternDemiHole->unit = $demiHole['unit'];
            $patternDemiHole->save();
            $err->patternDemiHole = $patternDemiHole;
            foreach($demiHole['shapes'] as $shape){
                $patternDemiShape = new PatternDemiShape();
                $patternDemiShape->pattern_demi_hole_id = $patternDemiHole->id;
                $patternDemiShape->shape_id = $shape['shapeId'];
                $patternDemiShape->price = $shape['price'];
                $patternDemiShape->currency = $shape['currency'];
                $patternDemiShape->unit = $shape['unit'];
                $patternDemiShape->save();
                $err->patternDemiShape = $patternDemiShape;
            }
        }
        return $err;
    }
    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 修改页面
     */
    public function edit($id){
        $err = new Error();
        $pattern = Pattern::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $demiHole = DemiFrame::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $shape = Shape::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $patternDemiHole = PatternDemiHole::where(IekModel::ID,$id)
            ->with('patternDemiShape')
            ->where(IekModel::CONDITION)
            ->get();
        $data = new \stdClass();
        $data->pattern = $pattern;
        $data->demiHole = $demiHole;
        $data->shape = $shape;
        $data->patternDemiHole = $patternDemiHole;
        if($patternDemiHole->isEmpty()){
            $data = null;
        }
        $err->setData($data);
        return view('admin.systemSetting.product.patternDemiHole',['result'=>$err]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 保存数据
     */
    public function modify(Request $request , $id){
        $err = new Error();
        try{
            DB::beginTransaction();
            PatternDemiHole::where(IekModel::ID,$id)
                ->update([
                    IekModel::IS_MODIFY => true
                ]);
            $err = $this->createParam();
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
        }
        return view('message.formResult',['result'=>$err]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * 列表页
     */
    public function lists(Request $request){
        $tableName = 'tblPatternDemiHoles';
        $start = $request->input('start');
        $getAll = new PrivilegeController();
        $res = $getAll->getAllList($request , $tableName);
        if(is_null($start)){
            $params = new \stdClass();
            $params-> type = 'patternDemiHole';
            $params-> url = 'patternDemiHole';
            $field = $res;
            return view('tableData.index',compact('field', 'params'));
        }
        return response()->json($res);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\Views
     * 删除
     */
    public function del(){
        $model = new PatternDemiHole();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 恢复
     */
    public function recover(){
        $model = new PatternDemiHole();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
}