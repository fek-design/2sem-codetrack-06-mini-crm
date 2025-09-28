<?php
/**
 * @var \App\Template $this
 * @var \App\Models\Lead[] $leads
 * @var \App\Http\Request $request
 * @var string|null $success
 * @var array $errors
 */

use App\Utils\TimezoneHelper;
use App\Enums\LeadStatus;
use App\Enums\LeadSource;

$this->extend('layout');
?>

<?php $this->start('title', 'Leads') ?>

<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1 class="page-heading">Leads</h1>
            <a href="/leads/create" class="btn btn-primary">Add Lead</a>
        </div>
    </div>
</section>

<section class="leads">
    <div class="container">
        <?php if (empty($leads)): ?>
            <div class="empty-state">
                <h3>No leads found</h3>
                <p>You haven't added any leads yet.</p>
                <a href="/leads/create" class="btn btn-primary">Add Your First Lead</a>
            </div>
        <?php else: ?>
            <div class="leads-grid">
                <?php foreach ($leads as $lead): ?>
                    <div class="lead-card">
                        <div class="lead-header">
                            <h3 class="lead-name">
                                <a href="/leads/<?= $lead->id ?>">
                                    <?= htmlspecialchars($lead->name) ?>
                                </a>
                            </h3>
                            <span class="status-badge status-<?= $lead->status->value ?>">
                                <?= $lead->status->getDisplayName() ?>
                            </span>
                        </div>
                        <div class="lead-details">
                            <p class="lead-email">
                                <strong>Email:</strong> <?= htmlspecialchars($lead->email) ?>
                            </p>
                            <?php if ($lead->phone): ?>
                                <p class="lead-phone">
                                    <strong>Phone:</strong> <?= htmlspecialchars($lead->phone) ?>
                                </p>
                            <?php endif; ?>
                            <?php if ($lead->company): ?>
                                <p class="lead-company">
                                    <strong>Company:</strong> <?= htmlspecialchars($lead->company) ?>
                                </p>
                            <?php endif; ?>
                            <?php if ($lead->source): ?>
                                <p class="lead-source">
                                    <strong>Source:</strong> <?= htmlspecialchars($lead->source->getDisplayName()) ?>
                                </p>
                            <?php endif; ?>
                            <p class="lead-date">
                                <strong>Added:</strong> <?= TimezoneHelper::formatDateForDisplay($lead->created_at) ?>
                            </p>
                        </div>
                        <div class="lead-actions">
                            <a href="/leads/<?= $lead->id ?>" class="btn btn-sm btn-primary">View</a>
                            <a href="/leads/<?= $lead->id ?>/edit" class="btn btn-sm btn-secondary">Edit</a>
                            <?php if ($lead->status !== LeadStatus::CONVERTED->value): ?>
                                <form method="POST" action="/leads/<?= $lead->id ?>/convert" style="display: inline;">
                                    <button type="submit" class="btn btn-sm btn-success"
                                            onclick="return confirm('Convert this lead to a customer?')">Convert</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
