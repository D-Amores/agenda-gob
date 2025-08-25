const today = new Date();

// Función para formatear hora HH:mm
function formatTime(date) {
    let hours = date.getHours().toString().padStart(2, '0');
    let minutes = date.getMinutes().toString().padStart(2, '0');
    return `${hours}:${minutes}`;
}

toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: "toast-top-right",
    timeOut: 3000
};

// ---------------------- Configuración inicial ----------------------

// Obtener valores iniciales desde los inputs (edit)
const initialDate = $('#formValidationFecha').val(); // YYYY-MM-DD
const initialHoraInicio = $('#hora_evento').val().split(':').slice(0, 2).join(':'); // HH:mm
const initialHoraFin = $('#hora_fin_evento').val().split(':').slice(0, 2).join(':');   // HH:mm

// Función para formatear hora HH:mm
function formatTime(date) {
    let hours = date.getHours().toString().padStart(2, '0');
    let minutes = date.getMinutes().toString().padStart(2, '0');
    return `${hours}:${minutes}`;
}

// ---------------------- Fecha ----------------------
flatpickr("#formValidationFecha", {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "d-m-Y",
    defaultDate: initialDate,
    minDate: new Date().setHours(0, 0, 0, 0),
    altInputClass: "form-control flatpickr-input"
});

// ---------------------- Hora inicio ----------------------
$('#hora_evento').val(initialHoraInicio || formatTime(today));
$('#hora_fin_evento').val(initialHoraFin || formatTime(new Date(today.getTime() + 60 * 60 * 1000)));

// Guardar hora mínima para validar fin
let minHour = parseInt(initialHoraInicio?.split(':')[0] ?? today.getHours());
let minMinute = parseInt(initialHoraInicio?.split(':')[1] ?? today.getMinutes());
$('#hora_fin_evento').data('minHour', minHour);
$('#hora_fin_evento').data('minMinute', minMinute);

// Hora inicio
$('#hora_evento').clockpicker({
    autoclose: true,
    donetext: 'OK'
}).change(function() {
    const selectedDate = $('#formValidationFecha').val();
    const now = new Date();

    let parts = $(this).val().split(':');
    let hour = parseInt(parts[0]);
    let min = parseInt(parts[1]);

    const todayStr = `${now.getFullYear()}-${(now.getMonth() + 1).toString().padStart(2, '0')}-${now.getDate().toString().padStart(2, '0')}`;
    if (selectedDate === todayStr) {
        // Hoy, no permitir hora pasada
        if (hour < now.getHours() || (hour === now.getHours() && min < now.getMinutes())) {
            toastr.error('No puedes seleccionar una hora pasada para hoy', 'Error');
            hour = now.getHours();
            min = now.getMinutes();
            $(this).val(`${hour.toString().padStart(2, '0')}:${min.toString().padStart(2, '0')}`);
        }
    }

    // Ajustar hora fin si es menor que inicio
    let hourFin = parseInt($('#hora_fin_evento').val().split(':')[0]);
    let minuteFin = parseInt($('#hora_fin_evento').val().split(':')[1]);
    if (hourFin < hour || (hourFin === hour && minuteFin < min)) {
        // Subir hora fin automáticamente +1h
        hourFin = hour + 1;
        if (hourFin >= 24) hourFin = 23;
        $('#hora_fin_evento').val(`${hourFin.toString().padStart(2, '0')}:${min.toString().padStart(2, '0')}`);
    }

    // Guardar minimos
    $('#hora_fin_evento').data('minHour', hour);
    $('#hora_fin_evento').data('minMinute', min);
});

// ---------------------- Hora fin ----------------------
$('#hora_fin_evento').clockpicker({
    autoclose: true,
    donetext: 'OK'
}).change(function() {
    const now = new Date();
    const minHour = $(this).data('minHour') ?? now.getHours();
    const minMinute = $(this).data('minMinute') ?? now.getMinutes();

    const parts = $(this).val().split(':');
    let hour = parseInt(parts[0]);
    let minute = parseInt(parts[1]);

    // Validar hora fin
    if (hour < minHour || (hour === minHour && minute < minMinute)) {
        toastr.error('La hora de fin no puede ser menor que la hora de inicio!', 'Error');
        $(this).val(`${minHour.toString().padStart(2, '0')}:${minMinute.toString().padStart(2, '0')}`);
    }
});

