<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers;

use App\Models\User\UserModel;
use App\Models\User\AddressModel;

class User extends BaseController {

    public function index() {
        return view('welcome_message');
    }

    public function updateProfile() {
        $userModel = new UserModel();
        $addressModel = new AddressModel();
        helper("auth_helper");
        $responseArray = array();
        $statusArray = array();
       
        $json = file_get_contents('php://input');
        $userProfileData = json_decode($json, true);

        $auth = $userProfileData["auth"];
        $user = $userProfileData["user"];
        $address = $userProfileData["address"];

        if (checkSessionValidOnAccessToken($auth["accessToken"], $auth["sessionTypeId"]) == "true") {
            $updateUserResponse =0;
            $updateAddressResponse=0;
            $address["userId"] = $user["id"];
            if(!isset($user["id"])){
                $updateUserResponse = $userModel->insert($user); 
            }else{
                $updateUserResponse =  $userModel->update($user["id"], $user); 
            }
            
            if(!isset($address["id"])){
                $updateAddressResponse = $addressModel->insert($address); 
            }else{
                $updateAddressResponse =  $addressModel->update($address["id"], $address); 
            }
        
   
           
            if (!empty($updateUserResponse) && !empty($updateAddressResponse)) {
                $updatedUser = $userModel ->find($updateUserResponse);
                $updatedAddress =  $addressModel ->find($updateAddressResponse);
                $appUpdateDataResponse["user"]=$updatedUser;
                $appUpdateDataResponse["address"]=$updatedAddress;
                $responseArray["data"] = $appUpdateDataResponse;
                $statusArray["status_code"] = 200;
                $statusArray["status_message"] = "SUCCESS";
                $responseArray["status"] = $statusArray;
            } else {
                $responseArray["data"] = "";
                $statusArray["status_code"] = 500;
                $statusArray["status_message"] = "INTERNAL_SERVER_ERROR";
                $responseArray["status"] = $statusArray;
            }
        } else {
            $responseArray["data"] = "";
            $statusArray["status_code"] = 401;
            $statusArray["status_message"] = "UNAUTHORIZED";
            $responseArray["status"] = $statusArray;
        }

        echo json_encode($responseArray);
    }

}
