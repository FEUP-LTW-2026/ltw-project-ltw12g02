function showWithdrawResult(card, dialog, title, message) {
    card.classList.add('withdraw-result-card');

    const template = document.querySelector('#result-card');

    const clone = template.content.cloneNode(true);

    clone.querySelector('.popup-close').setAttribute('aria-label','Close withdrawal dialog');
    clone.querySelector('.result-topic').textContent = 'PowerPIT Withdrawal';
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
    const withdrawForm = event.target.closest('.withdraw-form');
    
    if (withdrawForm) {
        event.preventDefault();
        const request = new XMLHttpRequest();

        request.addEventListener('load', () => {
            const dialog = withdrawForm.closest('dialog');
            const card = withdrawForm.closest('.popup-card');

            if (!dialog || !card) return;

            if (request.status === 200) {
                showWithdrawResult(
                    card,
                    dialog,
                    'Withdrawn!',
                    'Your class withdrawal has been confirmed.'
                );

                const enrollmentCard = dialog.closest('.card-dl-row');
                if(enrollmentCard) enrollmentCard.remove();

                const container = document.querySelector('#next-classes-container');
                if (container.querySelectorAll('dl').length == 0) container.innerHTML = '<p>You do not have any booked classes yet.</p>';
            } else if (request.status === 401) {
                showWithdrawResult(
                    card,
                    dialog,
                    'Login required',
                    'You need to be logged in to withdraw from this class.'
                );
            } else if (request.status === 409) {
                showWithdrawResult(
                    card,
                    dialog,
                    'Already withdrawn',
                    'You have already withdrawn from this class.'
                );
            } else {
                showWithdrawResult(
                    card,
                    dialog,
                    'Withdrawal failed',
                    'Could not complete the withdrawal. Please try again.'
                );
            }
        });

        request.open('POST', withdrawForm.action, true);
        request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        const data = new FormData(withdrawForm);
        request.send(data);
    }
    
});