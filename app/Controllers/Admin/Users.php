<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Users extends BaseController
{
    protected UserModel $users;

    public function __construct()
    {
        $this->users = new UserModel();
    }

    public function index()
    {
        return view('admin/users', [
            'title' => 'Manage Users',
            'users' => $this->users->orderBy('created_at', 'DESC')->findAll(),
        ]);
    }

    public function toggleActive(int $id)
    {
        $user = $this->users->find($id);
        if ($user) {
            $this->users->update($id, ['is_active' => $user['is_active'] ? 0 : 1]);
        }

        return redirect()->to('/admin/users')->with('success', 'User status updated.');
    }

    public function updateRole(int $id)
    {
        $role = $this->request->getPost('role');
        if (in_array($role, ['admin', 'seller', 'bidder'], true)) {
            $this->users->update($id, ['role' => $role]);
        }

        return redirect()->to('/admin/users')->with('success', 'Role updated.');
    }
}
