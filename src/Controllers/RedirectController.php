<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controller;
use App\Http\Request;
use App\Http\Response;

/**
 * Handles root redirect based on authentication status
 */
class RedirectController extends Controller
{
    /**
     * Redirect users to dashboard if logged in, login page if not
     */
    public function index(Request $request): Response
    {
        $response = new Response();

        if (isset($_SESSION['user_id'])) {
            $response->redirect('/dashboard');
        } else {
            $response->redirect('/login');
        }

        return $response;
    }
}
