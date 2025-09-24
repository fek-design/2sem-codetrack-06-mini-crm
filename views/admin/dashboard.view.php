<?php
/** @var \App\Template $this */
/** @var string|null $success */
/** @var array<string, array<string>> $errors */
/** @var \App\Http\Request $request */
/** @var int $unreadMessages */
/** @var int $totalCustomers */
/** @var int $totalLeads */
/** @var array $customersByStatus */
/** @var array $leadsByStatus */
/** @var array $recentInteractions */

$this->extend('layout');
?>

<?php $this->start('title', 'CRM Dashboard') ?>

<section class="page-header">
    <div class="container">
        <h1 class="page-heading">
            CRM Dashboard
        </h1>
    </div>
</section>

<section class="dashboard">
    <div class="container">
        <!-- CRM Metrics -->
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h2 class="section-heading">CRM Overview</h2>
                <div class="metrics-grid">
                    <div class="metric-card">
                        <h3><?= $totalCustomers ?></h3>
                        <p>Total Customers</p>
                        <a href="/admin/customers" class="metric-link">View All →</a>
                    </div>
                    <div class="metric-card">
                        <h3><?= $totalLeads ?></h3>
                        <p>Total Leads</p>
                        <a href="/admin/leads" class="metric-link">View All →</a>
                    </div>
                    <div class="metric-card">
                        <h3><?= $unreadMessages ?></h3>
                        <p>Unread Messages</p>
                        <a href="/admin/messages" class="metric-link">View All →</a>
                    </div>
                </div>
            </div>

            <div class="dashboard-card">
                <h2 class="section-heading">Quick Actions</h2>
                <div class="action-grid">
                    <a href="/admin/customers/create" class="link-card">
                        <h3>Add Customer</h3>
                        <p>Create a new customer record</p>
                    </a>
                    <a href="/admin/leads/create" class="link-card">
                        <h3>Add Lead</h3>
                        <p>Add a new potential customer</p>
                    </a>
                    <a href="/admin/customers" class="link-card">
                        <h3>Manage Customers</h3>
                        <p>View and manage customer records</p>
                    </a>
                    <a href="/admin/leads" class="link-card">
                        <h3>Manage Leads</h3>
                        <p>View and convert leads</p>
                    </a>
                </div>
            </div>
        </div>

        <!-- Status Breakdown -->
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h2 class="section-heading">Customer Status</h2>
                <div class="status-list">
                    <?php if (empty($customersByStatus)): ?>
                        <p class="text-muted">No customers yet.</p>
                    <?php else: ?>
                        <?php foreach ($customersByStatus as $status => $count): ?>
                            <div class="status-item">
                                <span class="status-label"><?= ucfirst($status) ?></span>
                                <span class="status-count"><?= $count ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="dashboard-card">
                <h2 class="section-heading">Lead Status</h2>
                <div class="status-list">
                    <?php if (empty($leadsByStatus)): ?>
                        <p class="text-muted">No leads yet.</p>
                    <?php else: ?>
                        <?php foreach ($leadsByStatus as $status => $count): ?>
                            <div class="status-item">
                                <span class="status-label"><?= ucfirst($status) ?></span>
                                <span class="status-count"><?= $count ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="dashboard-card">
            <h2 class="section-heading">Recent Interactions</h2>
            <div class="activity-list">
                <?php if (empty($recentInteractions)): ?>
                    <p class="text-muted">No recent interactions to show.</p>
                <?php else: ?>
                    <?php foreach ($recentInteractions as $interaction): ?>
                        <div class="activity-item">
                            <div class="activity-content">
                                <strong><?= htmlspecialchars($interaction->getSubject()) ?></strong>
                                <span class="activity-type">[<?= ucfirst($interaction->getType()) ?>]</span>
                                <p class="activity-description"><?= htmlspecialchars($interaction->getDescription()) ?></p>
                                <small class="activity-date"><?= date('M j, Y g:i A', strtotime($interaction->getInteractionDate())) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
