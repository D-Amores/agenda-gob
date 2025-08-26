const today = new Date();

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
    defaultDate: today,
    minDate: today,
    altInputClass: "form-control flatpickr-input"
});

toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: "toast-top-right",
    timeOut: 3000
};

// ---------------------- Hora inicio ----------------------
$('#hora_evento').val(formatTime(today)); // <--- aquí seteamos hora actual
$('#hora_fin_evento').val(formatTime(new Date(today.getTime() + 60 * 60 * 1000))); // hora fin +1h
// Inicializamos restricciones mínimas para hora_fin_evento
$('#hora_fin_evento').data('minHour', today.getHours());
$('#hora_fin_evento').data('minMinute', today.getMinutes());

$('#hora_evento').clockpicker({
    autoclose: true,
    donetext: 'OK'
}).change(function() {
    const selectedDate = $('#formValidationFecha').val(); // fecha elegida
    const now = new Date(); // hora actual

    let parts = $(this).val().split(':');
    let hour = parseInt(parts[0]);
    let min = parseInt(parts[1]);

    // Si seleccionó hoy, no permitir hora pasada
    const todayStr = `${now.getFullYear()}-${(now.getMonth() + 1).toString().padStart(2, '0')}-${now.getDate().toString().padStart(2, '0')}`;
    if (selectedDate === todayStr) {
        if (hour < now.getHours() || (hour === now.getHours() && min < now.getMinutes())) {
            // Notificación más bonita en lugar de alert
            toastr.error('No puedes seleccionar una hora pasada para hoy', 'Error');

            // Corrige la hora al valor actual
            hour = now.getHours();
            min = now.getMinutes();
            $(this).val(`${hour.toString().padStart(2, '0')}:${min.toString().padStart(2, '0')}`);
        }
    }
    // Ajustar hora fin automáticamente +1h
    let hourFin = hour + 1;
    if (hourFin >= 24) hourFin = 23;
    $('#hora_fin_evento').val(`${hourFin.toString().padStart(2, '0')}:${min.toString().padStart(2, '0')}`);

    // Guardar hora de inicio para validación de fin
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

    // Validar que hora de fin no sea menor que inicio
    if (hour < minHour || (hour === minHour && minute < minMinute)) {
        // Reemplazamos alert por Toastr
        toastr.error('La hora de fin no puede ser menor que la hora de inicio!', 'Error');

        // Ajustamos el valor al mínimo permitido
        $(this).val(`${minHour.toString().padStart(2, '0')}:${minMinute.toString().padStart(2, '0')}`);
    }
});
