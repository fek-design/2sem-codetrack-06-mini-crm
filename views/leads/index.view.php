<?php
/** @var \App\Template $this */
/** @var array $leads */

$this->extend('layout');
?>

<?php $this->start('title', 'Leads') ?>

<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1 class="page-heading">Leads</h1>
            <a href="/admin/leads/create" class="btn btn-primary">Add Lead</a>
        </div>
    </div>
</section>

<section class="leads">
    <div class="container">
        <?php if (empty($leads)): ?>
            <div class="empty-state">
                <h3>No leads found</h3>
                <p>You haven't added any leads yet.</p>
                <a href="/admin/leads/create" class="btn btn-primary">Add Your First Lead</a>
            </div>
        <?php else: ?>
            <div class="leads-grid">
                <?php foreach ($leads as $lead): ?>
                    <div class="lead-card">
                        <div class="lead-header">
                            <h3 class="lead-name">
                                <a href="/admin/leads/<?= $lead->getId() ?>">
                                    <?= htmlspecialchars($lead->getName()) ?>
                                </a>
                            </h3>
                            <span class="status-badge status-<?= $lead->getStatus() ?>">
                                <?= ucfirst($lead->getStatus()) ?>
                            </span>
                        </div>
                        <div class="lead-details">
                            <p class="lead-email">
                                <strong>Email:</strong> <?= htmlspecialchars($lead->getEmail()) ?>
                            </p>
                            <?php if ($lead->getPhone()): ?>
                                <p class="lead-phone">
                                    <strong>Phone:</strong> <?= htmlspecialchars($lead->getPhone()) ?>
                                </p>
                            <?php endif; ?>
                            <?php if ($lead->getCompany()): ?>
                                <p class="lead-company">
                                    <strong>Company:</strong> <?= htmlspecialchars($lead->getCompany()) ?>
                                </p>
                            <?php endif; ?>
                            <?php if ($lead->getSource()): ?>
                                <p class="lead-source">
                                    <strong>Source:</strong> <?= htmlspecialchars($lead->getSource()) ?>
                                </p>
                            <?php endif; ?>
                            <p class="lead-date">
                                <strong>Added:</strong> <?= date('M j, Y', strtotime($lead->getCreatedAt())) ?>
                            </p>
                        </div>
                        <div class="lead-actions">
                            <a href="/admin/leads/<?= $lead->getId() ?>" class="btn btn-sm btn-primary">View</a>
                            <a href="/admin/leads/<?= $lead->getId() ?>/edit" class="btn btn-sm btn-secondary">Edit</a>
                            <?php if ($lead->getStatus() !== 'converted'): ?>
                                <form method="POST" action="/admin/leads/<?= $lead->getId() ?>/convert" style="display: inline;">
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
