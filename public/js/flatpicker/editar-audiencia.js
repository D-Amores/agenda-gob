const today = new Date();

// Función para formatear hora HH:mm
function formatTime(date) {
    const hours = date.getHours().toString().padStart(2, '0');
    const minutes = date.getMinutes().toString().padStart(2, '0');
    return `${hours}:${minutes}`;
}

toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: "toast-top-right",
    timeOut: 3000
};

// ---------------------- Fecha ----------------------
flatpickr("#formValidationFecha", {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "d-m-Y",
    defaultDate: $('#formValidationFecha').val() || today,
    minDate: new Date().setHours(0, 0, 0, 0),
    altInputClass: "form-control flatpickr-input"
});

// ---------------------- Inicializar horas ----------------------
let initialHoraInicio = $('#hora_audiencia').val()?.split(':').slice(0, 2).join(':') || formatTime(today);
let initialHoraFin = $('#hora_fin_audiencia').val()?.split(':').slice(0, 2).join(':') || formatTime(new Date(today.getTime() + 60 * 60 * 1000));

$('#hora_audiencia').val(initialHoraInicio);
$('#hora_fin_audiencia').val(initialHoraFin);
// Guardar hora mínima para validar fin
let [minHour, minMinute] = initialHoraInicio.split(':').map(Number);
$('#hora_fin_audiencia').data('minHour', minHour);
$('#hora_fin_audiencia').data('minMinute', minMinute);

// ---------------------- Hora inicio ----------------------
$('#hora_audiencia').clockpicker({ autoclose: true, donetext: 'OK' }).change(function() {
    const selectedDate = $('#formValidationFecha').val();
    const now = new Date();
    let [hour, min] = $(this).val().split(':').map(Number);

    // No permitir hora pasada si es hoy
    const todayStr = `${now.getFullYear()}-${(now.getMonth() + 1).toString().padStart(2, '0')}-${now.getDate().toString().padStart(2, '0')}`;
    if (selectedDate === todayStr) {
        if (hour < now.getHours() || (hour === now.getHours() && min < now.getMinutes())) {
            toastr.error('No puedes seleccionar una hora pasada para hoy', 'Error');
            hour = now.getHours();
            min = now.getMinutes();
            $(this).val(`${hour.toString().padStart(2, '0')}:${min.toString().padStart(2, '0')}`);
        }
    }

    // ---------------- Ajustar hora fin siempre +1 hora ----------------
    let hourFin = hour + 1;
    let minuteFin = min;
    if (hourFin >= 24) {
        hourFin = 23;
        minuteFin = 59;
    }
    $('#hora_fin_audiencia').val(`${hourFin.toString().padStart(2, '0')}:${minuteFin.toString().padStart(2, '0')}`);

    // Guardar mínimos para hora fin
    $('#hora_fin_audiencia').data('minHour', hour);
    $('#hora_fin_audiencia').data('minMinute', min);
});

// ---------------------- Hora fin ----------------------
$('#hora_fin_audiencia').clockpicker({ autoclose: true, donetext: 'OK' }).change(function() {
    const now = new Date();
    const minHour = $(this).data('minHour') ?? now.getHours();
    const minMinute = $(this).data('minMinute') ?? now.getMinutes();

    let [hour, minute] = $(this).val().split(':').map(Number);

    // Validar que hora fin sea siempre mayor que hora inicio
    if (hour < minHour || (hour === minHour && minute <= minMinute)) {
        // Ajustar automáticamente hora inicio +1 hora
        let newHour = minHour + 1;
        let newMinute = minMinute;
        if (newHour >= 24) {
            newHour = 23;
            newMinute = 59;
        }
        toastr.error('La hora de fin debe ser mayor que la hora de inicio.', 'Error');
        $(this).val(`${newHour.toString().padStart(2, '0')}:${newMinute.toString().padStart(2, '0')}`);
    }
});

