<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controller;
use App\Http\Request;
use App\Http\Response;
use App\Repositories\MessageRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\LeadRepository;
use App\Repositories\InteractionRepository;

/**
 * Handles the CRM dashboard functionality.
 * This controller is protected by authentication.
 */
class DashboardController extends Controller
{
    private MessageRepository $messages;
    private CustomerRepository $customers;
    private LeadRepository $leads;
    private InteractionRepository $interactions;

    public function __construct()
    {
        $this->messages = new MessageRepository();
        $this->customers = new CustomerRepository();
        $this->leads = new LeadRepository();
        $this->interactions = new InteractionRepository();
    }

    /**
     * Show the CRM dashboard.
     */
    public function index(Request $request): Response
    {
        if (!$this->isLoggedIn()) {
            return $this->redirectToLoginWithError();
        }

        $unreadMessages = $this->messages->countUnread();

        // CRM metrics
        $totalCustomers = count($this->customers->findAll());
        $totalLeads = count($this->leads->findAll());
        $customersByStatus = $this->customers->countByStatus();
        $leadsByStatus = $this->leads->countByStatus();
        $recentInteractions = $this->interactions->getRecentInteractions(5);

        $response = new Response();
        $response->setTemplate($this->template, 'dashboard', [
            ...$this->pullFlash($response),
            'request' => $request,
            'unreadMessages' => $unreadMessages,
            'totalCustomers' => $totalCustomers,
            'totalLeads' => $totalLeads,
            'customersByStatus' => $customersByStatus,
            'leadsByStatus' => $leadsByStatus,
            'recentInteractions' => $recentInteractions,
        ]);
        return $response;
    }
}
