(() => {
    const searchForm = document.querySelector('.search-trainers-form');
    const searchInput = document.querySelector('#trainer-search-input');
    const resultsContainer = document.querySelector('#trainer-search-results');

    if (searchInput === null || resultsContainer === null) {
        return;
    }

    function search(query) {
        const request = new XMLHttpRequest();

        request.open(
            'GET',
            '../actions/action_search_trainers.php?q=' + encodeURIComponent(query),
            true
        );

        request.addEventListener('load', () => {
            if (request.status !== 200) {
                resultsContainer.innerHTML = '<p class="error-message">Error searching trainers.</p>';
                return;
            }

            const data = JSON.parse(request.responseText);

            if (!data.success) {
                resultsContainer.innerHTML = '<p class="error-message">Could not search trainers.</p>';
                return;
            }

            showTrainers(data.trainers);
        });

        request.addEventListener('error', () => {
            resultsContainer.innerHTML = '<p class="error-message">Network error.</p>';
        });

        request.send();
    }

    function showTrainers(trainers) {
        resultsContainer.innerHTML = '';

        if (trainers.length === 0) {
            resultsContainer.innerHTML = '<p class="empty-search-message">No trainers found.</p>';
            return;
        }

        for (const trainer of trainers) {
            const trainerRow = document.createElement('article');

            trainerRow.classList.add('admin-user-row');
            trainerRow.classList.add('admin-trainer-row');

            trainerRow.innerHTML = `
                <div class="admin-user-main">
                    <img src="../assets/users/${trainer.ProfileImage ?? 'default.png'}" alt="Trainer profile image">

                    <div>
                        <strong>${trainer.Name}</strong>
                        <p>@${trainer.Username}</p>
                    </div>
                </div>

                <p class="admin-user-email">${trainer.Email}</p>

                <span class="admin-user-role">${trainer.Specializations ?? 'Trainer'}</span>

                <div class="admin-user-actions">
                    <a href="admin_edit_trainer.php?id=${trainer.TrainerId}">Edit</a>
                </div>
            `;

            resultsContainer.appendChild(trainerRow);
        }
    }

    searchInput.addEventListener('input', () => {
        search(searchInput.value.trim());
    });

    if (searchForm !== null) {
        searchForm.addEventListener('submit', (event) => {
            event.preventDefault();
            search(searchInput.value.trim());
        });
    }

    search('');
})();