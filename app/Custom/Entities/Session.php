<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Users {

    private $id;
    private $firstName;
    private $lastName;
    private $phone;
    private $email;
    private $dateOfBirth;
    private $sex;
    private $verified;
    private $uuid;
    private $password;
    private $signInCount;
    private $currentSignInAt;
    private $lastSignInAt;
    private $currentSignInIp;
    private $lastSignInIp;
    private $failedAttempts;
    private $verifiedEmail;
    private $verifiedPhone;
    private $ownerAccountId;
    private $isPrimary;
    private $status;
    private $created_at;
    private $updated_at;
    private $deleted_at;

    function _construct() {
        
    }

    /*function _construct($id,
            $firstName,
            $lastName,
            $phone,
            $email,
            $dateOfBirth,
            $sex,
            $verified,
            $uuid,
            $password,
            $signInCount,
            $currentSignInAt,
            $lastSignInAt,
            $currentSignInIp,
            $lastSignInIp,
            $failedAttempts,
            $verifiedEmail,
            $verifiedPhone,
            $ownerAccountId,
            $isPrimary,
            $status,
            $created_at,
            $updated_at,
            $deleted_at) {
        
    }*/

    public function getId() {
        return $this->$id;
    }

    public function setId($id) {
        $this->$id = $id;
    }

    public function getFirstName() {
        return $this->$firstName;
    }

    public function setFirstName($firstName) {
        $this->$firstName = $firstName;
    }

    public function getLastName() {
        return $this->$lastName;
    }

    public function setLastName($lastName) {
        $this->$lastName = $lastName;
    }

    public function getPhone() {
        return $this->$phone;
    }

    public function setPhone($phone) {
        $this->$phone = $phone;
    }

    public function getEmail() {
        return $this->$email;
    }

    public function setEmail($email) {
        $this->$email = $email;
    }

    public function getDateOfBirth() {
        return $this->$dateOfBirth;
    }

    public function setDateOfBirth($dateOfBirth) {
        $this->$dateOfBirth = $dateOfBirth;
    }

    public function getSex() {
        return $this->$sex;
    }

    public function setSex($sex) {
        $this->$sex = $sex;
    }

    public function getVerified() {
        return $this->$verified;
    }

    public function setVerified($verified) {
        $this->$verified = $verified;
    }

    public function getUuid() {
        return $this->$uuid;
    }

    public function setUuid($uuid) {
        $this->$uuid = $uuid;
    }

    public function getPassword() {
        return $this->$password;
    }

    public function setPassword($password) {
        $this->$password = $password;
    }

    public function getSignInCount() {
        return $this->$signInCount;
    }

    public function setSignInCount($signInCount) {
        $this->$signInCount = $signInCount;
    }

    public function getCurrentSignInAt() {
        return $this->$currentSignInAt;
    }

    public function setCurrentSignInAt($currentSignInAt) {
        $this->$currentSignInAt = $currentSignInAt;
    }

    public function getLastSignInAt() {
        return $this->$lastSignInAt;
    }

    public function setLastSignInAt($lastSignInAt) {
        $this->$lastSignInAt = $lastSignInAt;
    }

    public function getCurrentSignInIp() {
        return $this->$currentSignInIp;
    }

    public function setCurrentSignInIp($currentSignInIp) {
        $this->$currentSignInIp = $currentSignInIp;
    }

    public function getLastSignInIp() {
        return $this->$lastSignInIp;
    }

    public function setLastSignInIp($lastSignInIp) {
        $this->$lastSignInIp = $lastSignInIp;
    }

    public function getFailedAttempts() {
        return $this->$failedAttempts;
    }

    public function setFailedAttempts($failedAttempts) {
        $this->$failedAttempts = $failedAttempts;
    }

    public function getVerifiedEmail() {
        return $this->$verifiedEmail;
    }

    public function setVerifiedEmail($verifiedEmail) {
        $this->$verifiedEmail = $verifiedEmail;
    }

    public function getVerifiedPhone() {
        return $this->$verifiedPhone;
    }

    public function setVerifiedPhone($verifiedPhone) {
        $this->$verifiedPhone = $verifiedPhone;
    }

    public function getOownerAccountId() {
        return $this->$ownerAccountId;
    }

    public function setOwnerAccountId($ownerAccountId) {
        $this->$ownerAccountId = $ownerAccountId;
    }

    public function isPrimary() {
        return $this->$isPrimary;
    }

    public function setIsPrimary($isPrimary) {
        $this->$isPrimary = $isPrimary;
    }

    public function getStatus() {
        return $this->$status;
    }

    public function setStatus($status) {
        $this->$status = $status;
    }

    public function getCreated_at() {
        return $this->$created_at;
    }

    public function setCreated_at($created_at) {
        $this->$created_at = $created_at;
    }

    public function getUpdated_at() {
        return $this->$updated_at;
    }

    public function setUpdated_at($updated_at) {
        $this->$updated_at = $updated_at;
    }

    public function getDeleted_at() {
        return $this->$deleted_at;
    }

    public function setDeleted_at($deleted_at) {
        $this->$deleted_at = $deleted_at;
    }

}
