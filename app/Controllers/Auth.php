<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected UserModel $users;

    public function __construct()
    {
        $this->users = new UserModel();
    }

    public function showLogin()
    {
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function login()
    {
        $rules = [
            'login'    => 'required',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $user = $this->users->findByLogin($this->request->getPost('login'));

        if (! $user || ! password_verify($this->request->getPost('password'), $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Invalid credentials.');
        }

        if (! $user['is_active']) {
            return redirect()->back()->with('error', 'Your account has been deactivated.');
        }

        $this->session->set([
            'userId'      => $user['id'],
            'username'    => $user['username'],
            'fullName'    => $user['full_name'],
            'role'        => $user['role'],
            'isLoggedIn'  => true,
        ]);

        return redirect()->to('/dashboard')->with('success', 'Welcome back, ' . $user['full_name'] . '!');
    }

    public function showRegister()
    {
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/register');
    }

    public function register()
    {
        $rules = [
            'username'         => 'required|min_length[3]|max_length[100]|is_unique[users.username]|alpha_numeric',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'full_name'        => 'required|min_length[2]|max_length[150]',
            'password'         => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
            'role'             => 'required|in_list[seller,bidder]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $id = $this->users->registerUser([
            'username'  => $this->request->getPost('username'),
            'email'     => $this->request->getPost('email'),
            'full_name' => $this->request->getPost('full_name'),
            'phone'     => $this->request->getPost('phone'),
            'password'  => $this->request->getPost('password'),
            'role'      => $this->request->getPost('role'),
        ]);

        if (! $id) {
            return redirect()->back()->withInput()->with('error', 'Could not create account. Please try again.');
        }

        return redirect()->to('/login')->with('success', 'Account created! Please log in.');
    }

    public function logout()
    {
        $this->session->destroy();

        return redirect()->to('/login')->with('success', 'You have been logged out.');
    }
}
