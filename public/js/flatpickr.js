const today = new Date();

flatpickr("#formValidationFecha", {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "d-m-Y",
    defaultDate: today,
    minDate: today,
    altInputClass: "form-control flatpickr-input"
});

$(document).ready(function() {
    // Función para formatear hora HH:mm
    function formatTime(date) {
        let hours = date.getHours().toString().padStart(2, '0');
        let minutes = date.getMinutes().toString().padStart(2, '0');
        return `${hours}:${minutes}`;
    }

    // -------------------- EVENTO --------------------
    const nowEvento = new Date();
    const oneHourLaterEvento = new Date(nowEvento.getTime() + 60 * 60 * 1000);

    $('#hora_evento').val(formatTime(nowEvento));
    $('#hora_fin_evento').val(formatTime(oneHourLaterEvento));

    $('#hora_evento').clockpicker({
        autoclose: true,
        donetext: 'OK'
    }).change(function() {
        const parts = $(this).val().split(':');
        let hour = parseInt(parts[0]) + 1;
        if (hour >= 24) hour = 23;
        const newTime = `${hour.toString().padStart(2, '0')}:${parts[1]}`;
        $('#hora_fin_evento').val(newTime);
    });

    $('#hora_fin_evento').clockpicker({
        autoclose: true,
        donetext: 'OK'
    });

    // -------------------- AUDIENCIA --------------------
    const nowAud = new Date();
    const oneHourLaterAud = new Date(nowAud.getTime() + 60 * 60 * 1000);

    $('#hora_audiencia').val(formatTime(nowAud));
    $('#hora_fin_audiencia').val(formatTime(oneHourLaterAud));

    $('#hora_audiencia').clockpicker({
        autoclose: true,
        donetext: 'OK'
    }).change(function() {
        const parts = $(this).val().split(':');
        let hour = parseInt(parts[0]) + 1;
        if (hour >= 24) hour = 23;
        const newTime = `${hour.toString().padStart(2, '0')}:${parts[1]}`;
        $('#hora_fin_audiencia').val(newTime);
    });

    $('#hora_fin_audiencia').clockpicker({
        autoclose: true,
        donetext: 'OK'
    });
});
