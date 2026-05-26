function messageRemove(message) {
    message.classList.add('closing');
    setTimeout(() => {
        message.remove();
    }, 300);
}


document.addEventListener('click', (event) => {
    const button = event.target.closest('.message_close');

    if (!button) return;

    const message = button.closest('.message');

    if (message) {
        messageRemove(message);
    }
});

document.querySelectorAll('.message')
    .forEach((message) => {
        setTimeout(() => {
            messageRemove(message);
        }, 3000);
    }
);

