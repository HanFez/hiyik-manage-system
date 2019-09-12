<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 16-7-24
 * Time: 下午4:36
 */

namespace Libs\Robot;


class ToneParser {

    public $parser = null;

    function __construct(InterfaceToneParser $parser) {
        $this->parser = $parser;
    }

    function setImageFile($filePath) {
        if(!is_null($this->parser)) {
            return $this->parser->setImageFile($filePath);
        }
        return false;
    }
    function setImageBlob($blob) {
        if(!is_null($this->parser)) {
            return $this->parser->setImageBlob($blob);
        }
        return false;
    }

    public function getTones($delta = null) {
        $result = null;
        if(!is_null($this->parser)) {
            $result = $this->parser->getTones($delta);
        }
        return $result;
    }

    public function getMainTones() {
        $result = null;
        if(!is_null($this->parser)) {
            $result = $this->parser->getMainTones();
        }
        return $result;
    }
}