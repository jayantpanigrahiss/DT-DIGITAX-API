<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function checkSessionValidOnUserIdAndAccessToken($userId, $accessToken) {
    // Get a reference to the controller object
    $CI = get_instance();
    // You may need to load the model if it hasn't been pre-loaded
    
    $CI->load->model('accesscontrolmodel');
    $result = $CI->accesscontrolmodel->checkSessionValidOnUserIdAndAccessToken($userId, $accessToken);
    return $result;
}

//TODO: User does not need to pass UserId??
function checkSessionValidOnAccessToken($accessToken,$userTypeId=-1) {
    // Get a reference to the controller object
    //$CI = get_instance();
    $authModel = new App\Models\Authentication\AuthenticationModel();

    
    $result = $authModel->checkSessionValidOnAccessToken($accessToken,$userTypeId);
    return $result;
}

function getUserIdFromAccessToken($accessToken){
    $CI = get_instance();

    // You may need to load the model if it hasn't been pre-loaded
    $CI->load->model('accesscontrolmodel');
    $result = $CI->accesscontrolmodel->getUserIdFromAccessToken($accessToken);
    return $result;
}


function getUserdetails($userId){
    $CI = get_instance();

    // You may need to load the model if it hasn't been pre-loaded
    $CI->load->model('accesscontrolmodel');
    $result = $CI->accesscontrolmodel->getUserDetailsById($userId);
    return $result;
}




function getPDTSessExpTimeFromStrDate($sessionCreationTimeString){
    $usPDTTimeZone = new DateTimeZone('America/Los_Angeles');
    $dateTime = new DateTime($sessionCreationTimeString, $usPDTTimeZone);
    $dateTime->modify("+".SESSION_EXPIRY_MINS." minutes");
     $formattedDateTimeStr = $dateTime->format('Y-m-d H:i:s');
    return $formattedDateTimeStr;
   
}

function getUTCSessExpTimeFromStrDate($sessionCreationTimeString){
    $dateTime = new DateTime($sessionCreationTimeString);
    $dateTime->modify("+".SESSION_EXPIRY_MINS." minutes");
    $formattedDateTimeStr = $dateTime->format('Y-m-d H:i:s');
    return $formattedDateTimeStr;
   
}

function getPDTRefreshTokenExpTimeFromStrDate($refreshTokenCreationTimeString){
    $usPDTTimeZone = new DateTimeZone('America/Los_Angeles');
    $dateTime = new DateTime($refreshTokenCreationTimeString, $usPDTTimeZone);
    $dateTime->modify("+".REFRESH_TOKEN_EXPIRY_DAYS." days");
    $formattedDateTimeStr = $dateTime->format('Y-m-d H:i:s');
    return $formattedDateTimeStr;
   
}

function getUTCRefreshTokenExpTimeFromStrDate($refreshTokenCreationTimeString){
    $dateTime = new DateTime($refreshTokenCreationTimeString);
    $dateTime->modify("+".REFRESH_TOKEN_EXPIRY_DAYS." days");
    $formattedDateTimeStr = $dateTime->format('Y-m-d H:i:s');
    return $formattedDateTimeStr;
   
}

function getPDTSessExpTimeFromUTCDate($sessionCreationTime){
    $usPDTTimeZone = new DateTimeZone('America/Los_Angeles');
    $sessionCreationTime->setTimezone($usPDTTimeZone);
    $sessionCreationTime->modify("+".SESSION_EXPIRY_MINS." minutes");
    $formattedDateTimeStr = $sessionCreationTime->format('Y-m-d H:i:s');
    return $formattedDateTimeStr;
   
}
