function showBookingResult(card, dialog, title, message) {
    card.classList.add('booking-result-card');

    const template = document.querySelector('#result-card');

    const clone = template.content.cloneNode(true);

    clone.querySelector('.popup-close').setAttribute('aria-label','Close booking dialog');
    clone.querySelector('.result-topic').textContent = 'PowerPIT Booking';
    clone.querySelector('.result-title').textContent = title;
    clone.querySelector('.result-message').textContent = message;

    card.innerHTML = '';
    card.appendChild(clone);
}

document.addEventListener('click', (event) => {
    const closeButton = event.target.closest('.popup-close, .popup-ok');

    if (!closeButton) return;

    const dialog = closeButton.closest('dialog');

    if (dialog) {
        dialog.close();
    }
});

document.addEventListener('submit', (event) => {
    const bookingForm = event.target.closest('.booking-form');
    
    if (bookingForm) {
        event.preventDefault();
        const request = new XMLHttpRequest();

        request.addEventListener('load', () => {
            const dialog = bookingForm.closest('dialog');
            const card = bookingForm.closest('.popup-card');

            if (!dialog || !card) return;

            if (request.status === 200) {
                showBookingResult(
                    card,
                    dialog,
                    'Booked!',
                    'Your class booking has been confirmed.'
                );
                const classId = bookingForm.querySelector('[name="class_id"]').value.trim();
                const response = JSON.parse(request.responseText);
                const oldCard = document.querySelector(`.card[data-class-id="${classId}"]`);
                if (oldCard) oldCard.outerHTML = response.html;

            } else if (request.status === 401) {
                showBookingResult(
                    card,
                    dialog,
                    'Login required',
                    'You need to be logged in to book this class.'
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

        request.open('POST', bookingForm.action, true);
        request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        const data = new FormData(bookingForm);
        request.send(data);
    }
    
});