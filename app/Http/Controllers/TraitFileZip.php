<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 1/2/18
 * Time: 2:25 PM
 */

namespace App\Http\Controllers;

use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Constants\RealProductStatus;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\ProductTraceability\RealProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

trait TraitFileZip
{
    private $ctrl_dir     = array();
    private $datasec      = array();


    /**********************************************************
     * 压缩部分
     **********************************************************/
    // ------------------------------------------------------ //
    // #遍历指定文件夹
    //
    // $archive  = new PHPZip();
    // $filelist = $archive->visitFile(文件夹路径);
    // print "当前文件夹的文件:<p>\r\n";
    // foreach($filelist as $file)
    //     printf("%s<br>\r\n", $file);
    // ------------------------------------------------------ //
    var $fileList = array();
    public function visitFile($path)
    {
        global $fileList;
        $path = str_replace("\\", "/", $path);
        $fdir = dir($path);

        while(($file = $fdir->read()) !== false)
        {
            if($file == '.' || $file == '..'){ continue; }

            $pathSub    = preg_replace("*/{2,}*", "/", $path."/".$file);  // 替换多个反斜杠
            $fileList[] = is_dir($pathSub) ? $pathSub."/" : $pathSub;
            if(is_dir($pathSub)){ $this->visitFile($pathSub); }
        }
        $fdir->close();
        return $fileList;
    }


    private function unix2DosTime($unixtime = 0)
    {
        $timearray = ($unixtime == 0) ? getdate() : getdate($unixtime);

        if($timearray['year'] < 1980)
        {
            $timearray['year']    = 1980;
            $timearray['mon']     = 1;
            $timearray['mday']    = 1;
            $timearray['hours']   = 0;
            $timearray['minutes'] = 0;
            $timearray['seconds'] = 0;
        }

        return (  ($timearray['year'] - 1980) << 25)
            | ($timearray['mon'] << 21)
            | ($timearray['mday'] << 16)
            | ($timearray['hours'] << 11)
            | ($timearray['minutes'] << 5)
            | ($timearray['seconds'] >> 1);
    }


    var $old_offset = 0;
    private function addFile($data, $filename, $time = 0)
    {
        $filename = str_replace('\\', '/', $filename);

        $dtime    = dechex($this->unix2DosTime($time));
        $hexdtime = '\x' . $dtime[6] . $dtime[7]
            . '\x' . $dtime[4] . $dtime[5]
            . '\x' . $dtime[2] . $dtime[3]
            . '\x' . $dtime[0] . $dtime[1];
        eval('$hexdtime = "' . $hexdtime . '";');

        $fr       = "\x50\x4b\x03\x04";
        $fr      .= "\x14\x00";
        $fr      .= "\x00\x00";
        $fr      .= "\x08\x00";
        $fr      .= $hexdtime;
        $unc_len  = strlen($data);
        $crc      = crc32($data);
        $zdata    = gzcompress($data);
        $c_len    = strlen($zdata);
        $zdata    = substr(substr($zdata, 0, strlen($zdata) - 4), 2);
        $fr      .= pack('V', $crc);
        $fr      .= pack('V', $c_len);
        $fr      .= pack('V', $unc_len);
        $fr      .= pack('v', strlen($filename));
        $fr      .= pack('v', 0);
        $fr      .= $filename;

        $fr      .= $zdata;

        $fr      .= pack('V', $crc);
        $fr      .= pack('V', $c_len);
        $fr      .= pack('V', $unc_len);

        $this->datasec[] = $fr;
        $new_offset      = strlen(implode('', $this->datasec));

        $cdrec  = "\x50\x4b\x01\x02";
        $cdrec .= "\x00\x00";
        $cdrec .= "\x14\x00";
        $cdrec .= "\x00\x00";
        $cdrec .= "\x08\x00";
        $cdrec .= $hexdtime;
        $cdrec .= pack('V', $crc);
        $cdrec .= pack('V', $c_len);
        $cdrec .= pack('V', $unc_len);
        $cdrec .= pack('v', strlen($filename) );
        $cdrec .= pack('v', 0 );
        $cdrec .= pack('v', 0 );
        $cdrec .= pack('v', 0 );
        $cdrec .= pack('v', 0 );
        $cdrec .= pack('V', 32 );

        $cdrec .= pack('V', $this->old_offset );
        $this->old_offset = $new_offset;

        $cdrec .= $filename;
        $this->ctrl_dir[] = $cdrec;
    }


    var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00";
    private function file()
    {
        $data    = implode('', $this->datasec);
        $ctrldir = implode('', $this->ctrl_dir);

        return   $data
            . $ctrldir
            . $this->eof_ctrl_dir
            . pack('v', sizeof($this->ctrl_dir))
            . pack('v', sizeof($this->ctrl_dir))
            . pack('V', strlen($ctrldir))
            . pack('V', strlen($data))
            . "\x00\x00";
    }


    // ------------------------------------------------------ //
    // #压缩到服务器
    //
    // $archive = new PHPZip();
    // $archive->Zip("需压缩的文件所在目录", "ZIP压缩文件名");
    // ------------------------------------------------------ //
    public function Zip($dir, $saveName)
    {
        if(@!function_exists('gzcompress')){ return; }

        ob_end_clean();
        $filelist = $this->visitFile($dir);
        if(count($filelist) == 0){ return; }

        foreach($filelist as $file)
        {
            if(!file_exists($file) || !is_file($file)){ continue; }

            $fd       = fopen($file, "rb");
            $content  = @fread($fd, filesize($file));
            fclose($fd);

            // 1.删除$dir的字符(./folder/file.txt删除./folder/)
            // 2.如果存在/就删除(/file.txt删除/)
            $file = substr($file, strlen($dir));
            if(substr($file, 0, 1) == "\\" || substr($file, 0, 1) == "/"){ $file = substr($file, 1); }

            $this->addFile($content, $file);
        }
        $out = $this->file();

        $fp = fopen($saveName, "wb");
        fwrite($fp, $out, strlen($out));
        fclose($fp);
    }


    //生成需要下载的文件夹
    public function downloadProductsMake($realProductIds){
        $err = new Error();
//        $realProductIds = ['eb38aff3-bffb-40b0-af37-62cf4cae334d'];
        if(is_null($realProductIds) || empty($realProductIds)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setData('productIds can not null or empty');
            return $err;
        }
        try{
            DB::connection('pgsql_product_traceability')->beginTransaction();
            $realProducts = RealProduct::whereIn(IekModel::ID,$realProductIds)
                ->where(IekModel::CONDITION)
                ->with('product.produceParams')
                ->where('status',RealProductStatus::WAIT_PRODUCT)
                ->with('QRImage.QRImage')
                ->get();
            if(count($realProducts) != count($realProductIds)){
                $err->setError(Errors::NOT_FOUND);
                $err->setData('some productIds invalid');
                return $err;
            }
            $cellData = [
                [
                    '编号',
                    '用户所见编号',
                    '真实产品编号',
                    '产品编号',
                    '产品宽(cm)',
                    '产品高(cm)',
                    '产品等级',
                    '装裱方式',
                    '框',
                    '卡纸',
                    '防尘玻璃',
                    '背板',
                    '画心编号',
                    '画心材料'
                ]
            ];
            foreach ($realProducts as $k=>$realProduct){
                $pro = $realProduct->product;
                foreach ($realProduct->QRImage as $qr){
                    if($qr->type == 'origin'){
                        $qrOriginImgPath = $qr->QRImage->uri;
                    }else{
                        $qrManageImgPath = $qr->QRImage->uri;
                    }
                }
                $cellProductData = [
                    $k+1,
                    $realProduct->user_no,
                    $realProduct->no,
                    $pro->no,
                    $pro->width,
                    $pro->height,
                    $pro->level,
                    $pro->mount,
                    $pro->border,
                    $pro->frame,
                    $pro->front,
                    $pro->back,
                    $pro->produceParams->core_no,
                    $pro->produceParams->core_material,
                ];
                $cellData[] = $cellProductData;
                if (Storage::exists($qrOriginImgPath)) {
                    $qrimg = Storage::get($qrOriginImgPath);
                    Storage::disk('public')->put('temporaryQR/'.($k+1).'-origin.svg', $qrimg);
                }
                if (Storage::exists($qrManageImgPath)) {
                    $qrimg = Storage::get($qrManageImgPath);
                    Storage::disk('public')->put('temporaryQR/'.($k+1).'-manage.svg', $qrimg);
                }
            }
            Excel::create(iconv('UTF-8', 'GBK', '待生产产品列表'),function($excel) use ($cellData){
                $excel->sheet('sheet1', function($sheet) use ($cellData){
                    $sheet->rows($cellData);
                });
            })->store('csv',storage_path('app/public/temporaryQR'));
            RealProduct::whereIn(IekModel::ID,$realProductIds)->update(['status'=>RealProductStatus::PRODUCING]);
            DB::connection('pgsql_product_traceability')->commit();
        }catch (\Exception $e){
            DB::connection('pgsql_product_traceability')->rollback();
            if($err->isOK()) {
                $err->setError(Errors::FAILED);
                $err->setMessage($e->getMessage());
                $err->setData($e->getLine());
            }
        }
        return $err;
    }

    public function download(){
        $this->ZipAndDownload(storage_path('app/public/temporaryQR'),true);
    }




    // ------------------------------------------------------ //
    // #压缩并直接下载
    //
    // $archive = new PHPZip();
    // $archive->ZipAndDownload("需压缩的文件所在目录");
    // ------------------------------------------------------ //
    public function ZipAndDownload($dir=null,$isDelete=false)
    {
//        dd(Storage::disk('public')->put('test.txt', 'content'));
        if(@!function_exists('gzcompress')){ return; }

        ob_end_clean();
        $filelist = $this->visitFile($dir);
        if(count($filelist) == 0){ return; }

        foreach($filelist as $file)
        {
            if(!file_exists($file) || !is_file($file)){ continue; }

            $fd       = fopen($file, "rb");
            $content  = @fread($fd, filesize($file));
            fclose($fd);

            // 1.删除$dir的字符(./folder/file.txt 删除./folder/)
            // 2.如果存在/就删除(/file.txt 删除/)
            $file = substr($file, strlen($dir));
            if(substr($file, 0, 1) == "\\" || substr($file, 0, 1) == "/"){ $file = substr($file, 1); }
            $this->addFile($content, $file);
        }
        $out = $this->file();
        //删除目录文件夹
        if($isDelete){
            $this->deldir($dir);
//            $result = $this->deldir(/*storage_path('app/public/testFile')*/$dir);
        }
        @header('Content-Encoding: none');
        @header('Content-Type: application/zip');
        @header('Content-Disposition: attachment ; filename='.date("Ymd").'.zip');
        @header('Pragma: no-cache');
        @header('Expires: 0');
        print($out);
    }

    //delete directory 删除文件夹
    public function deldir($dir) {
        //先删除目录下的文件：
        $dh=opendir($dir);
        while ($file=readdir($dh)) {
            if($file!="." && $file!="..") {
                $fullpath=$dir."/".$file;
                if(!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    $this->deldir($fullpath);
                }
            }
        }

        closedir($dh);
        //删除当前文件夹：
        if(rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }
}