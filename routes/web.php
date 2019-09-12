<?php
/*
|---------------------------------------------------------------------
| Web Routes
|---------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/
Route::get('/','AdminController@index');
//Log in,
Route::get('admin/login','Login\LoginController@index');
Route::post('admin/login','Login\LoginController@login');
//deal image and save ftp
Route::get('imageFiles', 'Table\ImageFileController@checkImageByHash');
Route::post('imageFiles', 'Table\ImageFileController@handleImageFile');
Route::post('imageFiles/{id}', 'Table\ImageFileController@handleCropImage');
/*end*/

Route::group(['middleware' => 'loginStatus'], function () {
    /* Code by XuJun start*/
    Route::get('userNav', 'LayoutController@userNav');
    Route::get('subMenu', 'LayoutController@subMenu');
    Route::get('content', 'LayoutController@content');
    Route::get('tables', 'LayoutController@allTables');
    Route::get('errors/{status}', 'LayoutController@errors');
    Route::get('translation', 'TranslationController@translation');
    /* Code by XuJun end*/
    // exit, home page and take pictures
    Route::get('admin/quit','Login\LoginController@quit');
    //get ftp image
    Route::get('rest/1_0/files/{subdir}/{norm}/{id}', 'Table\FileController@normImage');
    Route::get('rest/1_0/files/{subdir}/{id}', 'Table\FileController@normImage');
    Route::get('rest/1_0/files/{products}/{subdir}/{norm}/{id}', 'Table\FileController@normImage');
    /*end*/
    //modify password
    Route::get('manager/password','Admin\PassController@index');
    Route::post('manager/password','Admin\PassController@modifyPass');
    /*end*/
    //manager,role,privilege and employee
    Route::get('manager/relation/{id}','Admin\ManagerController@managerRelationRole');
    Route::delete('manager/del','Admin\ManagerController@del');
    Route::post('manager/recover','Admin\ManagerController@recover');
    Route::resource('manager','Admin\ManagerController');

    Route::get('managerRoles','Admin\ManagerController@managerRoles');
    Route::get('getManagerRoles/{id}','Admin\ManagerController@getManagerRoles');
    Route::post('allotRole/{id}','Admin\ManagerController@allotRole');
    Route::delete('manager/relation/{id}/del','Admin\ManagerController@delManagerRole');
    Route::post('manager/relation/{id}/recover','Admin\ManagerController@coverManagerRole');

    Route::get('role/relation/{id}','Admin\RoleController@roleRelationPrivilege');
    Route::delete('role/relation/{id}/del','Admin\RoleController@delPrivilege');
    Route::post('role/relation/{id}/recover','Admin\RoleController@recoverPrivilege');
    Route::delete('role/del','Admin\RoleController@del');
    Route::post('role/recover','Admin\RoleController@recover');
    Route::resource('role','Admin\RoleController');

    Route::get('getRoleList','Admin\RoleController@getRoleList');
    Route::get('getRoleTable/{id}','Admin\RoleController@getRoleTable');
    Route::get('getRoleTablePrivilege/{id}/{tableName}','Admin\RoleController@getRoleTablePrivilege');
    Route::post('allotPrivilege/{id}','Admin\RoleController@allotPrivilege');

    Route::get('privilege/checkAction','Admin\PrivilegeController@checkAction');
    Route::get('definedPrivilege','Admin\PrivilegeController@actionList');
    Route::post('privilege/assign','Admin\PrivilegeController@assignAction');
    Route::get('privilege','Admin\PrivilegeController@index');

    Route::get('getAll/{tableName}','Admin\PrivilegeController@getAll');
    Route::delete('getAll/{tableName}/del','Admin\PrivilegeController@deleteRecord');
    Route::post('getAll/{tableName}/recover','Admin\PrivilegeController@coverRecord');

    Route::delete('employee/del','Admin\EmployeeController@del');
    Route::post('employee/recover','Admin\EmployeeController@recover');
    Route::resource('employee','Admin\EmployeeController');
    /*end */
    //publications
    Route::get('publications', 'Table\PublicationController@publicationList');
    Route::get('publications/{id}', 'Table\PublicationController@publication');
    Route::post('publications/{id}/official', 'Table\PublicationController@official');
    Route::post('publications/{id}/unOfficial', 'Table\PublicationController@unOfficial');

    //person
    Route::get('persons', 'Table\PersonController@personList');
    Route::get('persons/{id}', 'Table\PersonController@person');
    Route::post('persons/{id}/gag', 'Table\PersonController@personGag');
    //Route::put('personGag/{id}', 'Table\PersonController@updatePersonGag');
    Route::post('personGag/{id}', 'Table\PersonController@cancelPersonGag');
    Route::post('avatarForbidden', 'Table\PersonController@avatarForbidden');
    //Route::get('persons/nick', 'Table\PersonController@getPersonListByNick');
    Route::post('person/{id}/wallet','Table\PersonController@freezeWallet');
    //forbidden
    Route::post('unForbidden', 'Operation\ForbiddenController@unForbidden');
    Route::post('forbidden', 'Operation\ForbiddenController@forbidden')->middleware('checkStatus');

    //systemSetting
    Route::get('settings', 'Table\SystemSettingController@systemSettingList');
    Route::post('settings/child/save', 'Table\SystemSettingController@saveSystemSettingChild');
    Route::post('settings/save', 'Table\SystemSettingController@saveSystemSettingParent');
    Route::delete('settings/delete/{id}', 'Table\SystemSettingController@deleteParent');
    Route::post('settings/recover/{id}', 'Table\SystemSettingController@coverParent');
    Route::delete('settings/child/delete/{id}/{index}', 'Table\SystemSettingController@deleteChild');
    Route::post('settings/child/recover/{id}/{index}', 'Table\SystemSettingController@coverChild');
    Route::post('settings/child/setDefault/{id}/{index}', 'Table\SystemSettingController@setDefaultChild');


    //announce
    Route::post('createAnnounce', 'Table\AnnounceController@createAnnounce');
    Route::post('modifyAnnounce/{id}', 'Table\AnnounceController@modifyAnnounce');
    Route::get('announce/{id}/edit', 'Table\AnnounceController@getModifyAnnounce');
    Route::post('announce/delete/{id}', 'Table\AnnounceController@deleteAnnounce');
    Route::get('announceList', 'Table\AnnounceController@getAnnounceList');
    Route::get('announce/{id}', 'Table\AnnounceController@getViewAnnounce');
    Route::post('auditAnnounce/{id}', 'Table\AnnounceController@AuditAnnounce');

    Route::get('createAnnounce', function(){
        return view('admin.announce.createAnnounce');
    });
    //tag
    Route::get('tags', 'Table\TagController@tagList');
    Route::post('tags/create', 'Table\TagController@createTag');
    Route::post('tags/modify', 'Table\TagController@modifyTag');
    Route::delete('tags/delete', 'Table\TagController@deleteTag');
    Route::post('tags/recover', 'Table\TagController@coverTag');
    Route::get('tag/{id}', 'Table\TagController@getTagById');
    //report
    Route::get('reports', 'Table\ReportController@getReportList');
    Route::post('reports/{id}/deal', 'Table\ReportController@dealReport');

    Route::post('comment', 'Table\CommentController@commentList');

    Route::get('conversation/{id}', 'Table\ConversationController@conversationList');
    Route::get('session/{id}', 'Table\ChatSessionController@conversationSession');
    //advice
    Route::get('getAdviceList', 'Table\AdviceController@getAdviceList');
    Route::post('advice/{id}/deal', 'Table\AdviceController@dealAdvice');
    Route::post('ignore/{id}', 'Table\AdviceController@ignoreAdvice');
    Route::post('recover/{id}', 'Table\AdviceController@recoverAdvice');
    //folder
    Route::get('getFolderList', 'Table\FolderController@getFolderList');

    /* See forbidden reason */
    Route::get('seeTitle/{id}','Admin\SeeReasonController@seeTitle');
    Route::get('seeTag/{id}','Admin\SeeReasonController@seeTag');
    Route::get('seeImage/{id}','Admin\SeeReasonController@seeImage');
    Route::get('seeNick/{id}','Admin\SeeReasonController@seeNick');
    Route::get('seeAvatar/{id}','Admin\SeeReasonController@seeAvatar');
    Route::get('seeSignature/{id}','Admin\SeeReasonController@seeSignature');
    Route::get('seeComment/{id}','Admin\SeeReasonController@seeComment');
    Route::get('seeMessage/{id}','Admin\SeeReasonController@seeMessage');
    Route::get('seeName/{id}','Admin\SeeReasonController@seeName');
    Route::get('seeCommentText/{id}','Admin\SeeReasonController@seeCommentText');
    Route::get('seeCommentImage/{id}','Admin\SeeReasonController@seeCommentImage');
    /* End see forbidden reason */

    //scene
    Route::get('scene','Table\SceneController@sceneList');
    Route::get('sceneAdd','Table\SceneController@sceneAdd');
    Route::get('scene/{id}/edit','Table\SceneController@sceneEdit');
    Route::post('addScene','Table\SceneController@createScene');
    Route::put('scene/{id}','Table\SceneController@modifyScene');
    Route::delete('scene/del','Table\SceneController@deleteScene');
    Route::post('scene/recover','Table\SceneController@recoverScene');

    //crowd
    Route::get('crowd','Table\CrowdController@crowdList');
    Route::get('crowdAdd','Table\CrowdController@crowdAdd');
    Route::get('crowd/{id}/edit','Table\CrowdController@crowdEdit');
    Route::post('addCrowd','Table\CrowdController@createCrowd');
    Route::put('crowd/{id}','Table\CrowdController@modifyCrowd');
    Route::delete('crowd/del','Table\CrowdController@deleteCrowd');
    Route::post('crowd/recover','Table\CrowdController@recoverCrowd');
    //sex
    Route::get('sex','Table\SexController@sexList');
    Route::get('sexAdd','Table\SexController@sexAdd');
    Route::get('sex/{id}/edit','Table\SexController@sexEdit');
    Route::post('addSex','Table\SexController@createSex');
    Route::put('sex/{id}','Table\SexController@modifySex');
    Route::delete('sex/del','Table\SexController@deleteSex');
    Route::post('sex/recover','Table\SexController@recoverSex');

    /*Iwall*/
    Route::get('iwall','Table\IWallController@iwallList');
    Route::get('iwall/{id}','Table\IWallController@iwall');
    Route::post('iwall/{id}/official','Table\IWallController@official');
    Route::post('iwall/{id}/unOfficial','Table\IWallController@unOfficial');
    /* end */
    /* order */
    Route::get('order','Table\OrderController@orderList');
    Route::get('logistics/{order_id}','Table\OrderController@logisticsInfo');
    Route::get('queryLog','Table\OrderController@queryLog');
    Route::get('order/{id}','Table\OrderController@order');
    Route::get('replyComment','Table\OrderController@replyView');
    Route::post('reply/{id}','Table\OrderController@reply');
    Route::post('platformMemo/{order_id}','Table\OrderController@addOfficialMemo');

    Route::get('toCheck/{order_id}','Table\OrderAuditController@waitProduct');
    Route::post('checkMaterial/{order_id}','Table\OrderAuditController@checkMaterial');
    Route::post('checkProduce/{order_id}','Table\OrderAuditController@checkProduce');
    Route::get('toAccept','Table\OrderAuditController@acceptProduct');
    Route::post('accept/{order_id}','Table\OrderAuditController@accept');
    Route::get('toSend','Table\OrderAuditController@toSend');
    Route::post('send/{order_id}','Table\OrderAuditController@send');

    //refund
    Route::get('refundRequest','Table\RefundController@refundRequestList');
    Route::get('refundDetail/{id}','Table\RefundController@refundDetail');
    Route::get('checkView','Table\RefundController@checkView');
    Route::post('refundOrder/check/{id}','Table\RefundController@checkRefund');
    Route::get('refundList','Table\RefundController@refundList');
    Route::get('refund','Table\RefundController@refundView');
    Route::post('refund','Table\RefundController@refund');
    //Route::post('refund_money','Table\RefundController@refund_money');

    //reject
    Route::get('reject','Table\RejectController@rejectList');
    Route::get('reject/detail/{order_id}','Table\RejectController@returnDetail');
    Route::get('reject/audit/{reject_request_id}','Table\RejectController@audit');
    Route::post('reject','Table\RejectController@exchangeCheck');
    Route::get('exchange/{id}','Table\RejectController@exchangeView');
    Route::post('exchange','Table\RejectController@exchange');
    Route::post('platformConfirm/{reject_id}','Table\RejectController@confirmPersonSendGoods');
    Route::get('receipt/{reject_id}','Table\RejectController@confirmReceipt');
    Route::get('shipFee/{reject_request_id}','Table\RejectController@shipFee');
    Route::post('returnShipFee/{reject_request_id}','Table\RejectController@returnShipFee');

    Route::get('orderStatistics','Table\OrderStatisticController@statistics');

    Route::get('status','Table\StatusController@index');
    Route::get('addStatus','Table\StatusController@add');
    Route::post('addStatus','Table\StatusController@postStatus');
    Route::get('status/{id}/edit','Table\StatusController@edit');
    Route::put('status/{id}','Table\StatusController@putStatus');
    Route::delete('status/del','Table\StatusController@del');
    Route::post('status/recover','Table\StatusController@recover');
    /* end */
    //company
    Route::delete('company/del','Table\CompanyController@del');
    Route::post('company/recover','Table\CompanyController@cover');
    Route::resource('company','Table\CompanyController');

    //accessory
    Route::delete('accessory/del','Table\AccessoryController@del');
    Route::post('accessory/recover','Table\AccessoryController@recover');
    Route::resource('accessory','Table\AccessoryController');

    //voucher
    Route::get('voucher','Table\VoucherController@voucherList');
    Route::get('voucherAdd','Table\VoucherController@add');
    Route::post('voucher','Table\VoucherController@create');
    Route::get('voucher/{id}/edit','Table\VoucherController@edit');
    Route::put('voucher/{id}','Table\VoucherController@update');
    Route::delete('voucher/del','Table\VoucherController@del');
    Route::post('voucher/recover','Table\VoucherController@recover');
    /* end */

    /* cart */
    Route::get('cartList','Table\CartController@cartList');
    Route::get('cart/{id}','Table\CartController@cartInfo');
    /*end*/

    /* trades record */
    Route::get('purchase','Table\PurchaseRecordController@purchaseList');
    Route::get('reward','Table\RewardRecordController@rewardList');
    Route::get('refundRecord','Table\RefundRecordController@refundRecordList');
    Route::get('recharge','Table\RechargeRecordController@rechargeList');
    Route::get('cash','Table\CashRecordController@cashList');
    Route::get('gain','Table\GainRecordController@gainList');
    Route::get('postage','Table\PostageRecordController@postageList');
    /* end */
    /* share */
    Route::get('share','Table\ShareController@shareList');
    /*end*/
    /* cash transfer*/
    Route::get('withdraw','Table\CashController@cashList');
    Route::post('batchAudit','Table\CashController@twoAudit');
    Route::post('transfer','Table\CashController@transfer');
    /*end*/

    Route::group(['prefix'=>'new_pro'],function(){
        /*category*/
        Route::get('addHCategory','Product\HCategoryController@addHCategory');
        Route::post('createHCategory','Product\HCategoryController@createHCategory');
        Route::get('listHCategory','Product\HCategoryController@listHCategory');
        Route::get('listHCategory/{id}/edit','Product\HCategoryController@editHCategory');
        Route::put('updateHCategory/{id}','Product\HCategoryController@updateHCategory');
        Route::delete('listHCategory/del','Product\HCategoryController@delHCategory');
        Route::post('listHCategory/recover','Product\HCategoryController@coverHCategory');

        Route::get('addMCategory','Product\MCategoryController@addMCategory');
        Route::post('createMCategory','Product\MCategoryController@createMCategory');
        Route::get('listMCategory','Product\MCategoryController@listMCategory');
        Route::get('listMCategory/{id}/edit','Product\MCategoryController@editMCategory');
        Route::put('updateMCategory/{id}','Product\MCategoryController@updateMCategory');
        Route::delete('listMCategory/del','Product\MCategoryController@delMCategory');
        Route::post('listMCategory/recover','Product\MCategoryController@coverMCategory');

        Route::get('addPCategory','Product\PCategoryController@addPCategory');
        Route::post('createPCategory','Product\PCategoryController@createPCategory');
        Route::get('listPCategory','Product\PCategoryController@listPCategory');
        Route::get('listPCategory/{id}/edit','Product\PCategoryController@editPCategory');
        Route::put('updatePCategory/{id}','Product\PCategoryController@updatePCategory');
        Route::delete('listPCategory/del','Product\PCategoryController@delPCategory');
        Route::post('listPCategory/recover','Product\PCategoryController@coverPCategory');

        /*produce*/
        Route::get('addHandle','Product\HandleController@addHandle');
        Route::post('createHandle','Product\HandleController@createHandle');
        Route::get('listHandle','Product\HandleController@listHandle');
        Route::get('listHandle/{id}/edit','Product\HandleController@editHandle');
        Route::put('updateHandle/{id}','Product\HandleController@updateHandle');
        Route::delete('listHandle/del','Product\HandleController@delHandle');
        Route::post('listHandle/recover','Product\HandleController@coverHandle');

        Route::get('addShape','Product\ShapeController@addShape');
        Route::post('createShape','Product\ShapeController@createShape');
        Route::get('listShape','Product\ShapeController@listShape');
        Route::get('listShape/{id}/edit','Product\ShapeController@editShape');
        Route::put('updateShape/{id}','Product\ShapeController@updateShape');
        Route::delete('listShape/del','Product\ShapeController@delShape');
        Route::post('listShape/recover','Product\ShapeController@coverShape');

        Route::get('addFacade','Product\FacadeController@addFacade');
        Route::post('uploadFacade','Product\FacadeController@uploadFacade');
        //Route::post('createFacade','Product\FacadeController@createFacade');
        Route::get('listFacade','Product\FacadeController@listFacade');
        Route::get('listFacade/{id}/edit','Product\FacadeController@editFacade');
        Route::put('updateFacade/{id}','Product\FacadeController@updateFacade');
        Route::delete('listFacade/del','Product\FacadeController@delFacade');
        Route::post('listFacade/recover','Product\FacadeController@coverFacade');

        Route::get('addMaterial','Product\MaterialController@addMaterial');
        Route::post('createMaterial','Product\MaterialController@createMaterial');
        Route::get('listMaterial','Product\MaterialController@listMaterial');
        Route::get('listMaterial/{id}/edit','Product\MaterialController@editMaterial');
        Route::put('updateMaterial/{id}','Product\MaterialController@updateMaterial');
        Route::delete('listMaterial/del','Product\MaterialController@delMaterial');
        Route::post('listMaterial/recover','Product\MaterialController@coverMaterial');

        Route::get('addTexture','Product\TextureController@addTexture');
        Route::post('createTexture','Product\TextureController@createTexture');
        Route::get('listTexture','Product\TextureController@listTexture');
        Route::get('listTexture/{id}/edit','Product\TextureController@editTexture');
        Route::put('updateTexture/{id}','Product\TextureController@updateTexture');
        Route::delete('listTexture/del','Product\TextureController@delTexture');
        Route::post('listTexture/recover','Product\TextureController@coverTexture');
        Route::post('uploadTexture','Product\TextureController@uploadTexture');

        Route::get('addMaterialSection','Product\MaterialSectionController@add');
        Route::post('createMaterialSection','Product\MaterialSectionController@create');
        Route::get('materialSection','Product\MaterialSectionController@listMaterialSection');
        Route::get('materialSection/{id}/edit','Product\MaterialSectionController@edit');
        Route::put('updateMaterialSection/{id}','Product\MaterialSectionController@update');
        Route::delete('materialSection/del','Product\MaterialSectionController@del');
        Route::post('materialSection/recover','Product\MaterialSectionController@cover');
        Route::get('draw/materialSection','Product\MaterialSectionController@paperView');
        Route::post('draw/section','Product\MaterialSectionController@getBezier');

        Route::get('addMaterialTexture','Product\MaterialTextureController@add');
        Route::post('createMaterialTexture','Product\MaterialTextureController@create');
        Route::get('materialTexture','Product\MaterialTextureController@listMaterialTexture');
        Route::get('materialTexture/{id}/edit','Product\MaterialTextureController@edit');
        Route::put('updateMaterialTexture/{id}','Product\MaterialTextureController@update');
        Route::delete('materialTexture/del','Product\MaterialTextureController@del');
        Route::post('materialTexture/recover','Product\MaterialTextureController@cover');

        /*define*/
        Route::get('addProductDefine','Product\ProductDefineController@add');
        Route::post('createProductDefine','Product\ProductDefineController@create');
        Route::get('productDefine','Product\ProductDefineController@listProductDefine');
        Route::get('productDefine/{id}/edit','Product\ProductDefineController@edit');
        Route::put('updateProductDefine/{id}','Product\ProductDefineController@update');
        Route::delete('productDefine/del','Product\ProductDefineController@del');
        Route::post('productDefine/recover','Product\ProductDefineController@cover');

        Route::get('addLineSize','Product\LineSizeController@add');
        Route::post('createLineSize','Product\LineSizeController@create');
        Route::get('lineSize','Product\LineSizeController@listLineSize');
        Route::get('lineSize/{id}/edit','Product\LineSizeController@edit');
        Route::put('updateLineSize/{id}','Product\LineSizeController@update');
        Route::delete('lineSize/del','Product\LineSizeController@del');
        Route::post('lineSize/recover','Product\LineSizeController@cover');

        Route::get('addPredefine','Product\HiyikProductController@add');
        Route::post('createPredefine','Product\HiyikProductController@create');
        Route::get('predefine','Product\HiyikProductController@listPredefine');
        Route::get('predefine/{id}/edit','Product\HiyikProductController@edit');
        Route::put('updatePredefine/{id}','Product\HiyikProductController@update');
        Route::delete('predefine/del','Product\HiyikProductController@del');
        Route::post('predefine/recover','Product\HiyikProductController@cover');

        Route::get('addProductDefineCategory','Product\ProductDefineCategoryController@add');
        Route::post('createProductDefineCategory','Product\ProductDefineCategoryController@create');
        Route::get('productDefineCategory','Product\ProductDefineCategoryController@listProductDefineCategory');
        Route::get('productDefineCategory/{id}/edit','Product\ProductDefineCategoryController@edit');
        Route::put('updateProductDefineCategory/{id}','Product\ProductDefineCategoryController@update');
        Route::delete('productDefineCategory/del','Product\ProductDefineCategoryController@del');
        Route::post('productDefineCategory/recover','Product\ProductDefineCategoryController@cover');

        Route::get('addBorderDefine','Product\BorderDefineController@add');
        Route::post('createBorderDefine','Product\BorderDefineController@create');
        Route::get('borderDefine','Product\BorderDefineController@listBorderDefine');
        Route::get('borderDefine/{id}/edit','Product\BorderDefineController@edit');
        Route::put('updateBorderDefine/{id}','Product\BorderDefineController@update');
        Route::delete('borderDefine/del','Product\BorderDefineController@del');
        Route::post('borderDefine/recover','Product\BorderDefineController@cover');

        Route::get('addBorderMaterialDefine','Product\BorderMaterialDefineController@add');
        Route::post('createBorderMaterialDefine','Product\BorderMaterialDefineController@create');
        Route::get('borderMaterialDefine','Product\BorderMaterialDefineController@listBMD');
        Route::get('borderMaterialDefine/{id}/edit','Product\BorderMaterialDefineController@edit');
        Route::put('updateBorderMaterialDefine/{id}','Product\BorderMaterialDefineController@update');
        Route::delete('borderMaterialDefine/del','Product\BorderMaterialDefineController@del');
        Route::post('borderMaterialDefine/recover','Product\BorderMaterialDefineController@cover');

        Route::get('addCoreDefine','Product\CoreDefineController@add');
        Route::post('createCoreDefine','Product\CoreDefineController@create');
        Route::get('coreDefine','Product\CoreDefineController@listCore');
        Route::get('coreDefine/{id}/edit','Product\CoreDefineController@edit');
        Route::put('updateCoreDefine/{id}','Product\CoreDefineController@update');
        Route::delete('coreDefine/del','Product\CoreDefineController@del');
        Route::post('coreDefine/recover','Product\CoreDefineController@cover');

        Route::get('addCoreMaterialDefine','Product\CoreMaterialDefineController@add');
        Route::post('createCoreMaterialDefine','Product\CoreMaterialDefineController@create');
        Route::get('coreMaterialDefine','Product\CoreMaterialDefineController@listCMD');
        Route::get('coreMaterialDefine/{id}/edit','Product\CoreMaterialDefineController@edit');
        Route::put('updateCoreMaterialDefine/{id}','Product\CoreMaterialDefineController@update');
        Route::delete('coreMaterialDefine/del','Product\CoreMaterialDefineController@del');
        Route::post('coreMaterialDefine/recover','Product\CoreMaterialDefineController@cover');

        Route::get('addCoreHandleDefine','Product\CoreHandleDefineController@add');
        Route::post('createCoreHandleDefine','Product\CoreHandleDefineController@create');
        Route::get('coreHandleDefine','Product\CoreHandleDefineController@listCHD');
        Route::get('coreHandleDefine/{id}/edit','Product\CoreHandleDefineController@edit');
        Route::put('updateCoreHandleDefine/{id}','Product\CoreHandleDefineController@update');
        Route::delete('coreHandleDefine/del','Product\CoreHandleDefineController@del');
        Route::post('coreHandleDefine/recover','Product\CoreHandleDefineController@cover');

        Route::get('addShowDefine','Product\ShowDefineController@add');
        Route::post('createShowDefine','Product\ShowDefineController@create');
        Route::get('showDefine','Product\ShowDefineController@listShowDefine');
        Route::get('showDefine/{id}/edit','Product\ShowDefineController@edit');
        Route::put('updateShowDefine/{id}','Product\ShowDefineController@update');
        Route::delete('showDefine/del','Product\ShowDefineController@del');
        Route::post('showDefine/recover','Product\ShowDefineController@cover');

        Route::get('addShowMaterialDefine','Product\ShowMaterialDefineController@add');
        Route::post('createShowMaterialDefine','Product\ShowMaterialDefineController@create');
        Route::get('showMaterialDefine','Product\ShowMaterialDefineController@listSMD');
        Route::get('showMaterialDefine/{id}/edit','Product\ShowMaterialDefineController@edit');
        Route::put('updateShowMaterialDefine/{id}','Product\ShowMaterialDefineController@update');
        Route::delete('showMaterialDefine/del','Product\ShowMaterialDefineController@del');
        Route::post('showMaterialDefine/recover','Product\ShowMaterialDefineController@cover');

        Route::get('addFrameMaterialDefine','Product\FrameMaterialDefineController@add');
        Route::post('createFrameMaterialDefine','Product\FrameMaterialDefineController@create');
        Route::get('frameMaterialDefine','Product\FrameMaterialDefineController@listFMD');
        Route::get('frameMaterialDefine/{id}/edit','Product\FrameMaterialDefineController@edit');
        Route::put('updateFrameMaterialDefine/{id}','Product\FrameMaterialDefineController@update');
        Route::delete('frameMaterialDefine/del','Product\FrameMaterialDefineController@del');
        Route::post('frameMaterialDefine/recover','Product\FrameMaterialDefineController@cover');

        Route::get('addBackMaterialDefine','Product\BackMaterialDefineController@add');
        Route::post('createBackMaterialDefine','Product\BackMaterialDefineController@create');
        Route::get('backMaterialDefine','Product\BackMaterialDefineController@listBKD');
        Route::get('backMaterialDefine/{id}/edit','Product\BackMaterialDefineController@edit');
        Route::put('updateBackMaterialDefine/{id}','Product\BackMaterialDefineController@update');
        Route::delete('backMaterialDefine/del','Product\BackMaterialDefineController@del');
        Route::post('backMaterialDefine/recover','Product\BackMaterialDefineController@cover');

        Route::get('addFrontMaterialDefine','Product\FrontMaterialDefineController@add');
        Route::post('createFrontMaterialDefine','Product\FrontMaterialDefineController@create');
        Route::get('frontMaterialDefine','Product\FrontMaterialDefineController@listFTD');
        Route::get('frontMaterialDefine/{id}/edit','Product\FrontMaterialDefineController@edit');
        Route::put('updateFrontMaterialDefine/{id}','Product\FrontMaterialDefineController@update');
        Route::delete('frontMaterialDefine/del','Product\FrontMaterialDefineController@del');
        Route::post('frontMaterialDefine/recover','Product\FrontMaterialDefineController@cover');

        Route::get('addBackFacade','Product\BackFacadeController@add');
        Route::post('createBackFacade','Product\BackFacadeController@create');
        Route::get('backFacade','Product\BackFacadeController@listBF');
        Route::get('backFacade/{id}/edit','Product\BackFacadeController@edit');
        Route::put('updateBackFacade/{id}','Product\BackFacadeController@update');
        Route::delete('backFacade/del','Product\BackFacadeController@del');
        Route::post('backFacade/recover','Product\BackFacadeController@cover');

        Route::get('addHoleLine','Product\HoleLineController@add');
        Route::post('createHoleLine','Product\HoleLineController@create');
        Route::get('holeLine','Product\HoleLineController@listHL');
        Route::get('holeLine/{id}/edit','Product\HoleLineController@edit');
        Route::put('updateHoleLine/{id}','Product\HoleLineController@update');
        Route::delete('holeLine/del','Product\HoleLineController@del');
        Route::post('holeLine/recover','Product\HoleLineController@cover');

        Route::get('addLineMaterial','Product\LineMaterialDefineController@add');
        Route::post('createLineMaterial','Product\LineMaterialDefineController@create');
        Route::get('lineMaterial','Product\LineMaterialDefineController@listHLMD');
        Route::get('lineMaterial/{id}/edit','Product\LineMaterialDefineController@edit');
        Route::put('updateLineMaterial/{id}','Product\LineMaterialDefineController@update');
        Route::delete('lineMaterial/del','Product\LineMaterialDefineController@del');
        Route::post('lineMaterial/recover','Product\LineMaterialDefineController@cover');

        Route::get('addFrameHole','Product\FrameHoleDefineController@add');
        Route::post('createFrameHole','Product\FrameHoleDefineController@create');
        Route::get('frameHole','Product\FrameHoleDefineController@listFHD');
        Route::get('frameHole/{id}/edit','Product\FrameHoleDefineController@edit');
        Route::put('updateFrameHole/{id}','Product\FrameHoleDefineController@update');
        Route::delete('frameHole/del','Product\FrameHoleDefineController@del');
        Route::post('frameHole/recover','Product\FrameHoleDefineController@cover');

        Route::get('products','Product\ProductController@productList');
        Route::get('products/{id}','Product\ProductController@product');
    });
});



Route::group(['prefix' => 'tb', 'middleware' => 'web'], function () {
    /*origin tao bao product manage interface*/
    /*=========== start ==========*/
    Route::get('downloadCheck', 'FileZIPController@downloadProductsCheck');
    Route::get('downloadQR', 'FileZIPController@download');
    Route::post('imageFiles', 'ProductTraceability\ImageController@handleTBProductImageFile');

    Route::post('produceParams/import', 'ProductTraceability\ProduceParamController@import');

    Route::get('introductions/{id}', 'ProductTraceability\IntroductionController@getIntroduction');
    Route::post('introductions', 'ProductTraceability\IntroductionController@create');
    Route::put('introductions/{id}', 'ProductTraceability\IntroductionController@modify');
    Route::post('introductions/recover', 'ProductTraceability\IntroductionController@recover');
    Route::delete('introductions/del', 'ProductTraceability\IntroductionController@del');

    Route::post('realProducts', 'ProductTraceability\RealProductController@create');
    Route::post('realProducts/createAgain', 'ProductTraceability\RealProductController@createAgain');
    Route::post('realProducts/recover', 'ProductTraceability\RealProductController@recover');
    Route::delete('realProducts/del', 'ProductTraceability\RealProductController@del');

    Route::get('products/{no}/produceParams', 'ProductTraceability\ProduceParamController@checkExist');
    Route::post('products', 'ProductTraceability\ProductController@create');
    Route::put('products/{id}', 'ProductTraceability\ProductController@create');
    Route::put('products/{id}/isSell', 'ProductTraceability\ProductController@changeSell');
    Route::post('products/recover', 'ProductTraceability\ProductController@recover');
    Route::delete('products/del', 'ProductTraceability\ProductController@del');

    Route::get('publications/{id}', 'ProductTraceability\PublicationController@getPublication');
    Route::post('publications', 'ProductTraceability\PublicationController@create');
    Route::post('publications/import', 'ProductTraceability\PublicationController@importPublication');
    Route::put('publications/{id}', 'ProductTraceability\PublicationController@modify');
    Route::post('publications/recover', 'ProductTraceability\PublicationController@recover');
    Route::delete('publications/del', 'ProductTraceability\PublicationController@del');

    Route::post('authors', 'ProductTraceability\AuthorController@create');
    Route::put('authors/{id}', 'ProductTraceability\AuthorController@modify');
    Route::post('authors/import', 'ProductTraceability\AuthorController@importAuthor');
    Route::post('authors/recover', 'ProductTraceability\AuthorController@recover');
    Route::delete('authors/del', 'ProductTraceability\AuthorController@del');

    Route::post('museums', 'ProductTraceability\MuseumController@create');
    Route::put('museums/{id}', 'ProductTraceability\MuseumController@modify');
    Route::post('museums/import', 'ProductTraceability\MuseumController@importMuseum');
    Route::post('museums/recover', 'ProductTraceability\MuseumController@recover');
    Route::delete('museums/del', 'ProductTraceability\MuseumController@del');

    Route::post('shops', 'ProductTraceability\ShopController@create');
    Route::put('shops/{id}', 'ProductTraceability\ShopController@modify');
    Route::post('shops/recover', 'ProductTraceability\ShopController@recover');
    Route::delete('shops/del', 'ProductTraceability\ShopController@del');
    /*============ end ===========*/
    /*tao bao product manage interface*/

    /*tao bao product manage view.*/
    /*=========== start ==========*/
    Route::get('createRealProduct', 'ProductTraceability\RealProductController@createView');
    Route::get('realProducts', 'ProductTraceability\RealProductController@index'); // list view
    Route::get('realProducts/{id}/edit', 'ProductTraceability\RealProductController@modifyView');

    Route::get('createProduct', 'ProductTraceability\ProductController@createView');
    Route::get('products', 'ProductTraceability\ProductController@productList'); // list view
    Route::get('products/{id}', 'ProductTraceability\ProductController@getProduct'); // info view
    Route::get('products/{id}/edit', 'ProductTraceability\ProductController@modifyView');

    Route::get('createPublication', 'ProductTraceability\PublicationController@createView');
    Route::get('publications', 'ProductTraceability\PublicationController@index'); // list view
    Route::get('publications/{id}/edit', 'ProductTraceability\PublicationController@modifyView');

    Route::get('createAuthor', function () {
        return view('thirdProduct.authorForm');
    });
    Route::get('authors', 'ProductTraceability\AuthorController@index'); // list view
    Route::get('authors/{id}/edit', 'ProductTraceability\AuthorController@modifyView');

    Route::get('createMuseum', function () {
        return view('thirdProduct.museumForm');
    });
    Route::get('museums', 'ProductTraceability\MuseumController@index'); // list view
    Route::get('museums/{id}/edit', 'ProductTraceability\MuseumController@modifyView');

    Route::get('createShop', function () {
        return view('thirdProduct.shopForm');
    });
    Route::get('shops', 'ProductTraceability\ShopController@index'); // list view
    Route::get('shops/{id}/edit', 'ProductTraceability\ShopController@modifyView');

    Route::get('createIntroduction', function () {
        return view('thirdProduct.introductionEditForm');
    });
    Route::get('introductions', 'ProductTraceability\IntroductionController@index'); // list view
    Route::get('introductions/{id}/edit', 'ProductTraceability\IntroductionController@modifyView');

    Route::get('realProductSearch', function (){
        return view('thirdProduct.realProductSearch');
    });
    Route::get('realProducts/{id}', 'ProductTraceability\RealProductController@getProduceView');

    Route::post('check/{id}', 'ProductTraceability\RealProductController@checkProduct');
    /*============ end ===========*/
    /*tao bao product manage view.*/

    Route::get('test/{tbl}', 'ProductTraceability\RealProductController@test');

    //-------------------------------------first phase taobao order handel -------------------------------------
    Route::post('taoBaoOrder/import', 'ProductTraceability\TBOrderController@import');
    Route::post('taoBaoOrderProduct/import', 'ProductTraceability\RealProductController@import');
    Route::post('orders/{orderId}/ships', 'ProductTraceability\TBOrderShipController@addShip');

    Route::get('orderSearch', function (){
        return view('thirdProduct.orderSearch');
    });
    Route::get('orders', 'ProductTraceability\TBOrderController@index');
});

