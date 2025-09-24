<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controller;
use App\Http\Request;
use App\Http\Response;
use App\Repositories\CustomerRepository;
use App\Repositories\InteractionRepository;

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
            $response->redirect('/admin/customers');
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
            $response->redirect('/admin/customers/create');
            return $response;
        }

        // Check if customer already exists
        if ($this->customerRepository->findByEmail($email)) {
            $this->flashErrors($response, ['general' => ['A customer with this email already exists.']]);
            $this->flashOldInput($response, $request->getAll());
            $response->redirect('/admin/customers/create');
            return $response;
        }

        $customer = $this->customerRepository->create($name, $email, $phone, $company, $notes);

        // Log initial interaction
        $this->interactionRepository->createForCustomer(
            $customer->getId(),
            'note',
            'Customer created',
            'Customer record created in CRM system'
        );

        $response->redirect('/admin/customers/' . $customer->getId());
        return $response;
    }

    public function edit(Request $request): Response
    {
        $id = (int) $request->getQuery('id');
        $customer = $this->customerRepository->findById($id);

        if (!$customer) {
            $response = new Response();
            $response->redirect('/admin/customers');
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
            $response->redirect('/admin/customers');
            return $response;
        }

        $name = $request->getInput('name', '');
        $email = $request->getInput('email', '');
        $phone = $request->getInput('phone', '');
        $company = $request->getInput('company', '');
        $status = $request->getInput('status', 'active');
        $notes = $request->getInput('notes', '');

        if (empty($name) || empty($email)) {
            $this->flashErrors($response, ['general' => ['Name and email are required.']]);
            $response->redirect('/admin/customers/' . $id . '/edit');
            return $response;
        }

        $success = $this->customerRepository->update($id, $name, $email, $phone, $company, $status, $notes);

        if ($success) {
            // Log update interaction
            $this->interactionRepository->createForCustomer(
                $id,
                'note',
                'Customer updated',
                'Customer information was updated'
            );
        }

        $response->redirect('/admin/customers/' . $id);
        return $response;
    }

    public function addInteraction(Request $request): Response
    {
        $customerId = (int) $request->getQuery('id');
        $customer = $this->customerRepository->findById($customerId);

        $response = new Response();

        if (!$customer) {
            $response->redirect('/admin/customers');
            return $response;
        }

        $type = $request->getInput('type', '');
        $subject = $request->getInput('subject', '');
        $description = $request->getInput('description', '');
        $interactionDate = $request->getInput('interaction_date', '');

        if (empty($type) || empty($subject)) {
            $this->flashErrors($response, ['general' => ['Type and subject are required.']]);
            $response->redirect('/admin/customers/' . $customerId);
            return $response;
        }

        $this->interactionRepository->createForCustomer(
            $customerId,
            $type,
            $subject,
            $description,
            $interactionDate ?: null
        );

        $response->redirect('/admin/customers/' . $customerId);
        return $response;
    }
}
