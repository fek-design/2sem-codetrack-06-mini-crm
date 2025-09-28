<?php
/**
 * @var \App\Template $this
 * @var \App\Models\Customer $customer
 * @var \App\Models\Interaction[] $interactions
 * @var \App\Http\Request $request
 */

use App\Utils\TimezoneHelper;

$this->extend('layout');
?>

<?php $this->start('title', htmlspecialchars($customer->name) . ' - Customer Details') ?>

<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1 class="page-heading"><?= htmlspecialchars($customer->name) ?></h1>
            <div class="header-actions">
                <a href="/customers/<?= $customer->id ?>/edit" class="btn btn-primary">Edit Customer</a>
                <a href="/customers" class="btn btn-secondary">Back to Customers</a>
            </div>
        </div>
    </div>
</section>

<section class="customer-details">
    <div class="container">
        <div class="details-grid">
            <!-- Customer Information -->
            <div class="info-card">
                <h2>Customer Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Name:</label>
                        <span><?= htmlspecialchars($customer->name) ?></span>
                    </div>
                    <div class="info-item">
                        <label>Email:</label>
                        <span><a href="mailto:<?= htmlspecialchars($customer->email) ?>"><?= htmlspecialchars($customer->email) ?></a></span>
                    </div>
                    <div class="info-item">
                        <label>Phone:</label>
                        <span><?= $customer->phone ? htmlspecialchars($customer->phone) : 'Not provided' ?></span>
                    </div>
                    <div class="info-item">
                        <label>Company:</label>
                        <span><?= $customer->company ? htmlspecialchars($customer->company) : 'Not provided' ?></span>
                    </div>
                    <div class="info-item">
                        <label>Status:</label>
                        <span class="status-badge status-<?= $customer->status->value ?>"><?= ucfirst($customer->status->value) ?></span>
                    </div>
                    <div class="info-item">
                        <label>Added:</label>
                        <span><?= TimezoneHelper::formatForDisplay($customer->created_at) ?></span>
                    </div>
                </div>
                <?php if ($customer->notes): ?>
                    <div class="notes-section">
                        <label>Notes:</label>
                        <p><?= nl2br(htmlspecialchars($customer->notes)) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Add Interaction Form -->
            <div class="interaction-form-card">
                <h2>Add Interaction</h2>
                <form method="POST" action="/customers/<?= $customer->id ?>/interactions" class="interaction-form form-spacing">
                    <?php if (isset($request)): ?>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($request->getCsrfToken()) ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="type">Type:</label>
                        <select name="type" id="type" required>
                            <option value="">Select type...</option>
                            <option value="call">Phone Call</option>
                            <option value="email">Email</option>
                            <option value="meeting">Meeting</option>
                            <option value="note">Note</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject:</label>
                        <input type="text" name="subject" id="subject" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea name="description" id="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="interaction_date">Date:</label>
                        <input type="datetime-local" name="interaction_date" id="interaction_date" value="<?= date('Y-m-d\TH:i') ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Interaction</button>
                </form>
            </div>
        </div>

        <!-- Interaction History -->
        <div class="interactions-card">
            <h2>Interaction History</h2>
            <?php if (empty($interactions)): ?>
                <p class="text-muted">No interactions recorded yet.</p>
            <?php else: ?>
                <div class="interactions-list">
                    <?php foreach ($interactions as $interaction): ?>
                        <div class="interaction-item">
                            <div class="interaction-header">
                                <strong><?= htmlspecialchars($interaction->subject) ?></strong>
                                <span class="interaction-type">[<?= ucfirst($interaction->type) ?>]</span>
                                <span class="interaction-date"><?= TimezoneHelper::formatForDisplay($interaction->interaction_date) ?></span>
                            </div>
                            <?php if ($interaction->description): ?>
                                <div class="interaction-description">
                                    <?= nl2br(htmlspecialchars($interaction->description)) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
