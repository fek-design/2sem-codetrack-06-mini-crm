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
use App\Enums\LeadStatus;
use App\Enums\LeadSource;

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
            ...$this->pullFlash($response),
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
            $response->redirect('/leads');
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
        $source = $request->getInput('source', 'none');
        $notes = $request->getInput('notes', '');

        $response = new Response();

        if (empty($name) || empty($email)) {
            $this->flashErrors($response, ['general' => ['Name and email are required.']]);
            $this->flashOldInput($response, $request->getAll());
            $response->redirect('/leads/create');
            return $response;
        }

        $lead = $this->leadRepository->create($name, $email, $phone, $company, LeadSource::from($source), $notes);

        // Log initial interaction
        $this->interactionRepository->createForLead(
            $lead->id,
            'note',
            'Lead created',
            'Lead record created in CRM system from source: ' . $lead->source->getDisplayName()
        );

        $response->redirect('/leads/' . $lead->id);
        return $response;
    }

    public function edit(Request $request): Response
    {
        $id = (int) $request->getQuery('id');
        $lead = $this->leadRepository->findById($id);

        if (!$lead) {
            $response = new Response();
            $response->redirect('/leads');
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
            $response->redirect('/leads');
            return $response;
        }

        $name = $request->getInput('name', '');
        $email = $request->getInput('email', '');
        $phone = $request->getInput('phone', '');
        $company = $request->getInput('company', '');
        $source = $request->getInput('source', 'none');
        $status = LeadStatus::from($request->getInput('status', 'new'));
        $notes = $request->getInput('notes', '');

        if (empty($name) || empty($email)) {
            $this->flashErrors($response, ['general' => ['Name and email are required.']]);
            $response->redirect('/leads/' . $id . '/edit');
            return $response;
        }

        // Track changes for interaction log using ChangeTracker
        $changeTracker = ChangeTracker::track([
            'Name' => [$lead->name, $name],
            'Email' => [$lead->email, $email],
            'Phone' => [$lead->phone, $phone],
            'Company' => [$lead->company, $company],
            'Source' => [$lead->source->value, $source],
            'Status' => [$lead->status->value, $status->value],
            'Notes' => [$lead->notes, $notes],
        ]);

        $success = $this->leadRepository->update($id, $name, $email, $phone, $company, LeadSource::from($source), $status->value, $notes);

        if ($success && $changeTracker->hasChanges()) {
            // Log detailed update interaction
            $this->interactionRepository->createForLead(
                $id,
                'note',
                'Lead updated',
                $changeTracker->getChangeDescription('Lead')
            );
        }

        $response->redirect('/leads/' . $id);
        return $response;
    }

    public function convertToCustomer(Request $request): Response
    {
        $id = (int) $request->getQuery('id');
        $lead = $this->leadRepository->findById($id);

        $response = new Response();

        if (!$lead) {
            $response->redirect('/leads');
            return $response;
        }

        // Check if customer already exists with this email
        $existingCustomer = $this->customerRepository->findByEmail($lead->email);

        if ($existingCustomer) {
            // Customer already exists, just update the lead status and redirect
            $this->leadRepository->update(
                $id,
                $lead->name,
                $lead->email,
                $lead->phone,
                $lead->company,
                $lead->source,
                LeadStatus::CONVERTED->value,
                $lead->notes
            );

            // Log conversion interaction on existing customer
            $this->interactionRepository->createForCustomer(
                $existingCustomer->id,
                'note',
                'Lead re-converted',
                "{Lead #{$id}} converted to existing customer account",
            );

            // Log re-conversion interaction on the lead side
            $this->interactionRepository->createForLead(
                $id,
                'note',
                'Re-converted to existing customer',
                "Lead re-converted to existing customer {Customer #{$existingCustomer->id}}",
            );

            $response->setFlash('success', 'Lead converted to existing customer: ' . $existingCustomer->name);
            $response->redirect('/customers/' . $existingCustomer->id);
            return $response;
        }

        // Create new customer from lead data
        $customer = $this->customerRepository->create(
            $lead->name,
            $lead->email,
            $lead->phone,
            $lead->company,
            $lead->notes . "\n\nConverted from lead (Source: " . $lead->source->getDisplayName() . ")"
        );

        // Transfer interactions from lead to customer
        $leadInteractions = $this->interactionRepository->findByLeadId($id);
        foreach ($leadInteractions as $interaction) {
            $this->interactionRepository->createForCustomer(
                $customer->id,
                $interaction->type,
                '[From Lead] ' . $interaction->subject,
                $interaction->description,
                $interaction->interaction_date
            );
        }

        // Log conversion
        $this->interactionRepository->createForCustomer(
            $customer->id,
            'note',
            'Converted from lead',
            "Customer converted from {Lead #{$id}}",
        );

        // Log conversion on lead side with link to customer
        $this->interactionRepository->createForLead(
            $id,
            'note',
            'Converted to customer',
            "Lead converted to customer {Customer #{$customer->id}}",
        );

        // Update lead status
        $this->leadRepository->update(
            $id,
            $lead->name,
            $lead->email,
            $lead->phone,
            $lead->company,
            $lead->source,
            LeadStatus::CONVERTED->value,
            $lead->notes
        );

        $response->redirect('/customers/' . $customer->id);
        return $response;
    }

    public function addInteraction(Request $request): Response
    {
        $leadId = (int) $request->getQuery('id');
        $lead = $this->leadRepository->findById($leadId);

        $response = new Response();

        if (!$lead) {
            $response->redirect('/leads');
            return $response;
        }

        $type = $request->getInput('type', '');
        $subject = $request->getInput('subject', '');
        $description = $request->getInput('description', '');
        $interactionDate = $request->getInput('interaction_date', '');

        if (empty($type) || empty($subject)) {
            $this->flashErrors($response, ['general' => ['Type and subject are required.']]);
            $response->redirect('/leads/' . $leadId);
            return $response;
        }

        $this->interactionRepository->createForLead(
            $leadId,
            $type,
            $subject,
            $description,
            $interactionDate ?: null
        );

        $response->redirect('/leads/' . $leadId);
        return $response;
    }

    public function delete(Request $request): Response
    {
        $id = (int) $request->getQuery('id');
        $lead = $this->leadRepository->findById($id);

        $response = new Response();

        if (!$lead) {
            $response->redirect('/leads');
            return $response;
        }

        // Delete associated interactions first
        $interactions = $this->interactionRepository->findByLeadId($id);
        foreach ($interactions as $interaction) {
            $this->interactionRepository->delete($interaction->id);
        }

        // Delete the lead
        $success = $this->leadRepository->delete($id);

        if ($success) {
            $response->setFlash('success', 'Lead "' . $lead->name . '" has been deleted successfully.');
        } else {
            $response->setFlash('error', 'Failed to delete lead. Please try again.');
        }

        $response->redirect('/leads');
        return $response;
    }
}
