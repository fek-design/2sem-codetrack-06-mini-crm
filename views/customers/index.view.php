<?php
/** @var \App\Template $this */
/** @var array $customers */
/** @var string $search */

use App\Utils\TimezoneHelper;

$this->extend('layout');
?>

<?php $this->start('title', 'Customers') ?>

<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1 class="page-heading">Customers</h1>
            <a href="/customers/create" class="btn btn-primary">Add Customer</a>
        </div>
    </div>
</section>

<section class="customers">
    <div class="container">
        <!-- Search Form -->
        <div class="search-section">
            <form method="GET" class="search-form form-spacing">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                       placeholder="Search customers by name, email, or company..." class="search-input">
                <button type="submit" class="btn btn-secondary">Search</button>
                <?php if ($search): ?>
                    <a href="/customers" class="btn btn-outline">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Customer List -->
        <?php if (empty($customers)): ?>
            <div class="empty-state">
                <h3>No customers found</h3>
                <?php if ($search): ?>
                    <p>No customers match your search criteria.</p>
                <?php else: ?>
                    <p>You haven't added any customers yet.</p>
                    <a href="/customers/create" class="btn btn-primary">Add Your First Customer</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="customers-grid">
                <?php foreach ($customers as $customer): ?>
                    <div class="customer-card">
                        <div class="customer-header">
                            <h3 class="customer-name">
                                <a href="/customers/<?= $customer->getId() ?>">
                                    <?= htmlspecialchars($customer->getName()) ?>
                                </a>
                            </h3>
                            <span class="status-badge status-<?= $customer->getStatus() ?>">
                                <?= ucfirst($customer->getStatus()) ?>
                            </span>
                        </div>
                        <div class="customer-details">
                            <p class="customer-email">
                                <strong>Email:</strong> <?= htmlspecialchars($customer->getEmail()) ?>
                            </p>
                            <?php if ($customer->getPhone()): ?>
                                <p class="customer-phone">
                                    <strong>Phone:</strong> <?= htmlspecialchars($customer->getPhone()) ?>
                                </p>
                            <?php endif; ?>
                            <?php if ($customer->getCompany()): ?>
                                <p class="customer-company">
                                    <strong>Company:</strong> <?= htmlspecialchars($customer->getCompany()) ?>
                                </p>
                            <?php endif; ?>
                            <p class="customer-date">
                                <strong>Added:</strong> <?= TimezoneHelper::formatForDisplay($customer->getCreatedAt(), 'M j, Y') ?>
                            </p>
                        </div>
                        <div class="customer-actions">
                            <a href="/customers/<?= $customer->getId() ?>" class="btn btn-sm btn-primary">View</a>
                            <a href="/customers/<?= $customer->getId() ?>/edit" class="btn btn-sm btn-secondary">Edit</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
