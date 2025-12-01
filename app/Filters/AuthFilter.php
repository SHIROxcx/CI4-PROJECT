<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            // For AJAX requests, return JSON response
            if ($request->isAJAX()) {
                return service('response')
                    ->setJSON(['error' => 'Unauthorized access. Please log in.'])
                    ->setStatusCode(401);
            }
            
            // For regular requests, redirect to login
            return redirect()->to('/login');
        }

        // ✅ REMOVED THE ADMIN CHECK - Let routes handle role-specific access
        // The 'auth' filter should ONLY check if user is logged in
        // Use a separate 'admin' filter for admin-only routes
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here if needed after the request
    }
}