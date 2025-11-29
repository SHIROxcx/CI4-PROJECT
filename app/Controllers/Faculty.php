<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Faculty extends Controller
{
    protected $db;
    protected $session;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->session = session();
        helper(['url', 'form']);
    }

    /**
     * Faculty Dashboard
     */
    public function index()
    {
        // Check if user is faculty
        if ($this->session->get('role') !== 'faculty') {
            return redirect()->to('/unauthorized')->with('error', 'Access denied. Faculty role required.');
        }

        return redirect()->to('/faculty/dashboard');
    }

    /**
     * Main dashboard view
     */
    public function dashboard()
    {
        // Check if user is faculty
        if ($this->session->get('role') !== 'faculty') {
            return redirect()->to('/unauthorized')->with('error', 'Access denied. Faculty role required.');
        }

        $data = [
            'title' => 'Faculty Dashboard',
            'user_name' => $this->session->get('full_name'),
            'user_email' => $this->session->get('email'),
            'user_role' => $this->session->get('role')
        ];

        return view('faculty/dashboard', $data);
    }

    /**
     * Bookings page
     */
    public function bookings()
    {
        // Check if user is faculty
        if ($this->session->get('role') !== 'faculty') {
            return redirect()->to('/unauthorized')->with('error', 'Access denied. Faculty role required.');
        }

        $data = [
            'title' => 'My Bookings',
            'user_name' => $this->session->get('full_name'),
            'user_email' => $this->session->get('email'),
            'user_role' => $this->session->get('role')
        ];

        return view('faculty/bookings', $data);
    }

    /**
     * Profile page
     */
    public function profile()
    {
        // Check if user is faculty
        if ($this->session->get('role') !== 'faculty') {
            return redirect()->to('/unauthorized')->with('error', 'Access denied. Faculty role required.');
        }

        $userId = $this->session->get('user_id');

        // Get user details from database
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->to('/faculty/dashboard')->with('error', 'User not found.');
        }

        $data = [
            'title' => 'My Profile',
            'user' => $user,
            'user_name' => $this->session->get('full_name'),
            'user_email' => $this->session->get('email'),
            'user_role' => $this->session->get('role')
        ];

        return view('faculty/profile', $data);
    }

    /**
     * Booking history page
     */
    public function history()
    {
        // Check if user is faculty
        if ($this->session->get('role') !== 'faculty') {
            return redirect()->to('/unauthorized')->with('error', 'Access denied. Faculty role required.');
        }

        $data = [
            'title' => 'Booking History',
            'user_name' => $this->session->get('full_name'),
            'user_email' => $this->session->get('email'),
            'user_role' => $this->session->get('role')
        ];

        return view('faculty/history', $data);
    }
    public function book()
    {
        // Check if user is faculty
        if ($this->session->get('role') !== 'faculty') {
            return redirect()->to('/unauthorized')->with('error', 'Access denied. Faculty role required.');
        }

        // Get all facilities
        $facilitiesModel = new \App\Models\FacilityModel();
        $facilities = $facilitiesModel->findAll();

        $data = [
            'title' => 'Book Facility',
            'user_name' => $this->session->get('full_name'),
            'user_email' => $this->session->get('email'),
            'user_role' => $this->session->get('role'),
            'facilities' => $facilities
        ];

        return view('faculty/book', $data);
    }
    public function attendance()
    {
        // Check if user is faculty
        if ($this->session->get('role') !== 'faculty') {
            return redirect()->to('/unauthorized')->with('error', 'Access denied. Faculty role required.');
        }

        $data = [
            'title' => 'Booking History',
            'user_name' => $this->session->get('full_name'),
            'user_email' => $this->session->get('email'),
            'user_role' => $this->session->get('role')
        ];

        return view('faculty/attendance', $data);
    }
}
