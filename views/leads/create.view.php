<?php
/** @var \App\Template $this */
/** @var string|null $error */
/** @var array|null $data */

$this->extend('layout');
?>

<?php $this->start('title', 'Add Lead') ?>

<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1 class="page-heading">Add Lead</h1>
            <a href="/leads" class="btn btn-secondary">Back to Leads</a>
        </div>
    </div>
</section>

<section class="form-section">
    <div class="container">
        <div class="form-card">
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="/leads" class="lead-form form-spacing">
                <div class="form-group">
                    <label for="name">Name *</label>
                    <input type="text" name="name" id="name" required
                           value="<?= htmlspecialchars($data['name'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email" required
                           value="<?= htmlspecialchars($data['email'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" name="phone" id="phone"
                           value="<?= htmlspecialchars($data['phone'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="company">Company</label>
                    <input type="text" name="company" id="company"
                           value="<?= htmlspecialchars($data['company'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="source">Lead Source</label>
                    <select name="source" id="source">
                        <option value="">Select source...</option>
                        <option value="website" <?= ($data['source'] ?? '') === 'website' ? 'selected' : '' ?>>Website</option>
                        <option value="referral" <?= ($data['source'] ?? '') === 'referral' ? 'selected' : '' ?>>Referral</option>
                        <option value="social_media" <?= ($data['source'] ?? '') === 'social_media' ? 'selected' : '' ?>>Social Media</option>
                        <option value="email_campaign" <?= ($data['source'] ?? '') === 'email_campaign' ? 'selected' : '' ?>>Email Campaign</option>
                        <option value="cold_call" <?= ($data['source'] ?? '') === 'cold_call' ? 'selected' : '' ?>>Cold Call</option>
                        <option value="trade_show" <?= ($data['source'] ?? '') === 'trade_show' ? 'selected' : '' ?>>Trade Show</option>
                        <option value="other" <?= ($data['source'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" rows="4"><?= htmlspecialchars($data['notes'] ?? '') ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Add Lead</button>
                    <a href="/leads" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>
