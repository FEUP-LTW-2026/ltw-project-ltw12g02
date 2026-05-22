const bookingForms = document.querySelectorAll('#review-form');

bookingForms.forEach((form) => {
    form.addEventListener('submit', (event) => {
        event.preventDefault();

        const request = new XMLHttpRequest();

        request.addEventListener('load', () => {
            const dialog = form.closest('dialog');
            const card = form.closest('.popup-card');

            if (request.status === 200) {
                card.innerHTML = `
                    <button type="button" class="popup-close" aria-label="Close review dialog">
                        &times;
                    </button>

                    <header class="popup-header">
                        <p class="profile-member-card-label">PowerPIT Review</p>
                        <h1>Thank you!</h1>
                        <p>Your review has been sent. We appreciate your feedback.</p>
                    </header>

                    <div class="popup-actions">
                        <button type="button" class="btn small light popup-ok">
                            Done
                        </button>
                    </div>
                `;
            } else if (request.status === 409) {
                card.innerHTML = `
                    <button type="button" class="popup-close" aria-label="Close review dialog">
                        &times;
                    </button>

                    <header class="popup-header">
                        <p class="profile-member-card-label">PowerPIT Review</p>
                        <h1>Already reviewed</h1>
                        <p>You have already reviewed this class.</p>
                    </header>

                    <div class="popup-actions">
                        <button type="button" class="btn small light popup-ok">
                            Done
                        </button>
                    </div>
                `;
            } else {
                card.innerHTML = `
                    <button type="button" class="popup-close" aria-label="Close review dialog">
                        &times;
                    </button>

                    <header class="popup-header">
                        <p class="profile-member-card-label">PowerPIT Review</p>
                        <h1>Review failed</h1>
                        <p>Could not send the review. Please try again.</p>
                    </header>

                    <div class="popup-actions">
                        <button type="button" class="btn small light popup-ok">
                            Done
                        </button>
                    </div>
                `;
            }

            card.querySelector('.popup-close').addEventListener('click', () => {
                dialog.close();
            });

            card.querySelector('.popup-ok').addEventListener('click', () => {
                dialog.close();
            });
        });

        request.open("POST", form.action, true);

        const data = new FormData(form);
        request.send(data);
    });
});