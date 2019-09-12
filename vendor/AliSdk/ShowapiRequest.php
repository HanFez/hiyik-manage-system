<?php
namespace AliSdk;
include_once 'Util/Autoloader.php';
header("Content-type: text/html; charset=utf-8");


////get
//$req = new ShowapiRequest("https://ali-deliver.showapi.com/showapi_expInfo?com=auto&nu=536403856881","b6da9b4f53a541f981ba54fc4e8d5def");
//$response=$req->addTextPara("para1_name", "para1_value")
//    		  ->addTextPara("para2_name", "para2_value")
//    		  ->get( );//也可以换成->post()

// echo ("server return is:\r\n" );
// echo($response->getContent()."\r\n" ); 				//content包括了头和返回体
//echo($response->getBody()."\r\n" );					//body是返回体
// $result = json_decode($response->getBody()."\r\n"); //做json转换
// echo ("showapi_res_code  is:\r\n");
// echo($result->showapi_res_code."\r\n" );


 
// $file= 'c:/a.jpg';
// $fp  = fopen($file, 'rb', 0);
// $b_base64=base64_encode(fread($fp,filesize($file)));
// fclose($fp);
// $req = new ShowapiRequest("请求地址，比如http://ali-checkcode.showapi.com/checkcode","appcode");
// $response=$req->addTextPara("typeId", "3040")
//     		  ->addTextPara("convert_to_jpg", "1")
//     		  ->addTextPara("img_base64",$b_base64)
//     		  ->post( );
// echo ("server return is:\r\n" );
// echo($response->getContent()."\r\n" ); 				//content包括了头和返回体
// echo($response->getBody()."\r\n" );					//body是返回体
// $result = json_decode($response->getBody()."\r\n"); //做json转换
// echo ("showapi_res_code  is:\r\n");
// echo($result->showapi_res_code."\r\n" );


use AliSdk\Http\HttpRequest;
use AliSdk\Constant\HttpMethod;
use AliSdk\Http\HttpClient;
class ShowapiRequest
{
	
	protected  $appcode ;
	protected  $url;
	protected  $querys = array();

	
	function  __construct( $url,$appcode    )
	{
		$this->appcode = $appcode;
		$this->url=$url;
	}
	
	public function addTextPara($key, $value)
	{
		$this->querys[$key] = $value;
		return $this;
	}

	/**
	*method=GET请求示例
	*/
    public function get() {
    	//echo ($this->host."\r\n");
    	//echo ($this->path."\r\n");
    	$request= new HttpRequest($this->url , $this->appcode,HttpMethod::GET );
    	foreach ($this->querys as $itemKey => $itemValue) {
			 	//echo ($itemValue."\r\n");
			 	$request->setQuery($itemKey, $itemValue);
		}
		$response = HttpClient::execute($request);
        return  $response;
	}

	/**
	*method=POST且是表单提交，请求示例
	*/
	public function post() {
		$request =  new HttpRequest($this->url,$this->appcode, HttpMethod::POST );
		foreach ($this->querys as $itemKey => $itemValue) {
    			//echo ("aaaaaaaaaaa:\r\n" );
    			echo ($itemKey."\r\n");
			 	echo ($itemValue."\r\n");
			 	$request->setQuery($itemKey, $itemValue);
		}
		$response = HttpClient::execute($request);
        return  $response;
	}
}