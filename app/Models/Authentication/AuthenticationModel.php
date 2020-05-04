<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models\Authentication;

use CodeIgniter\Model;
use App\Models\User\UserModel;
use CodeIgniter\Database\ConnectionInterface;

class AuthenticationModel extends Model {

    function __construct() {
        
    }

    public function init() {
        $dbHandle = \Config\Database::connect();
        if ($dbHandle == '') {
            error_log('can not create db handle', 'qna');
            echo (print_r($dbHandle, true));
        }
        return $dbHandle;
    }

    public function signup($data) {
        $userModel = new UserModel();
        $insertId = $userModel->insert($data);
        //echo "isseettt: ".$insertId;
        $user = $userModel->find($insertId);
        return $user;
    }

    public function authenticate($username, $password, $userTypeId, $overRideTypeIdCheck) {
        $dbHandle = \Config\Database::connect();
        // $dbHandle = $this->init();

        $userTypeIdCond = "";
        if ($overRideTypeIdCheck == FALSE) {
            $userTypeIdCond = "AND a.userTypeId = $userTypeId ";
        }
        $username2 = strtolower($username);
        $query = $dbHandle->query("SELECT  a.id,
                    a.phone,
                    a.email,
                    a.userTypeId,
                    a.firstName,
                    a.lastName,
                    a.dateOfBirth,
                    a.sex,
                    a.verified,
                    a.verifiedEmail,
                    a.verifiedPhone,
                    a.status,
                    a.created_at, 
                    a.updated_at,
                    a.deleted_at,
                    b.`code` as userTypeCode,
                    b.`name` as userTypeName
                    FROM `user` as a,  `user_type` as b 
                    WHERE a.userTypeId=b.id  AND  LCASE(email)='" . strtolower($username) . "' and password='" . $password . "' "
                . $userTypeIdCond . " and a.status='active';");
        //$result = mysqli_query($query);
        $results = $query->getResultArray();
        $userArray = array();
        $i = 0;
        foreach ($results as $row) {
            $userArray[$i] = $row;
            $i++;
        }
       
        return $userArray;
    }

    public function checkIfSessionAlreadyActiveByUserId($userId, $sessionTypeId) {
        $dbHandle = \Config\Database::connect();
        $query = $dbHandle->query("SELECT * FROM `user_session` where `userId`=" . $userId . " AND session_type_id=" . $sessionTypeId . " AND created_at > DATE_SUB(NOW(), INTERVAL " . SESSION_EXPIRY_MINS . " MINUTE) "
                . "AND status='active';");
        $results = $query->getResultArray();
        $sessionDataArray = array();
        if (count($results) == 1) {
            $sessionDataArray = $results[0];
        }
       
        return $sessionDataArray;
    }
    
    

    public function getUserDetailsById($userId) {
        $dbHandle = $this->init();
        $query = "SELECT  a.id,
                    a.phone,
                    a.email,
                    a.userTypeId,
                    a.name,
                    a.firstName,
                    a.lastName,
                    a.dateOfBirth,
                    a.sex,
                    a.verifiedEmail,
                    a.verifiedPhone,
                    a.tcoinBalance,
                    a.active,
                    a.archived,
                    a.created,
                    a.updated,
                    a.deleted,
                    b.`code` as userTypeCode,
                    b.`name` as userTypeName
                        FROM `user` as a,  `user_type` as b WHERE a.userTypeId=b.id  and a.id=" . $userId . ";";

        //echo $query;
        $result = $dbHandle->query($query) or die($dbHandle->_error_message());
        $userArray = array();
        $i = 0;
        foreach ($result->result_array() as $row) {
            $userArray[$i] = $row;
            $i++;
        }
        #var_dump($userArray);
        return $userArray;
    }

    //TODO: check base encode 64
    public function saveAccessToken($userId, $accessToken, $sessionTypeId, $refreshTokenId) {
        //TODO: Get sessionTypeId
        $dbHandle = \Config\Database::connect();
      
        $query = "INSERT INTO `user_session`
                            (`userId`,
                            `session_type_id`,
                            `access_token`,
                            `access_token_expiration`,
                            `refresh_token_id`,
                            `user_session_start`,
                            `status`
                            )
                             VALUES('" . $userId . "','" . $sessionTypeId . "','" . $accessToken . "','" . SESSION_EXPIRY_MINS . "',".$refreshTokenId.",NOW(),'active');";
       
        $dbHandle->query($query);
        $lastSessionId = $dbHandle->insertID();
        $sessionDataArray = $this->getSessionDataById($lastSessionId);
        return $sessionDataArray;
    }

    public function getSessionDataById($recordId) {
         $dbHandle = \Config\Database::connect();
        
        //va
        $query = $dbHandle->query( "SELECT * FROM user_session where id=$recordId;");
        $results = $query->getResultArray();
        $sessionDataArray = array();
        if (count($results) == 1) {
            $sessionDataArray = $results[0];
        }
        return $sessionDataArray;
    }

    public function logout($accessToken) {
          $dbHandle = \Config\Database::connect();
        
        //va
        $query = $dbHandle->query("UPDATE `user_session` SET `user_session_end`= NOW(), status='".INACTIVE."' WHERE access_token='" . $accessToken . "';");
        //echo $query;
        //$result = $dbHandle->query($query) or die($dbHandle->_error_message());
    }

    public function checkSessionValidOnUserIdAndAccessToken($userId, $accessToken) {
        $dbHandle = $this->init();
        $query = "SELECT * FROM `user_session` where `userId`=" . $userId . " AND `access_token`='" . $accessToken . "' AND created > DATE_SUB(NOW(), INTERVAL " . SESSION_EXPIRY_MINS . " MINUTE)"
                . " AND active=1 AND deleted=0;";

        $result = $dbHandle->query($query) or die($dbHandle->_error_message());
        if ($result->num_rows() == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function checkSessionValidOnAccessToken($accessToken, $sessionTypeId) {
         $dbHandle = \Config\Database::connect();
        $sessionTypeIdString = "";
        if ($sessionTypeId != -1) {
            $sessionTypeIdString .= " AND session_type_id=" . $sessionTypeId;
        }
        $query = "SELECT * FROM `user_session` where `access_token`='" . $accessToken . "' AND session_type_id=" . $sessionTypeId . " AND created_at > DATE_SUB(NOW(), INTERVAL " . SESSION_EXPIRY_MINS . " MINUTE) "
                . " AND status='active';";
        //echo $query;
        //die();
        $queryResponse = $dbHandle->query($query);
        $results = $queryResponse->getResultArray();
        if ((count($results)) == 1) {
            return "true";
        } else {
            return "false";
        }
    }

    function getUserIdFromAccessToken($accessToken) {
         $dbHandle = \Config\Database::connect();
      
        $query = $dbHandle->query("SELECT userId FROM `user_session` where `access_token`='" . $accessToken . "' AND created > DATE_SUB(NOW(), INTERVAL " . SESSION_EXPIRY_MINS . " MINUTE) AND status='active';");

         $results = $query->getResultArray();
        $userId = -1;
        if (count($results) == 1) {
            $userId = $results[0]['userId'];
        }
        return $userId;
    }

    function checkIfUserAlreadyExistsByEmail($username, $userTypeId) {
        $dbHandle = \Config\Database::connect();
        $query = $dbHandle->query("SELECT * FROM user where LCASE(email)='" . strtolower($username) . "' AND userTypeId=" . $userTypeId . " and status='" . ACTIVE . "'");
        $results = $query->getResult();
        //$result = $dbHandle->query($query) or die($dbHandle->_error_message());

        if (count($results) >= 1) {
            return "true";
        } else {
            return "false";
        }
    }

    public function getUserTypes() {
        $dbHandle = $this->init();
        $query = "SELECT * FROM user_type;";
        $result = $dbHandle->query($query) or die($dbHandle->_error_message());
        $userTypesArray = array();
        $i = 0;
        foreach ($result->result_array() as $row) {
            $userTypesArray[$i] = $row;
            $i++;
        }
        #var_dump($userArray);
        return $userTypesArray;
    }

    public function getSessionTypes() {
        $dbHandle = $this->init();
        $query = "SELECT * FROM user_session_type;";
        $result = $dbHandle->query($query) or die($dbHandle->_error_message());
        $sessionTypesArray = array();
        $i = 0;
        foreach ($result->result_array() as $row) {
            $sessionTypesArray[$i] = $row;
            $i++;
        }
        #var_dump($userArray);
        return $sessionTypesArray;
    }

    public function validateRefreshToken($userId, $refreshToken, $checkActive, $checkExpired) {
        $dbHandle = \Config\Database::connect();
        $activeCheckCond = "";
        $expiredCheckCond = "";
        if ($checkActive == TRUE) {
            $activeCheckCond = " AND status='active'";
        }
        if ($checkExpired == TRUE) {
            $expiredCheckCond = " AND created > DATE_SUB(NOW(), INTERVAL " . SESSION_EXPIRY_MINS . " DAY)";
        }
        $query= $dbHandle->query("SELECT * FROM user_refresh_tokens where userId=$userId AND refreshToken='" . $refreshToken . "'" . $activeCheckCond . ""
                . $expiredCheckCond . ";");
        $results = $query->getResultArray();
        $refreshTokenDataArray = array();
        $i = 0;

        foreach($results as $row) {
            $refreshTokenDataArray[$i] = $row;
            $i++;
        }
        return $refreshTokenDataArray;
    }

    public function insertNewRefreshToken($userId, $refreshToken, $oldRefreshTokenId) {
        $dbHandle = $this->init();

        $query = "INSERT INTO `user_refresh_tokens`
                    (`userId`,
                    `refreshToken`,
                    `oldRefreshTokenId`,
                    `validityDays`,
                    `active`,
                    `archived`,
                    `created`,
                    `updated`,
                    `deleted`)
                    VALUES
                    (
                    $userId,'" . $refreshToken . "',
                    $oldRefreshTokenId," . REFRESH_TOKEN_EXPIRY_DAYS .
                ",1,
                    0,
                    NOW(),
                    NOW(),
                   0);";

        //echo $query;        die();
        $result = $dbHandle->query($query) or die($dbHandle->_error_message());
        $lastRecordId = $dbHandle->insert_id();
        $refreshTokenDataArray = $this->getRefreshTokenDataById($lastRecordId);
        return $refreshTokenDataArray;
    }

    public function getRefreshTokenDataById($recordId) {
        $dbHandle = $this->init();
        $query = "SELECT * FROM user_refresh_tokens where id=$recordId;";
        $result = $dbHandle->query($query) or die($dbHandle->_error_message());
        $refreshTokenDataArray = array();
        if ($result->num_rows() == 1) {
            $row = $result->row_array();
            $refreshTokenDataArray = $row;
        }
        return $refreshTokenDataArray;
    }

    public function getRefreshTokenIdFromRefreshToken($refreshToken, $checkActive) {
        $dbHandle = $this->init();
        $activeCheckCond = "";
        if ($checkActive == TRUE) {
            $activeCheckCond = " AND active=1 AND deleted=0";
        }
        $query = "SELECT * FROM user_refresh_tokens where refreshToken='" . $refreshToken . "'" . $activeCheckCond . ";";
        $result = $dbHandle->query($query) or die($dbHandle->_error_message());
        $refreshTokenDataArray = array();
        $i = 0;

        foreach ($result->result_array() as $row) {
            $refreshTokenDataArray[$i] = $row;
            $i++;
        }
        return $refreshTokenDataArray;
    }

    public function getUserDataFromRefreshToken($refreshToken) {
        $dbHandle = $this->init();
        $query = "SELECT  a.id,
                    a.phone,
                    a.email,
                    a.userTypeId,
                    a.name,
                    a.firstName,
                    a.lastName,
                    a.dateOfBirth,
                    a.sex,
                    a.verifiedEmail,
                    a.verifiedPhone,
                    a.tcoinBalance,
                    a.active,
                    a.archived,
                    a.created,
                    a.updated,
                    a.deleted,
                    b.`code` as userTypeCode,
                    b.`name` as userTypeName
                    FROM `user` as a, `user_type` as b, user_refresh_tokens  as c
                    WHERE a.userTypeId=b.id  AND a.id= c.userId
                    AND c.refreshToken='" . $refreshToken . "' and c.active=1 AND c.deleted=0";

        //$result = mysqli_query($query);
        $result = $dbHandle->query($query) or die($dbHandle->_error_message());
        $userArray = array();
        $i = 0;
        foreach ($result->result_array() as $row) {
            $userArray[$i] = $row;
            $i++;
        }
        //var_dump($userArray);
        return $userArray;
    }

    public function deactivateOldAccessToken($userId, $oldAccessToken) {
        $dbHandle = \Config\Database::connect();
        $query = $dbHandle->query( "UPDATE `user_session` SET status='".INACTIVE."' WHERE userId=$userId AND access_token='" . $oldAccessToken . "';");
        
        if ($dbHandle->affectedRows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function deactivateOldRefreshToken($userId, $oldRefreshToken) {
        $dbHandle = $this->init();
        $query = "UPDATE `user_refresh_tokens` SET active=0,deleted =1 WHERE userId=$userId AND refreshToken='" . $oldRefreshToken . "';";
        $result = $dbHandle->query($query) or die($dbHandle->_error_message());
        if ($dbHandle->affected_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function saveAccountVerifyToken($userId, $verifyToken) {
        //TODO: Get sessionTypeId
        $dbHandle = $this->init();
        $query = "INSERT INTO `user_account_verify_tokens`
                    (`userId`,
                    `token`,
                    `active`,
                    `archived`,
                    `created`,
                    `updated`,
                    `deleted`)
                    VALUES
                    ($userId,
                    '" . $verifyToken . "',
                    1,
                    0,
                    NOW(),
                    NOW(),
                    0);";

        //echo $query;
        $result = $dbHandle->query($query) or die($dbHandle->_error_message());
        $lastInsertId = $dbHandle->insert_id();
        return $lastInsertId;
    }

    public function getAccountVerifyToken($verifyToken) {
        //TODO: Get sessionTypeId
        $dbHandle = $this->init();
        $query = "SELECT `id`,
                        `userId`,
                        `token`,
                        `active`,
                        `archived`,
                        `created`,
                        `updated`,
                        `deleted`
                    FROM `user_account_verify_tokens` 
                    WHERE token='" . $verifyToken . "'"
                . "AND active=1 AND archived=0;";

        //echo $query;
        $result = $dbHandle->query($query) or die($dbHandle->_error_message());
        $verifyTokenDataArray = array();
        $i = 0;

        foreach ($result->result_array() as $row) {
            $verifyTokenDataArray[$i] = $row;
            $i++;
        }
        return $verifyTokenDataArray;
    }

    public function activateAccountVerifyToken($userId, $verifyToken) {
        //TODO: Get sessionTypeId
        $dbHandle = $this->init();
        $query = "UPDATE `user_account_verify_tokens`
                SET
                `active` = 0,
                `updated` = NOW(),
                `archived` = 1,
                `deleted` = 0
                WHERE `userId` = $userId
                AND token= '$verifyToken';";
        $result = $dbHandle->query($query) or die($dbHandle->_error_message());
        if ($dbHandle->affected_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function activateUserAccount($userId) {
        $dbHandle = $this->init();
        $query = "UPDATE `user` SET verified=1,active=1 WHERE id=$userId;";
        $result = $dbHandle->query($query) or die($dbHandle->_error_message());
        if ($dbHandle->affected_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
