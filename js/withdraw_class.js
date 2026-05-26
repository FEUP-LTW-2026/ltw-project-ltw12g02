function showWithdrawResult(card, dialog, title, message) {
    card.classList.add('withdraw-result-card');

    card.innerHTML = `
        <button type="button" class="popup-close" aria-label="Close withdraw dialog">
            &times;
        </button>

        <header class="popup-header">
            <p class="profile-member-card-label">PowerPIT Withdrawal</p>
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