<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers;

class Auth extends BaseController {

    public function index() {
        return view('welcome_message');
    }

    public function signin() {
        $authenticationModel = new \App\Models\Authentication\AuthenticationModel();
        helper("auth_helper");
        $json = file_get_contents('php://input');

        $signInData = json_decode($json, true);
        $userData = $authenticationModel->authenticate($signInData['email'], $signInData['password'], WEB_USER_TYPE_ID, false);
        $responseArray = array();
        $statusArray = array();
        $sessionDataArray = array();
        if (!empty($userData)) {
            if ($userData[0]["verified"] == 0) {
                $responseArray["data"] = "";
                $statusArray["status_code"] = 401;
                $statusArray["status_message"] = "ACCOUNT_NOT_VERIFIED";
                $responseArray["status"] = $statusArray;
            } else {
                $activeSessionData = $this->getAccessTokenForLogin($userData, WEB_SESSION_TYPE_ID);
                $sessionDataArray["accessToken"] = $activeSessionData['access_token'];
                $sessionDataArray["expirationMinutes"] = SESSION_EXPIRY_MINS;
                $sessionDataArray["UTCExpirationTime"] = getUTCSessExpTimeFromStrDate($activeSessionData["created_at"]);
                $PTSessionExpTime = getPDTSessExpTimeFromStrDate($activeSessionData["created_at"]);
                $sessionDataArray["PTExpirationTime"] = $PTSessionExpTime;

                $responseArray["data"]["user"] = $userData[0];
                $responseArray["data"]['session'] = $sessionDataArray;
                $statusArray["status_code"] = 200;
                $statusArray["status_message"] = "SUCCESS";
                $responseArray["status"] = $statusArray;
            }
        } else {
            $responseArray["data"] = "";
            $statusArray["status_code"] = 401;
            $statusArray["status_message"] = "WRONG_EMAIL_OR_PASSWORD";
            $responseArray["status"] = $statusArray;
        }
        echo json_encode($responseArray);
        // $this->load->view('hello');
    }

    public function signup() {
        $authenticationModel = new \App\Models\Authentication\AuthenticationModel();
        helper("auth_helper");
        #var_dump($_POST);
        $responseArray = array();
        $statusArray = array();
        $refreshTokenDataResponse = array();
        $json = file_get_contents('php://input');
        $signUpData = json_decode($json, true);

        $userALreadyExist = $this->checkIfUserAlreadyExistsByEmail($signUpData['email'], WEB_USER_TYPE_ID);

        if ($userALreadyExist == "false") {
            $signUpData["userTypeId"] = WEB_USER_TYPE_ID;
            $userData = $authenticationModel->signup($signUpData);
            // var_dump($userData);
            if (!empty($userData)) {
                //$this->sendAccountVerifyEmail($userData);
                $responseArray["data"] ["user"] = $userData;
                $statusArray["status_code"] = 201;
                $statusArray["status_message"] = "CREATE_SUCCESS";
                $responseArray["status"] = $statusArray;
            } else {
                $responseArray["data"] = "";
                $statusArray["status_code"] = 500;
                $statusArray["status_message"] = "INTERNAL_SERVER_ERROR";
                $responseArray["status"] = $statusArray;
            }
        } else {
            $statusArray["status_code"] = 400;
            $statusArray["status_message"] = "USER_ALREADY_EXISTS";
            $responseArray["status"] = $statusArray;
        }

        echo json_encode($responseArray);
    }

    protected function checkIfUserAlreadyExistsByEmail($email, $userTypeId) {
        $authenticationModel = new \App\Models\Authentication\AuthenticationModel();

        $response = $authenticationModel->checkIfUserAlreadyExistsByEmail($email, $userTypeId);
        return $response;
    }

    function saveAccessToken($userId, $accessToken, $sessionTypeId, $refreshTokenId) {
        $authenticationModel = new \App\Models\Authentication\AuthenticationModel();
        helper("auth_helper");
        $response = $authenticationModel->saveAccessToken($userId, $accessToken, $sessionTypeId, $refreshTokenId);
        return $response;
    }

    public function generateNewAccessToken() {
        $authenticationModel = new \App\Models\Authentication\AuthenticationModel();
        $userModel = new \App\Models\User\UserModel();
        helper("auth_helper");
        #var_dump($_POST);
        $responseArray = array();
        $statusArray = array();
        $json = file_get_contents('php://input');
        $accessTokenData = json_decode($json, true);

        $oldAccessToken = $accessTokenData["oldAccessToken"];
        $refreshToken = $accessTokenData["refreshToken"];
        $refreshTokenDataResponse = array();
        $userId = $authenticationModel->getUserIdFromAccessToken($oldAccessToken);
        $validateRefreshTokenData = $this->validateRefreshToken($userId, $refreshToken, TRUE, TRUE);
        if (!empty($validateRefreshTokenData)) {
            $userDataArray = $userModel->find($userId);
            $deactivateResponse = $this->deactivateOldAccessToken($userId, $oldAccessToken);
            if ($deactivateResponse == TRUE) {
                $newAccessToken = $this->generateAuthToken($userDataArray);
                $activeSessionData = $this->saveAccessToken($userDataArray[0]['id'], $newAccessToken, $userDataArray[0]["userTypeId"], $validateRefreshTokenData[0]['id']);

                $sessionDataArray["accessToken"] = $activeSessionData['access_token'];
                $sessionDataArray["expirationMinutes"] = SESSION_EXPIRY_MINS;
                $sessionDataArray["UTCExpirationTime"] = getUTCSessExpTimeFromStrDate($activeSessionData["created"]);
                $PTSessionExpTime = getPDTSessExpTimeFromStrDate($activeSessionData["created"]);
                $sessionDataArray["PTExpirationTime"] = $PTSessionExpTime;

                $responseArray["data"] ["user"] = $userDataArray[0];
                $responseArray["data"]['session'] = $sessionDataArray;
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

    public function deactivateOldAccessToken($userId, $oldAccessToken) {
        $authenticationModel = new \App\Models\Authentication\AuthenticationModel();
        helper("auth_helper");
        $response = $authenticationModel->deactivateOldAccessToken($userId, $oldAccessToken);
        return $response;
    }

    public function deactivateOldRefreshToken($userId, $oldRefreshToken) {
        $authenticationModel = new \App\Models\Authentication\AuthenticationModel();
        helper("auth_helper");
        $response = $authenticationModel->deactivateOldRefreshToken($userId, $oldRefreshToken);
        return $response;
    }

    public function getUserDataFromRefreshToken($refreshToken) {
        $authenticationModel = new \App\Models\Authentication\AuthenticationModel();
        helper("auth_helper");
        $userData = $authenticationModel->getUserDataFromRefreshToken($refreshToken);
        return $userData;
    }

    public function generateNewRefreshToken() {
        $authenticationModel = new \App\Models\Authentication\AuthenticationModel();
        helper("auth_helper");
        #var_dump($_POST);
        $responseArray = array();
        $statusArray = array();
        $json = file_get_contents('php://input');
        $refreshTokenData = json_decode($json, true);
        $email = $refreshTokenData["email"];
        $password = $refreshTokenData["password"];
        $oldRefreshToken = $refreshTokenData["oldRefreshToken"];
        $refreshTokenDataResponse = array();
        $userData = $authenticationModel->authenticate($email, $password, -1, true);
        if (!empty($userData)) {
            $oldRefreshTokenData = $this->validateRefreshToken($userData[0]['id'], $oldRefreshToken, TRUE, FALSE);
            $deactivateResponse = $this->deactivateOldRefreshToken($userData[0]['id'], $oldRefreshToken);
            if (!empty($oldRefreshTokenData) && $deactivateResponse == TRUE) {
                $newRefreshToken = $this->generateAuthToken(NULL);

                $newRefreshTokenData = $this->generateAndSaveRefreshToken($userData[0]['id'], $newRefreshToken, $oldRefreshTokenData[0]['id']);
                $refreshTokenDataResponse["refreshToken"] = $newRefreshTokenData['refreshToken'];
                $refreshTokenDataResponse["expirationDays"] = REFRESH_TOKEN_EXPIRY_DAYS;
                $refreshTokenDataResponse["UTCExpirationTime"] = getUTCRefreshTokenExpTimeFromStrDate($newRefreshTokenData["created"]);
                $pdtRefreshTokenExpDate = getPDTRefreshTokenExpTimeFromStrDate($newRefreshTokenData["created"]);
                $refreshTokenDataResponse["PTExpirationTime"] = $pdtRefreshTokenExpDate;

                $responseArray["data"]["refreshTokenData"] = $refreshTokenDataResponse;
                $statusArray["status_code"] = 200;
                $statusArray["status_message"] = "SUCCESS";
                $responseArray["status"] = $statusArray;
            } else {
                $responseArray["data"] = "";
                $statusArray["status_code"] = 400;
                $statusArray["status_message"] = "REFRESH_TOKEN_DOES_NOT_EXIST";
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

    public function validateRefreshToken($userId, $refreshToken, $checkActive, $checkExpired) {
        $authenticationModel = new \App\Models\Authentication\AuthenticationModel();
        helper("auth_helper");
        #var_dump($_POST);
        $responseArray = array();
        $statusArray = array();
        $validateResponseData = $authenticationModel->validateRefreshToken($userId, $refreshToken, $checkActive, $checkExpired);
        return $validateResponseData;
    }

    public function generateAndSaveRefreshToken($userId, $newRefreshToken, $oldRefreshTokenId) {
        $authenticationModel = new \App\Models\Authentication\AuthenticationModel();
        helper("auth_helper");
        #var_dump($_POST);
        $newRefreshToken = $this->generateAuthToken(NULL);
        $newRefreshTokenData = $authenticationModel->insertNewRefreshToken($userId, $newRefreshToken, $oldRefreshTokenId);
        return $newRefreshTokenData;
    }

    public function getRefreshTokenIdFromRefreshToken($refreshToken, $checkActive) {
        $authenticationModel = new \App\Models\Authentication\AuthenticationModel();
        helper("auth_helper");
        #var_dump($_POST);
        $responseArray = array();
        $statusArray = array();
        $responseData = $authenticationModel->getRefreshTokenIdFromRefreshToken($refreshToken, $checkActive);
        return $responseData;
    }

    function signout() {
        $authenticationModel = new \App\Models\Authentication\AuthenticationModel();
        helper("auth_helper");
        $responseArray = array();
        $statusArray = array();
        $json = file_get_contents('php://input');
        $accessTokenData = json_decode($json, true);
        if (isset($accessTokenData['accessToken'])) {
            $response = $authenticationModel->logout($accessTokenData['accessToken']);
            $statusArray["status_code"] = 200;
            $statusArray["status_message"] = "SUCCESS";
            $responseArray["status"] = $statusArray;
        } else {
            $responseArray["data"] = "";
            $statusArray["status_code"] = 500;
            $statusArray["status_message"] = "INTERNAL_SERVER_ERROR";
            $responseArray["status"] = $statusArray;
        }

        echo json_encode($responseArray);
    }

    public function getAccessTokenForLogin($userData, $sessionTypeId) {
        $authenticationModel = new \App\Models\Authentication\AuthenticationModel();
        helper("auth_helper");
        $sessionActiveData = $authenticationModel->checkIfSessionAlreadyActiveByUserId($userData[0]["id"], $sessionTypeId);
        if (empty($sessionActiveData)) {
            $accessToken = $this->generateAuthToken($userData);
            $sessionActiveData = $this->saveAccessToken($userData[0]["id"], $accessToken, $userData[0]["userTypeId"], 0);
        }

        return $sessionActiveData;
    }

    private function generateAuthToken($inUserData) {
        $token = \CodeIgniter\Encryption\Encryption::createKey(32);
        return md5(time());
    }

}
