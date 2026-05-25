const form = document.querySelector('.filter-form');

const container = document.querySelector('#available-classes');

function loadClasses() {
    container.innerHTML = `
        <p>
            Loading classes...
        </p>
    `;

    const params = new URLSearchParams(
        new FormData(form)
    );

    fetch(
        '../actions/action_filter_classes.php?' +
        params.toString()
    )

    .then((response) => response.text())

    .then((html) => {

        container.innerHTML = html;

        history.replaceState(
            null,
            '',
            '?' + params.toString()
        );

    })

    .catch(() => {

        container.innerHTML = `
            <p>
                Could not load classes.
            </p>
        `;

    });
}

const urlParams = new URLSearchParams(
    window.location.search
);

form.addEventListener('change', loadClasses);


for (const [key, value] of urlParams.entries()) {

    const field = form.elements[key];

    if (field) {

        field.value = value;
    }
}

loadClasses();