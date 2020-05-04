<?php

namespace App\Models\User;

use CodeIgniter\Model;

class UserModel extends Model {

    protected $DBGroup = 'default';
    protected $table = 'user';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['firstName',
                                'lastName',
                                'phone',
                                'userTypeId',
                                'email',
                                'dateOfBirth',
                                'sex',
                                'verified',
                                'uuid',
                                'password',
                                'signInCount',
                                'currentSignInAt',
                                'lastSignInAt',
                                'currentSignInIp',
                                'lastSignInIp',
                                'failedAttempts',
                                'verifiedEmail',
                                'verifiedPhone',
                                'ownerAccountId',
                                'isPrimary',
                                'status'];
    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    //protected $afterInsert=['getInsertedId'];
    protected $afterInsert=['getInsertedData'];
    protected $afterUpdate=['getUpdatedData'];
  


    protected function getInsertedData(array $data) {
       return $data["id"];
    }

    protected function getUpdatedData(array $data) {
       
        return $this->find($data["id"][0]);
       
    }
 
}
