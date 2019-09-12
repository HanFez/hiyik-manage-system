/**
 * Created by xj on 8/3/17 9:26 AM.
 */
/**
 * Constant ERRORS.
 * @type {{UNKNOWN_ERROR: {code: number, message: string}, OK: {code: number, message: string}, FAILED: {code: number, message: string}, EXCEPTION: {code: number, message: string}, EXPIRED: {code: number, message: string}, TOO_MANY_REQUEST: {code: number, message: string}, NOT_EMPTY: {code: number, message: string}, ACTION_ALREADY_BE_DONE: {code: number, message: string}, NON_COMPLIANCE: {code: number, message: string}, NOT_IMPLEMENT: {code: number, message: string}, TARGET_FORBIDDEN: {code: number, message: string}, INVALID_JSON: {code: number, Message: string}, INVALID_NICK: {code: number, message: string}, INVALID_PASSWORD: {code: number, message: string}, INVALID_INVITE: {code: number, message: string}, INVALID_VERIFY: {code: number, message: string}, INVALID_DATA: {code: number, message: string}, INVALID_FILE: {code: number, message: string}, LACK_PARAMS: {code: number, message: string}, NOT_LOGIN: {code: number, message: string}, UNKNOWN_LOCATION: {code: number, message: string}, NO_VALIDATION: {code: number, message: string}, NOT_ALLOWED: {code: number, message: string}, NOT_OWNER: {code: number, message: string}, INVALID_PARAMS: {code: number, message: string}, INVALID_ACCOUNT: {code: number, message: string}, USED_ACCOUNT: {code: number, message: string}, NOT_FOUND: {code: number, message: string}, EXIST: {code: number, message: string}, FILE_NOT_FOUND: {code: number, message: string}, FILE_TOO_HUGE: {code: number, message: string}, UNKNOWN_IMAGE: {code: number, message: string}, FILTER_FAILED: {code: number, message: string}, INVALID_PUBLICATION_ID: {code: number, message: string}, INVALID_PERSON_ID: {code: number, message: string}, INSUFFICIENT_BALANCE: {code: number, message: string}, RUN_OUT: {code: number, message: string}}}
 */
var ERRORS = {
    UNKNOWN_ERROR: {
        'code': -1,
        'message': 'Unknown Error'
    },
    OK: {
        'code': 0,
        'message': 'Ok'
    },
    FAILED: {
        'code': 1,
        'message': 'Failed'
    },
    EXCEPTION: {
        'code': 2,
        'message': 'Unnamed exception'
    },
    EXPIRED: {
        'code': 3,
        'message': 'Out of time'
    },
    TOO_MANY_REQUEST: {
        'code': 4,
        'message': 'Too Many Requests'
    },
    NOT_EMPTY: {
        'code': 5,
        'message': 'Content can not be empty'
    },
    ACTION_ALREADY_BE_DONE: {
        'code': 6,
        'message': 'The action you request already be done by other admin'
    },
    NON_COMPLIANCE: {
        'code': 7,
        'message': 'The case of non-compliance'
    },
    /*NOT_IMPLEMENT: {
        'code': 8,
        'message': 'Function not implement'
    },
    TARGET_FORBIDDEN: {
        'code': 9,
        'message': 'Target be forbidden'
    },*/
    INVALID_JSON: {
        'code': 8001,
        'Message': 'Json format needed',
    },
    INVALID_NICK: {
        'code': 10001,
        'message': 'Invalid Nick',
    },
    INVALID_PASSWORD: {
        'code': 10002,
        'message': 'Invalid Password',
    },
    INVALID_INVITE: {
        'code': 10003,
        'message': 'Invalid Invite Code',
    },
    INVALID_VERIFY: {
        'code': 10004,
        'message': 'Invalid Verify Code',
    },
    INVALID_DATA: {
        'code': 10005,
        'message': 'Invalid Data Format',
    },
    INVALID_FILE: {
        'code': 10006,
        'message': 'Invalid Upload File Status',
    },
    LACK_PARAMS: {
        'code': 10007,
        'message': 'Lack Upload Params',
    },
    NOT_LOGIN: {
        'code': 10008,
        'message': "Haven't Login",
    },
    UNKNOWN_LOCATION: {
        'code': 10009,
        'message': "Unknown Location",
    },
    /*INVALID_PAY_PASSWORD: {
        'code': 10010,
        'message': "Invalid Pay Password",
    },*/
    NO_VALIDATION: {
        'code': 10011,
        'message': 'No validation'
    },
    NOT_ALLOWED: {
        'code': 10012,
        'message': 'No allowed'
    },
    NOT_OWNER: {
        'code': 10013,
        'message': 'Not owner'
    },
    INVALID_PARAMS: {
        'code': 10014,
        'message': 'Invalid params'
    },
    /*INVALID_NUMBER: {
        'code': 10015,
        'message': 'Invalid number'
    },*/
    INVALID_ACCOUNT: {
        'code': 10016,
        'message': 'Invalid Account'
    },
    USED_ACCOUNT: {
        'code': 10019,
        'message': 'UsedAccount'
    },
    /*OUT_OF_DATE: {
        'code': 10020,
        'message': 'Out of date'
    },
    EARLY: {
        'code': 10021,
        'message': 'Not begin'
    },
    INVALID_COVER: {
        'code': 10022,
        'message': 'Invalid Cover'
    },*/
    NOT_FOUND: {
        'code': 11001,
        'message': 'Not Found',
    },
    EXIST: {
        'code': 11002,
        'message': 'Exist',
    },
    FILE_NOT_FOUND: {
        'code': 11003,
        'message': 'File not found',
    },
    /*FILE_TOO_HUGE: {
        'code': 11004,
        'message': 'File too huge',
    },
    GAG: {
        'code': 12001,
        'message': 'Gag',
    },*/
    UNKNOWN_IMAGE: {
        'code': 20001,
        'message': 'Unknown Image Format.'
    },
    FILTER_FAILED: {
        'code': 20002,
        'message': 'Keyword filter not passed.'
    },
    /*INVALID_PUBLICATION_ID: {
        'code': 21001,
        'message': 'Can not find publication with given ID'
    },
    INVALID_PERSON_ID: {
        'code': 21002,
        'message': 'Can not find person with given ID'
    },*/
    INSUFFICIENT_BALANCE: {
        'code': 30001,
        'message': 'Wallet balance is insufficient'
    },
    /*RUN_OUT: {
        'code': 40001,
        'message': 'Something Has run out'
    },*/
}

/**
 * Parse status code.
 * @param result
 * @returns null | {
 *  statusCode: number,
 *  message: string,
 *  data: null
 * }
 */
function parseResultStatus(result) {
    if(isNull(result) || isNull(result.status_code)) {
        return null;
    }
    if(isNull(result.message)) {
        result.message = null;
    }
    var statusCode = result.status_code;
    for(var k in ERRORS) {
        var error = ERRORS[k];
        if(statusCode == error['code']) {
            return {
                statusCode: statusCode,
                message: result.message,
                data: null
            };
        }
    }
    return null;
}
/**
 * Check result status is 'ok'.
 * @param result: { statusCode | status_code: number}
 * @returns {boolean}
 */
function isOk(result) {
    if(isNull(result)) {
        return false;
    }
    var statusCode = isUndefined(result.statusCode) ? result.status_code : result.statusCode;
    if(statusCode == ERRORS.OK['code']) {
        return true;
    } else {
        return false;
    }
}
/**
 * Check status code.
 * @param result: {status_code}
 * @returns {null | string}
 */
function checkStatusCode(result) {
    if(isNull(result) || isNull(result.status_code)) {
        return null;
    }
    var statusCode = result.status_code;
    var status = null;
    switch (statusCode) {
        case 0:
            status = 'ok';
            break;
        case 1:
            status = 'fail';
            break;
        case 10008:
            status = 'notLogin';
            break;
        case 10012:
            status = 'notAllowed';
            break;
        case 10014:
            status = 'invalidParams';
            break;
        case 11001:
            status = 'notFound';
            break;
    }
    return status;
}