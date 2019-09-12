<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-10-11
 * Time: 上午10:24
 */

namespace App\IekModel\Version1_0\Constants;


class Errors extends IekConstant {
    CONST OK = ['code' => 0,
            'message' => 'Ok',
        ];
    CONST FAILED = ['code' => 1,
            'message' => 'Failed',
        ];
    CONST INVALID_JSON = ['code' => 8001,
            'Message' => 'Json format needed',
        ];
    CONST INVALID_NICK = ['code' => 10001,
            'message' => 'Invalid Nick',
        ];
    CONST INVALID_PASSWORD = ['code' => 10002,
            'message' => 'Invalid Password',
        ];
    CONST INVALID_INVITE = ['code' => 10003,
            'message' => 'Invalid Invite Code',
        ];
    CONST INVALID_VERIFY = ['code' => 10004,
            'message' => 'Invalid Verify Code',
        ];
    CONST INVALID_DATA = ['code' => 10005,
            'message' => 'Invalid Data Format',
        ];
    CONST INVALID_FILE = ['code' => 10006,
            'message' => 'Invalid Upload File Status',
        ];
    CONST LACK_PARAMS = ['code' => 10007,
            'message' => 'Lack Upload Params',
        ];
    CONST NOT_LOGIN = ['code' => 10008,
            'message' => "Haven't Login",
        ];
    CONST UNKNOWN_LOCATION = ['code' => 10009,
            'message' => "Unknown Location",
        ];
    CONST NO_VALIDATION = ['code'=>10011,
            'message'=>'No validation'
        ];
    CONST NOT_ALLOWED = ['code'=>10012,
            'message'=>'No allowed'
        ];
    CONST NOT_OWNER = ['code'=>10013,
            'message'=>'Not owner'
        ];
    CONST INVALID_PARAMS = ['code'=>10014,
            'message'=>'Invalid params'
        ];
    CONST INVALID_ACCOUNT = ['code' =>10016,
            'message'=>'Invalid Account'
        ];
    CONST USED_ACCOUNT = ['code'=>10019,
            'message'=>'UsedAccount'
        ];
    CONST NOT_FOUND = ['code' => 11001,
            'message' => 'Not Found',
        ];
    CONST EXIST = ['code' => 11002,
            'message' => 'Exist',
        ];
    CONST FILE_NOT_FOUND = ['code' => 11003,
            'message' => 'File not found',
        ];
    CONST FILE_TOO_HUGE = ['code' => 11004,
            'message' => 'File too huge',
        ];
    CONST UNKNOWN_IMAGE = ['code'=>20001,
            'message'=>'Unknown Image Format.'
        ];
    CONST FILTER_FAILED = ['code'=>20002,
            'message'=>'Keyword filter not passed.'
        ];
    CONST EXCEPTION = ['code'=>2,
            'message'=>'Unnamed exception'
        ];
    CONST EXPIRED = ['code'=>3,
            'message'=>'Out of time'
        ];
    CONST UNKNOWN_ERROR = [
            'code' => -1,
            'message' => 'Unknown Error'
        ];
    CONST TOO_MANY_REQUEST = [
            'code' => 4,
            'message' => 'Too Many Requests'
        ];
    CONST NOT_EMPTY = [
            'code' => 5,
            'message' => 'Content can not be empty'
        ];
    CONST INVALID_PUBLICATION_ID = [
        'code' => 21001,
        'message' => 'Can not find publication with given ID'
    ];
    CONST ACTION_ALREADY_BE_DONE = [
        'code' => 6,
        'message' => 'The action you request already be done by other admin'
    ];
    CONST NON_COMPLIANCE = [
        'code' => 7,
        'message' => 'The case of non-compliance'
    ];
    CONST INVALID_IWALL_ID = [
        'code' => 30001,
        'message' => 'Can not find iwall with given ID'
    ];
}