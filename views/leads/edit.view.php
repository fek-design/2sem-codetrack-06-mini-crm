<?php
/**
 * @var \App\Template $this
 * @var \App\Models\Lead $lead
 * @var string|null $error
 * @var \App\Http\Request $request
 */

use App\Enums\LeadStatus;
use App\Enums\LeadSource;

$this->extend('layout');
?>

<?php $this->start('title', 'Edit Lead') ?>

<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1 class="page-heading">Edit Lead</h1>
            <div class="header-actions">
                <a href="/leads/<?= $lead->id ?>" class="btn btn-secondary">Back to Lead</a>
                <a href="/leads" class="btn btn-outline">All Leads</a>
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

            <form method="POST" action="/leads/<?= $lead->id ?>" class="lead-form form-spacing">
                <div class="form-group">
                    <label for="name">Name *</label>
                    <input type="text" name="name" id="name" required value="<?= htmlspecialchars($lead->name) ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email" required value="<?= htmlspecialchars($lead->email) ?>">
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" name="phone" id="phone" value="<?= htmlspecialchars($lead->phone) ?>">
                </div>

                <div class="form-group">
                    <label for="company">Company</label>
                    <input type="text" name="company" id="company" value="<?= htmlspecialchars($lead->company) ?>">
                </div>

                <div class="form-group">
                    <label for="source">Lead Source</label>
                    <select name="source" id="source">
                        <option value="">Select source...</option>
                        <?php foreach (LeadSource::cases() as $source): ?>
                            <option value="<?= $source->value ?>" <?= $lead->source->value === $source->value ? 'selected' : '' ?>>
                                <?= $source->getDisplayName() ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status">
                        <?php foreach (LeadStatus::cases() as $status): ?>
                            <option value="<?= $status->value ?>" <?= $lead->status->value === $status->value ? 'selected' : '' ?>>
                                <?= $status->getDisplayName() ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" rows="4"><?= htmlspecialchars($lead->notes) ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Lead</button>
                    <a href="/leads/<?= $lead->id ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>
