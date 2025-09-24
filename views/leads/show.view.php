<?php
/** @var \App\Template $this */
/** @var \App\Models\Lead $lead */
/** @var array $interactions */

use App\Utils\TimezoneHelper;

$this->extend('layout');
?>

<?php $this->start('title', htmlspecialchars($lead->getName()) . ' - Lead Details') ?>

<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1 class="page-heading"><?= htmlspecialchars($lead->getName()) ?></h1>
            <div class="header-actions">
                <?php if ($lead->getStatus() !== 'converted'): ?>
                    <form method="POST" action="/leads/<?= $lead->getId() ?>/convert" style="display: inline;">
                        <button type="submit" class="btn btn-success"
                                onclick="return confirm('Convert this lead to a customer?')">Convert to Customer</button>
                    </form>
                <?php endif; ?>
                <a href="/leads/<?= $lead->getId() ?>/edit" class="btn btn-primary">Edit Lead</a>
                <a href="/leads" class="btn btn-secondary">Back to Leads</a>
            </div>
        </div>
    </div>
</section>

<section class="lead-details">
    <div class="container">
        <div class="details-grid">
            <!-- Lead Information -->
            <div class="info-card">
                <h2>Lead Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Name:</label>
                        <span><?= htmlspecialchars($lead->getName()) ?></span>
                    </div>
                    <div class="info-item">
                        <label>Email:</label>
                        <span><a href="mailto:<?= htmlspecialchars($lead->getEmail()) ?>"><?= htmlspecialchars($lead->getEmail()) ?></a></span>
                    </div>
                    <div class="info-item">
                        <label>Phone:</label>
                        <span><?= $lead->getPhone() ? htmlspecialchars($lead->getPhone()) : 'Not provided' ?></span>
                    </div>
                    <div class="info-item">
                        <label>Company:</label>
                        <span><?= $lead->getCompany() ? htmlspecialchars($lead->getCompany()) : 'Not provided' ?></span>
                    </div>
                    <div class="info-item">
                        <label>Source:</label>
                        <span><?= $lead->getSource() ? htmlspecialchars($lead->getSource()) : 'Not provided' ?></span>
                    </div>
                    <div class="info-item">
                        <label>Status:</label>
                        <span class="status-badge status-<?= $lead->getStatus() ?>"><?= ucfirst($lead->getStatus()) ?></span>
                    </div>
                    <div class="info-item">
                        <label>Added:</label>
                        <span><?= TimezoneHelper::formatForDisplay($lead->getCreatedAt()) ?></span>
                    </div>
                </div>
                <?php if ($lead->getNotes()): ?>
                    <div class="notes-section">
                        <label>Notes:</label>
                        <p><?= nl2br(htmlspecialchars($lead->getNotes())) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Add Interaction Form -->
            <div class="interaction-form-card">
                <h2>Add Interaction</h2>
                <form method="POST" action="/leads/<?= $lead->getId() ?>/interactions" class="interaction-form">
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
                        <input type="datetime-local" name="interaction_date" id="interaction_date" value="<?= TimezoneHelper::formatForInput(TimezoneHelper::nowUtc()) ?>">
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
                                <span class="interaction-date"><?= TimezoneHelper::formatForDisplay($interaction->getInteractionDate()) ?></span>
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
