<?php

namespace App\Services;

use App\Services\Interfaces\UserServiceInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService implements UserServiceInterface
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers(array $filters = [], int $perPage = 15)
    {
        return $this->userRepository->getAllUsers($filters, $perPage);
    }

    public function getUserById(int $id)
    {
        return $this->userRepository->getUserById($id);
    }

    public function createUser(array $data)
    {
        // Şifre hash'leme (Model'de otomatik ama ek güvenlik)
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->userRepository->createUser($data);
    }

    public function updateUser(int $id, array $data)
    {
        // Şifre güncelleniyorsa hash'le
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->userRepository->updateUser($id, $data);
    }

    public function deleteUser(int $id)
    {
        return $this->userRepository->deleteUser($id);
    }

    public function getActiveUsers(int $perPage = 15)
    {
        return $this->userRepository->getActiveUsers($perPage);
    }

    public function getAdmins()
    {
        return $this->userRepository->getAdmins();
    }
}