<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Skip if not logged in (let AuthFilter handle this)
        if (!$session->get('isLoggedIn')) {
            return null;
        }
        
        $userRole = $session->get('role');
        $uri = $request->getUri();
        $path = $uri->getPath();
        
        // Admin trying to access non-admin pages
        if ($userRole === 'admin' && $this->isUserRoute($path)) {
            return redirect()->to('/admin/dashboard')->with('message', 'Redirected to admin dashboard');
        }
        
        // Non-admin trying to access admin pages
        if ($userRole !== 'admin' && $this->isAdminRoute($path)) {
            return redirect()->to('/user/dashboard')->with('message', 'Access denied to admin area');
        }
        
        // Non-facilitator trying to access facilitator pages
        if ($userRole !== 'facilitator' && $this->isFacilitatorRoute($path)) {
            return redirect()->to('/user/dashboard')->with('message', 'Access denied. Facilitator access required.');
        }
        
        return null;
    }
    
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
    
    private function isUserRoute($path)
    {
        $userRoutes = ['/', '/user', '/dashboard', '/booking', '/facilities', '/profile', '/my-bookings', '/student'];
        
        foreach ($userRoutes as $route) {
            if ($path === $route || ($route !== '/' && strpos($path, $route) === 0)) {
                return true;
            }
        }
        return false;
    }
    
    private function isAdminRoute($path)
    {
        return strpos($path, '/admin') === 0;
    }
    
    /**
     * Check if path is facilitator-only route
     */
    private function isFacilitatorRoute($path)
    {
        return strpos($path, '/facilitator') === 0;
    }
}