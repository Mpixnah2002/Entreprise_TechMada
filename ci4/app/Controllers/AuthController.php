<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        // show login form
        return view('auth/login');
    }

    public function attempt()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('email', $email)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'Email introuvable');
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Mot de passe incorrect');
        }

        // set session
        $sess = [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'nom' => $user['nom'],
            'isLoggedIn' => true,
        ];
        session()->set($sess);

        // redirect by role
        if ($user['role'] === 'admin') {
            return redirect()->to('/admin');
        }
        if ($user['role'] === 'rh') {
            return redirect()->to('/rh');
        }
        return redirect()->to('/employee');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login');
    }
}
