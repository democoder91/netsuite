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
    return view('welcome');
});

