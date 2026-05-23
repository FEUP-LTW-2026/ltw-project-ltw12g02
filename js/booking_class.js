const bookingForms = document.querySelectorAll('#booking-form');

function showBookingResult(card, dialog, title, message) {
    card.classList.add('booking-result-card');

    card.innerHTML = `
        <button type="button" class="popup-close" aria-label="Close booking dialog">
            &times;
        </button>

        <header class="popup-header">
            <p class="profile-member-card-label">PowerPIT Booking</p>
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

bookingForms.forEach((form) => {
    form.addEventListener('submit', (event) => {
        event.preventDefault();

        const request = new XMLHttpRequest();

        request.addEventListener('load', () => {
            const dialog = form.closest('dialog');
            const card = form.closest('.popup-card');

            if (!dialog || !card) return;

            if (request.status === 200) {
                showBookingResult(
                    card,
                    dialog,
                    'Booked!',
                    'Your class booking has been confirmed.'
                );
            } else if (request.status === 409) {
                showBookingResult(
                    card,
                    dialog,
                    'Already booked',
                    'You have already booked this class.'
                );
            } else {
                showBookingResult(
                    card,
                    dialog,
                    'Booking failed',
                    'Could not complete the booking. Please try again.'
                );
            }
        });

        request.open('POST', form.action, true);

        const data = new FormData(form);
        request.send(data);
    });
});