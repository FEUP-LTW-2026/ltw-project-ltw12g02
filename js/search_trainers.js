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

            trainerRow.classList.add('admin-user-row', 'admin-trainer-row');

            const trainerMain = document.createElement('div');
            trainerMain.classList.add('admin-user-main');

            const image = document.createElement('img');
            image.src = '../assets/users/' + (trainer.ProfileImage || 'default.png');
            image.alt = 'Trainer profile image';

            const trainerInfo = document.createElement('div');

            const name = document.createElement('strong');
            name.textContent = trainer.Name;

            const username = document.createElement('p');
            username.textContent = '@' + trainer.Username;

            trainerInfo.appendChild(name);
            trainerInfo.appendChild(username);

            trainerMain.appendChild(image);
            trainerMain.appendChild(trainerInfo);

            const email = document.createElement('p');
            email.classList.add('admin-user-email');
            email.textContent = trainer.Email;

            const specializations = document.createElement('span');
            specializations.classList.add('admin-user-role');
            specializations.textContent = trainer.Specializations || 'Trainer';

            const actions = document.createElement('div');
            actions.classList.add('admin-user-actions');

            const editLink = document.createElement('a');
            editLink.href = 'admin_edit_trainer.php?id=' + encodeURIComponent(trainer.TrainerId);
            editLink.textContent = 'Edit';

            actions.appendChild(editLink);

            trainerRow.appendChild(trainerMain);
            trainerRow.appendChild(email);
            trainerRow.appendChild(specializations);
            trainerRow.appendChild(actions);

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