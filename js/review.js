function showReviewResult(card, dialog, title, message) {
    card.classList.add('review-result-card');

    const template = document.querySelector('#result-card');

    const clone = template.content.cloneNode(true);

    clone.querySelector('.popup-close').setAttribute('aria-label','Close review dialog');
    clone.querySelector('.result-topic').textContent = 'PowerPIT Review';
    clone.querySelector('.result-title').textContent = title;
    clone.querySelector('.result-message').textContent = message;

    card.innerHTML = '';
    card.appendChild(clone);

    document.addEventListener('click', (event) => {
        const closeButton = event.target.closest('.popup-close, .popup-ok');

        if (!closeButton) return;

        const dialog = closeButton.closest('dialog');

        if (dialog) {
            dialog.close();
        }
    });
}

document.addEventListener('submit', (event) => {
    const reviewForm = event.target.closest('.review-form');

    if (reviewForm) {
        event.preventDefault();
        const request = new XMLHttpRequest();

        request.addEventListener('load', () => {
            const dialog = reviewForm.closest('dialog');
            const card = reviewForm.closest('.popup-card');

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
                console.log('Review request failed with status:', request.status);
                console.log('Response:', request.responseText);

                showReviewResult(
                    card,
                    dialog,
                    'Review failed',
                    'Could not send the review. Please try again.'
                );
            }
        });

        request.open('POST', reviewForm.action, true);

        const data = new FormData(reviewForm);
        request.send(data);
    }  
});