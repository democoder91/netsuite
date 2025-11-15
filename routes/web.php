<?php

use Illuminate\Support\Facades\Route;
use NetSuite\Classes\AsyncGetListRequest;
use NetSuite\Classes\GetAllRecord;
use NetSuite\Classes\GetAllRecordType;
use NetSuite\Classes\GetAllRequest;
use NetSuite\Classes\GetListRequest;
use NetSuite\Classes\GetRequest;
use NetSuite\Classes\RecordRef;
use NetSuite\Classes\RecordType;
use NetSuite\Classes\SearchRequest;
use NetSuite\Classes\VendorReturnAuthorization;
use NetSuite\NetSuiteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    // send a get record request to netsuite for a vendor return authorization record with internal id 12345
    $service = new NetSuiteService();
    $getRequest = new GetRequest();
    $recordRef = new RecordRef();
    $recordRef->internalId = '12345';
    $recordRef->type = RecordType::vendorReturnAuthorization;
    $getRequest->baseRef = $recordRef;
    $getResponse = $service->get($getRequest);
    dd($getResponse);
});
