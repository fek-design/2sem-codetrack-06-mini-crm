/**
 * Convert CRM entity references to clickable links
 * Converts patterns like {Lead #123} and {Customer #456} to clickable links
 */
document.addEventListener('DOMContentLoaded', function() {
    // Find all elements that might contain interaction descriptions
    const interactionDescriptions = document.querySelectorAll('.interaction-description, .activity-description');

    interactionDescriptions.forEach(function(element) {
        let html = element.innerHTML;

        // Convert {Lead #123} to clickable link
        html = html.replace(/\{Lead #(\d+)\}/g, function(match, leadId) {
            return `<a href="/admin/leads/${leadId}" class="entity-link lead-link" title="View Lead #${leadId}">{Lead #${leadId}}</a>`;
        });

        // Convert {Customer #123} to clickable link
        html = html.replace(/\{Customer #(\d+)\}/g, function(match, customerId) {
            return `<a href="/admin/customers/${customerId}" class="entity-link customer-link" title="View Customer #${customerId}">{Customer #${customerId}}</a>`;
        });

        element.innerHTML = html;
    });
});

