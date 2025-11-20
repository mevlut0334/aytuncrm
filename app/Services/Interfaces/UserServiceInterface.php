<?php

namespace App\Services\Interfaces;

interface UserServiceInterface
{
    public function getAllUsers(array $filters = [], int $perPage = 15);
    
    public function getUserById(int $id);
    
    public function createUser(array $data);
    
    public function updateUser(int $id, array $data);
    
    public function deleteUser(int $id);
    
    public function getActiveUsers(int $perPage = 15);
    
    public function getAdmins();
}