/**
 * Submission Echo Listeners
 *
 * This file handles dynamic subscription to submission-specific Echo channels
 * when viewing submission details. It manages subscribing and unsubscribing
 * from comment channels based on the current submission being viewed.
 *
 * @trace D03 SRS-FR-008, D04 §5.3 (Requirements 7.4)
 * @author dev-team@motac.gov.my
 * @last-updated 2025-11-06
 */

/**
 * Subscribe to submission comments channel
 *
 * This function is called when a submission detail page is loaded.
 * It subscribes to the submission-specific channel for real-time comment updates.
 *
 * @param {string} submissionType - Type of submission ('helpdesk' or 'loans')
 * @param {number} submissionId - ID of the submission
 */
export function subscribeToSubmissionComments(submissionType, submissionId) {
    if (!window.Echo) {
        console.warn("Submission Echo: Echo not initialized");
        return;
    }

    if (typeof window.subscribeToSubmissionComments === "function") {
        window.subscribeToSubmissionComments(submissionType, submissionId);
    }
}

/**
 * Unsubscribe from submission comments channel
 *
 * This function is called when leaving a submission detail page.
 * It unsubscribes from the submission-specific channel to prevent memory leaks.
 *
 * @param {string} submissionType - Type of submission ('helpdesk' or 'loans')
 * @param {number} submissionId - ID of the submission
 */
export function unsubscribeFromSubmissionComments(
    submissionType,
    submissionId
) {
    if (!window.Echo) {
        return;
    }

    if (typeof window.unsubscribeFromSubmissionComments === "function") {
        window.unsubscribeFromSubmissionComments(submissionType, submissionId);
    }
}

/**
 * Auto-subscribe when submission detail page loads
 */
document.addEventListener("DOMContentLoaded", () => {
    // Check if we're on a submission detail page
    const submissionDetailElement = document.querySelector(
        "[data-submission-type][data-submission-id]"
    );

    if (submissionDetailElement) {
        const submissionType = submissionDetailElement.dataset.submissionType;
        const submissionId = parseInt(
            submissionDetailElement.dataset.submissionId,
            10
        );

        if (submissionType && submissionId) {
            console.log(
                `Submission Echo: Auto-subscribing to ${submissionType} ${submissionId}`
            );
            subscribeToSubmissionComments(submissionType, submissionId);

            // Unsubscribe when leaving the page
            window.addEventListener("beforeunload", () => {
                unsubscribeFromSubmissionComments(submissionType, submissionId);
            });
        }
    }
});

/**
 * Handle Livewire navigation (for SPA-like behavior)
 */
if (window.Livewire) {
    document.addEventListener("livewire:navigated", () => {
        // Re-check for submission detail page after Livewire navigation
        const submissionDetailElement = document.querySelector(
            "[data-submission-type][data-submission-id]"
        );

        if (submissionDetailElement) {
            const submissionType =
                submissionDetailElement.dataset.submissionType;
            const submissionId = parseInt(
                submissionDetailElement.dataset.submissionId,
                10
            );

            if (submissionType && submissionId) {
                subscribeToSubmissionComments(submissionType, submissionId);
            }
        }
    });
}
