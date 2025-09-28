<?php
/**
 * @var \App\Template $this
 * @var \App\Http\Request $request
 * @var array $errors
 * @var string|null $success
 */
use App\Enums\LeadSource;

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
            <form method="POST" action="/leads" class="lead-form form-spacing">
                <div class="form-group">
                    <label for="name">Name *</label>
                    <input type="text" name="name" id="name" required
                           value="<?= htmlspecialchars($request->get('name') ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email" required
                           value="<?= htmlspecialchars($request->get('email') ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" name="phone" id="phone"
                           value="<?= htmlspecialchars($request->get('phone') ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="company">Company</label>
                    <input type="text" name="company" id="company"
                           value="<?= htmlspecialchars($request->get('company') ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="source">Lead Source</label>
                    <select name="source" id="source">
                        <option value="">Select source...</option>
                        <?php foreach (LeadSource::cases() as $source): ?>
                            <option value="<?= $source->value ?>" <?= ($request->get('source') ?? 'none') === $source->value ? 'selected' : '' ?>>
                                <?= $source->getDisplayName() ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" rows="4"><?= htmlspecialchars($request->get('notes') ?? '') ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Add Lead</button>
                    <a href="/leads" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>
