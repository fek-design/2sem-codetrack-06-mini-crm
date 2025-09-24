<?php
/** @var \App\Template $this */
/** @var \App\Models\Customer $customer */
/** @var array $interactions */

$this->extend('layout');
?>

<?php $this->start('title', htmlspecialchars($customer->getName()) . ' - Customer Details') ?>

<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1 class="page-heading"><?= htmlspecialchars($customer->getName()) ?></h1>
            <div class="header-actions">
                <a href="/admin/customers/<?= $customer->getId() ?>/edit" class="btn btn-primary">Edit Customer</a>
                <a href="/admin/customers" class="btn btn-secondary">Back to Customers</a>
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
                        <span><?= htmlspecialchars($customer->getName()) ?></span>
                    </div>
                    <div class="info-item">
                        <label>Email:</label>
                        <span><a href="mailto:<?= htmlspecialchars($customer->getEmail()) ?>"><?= htmlspecialchars($customer->getEmail()) ?></a></span>
                    </div>
                    <div class="info-item">
                        <label>Phone:</label>
                        <span><?= $customer->getPhone() ? htmlspecialchars($customer->getPhone()) : 'Not provided' ?></span>
                    </div>
                    <div class="info-item">
                        <label>Company:</label>
                        <span><?= $customer->getCompany() ? htmlspecialchars($customer->getCompany()) : 'Not provided' ?></span>
                    </div>
                    <div class="info-item">
                        <label>Status:</label>
                        <span class="status-badge status-<?= $customer->getStatus() ?>"><?= ucfirst($customer->getStatus()) ?></span>
                    </div>
                    <div class="info-item">
                        <label>Added:</label>
                        <span><?= date('M j, Y g:i A', strtotime($customer->getCreatedAt())) ?></span>
                    </div>
                </div>
                <?php if ($customer->getNotes()): ?>
                    <div class="notes-section">
                        <label>Notes:</label>
                        <p><?= nl2br(htmlspecialchars($customer->getNotes())) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Add Interaction Form -->
            <div class="interaction-form-card">
                <h2>Add Interaction</h2>
                <form method="POST" action="/admin/customers/<?= $customer->getId() ?>/interactions" class="interaction-form">
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
                                <strong><?= htmlspecialchars($interaction->getSubject()) ?></strong>
                                <span class="interaction-type">[<?= ucfirst($interaction->getType()) ?>]</span>
                                <span class="interaction-date"><?= date('M j, Y g:i A', strtotime($interaction->getInteractionDate())) ?></span>
                            </div>
                            <?php if ($interaction->getDescription()): ?>
                                <div class="interaction-description">
                                    <?= nl2br(htmlspecialchars($interaction->getDescription())) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
