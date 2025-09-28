<?php
/**
 * @var \App\Template $this
 * @var \App\Http\Request $request
 * @var array $errors
 * @var array $old
 * @var string|null $success
 */

$this->extend('layout');
?>

<?php $this->start('title', 'Add Customer') ?>

<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1 class="page-heading">Add Customer</h1>
            <a href="/customers" class="btn btn-secondary">Back to Customers</a>
        </div>
    </div>
</section>

<section class="form-section">
    <div class="container">
        <div class="form-card">
            <form method="POST" action="/customers" class="customer-form form-spacing">
                <div class="form-group">
                    <label for="name">Name *</label>
                    <input type="text" name="name" id="name" aria-required
                           value="<?= htmlspecialchars($old['name'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email" aria-required
                           value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" name="phone" id="phone"
                           value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="company">Company</label>
                    <input type="text" name="company" id="company"
                           value="<?= htmlspecialchars($old['company'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" rows="4"><?= htmlspecialchars($old['notes'] ?? '') ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Create Customer</button>
                    <a href="/customers" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>
