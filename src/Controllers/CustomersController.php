<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controller;
use App\Enums\CustomerStatus;
use App\Http\Request;
use App\Http\Response;
use App\Repositories\CustomerRepository;
use App\Repositories\InteractionRepository;
use App\Utils\ChangeTracker;

/**
 * Controller for managing customers in the CRM system.
 */
class CustomersController extends Controller
{
    private CustomerRepository $customerRepository;
    private InteractionRepository $interactionRepository;

    public function __construct()
    {
        $this->customerRepository = new CustomerRepository();
        $this->interactionRepository = new InteractionRepository();
    }

    public function index(Request $request): Response
    {
        $search = $request->getQuery('search', '');

        if ($search) {
            $customers = $this->customerRepository->search($search);
        } else {
            $customers = $this->customerRepository->findAll();
        }

        $response = new Response();
        $response->setTemplate($this->template, 'customers/index', [
            ...$this->pullFlash($response),
            'customers' => $customers,
            'search' => $search,
            'request' => $request,
        ]);
        return $response;
    }

    public function show(Request $request): Response
    {
        $id = (int) $request->getQuery('id');
        $customer = $this->customerRepository->findById($id);

        if (!$customer) {
            $response = new Response();
            $response->redirect('/customers');
            return $response;
        }

        $interactions = $this->interactionRepository->findByCustomerId($id);

        $response = new Response();
        $response->setTemplate($this->template, 'customers/show', [
            'customer' => $customer,
            'interactions' => $interactions,
            'request' => $request,
        ]);
        return $response;
    }

    public function create(Request $request): Response
    {
        $response = new Response();
        $response->setTemplate($this->template, 'customers/create', [
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
        $notes = $request->getInput('notes', '');

        $response = new Response();

        if (empty($name) || empty($email)) {
            $this->flashErrors($response, ['general' => ['Name and email are required.']]);
            $this->flashOldInput($response, $request->getAll());
            $response->redirect('/customers/create');
            return $response;
        }

        // Check if customer already exists
        if ($this->customerRepository->findByEmail($email)) {
            $this->flashErrors($response, ['general' => ['A customer with this email already exists.']]);
            $this->flashOldInput($response, $request->getAll());
            $response->redirect('/customers/create');
            return $response;
        }

        $customer = $this->customerRepository->create($name, $email, $phone, $company, $notes);

        // Log initial interaction
        $this->interactionRepository->createForCustomer(
            $customer->id,
            'note',
            'Customer created',
            'Customer record created in CRM system'
        );

        $response->redirect('/customers/' . $customer->id);
        return $response;
    }

    public function edit(Request $request): Response
    {
        $id = (int) $request->getQuery('id');
        $customer = $this->customerRepository->findById($id);

        if (!$customer) {
            $response = new Response();
            $response->redirect('/customers');
            return $response;
        }

        $response = new Response();
        $response->setTemplate($this->template, 'customers/edit', [
            ...$this->pullFlash($response),
            'customer' => $customer,
            'request' => $request,
        ]);
        return $response;
    }

    public function update(Request $request): Response
    {
        $id = (int) $request->getQuery('id');
        $customer = $this->customerRepository->findById($id);

        $response = new Response();

        if (!$customer) {
            $response->redirect('/customers');
            return $response;
        }

        $name = $request->getInput('name', '');
        $email = $request->getInput('email', '');
        $phone = $request->getInput('phone', '');
        $company = $request->getInput('company', '');
        $status = CustomerStatus::from($request->getInput('status', 'active'));
        $notes = $request->getInput('notes', '');

        if (empty($name) || empty($email)) {
            $this->flashErrors($response, ['general' => ['Name and email are required.']]);
            $response->redirect('/customers/' . $id . '/edit');
            return $response;
        }

        // Track changes for interaction log using ChangeTracker
        $changeTracker = ChangeTracker::track([
            'Name' => [$customer->name, $name],
            'Email' => [$customer->email, $email],
            'Phone' => [$customer->phone, $phone],
            'Company' => [$customer->company, $company],
            'Status' => [$customer->status->value, $status->value],
            'Notes' => [$customer->notes, $notes],
        ]);

        $success = $this->customerRepository->update($id, $name, $email, $phone, $company, $status, $notes);

        if ($success && $changeTracker->hasChanges()) {
            // Log detailed update interaction
            $this->interactionRepository->createForCustomer(
                $id,
                'note',
                'Customer updated',
                $changeTracker->getChangeDescription('Customer')
            );
        }

        $response->redirect('/customers/' . $id);
        return $response;
    }

    public function addInteraction(Request $request): Response
    {
        $customerId = (int) $request->getQuery('id');
        $customer = $this->customerRepository->findById($customerId);

        $response = new Response();

        if (!$customer) {
            $response->redirect('/customers');
            return $response;
        }

        $type = $request->getInput('type', '');
        $subject = $request->getInput('subject', '');
        $description = $request->getInput('description', '');
        $interactionDate = $request->getInput('interaction_date', '');

        if (empty($type) || empty($subject)) {
            $this->flashErrors($response, ['general' => ['Type and subject are required.']]);
            $response->redirect('/customers/' . $customerId);
            return $response;
        }

        $this->interactionRepository->createForCustomer(
            $customerId,
            $type,
            $subject,
            $description,
            $interactionDate ?: null
        );

        $response->redirect('/customers/' . $customerId);
        return $response;
    }

    public function delete(Request $request): Response
    {
        $id = (int) $request->getQuery('id');
        $customer = $this->customerRepository->findById($id);

        $response = new Response();

        if (!$customer) {
            $response->redirect('/customers');
            return $response;
        }

        // Delete associated interactions first
        $interactions = $this->interactionRepository->findByCustomerId($id);
        foreach ($interactions as $interaction) {
            $this->interactionRepository->delete($interaction->id);
        }

        // Delete the customer
        $success = $this->customerRepository->delete($id);

        if ($success) {
            $response->setFlash('success', 'Customer "' . $customer->name . '" has been deleted successfully.');
        } else {
            $response->setFlash('error', 'Failed to delete customer. Please try again.');
        }

        $response->redirect('/customers');
        return $response;
    }
}
