<?php

namespace App\Controllers;

class Student extends BaseController
{
    public function __construct()
    {
        // Check if user is logged in
        $session = session();
        if (!$session->get('user_id')) {
            return redirect()->to('/login');
        }
        
        // Allow both admin and student roles
        $allowedRoles = ['student', 'admin'];
        if (!in_array($session->get('role'), $allowedRoles)) {
            return redirect()->to('/unauthorized');
        }
    }


    public function index()
    {
        $session = session();
        $data = [
            'isLoggedIn' => true,
            'userRole' => $session->get('role'),
            'userName' => $session->get('full_name'),
            'userId' => $session->get('user_id'),
            'userEmail' => $session->get('email')
        ];
        
        return view('student/dashboard', $data);
    }

    public function bookings()
    {
        $session = session();
        $data = [
            'isLoggedIn' => true,
            'userRole' => $session->get('role'),
            'userName' => $session->get('full_name'),
            'userId' => $session->get('user_id'),
            'userEmail' => $session->get('email')
        ];
        
        return view('student/bookings', $data);
    }

    public function profile()
    {
        $session = session();
        $data = [
            'isLoggedIn' => true,
            'userRole' => $session->get('role'),
            'userName' => $session->get('full_name'),
            'userId' => $session->get('user_id'),
            'userEmail' => $session->get('email')
        ];
        
        return view('student/profile', $data);
    }

        public function attendance()
    {
        $session = session();
        $data = [
            'isLoggedIn' => true,
            'userRole' => $session->get('role'),
            'userName' => $session->get('full_name'),
            'userId' => $session->get('user_id'),
            'userEmail' => $session->get('email')
        ];
        
        return view('student/attendance', $data);
    }

    public function history()
    {
        $session = session();
        $data = [
            'isLoggedIn' => true,
            'userRole' => $session->get('role'),
            'userName' => $session->get('full_name'),
            'userId' => $session->get('user_id'),
            'userEmail' => $session->get('email')
        ];
        
        return view('student/history', $data);
    }


    public function booking()
{
    $session = session();
    $data = [
        'isLoggedIn' => true,
        'userRole' => $session->get('role'),
        'userName' => $session->get('full_name'),
        'userId' => $session->get('user_id'),
        'userEmail' => $session->get('email')
    ];
    
    return view('admin/student', $data);
}



}
