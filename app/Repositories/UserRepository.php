<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class UserRepository implements UserRepositoryInterface
{
    public function getAllUsers(array $filters = [], int $perPage = 15)
    {
        $query = User::query();

        // Filtreleme
        if (isset($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (isset($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }

        if (isset($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        // Performans için sadece gerekli kolonları çek
        return $query->select(['id', 'name', 'email', 'role', 'is_active', 'created_at'])
                     ->orderBy('created_at', 'desc')
                     ->paginate($perPage);
    }

    public function getUserById(int $id)
    {
        // Cache ile performans artışı (5 dakika cache)
        return Cache::remember("user_{$id}", 300, function () use ($id) {
            return User::select(['id', 'name', 'email', 'role', 'is_active', 'created_at', 'updated_at'])
                       ->findOrFail($id);
        });
    }

    public function createUser(array $data)
    {
        return User::create($data);
    }

    public function updateUser(int $id, array $data)
    {
        $user = User::findOrFail($id);
        $user->update($data);
        
        // Cache'i temizle
        Cache::forget("user_{$id}");
        
        return $user->fresh();
    }

    public function deleteUser(int $id)
    {
        $user = User::findOrFail($id);
        
        // Cache'i temizle
        Cache::forget("user_{$id}");
        
        return $user->delete();
    }

    public function getActiveUsers(int $perPage = 15)
    {
        return User::active()
                   ->select(['id', 'name', 'email', 'role', 'created_at'])
                   ->orderBy('name')
                   ->paginate($perPage);
    }

    public function getAdmins()
    {
        // Admin listesi cache'lenebilir (nadiren değişir)
        return Cache::remember('admin_users', 600, function () {
            return User::admins()
                       ->active()
                       ->select(['id', 'name', 'email'])
                       ->get();
        });
    }
}