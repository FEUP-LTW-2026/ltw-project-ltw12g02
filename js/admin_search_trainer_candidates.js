document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('#add-trainer-search-input');
    const resultsContainer = document.querySelector('#add-trainer-search-results');

    if (searchInput === null || resultsContainer === null) {
        return;
    }

    function searchTrainerCandidates(query) {
        const request = new XMLHttpRequest();

        request.open(
            'GET',
            '../actions/action_search_trainer_candidates.php?q=' + encodeURIComponent(query),
            true
        );

        request.addEventListener('load', () => {
            if (request.status !== 200) {
                resultsContainer.innerHTML = '<p class="error-message">Error searching users.</p>';
                console.error(request.responseText);
                return;
            }

            let data;

            try {
                data = JSON.parse(request.responseText);
            } catch (error) {
                resultsContainer.innerHTML = '<p class="error-message">Invalid response from server.</p>';
                console.error('Invalid JSON:', request.responseText);
                return;
            }

            if (!data.success) {
                resultsContainer.innerHTML = '<p class="error-message">Could not search users.</p>';
                return;
            }

            showTrainerCandidates(data.users);
        });

        request.addEventListener('error', () => {
            resultsContainer.innerHTML = '<p class="error-message">Network error.</p>';
        });

        request.send();
    }

    function showTrainerCandidates(users) {
        resultsContainer.innerHTML = '';

        if (users.length === 0) {
            resultsContainer.innerHTML = '<p class="empty-search-message">No members available to promote.</p>';
            return;
        }

        for (const user of users) {
            const userRow = document.createElement('article');
            userRow.classList.add('admin-user-row');

            const profileImage = user.ProfileImage ?? 'default.png';
            const dialogId = 'add-trainer-dialog-' + user.UserId;

            userRow.innerHTML = `
                <div class="admin-user-main">
                    <img
                        src="../assets/users/${escapeHtml(profileImage)}"
                        alt="User profile image"
                    >

                    <div>
                        <strong>${escapeHtml(user.Name)}</strong>
                        <p>@${escapeHtml(user.Username)}</p>
                    </div>
                </div>

                <p class="admin-user-email">
                    ${escapeHtml(user.Email)}
                </p>

                <span class="admin-user-role">
                    ${escapeHtml(user.Role)}
                </span>

                <div class="admin-user-actions">
                    <button
                        type="button"
                        data-confirm-open="${escapeHtml(dialogId)}"
                    >
                        Make Trainer
                    </button>
                </div>

                <dialog
                    id="${escapeHtml(dialogId)}"
                    class="popup-dialog admin-confirm-dialog"
                >
                    <section class="popup-card admin-confirm-card">
                        <button
                            type="button"
                            class="popup-close"
                            data-confirm-close
                        >
                            ×
                        </button>

                        <header class="popup-header">
                            <p class="profile-member-card-label">Confirm Action</p>

                            <h1>Make Trainer</h1>

                            <p>
                                This action will promote this member to trainer.
                            </p>
                        </header>

                        <form
                            action="../actions/action_admin_change_role.php"
                            method="post"
                            class="popup-form admin-confirm-form"
                            data-confirm-name="${escapeHtml(user.Name)}"
                        >
                            <input
                                type="hidden"
                                name="id"
                                value="${escapeHtml(String(user.UserId))}"
                            >

                            <input
                                type="hidden"
                                name="role"
                                value="trainer"
                            >

                            <p class="admin-confirm-warning">
                                To confirm promoting this user, type their name:
                                <strong>${escapeHtml(user.Name)}</strong>
                            </p>

                            <label>
                                User name

                                <input
                                    type="text"
                                    name="confirmation_name"
                                    autocomplete="off"
                                    data-confirm-input
                                    required
                                >
                            </label>

                            <div class="popup-actions">
                                <button
                                    type="button"
                                    class="btn"
                                    data-confirm-close
                                >
                                    Cancel
                                </button>

                                <button
                                    type="submit"
                                    class="btn light"
                                    data-confirm-submit
                                    disabled
                                >
                                    Confirm
                                </button>
                            </div>
                        </form>
                    </section>
                </dialog>
            `;

            resultsContainer.appendChild(userRow);
        }
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    searchInput.addEventListener('input', () => {
        searchTrainerCandidates(searchInput.value.trim());
    });

    searchTrainerCandidates('');
});