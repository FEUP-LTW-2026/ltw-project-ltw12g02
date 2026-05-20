const bookingForms = document.querySelectorAll('.popup-form');

bookingForms.forEach((form) => {
    form.addEventListener('submit', (event) => {
        event.preventDefault();

        const request = new XMLHttpRequest();

        request.addEventListener('load', () => {
            const dialog = form.closest('dialog');
            const card = form.closest('.popup-card');

            if (request.status === 200) {
                card.innerHTML = `
                    <button type="button" class="popup-close" aria-label="Close booking dialog">
                        &times;
                    </button>

                    <header class="popup-header">
                        <p class="profile-member-card-label">PowerPIT Booking</p>
                        <h1>Booked!</h1>
                        <p>Your class booking has been confirmed.</p>
                    </header>

                    <div class="popup-actions">
                        <button type="button" class="btn small light popup-ok">
                            Done
                        </button>
                    </div>
                `;
            } else if (request.status === 409) {
                card.innerHTML = `
                    <button type="button" class="popup-close" aria-label="Close booking dialog">
                        &times;
                    </button>

                    <header class="popup-header">
                        <p class="profile-member-card-label">PowerPIT Booking</p>
                        <h1>Already booked</h1>
                        <p>You have already booked this class.</p>
                    </header>

                    <div class="popup-actions">
                        <button type="button" class="btn small light popup-ok">
                            Done
                        </button>
                    </div>
                `;
            } else {
                card.innerHTML = `
                    <button type="button" class="popup-close" aria-label="Close booking dialog">
                        &times;
                    </button>

                    <header class="popup-header">
                        <p class="profile-member-card-label">PowerPIT Booking</p>
                        <h1>Booking failed</h1>
                        <p>Could not complete the booking. Please try again.</p>
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