const reviewForms = document.querySelectorAll('#review-form');

function showReviewResult(card, dialog, title, message) {
    card.classList.add('review-result-card');

    card.innerHTML = `
        <button type="button" class="popup-close" aria-label="Close review dialog">
            &times;
        </button>

        <header class="popup-header">
            <p class="profile-member-card-label">PowerPIT Review</p>
            <h1>${title}</h1>
            <p>${message}</p>
        </header>

        <div class="popup-actions">
            <button type="button" class="btn small light popup-ok">
                Done
            </button>
        </div>
    `;

    card.querySelector('.popup-close').addEventListener('click', () => {
        dialog.close();
    });

    card.querySelector('.popup-ok').addEventListener('click', () => {
        dialog.close();
    });
}

reviewForms.forEach((form) => {
    form.addEventListener('submit', (event) => {
        event.preventDefault();

        const request = new XMLHttpRequest();

        request.addEventListener('load', () => {
            const dialog = form.closest('dialog');
            const card = form.closest('.popup-card');

            if (!dialog || !card) return;

            if (request.status === 200) {
                showReviewResult(
                    card,
                    dialog,
                    'Review Sent!',
                    'We appreciate your feedback.'
                );
            } else if (request.status === 409) {
                showReviewResult(
                    card,
                    dialog,
                    'Already reviewed',
                    'You have already reviewed this class.'
                );
            } else {
                showReviewResult(
                    card,
                    dialog,
                    'Review failed',
                    'Could not send the review. Please try again.'
                );
            }
        });

        request.open('POST', form.action, true);

        const data = new FormData(form);
        request.send(data);
    });
});