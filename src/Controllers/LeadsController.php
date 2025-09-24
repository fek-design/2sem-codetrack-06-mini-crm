<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controller;
use App\Http\Request;
use App\Http\Response;
use App\Repositories\LeadRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\InteractionRepository;
use App\Utils\ChangeTracker;

/**
 * Controller for managing leads in the CRM system.
 */
class LeadsController extends Controller
{
    private LeadRepository $leadRepository;
    private CustomerRepository $customerRepository;
    private InteractionRepository $interactionRepository;

    public function __construct()
    {
        $this->leadRepository = new LeadRepository();
        $this->customerRepository = new CustomerRepository();
        $this->interactionRepository = new InteractionRepository();
    }

    public function index(Request $request): Response
    {
        $leads = $this->leadRepository->findAll();

        $response = new Response();
        $response->setTemplate($this->template, 'leads/index', [
            'leads' => $leads,
            'request' => $request,
        ]);
        return $response;
    }

    public function show(Request $request): Response
    {
        $id = (int) $request->getQuery('id');
        $lead = $this->leadRepository->findById($id);

        if (!$lead) {
            $response = new Response();
            $response->redirect('/admin/leads');
            return $response;
        }

        $interactions = $this->interactionRepository->findByLeadId($id);

        $response = new Response();
        $response->setTemplate($this->template, 'leads/show', [
            'lead' => $lead,
            'interactions' => $interactions,
            'request' => $request,
        ]);
        return $response;
    }

    public function create(Request $request): Response
    {
        $response = new Response();
        $response->setTemplate($this->template, 'leads/create', [
            ...$this->pullFlash($response),
            'request' => $request,
        ]);
        return $response;
    }

    public function store(Request $request): Response
    {
        $name = $request->getInput('name', '');
        $email = $request->getInput('email', '');
        $phone = $request->getInput('phone', '');
        $company = $request->getInput('company', '');
        $source = $request->getInput('source', '');
        $notes = $request->getInput('notes', '');

        $response = new Response();

        if (empty($name) || empty($email)) {
            $this->flashErrors($response, ['general' => ['Name and email are required.']]);
            $this->flashOldInput($response, $request->getAll());
            $response->redirect('/admin/leads/create');
            return $response;
        }

        $lead = $this->leadRepository->create($name, $email, $phone, $company, $source, $notes);

        // Log initial interaction
        $this->interactionRepository->createForLead(
            $lead->getId(),
            'note',
            'Lead created',
            'Lead record created in CRM system from source: ' . $source
        );

        $response->redirect('/admin/leads/' . $lead->getId());
        return $response;
    }

    public function edit(Request $request): Response
    {
        $id = (int) $request->getQuery('id');
        $lead = $this->leadRepository->findById($id);

        if (!$lead) {
            $response = new Response();
            $response->redirect('/admin/leads');
            return $response;
        }

        $response = new Response();
        $response->setTemplate($this->template, 'leads/edit', [
            ...$this->pullFlash($response),
            'lead' => $lead,
            'request' => $request,
        ]);
        return $response;
    }

    public function update(Request $request): Response
    {
        $id = (int) $request->getQuery('id');
        $lead = $this->leadRepository->findById($id);

        $response = new Response();

        if (!$lead) {
            $response->redirect('/admin/leads');
            return $response;
        }

        $name = $request->getInput('name', '');
        $email = $request->getInput('email', '');
        $phone = $request->getInput('phone', '');
        $company = $request->getInput('company', '');
        $source = $request->getInput('source', '');
        $status = $request->getInput('status', 'new');
        $notes = $request->getInput('notes', '');

        if (empty($name) || empty($email)) {
            $this->flashErrors($response, ['general' => ['Name and email are required.']]);
            $response->redirect('/admin/leads/' . $id . '/edit');
            return $response;
        }

        // Track changes for interaction log using ChangeTracker
        $changeTracker = ChangeTracker::track([
            'Name' => [$lead->getName(), $name],
            'Email' => [$lead->getEmail(), $email],
            'Phone' => [$lead->getPhone(), $phone],
            'Company' => [$lead->getCompany(), $company],
            'Source' => [$lead->getSource(), $source],
            'Status' => [$lead->getStatus(), $status],
            'Notes' => [$lead->getNotes(), $notes],
        ]);

        $success = $this->leadRepository->update($id, $name, $email, $phone, $company, $source, $status, $notes);

        if ($success && $changeTracker->hasChanges()) {
            // Log detailed update interaction
            $this->interactionRepository->createForLead(
                $id,
                'note',
                'Lead updated',
                $changeTracker->getChangeDescription('Lead')
            );
        }

        $response->redirect('/admin/leads/' . $id);
        return $response;
    }

    public function convertToCustomer(Request $request): Response
    {
        $id = (int) $request->getQuery('id');
        $lead = $this->leadRepository->findById($id);

        $response = new Response();

        if (!$lead) {
            $response->redirect('/admin/leads');
            return $response;
        }

        // Create customer from lead data
        $customer = $this->customerRepository->create(
            $lead->getName(),
            $lead->getEmail(),
            $lead->getPhone(),
            $lead->getCompany(),
            $lead->getNotes() . "\n\nConverted from lead (Source: " . $lead->getSource() . ")"
        );

        // Transfer interactions from lead to customer
        $leadInteractions = $this->interactionRepository->findByLeadId($id);
        foreach ($leadInteractions as $interaction) {
            $this->interactionRepository->createForCustomer(
                $customer->getId(),
                $interaction->getType(),
                '[From Lead] ' . $interaction->getSubject(),
                $interaction->getDescription(),
                $interaction->getInteractionDate()
            );
        }

        // Log conversion
        $this->interactionRepository->createForCustomer(
            $customer->getId(),
            'note',
            'Converted from lead',
            "Customer converted from {Lead #{$id}}",
        );

        // Log conversion on lead side with link to customer
        $this->interactionRepository->createForLead(
            $id,
            'note',
            'Converted to customer',
            "Lead converted to customer {Customer #{$customer->getId()}}",
        );

        // Update lead status
        $this->leadRepository->update(
            $id,
            $lead->getName(),
            $lead->getEmail(),
            $lead->getPhone(),
            $lead->getCompany(),
            $lead->getSource(),
            'converted',
            $lead->getNotes()
        );

        $response->redirect('/admin/customers/' . $customer->getId());
        return $response;
    }

    public function addInteraction(Request $request): Response
    {
        $leadId = (int) $request->getQuery('id');
        $lead = $this->leadRepository->findById($leadId);

        $response = new Response();

        if (!$lead) {
            $response->redirect('/admin/leads');
            return $response;
        }

        $type = $request->getInput('type', '');
        $subject = $request->getInput('subject', '');
        $description = $request->getInput('description', '');
        $interactionDate = $request->getInput('interaction_date', '');

        if (empty($type) || empty($subject)) {
            $this->flashErrors($response, ['general' => ['Type and subject are required.']]);
            $response->redirect('/admin/leads/' . $leadId);
            return $response;
        }

        $this->interactionRepository->createForLead(
            $leadId,
            $type,
            $subject,
            $description,
            $interactionDate ?: null
        );

        $response->redirect('/admin/leads/' . $leadId);
        return $response;
    }
}
