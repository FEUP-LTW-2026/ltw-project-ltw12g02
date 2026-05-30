document.addEventListener('change', () => {

    const attendanceInput = document.querySelector('.attendance-check');

    if (!attendanceInput) return;

    const data = new FormData();

    data.append(
        'user_id',
        attendanceInput.dataset.userId
    );

    data.append(
        'class_id',
        attendanceInput.dataset.classId
    );

    data.append(
        'attendance',
        attendanceInput.checked ? 1 : 0
    );

    fetch('../actions/action_attendance.php', {

        method: 'POST',

        body: data

    });

});