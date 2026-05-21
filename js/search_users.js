const searchForm = document.querySelector('.search-users-form');
const searchInput = document.querySelector('#user-search-input');
const resultsContainer = document.querySelector('#user-search-results');

function search(query) {
    const request = new XMLHttpRequest();

    request.open(
        'GET',
        '../actions/action_search_user.php?q=' + encodeURIComponent(query),
        true
    );

    request.addEventListener('load', () => {
        if (request.status !== 200) {
            resultsContainer.innerHTML = '<p class="error-message">Error searching users.</p>';
            return;
        }

        const data = JSON.parse(request.responseText);

        if (!data.success) {
            resultsContainer.innerHTML = '<p class="error-message">Could not search users.</p>';
            return;
        }

        showUsers(data.users);
    });

    request.addEventListener('error', () => {
        resultsContainer.innerHTML = '<p class="error-message">Network error.</p>';
    });

    request.send();
}

function showUsers(users) {
    resultsContainer.innerHTML = '';

    if (users.length === 0) {
        resultsContainer.innerHTML = '<p class="empty-search-message">No users found.</p>';
        return;
    }

    for (const user of users) {
        const userRow = document.createElement('article');
        userRow.classList.add('admin-user-row');

        userRow.innerHTML = `
            <div class="admin-user-main">
                <img src="../assets/users/${user.ProfileImage ?? 'default.png'}" alt="User profile image">

                <div>
                    <strong>${user.Name}</strong>
                    <p>@${user.Username}</p>
                </div>
            </div>

            <p class="admin-user-email">${user.Email}</p>

            <span class="admin-user-role">${user.Role}</span>

            <div class="admin-user-actions">
                <a href="admin_edit_user.php?id=${user.UserId}">Edit</a>
            </div>
        `;

        resultsContainer.appendChild(userRow);
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