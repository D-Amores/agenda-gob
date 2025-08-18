document.addEventListener("DOMContentLoaded", function () {
    const btnEliminar = document.getElementById("btnEliminar");
    const btnEditar = document.getElementById("btnEditar");

    // Botón Eliminar
    if (btnEliminar) {
        btnEliminar.addEventListener("click", function () {
            const id = this.dataset.id;
            const tipo = this.dataset.tipo;

            if (!id || !tipo) {
                alert("Datos faltantes");
                return;
            }

            if (!confirm("¿Estás seguro de que deseas eliminar este elemento?")) return;

            let url = '';
            if (tipo === 'evento') {
                url = urlEvento;
            } else if (tipo === 'audiencia') {
                url = urlAudiencia;
            }

            fetch(url, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ id })
            })
            .then(response => {
                if (!response.ok) throw new Error("Error al eliminar");
                return response.json();
            })
            .then(data => {
                console.log(data.message);
                location.reload();
            })
            .catch(error => {
                alert("Hubo un error al eliminar");
                console.error(error);
            });
        });
    }

    // Botón Editar
    if (btnEditar) {
        btnEditar.addEventListener("click", function () {
            const id = this.dataset.id;
            const tipo = this.dataset.tipo;

            if (!id || !tipo) {
                alert("Datos incompletos para redirigir");
                return;
            }

            let url = '';

            if (tipo === 'evento') {
                //url = urlEventoEditar.replace('__ID__', id);
            } else if (tipo === 'audiencia') {
                url = urlAudienciaEditar.replace('__ID__', id);
            }

            if (url) {
                window.location.href = url;
            } else {
                alert("No se pudo construir la URL de edición.");
            }
        });
    }
});
