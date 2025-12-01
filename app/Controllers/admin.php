<?php

namespace App\Controllers;

class Admin extends BaseController
{
    // Add this method at the beginning of your Admin class:
    private function checkAdminRole()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Admin access required');
        }
        
        return null;
    }

    public function dashboard()
    {
        $redirect = $this->checkAdminRole();
        if ($redirect) return $redirect;
        
        // load admin dashboard
        return view('admin/dashboard');
    }
    
    public function index()
    {
        $redirect = $this->checkAdminRole();
        if ($redirect) return $redirect;
        
        // load admin dashboard
        return view('admin/dashboard');
    }
    public function equipment()
    {
        $redirect = $this->checkAdminRole();
        if ($redirect) return $redirect;

        return view('admin/equipment');
    }

    public function plans()
    {
        $redirect = $this->checkAdminRole();
        if ($redirect) return $redirect;

        return view('admin/plans');
    }

    public function events()
    {
        $redirect = $this->checkAdminRole();
        if ($redirect) return $redirect;

        return view('admin/events');
    }
    

    public function users()
    {
        $redirect = $this->checkAdminRole();
        if ($redirect) return $redirect;
        
        return view('admin/users');
    }
    
    public function external()
    {
        $redirect = $this->checkAdminRole();
        if ($redirect) return $redirect;
        
        return view('admin/external');
    }
   
    public function booking()
    {
        $redirect = $this->checkAdminRole();
        if ($redirect) return $redirect;
        
        return view('admin/booking');
    }
   public function bookingManagement()
{
    $data = [
        'title' => 'Booking Management - CSPC Admin',
        'page' => 'booking-management'
    ];

    return view('admin/bookingManagement', $data);
}

/**
 * Alternative booking management page name
 */
public function bookings()
{
    return $this->bookingManagement();
}

public function student()
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

public function attendance()
{
    $redirect = $this->checkAdminRole();
    if ($redirect) return $redirect;

    $session = session();
    $data = [
        'isLoggedIn' => true,
        'userRole' => $session->get('role'),
        'userName' => $session->get('full_name'),
        'userId' => $session->get('user_id'),
        'userEmail' => $session->get('email')
    ];

    return view('admin/attendance', $data);
}

public function calendarDebug()
{
    $redirect = $this->checkAdminRole();
    if ($redirect) return $redirect;

    return view('admin/calendar_debug');
}

public function facilitiesManagement()
{
    $redirect = $this->checkAdminRole();
    if ($redirect) return $redirect;

    $session = session();
    $data = [
        'isLoggedIn' => true,
        'userRole' => $session->get('role'),
        'userName' => $session->get('full_name'),
        'userId' => $session->get('user_id'),
        'userEmail' => $session->get('email')
    ];

    return view('admin/facilities-management', $data);
}
}

