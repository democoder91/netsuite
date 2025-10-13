<?php

use Illuminate\Support\Facades\Route;
use NetSuite\Classes\Account;
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

Route::get('/', function () {
    $service = new NetSuiteService();

    // Example: Fetch all accounts
//    $getAllRequest = new GetRequest();
//    $getAllRequest->baseRef = new RecordRef();
//    $getAllRequest->baseRef->type = RecordType::account;
//    // id
//    $getAllRequest->baseRef->internalId = '1'; // Replace with a valid internal ID
//    $getAllResponse = $service->get($getAllRequest);

    $getAllRequest = new SearchRequest();
    $getAllRequest->searchRecord = new \NetSuite\Classes\AccountingPeriodSearch();
    $getAllResponse = $service->search($getAllRequest);

//    $getAllRequest = new GetAllRequest();
//    $getAllRequest->record = new GetAllRecord();
//    $getAllRequest->record->recordType = GetAllRecordType::budgetCategory;
//    $getAllResponse = $service->getAll($getAllRequest);




    dd($getAllResponse);

});
