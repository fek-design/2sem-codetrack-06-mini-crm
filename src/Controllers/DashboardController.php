<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controller;
use App\Http\Request;
use App\Http\Response;
use App\Repositories\CustomerRepository;
use App\Repositories\LeadRepository;
use App\Repositories\InteractionRepository;
use App\Enums\LeadStatus;
use App\Enums\CustomerStatus;

/**
 * Handles the CRM dashboard functionality.
 * This controller is protected by authentication.
 */
class DashboardController extends Controller
{
    private CustomerRepository $customers;
    private LeadRepository $leads;
    private InteractionRepository $interactions;

    public function __construct()
    {
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

        // Get all status counts from repositories
        $customersByStatus = $this->customers->countByStatus();
        $leadsByStatus = $this->leads->countByStatus();

        // Calculate active/inactive counts using enum groupings
        $activeCustomers = 0;
        foreach (CustomerStatus::getActiveStatuses() as $status) {
            $activeCustomers += $customersByStatus[$status->value] ?? 0;
        }

        $inactiveCustomers = 0;
        foreach (CustomerStatus::getInactiveStatuses() as $status) {
            $inactiveCustomers += $customersByStatus[$status->value] ?? 0;
        }

        $activeLeads = 0;
        foreach (LeadStatus::getActiveStatuses() as $status) {
            $activeLeads += $leadsByStatus[$status->value] ?? 0;
        }

        // Get specific lead counts using enum values
        $unqualifiedLeads = $leadsByStatus[LeadStatus::UNQUALIFIED->value] ?? 0;
        $convertedLeads = $leadsByStatus[LeadStatus::CONVERTED->value] ?? 0;

        $recentInteractions = $this->interactions->getRecentInteractions(5);

        $response = new Response();
        $response->setTemplate($this->template, 'dashboard', [
            ...$this->pullFlash($response),
            'request' => $request,
            'activeCustomers' => $activeCustomers,
            'activeLeads' => $activeLeads,
            'inactiveCustomers' => $inactiveCustomers,
            'unqualifiedLeads' => $unqualifiedLeads,
            'convertedLeads' => $convertedLeads,
            'customersByStatus' => $customersByStatus,
            'leadsByStatus' => $leadsByStatus,
            'recentInteractions' => $recentInteractions,
        ]);
        return $response;
    }
}
