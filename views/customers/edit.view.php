<?php
/** @var \App\Template $this */
/** @var \App\Models\Customer $customer */
/** @var string|null $error */

use App\Enums\CustomerStatus;

$this->extend('layout');
?>

<?php $this->start('title', 'Edit Customer') ?>

<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1 class="page-heading">Edit Customer</h1>
            <div class="header-actions">
                <a href="/customers/<?= $customer->getId() ?>" class="btn btn-secondary">Back to Customer</a>
                <a href="/customers" class="btn btn-outline">All Customers</a>
            </div>
        </div>
    </div>
</section>

<section class="form-section">
    <div class="container">
        <div class="form-card">
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="/customers/<?= $customer->getId() ?>" class="customer-form form-spacing">
                <div class="form-group">
                    <label for="name">Name *</label>
                    <input type="text" name="name" id="name" required
                           value="<?= htmlspecialchars($customer->getName()) ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email" required
                           value="<?= htmlspecialchars($customer->getEmail()) ?>">
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" name="phone" id="phone"
                           value="<?= htmlspecialchars($customer->getPhone()) ?>">
                </div>

                <div class="form-group">
                    <label for="company">Company</label>
                    <input type="text" name="company" id="company"
                           value="<?= htmlspecialchars($customer->getCompany()) ?>">
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status">
                        <?php foreach (CustomerStatus::cases() as $status): ?>
                            <option value="<?= $status->value ?>" <?= $customer->getStatus() === $status->value ? 'selected' : '' ?>>
                                <?= $status->getDisplayName() ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" rows="4"><?= htmlspecialchars($customer->getNotes()) ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Customer</button>
                    <a href="/customers/<?= $customer->getId() ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>
