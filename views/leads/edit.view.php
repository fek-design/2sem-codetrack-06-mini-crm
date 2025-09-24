<?php
/** @var \App\Template $this */
/** @var \App\Models\Lead $lead */
/** @var string|null $error */

use App\Enums\LeadStatus;

$this->extend('layout');
?>

<?php $this->start('title', 'Edit Lead') ?>

<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1 class="page-heading">Edit Lead</h1>
            <div class="header-actions">
                <a href="/admin/leads/<?= $lead->getId() ?>" class="btn btn-secondary">Back to Lead</a>
                <a href="/admin/leads" class="btn btn-outline">All Leads</a>
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

            <form method="POST" action="/admin/leads/<?= $lead->getId() ?>" class="lead-form">
                <div class="form-group">
                    <label for="name">Name *</label>
                    <input type="text" name="name" id="name" required
                           value="<?= htmlspecialchars($lead->getName()) ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email" required
                           value="<?= htmlspecialchars($lead->getEmail()) ?>">
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" name="phone" id="phone"
                           value="<?= htmlspecialchars($lead->getPhone()) ?>">
                </div>

                <div class="form-group">
                    <label for="company">Company</label>
                    <input type="text" name="company" id="company"
                           value="<?= htmlspecialchars($lead->getCompany()) ?>">
                </div>

                <div class="form-group">
                    <label for="source">Lead Source</label>
                    <select name="source" id="source">
                        <option value="">Select source...</option>
                        <option value="website" <?= $lead->getSource() === 'website' ? 'selected' : '' ?>>Website</option>
                        <option value="referral" <?= $lead->getSource() === 'referral' ? 'selected' : '' ?>>Referral</option>
                        <option value="social_media" <?= $lead->getSource() === 'social_media' ? 'selected' : '' ?>>Social Media</option>
                        <option value="email_campaign" <?= $lead->getSource() === 'email_campaign' ? 'selected' : '' ?>>Email Campaign</option>
                        <option value="cold_call" <?= $lead->getSource() === 'cold_call' ? 'selected' : '' ?>>Cold Call</option>
                        <option value="trade_show" <?= $lead->getSource() === 'trade_show' ? 'selected' : '' ?>>Trade Show</option>
                        <option value="other" <?= $lead->getSource() === 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status">
                        <?php foreach (LeadStatus::cases() as $status): ?>
                            <option value="<?= $status->value ?>" <?= $lead->getStatus() === $status->value ? 'selected' : '' ?>>
                                <?= $status->getDisplayName() ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" rows="4"><?= htmlspecialchars($lead->getNotes()) ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Lead</button>
                    <a href="/admin/leads/<?= $lead->getId() ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>
