<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models\User;

use CodeIgniter\Model;

class AddressModel extends Model {

    protected $DBGroup = 'default';
    protected $table = 'address';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['userId',
        'addressTypeId',
        'address1',
        'address2',
        'address3',
        'city',
        'state',
        'zip',
        'status'];
    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $afterInsert = ['getInsertedData'];
    protected $afterUpdate = ['getUpdatedData'];


    protected function getInsertedData(array $data) {
        return $data["id"];
    }

    protected function getUpdatedData(array $data) {
        return $data["id"][0];
       
    }

}
