document.addEventListener('DOMContentLoaded', function () {
    const horaInicioInput = document.getElementById('hora_audiencia');
    const horaFinInput = document.getElementById('hora_fin_audiencia');
    let horaFinModificadaManual = false;

    // Función para obtener hora actual en formato HH:MM
    function getHoraActual() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        return `${hours}:${minutes}`;
    }

    // Función para sumar una hora a un string "HH:MM"
    function sumarUnaHora(horaStr) {
        const [h, m] = horaStr.split(':').map(Number);
        const date = new Date();
        date.setHours(h);
        date.setMinutes(m);
        date.setSeconds(0);
        date.setMilliseconds(0);
        date.setTime(date.getTime() + 60 * 60 * 1000); // sumar una hora
        return `${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;
    }

    // Establecer horas al cargar
    const horaActual = getHoraActual();
    const horaFinCalculada = sumarUnaHora(horaActual);

    horaInicioInput.value = horaActual;
    horaFinInput.value = horaFinCalculada;

    // Cuando el usuario cambia la hora de inicio, actualizar fin solo si no fue modificada manualmente
    horaInicioInput.addEventListener('change', function () {
        if (!horaFinModificadaManual) {
            horaFinInput.value = sumarUnaHora(horaInicioInput.value);
        }
    });

    // Si el usuario modifica manualmente la hora fin, no volver a tocarla
    horaFinInput.addEventListener('input', function () {
        horaFinModificadaManual = true;
    });
});

