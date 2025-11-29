<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $session = session();
        $data = [
            'isLoggedIn' => $session->get('user_id') !== null,
            'userRole' => $session->get('role'),
            'userName' => $session->get('full_name')
        ];
        
        return view('home', $data); // Create a home view
    }
    
    public function dashboard(): string
    {
        return view('user/dashboard');
    }

    public function facilities()
    {
        $session = session();
        $data = [
            'isLoggedIn' => $session->get('user_id') !== null,
            'userRole' => $session->get('role'),
            'userName' => $session->get('full_name')
        ];
        
        return view('facilities', $data); // Your facilities listing page
    }
    
    public function contact()
    {
        $session = session();
        $data = [
            'isLoggedIn' => $session->get('user_id') !== null,
            'userRole' => $session->get('role'),
            'userName' => $session->get('full_name')
        ];
        
        return view('contact', $data);
    }
    
    public function event()
    {
        return view('event'); // example additional page
    }
   
    public function gymnasium()
    {
        $session = session();
        $data = [
            'isLoggedIn' => $session->get('user_id') !== null,
            'userRole' => $session->get('role'),
            'userName' => $session->get('full_name'),
            'userEmail' => $session->get('email'),
            'userContact' => $session->get('contact_number')
        ];
        return view('facilities/gymnasium', $data);
    }

    public function pearlmini()
    {
        $session = session();
        $data = [
            'isLoggedIn' => $session->get('user_id') !== null,
            'userRole' => $session->get('role'),
            'userName' => $session->get('full_name'),
            'userEmail' => $session->get('email'),
            'userContact' => $session->get('contact_number')
        ];
        return view('facilities/pearlmini', $data);
    }

    public function Auditorium()
    {
        $session = session();
        $data = [
            'isLoggedIn' => $session->get('user_id') !== null,
            'userRole' => $session->get('role'),
            'userName' => $session->get('full_name'),
            'userEmail' => $session->get('email'),
            'userContact' => $session->get('contact_number')
        ];
        return view('facilities/Auditorium', $data);
    }

      public function Dormitory()
    {
        $session = session();
        $data = [
            'isLoggedIn' => $session->get('user_id') !== null,
            'userRole' => $session->get('role'),
            'userName' => $session->get('full_name'),
            'userEmail' => $session->get('email'),
            'userContact' => $session->get('contact_number')
        ];
        return view('facilities/Dormitory', $data);
    }

    public function FunctionHall()
    {
        $session = session();
        $data = [
            'isLoggedIn' => $session->get('user_id') !== null,
            'userRole' => $session->get('role'),
            'userName' => $session->get('full_name'),
            'userEmail' => $session->get('email'),
            'userContact' => $session->get('contact_number')
        ];
        return view('facilities/FunctionHall', $data);
    }


    public function PearlHotelRooms()
    {
        return view('facilities/PearlHotelRooms');
    }
    
    public function classroom()
    {
        $session = session();
        $data = [
            'isLoggedIn' => $session->get('user_id') !== null,
            'userRole' => $session->get('role'),
            'userName' => $session->get('full_name'),
            'userEmail' => $session->get('email'),
            'userContact' => $session->get('contact_number')
        ];
        return view('facilities/classroom', $data);
    }

        public function staffhouse(): string
    {
        $session = session();
        $data = [
            'isLoggedIn' => $session->get('user_id') !== null,
            'userRole' => $session->get('role'),
            'userName' => $session->get('full_name'),
            'userEmail' => $session->get('email'),
            'userContact' => $session->get('contact_number')
        ];
        return view('facilities/staffhouse', $data);
    }
    
    public function events()
    {
        $session = session();
        $data = [
            'isLoggedIn' => $session->get('user_id') !== null,
            'userRole' => $session->get('role'),
            'userName' => $session->get('full_name')
        ];
        
        // Changed from 'events' to 'event' to match the actual file name
        return view('event', $data);
    }
}

