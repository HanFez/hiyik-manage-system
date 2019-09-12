<?php

namespace App\IekModel\Version1_0;

use Exception;
use App\IekModel\Version1_0\Constants\Errors;
/**
 * Define error status code and error messages.
 * @author       Rich
 */
class Error
{
    /**
     * @var  $statusCode  INT
     * @var  $message     String
     */
    public $statusCode;
    public $message;
    public $data;

    function __construct() {
        $this->setError(Errors::OK);
        $this->data = null;
    }

    /**
     * Set the error content.
     * @param $err mixed array|string
     * @return Error
     */
    public function setError($err) {
        if(is_array($err)) {
            $this->statusCode = $err['code'];
            $this->message = $err['message'];
        } else {
            $this->statusCode = Errors::UNKNOWN_ERROR['code'];
            $this->message = Errors::UNKNOWN_ERROR['message'];
        }
        return $this;
    }

    public function makeError($code, $message) {
        $this->statusCode = $code;
        $this->message = $message;
    }

    public function exception(Exception $ex) {
        $code = $ex->getCode();
        if($code != 0) {
            $this->statusCode = $ex->getCode();
        } else {
            $this->statusCode = Errors::EXCEPTION['code'];
        }
        $this->message = $ex->getMessage();
    }

    public function getCode(Array $errorConst) {
        return $errorConst['code'];
    }

    public function isOk() {
        if($this->statusCode == Errors::OK['code']) {
            return true;
        }
        return false;
    }

    public function isExist() {
        if($this->statusCode == Errors::EXIST['code']) {
            return true;
        }
        return false;
    }
    public function setData($data) {
        $this->data = $data;
    }

    public function setMessage($message) {
        $this->message = $message;
    }
}

?>
