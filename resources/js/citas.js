/**
 * Comportamiento de la interfaz (JavaScript sin dependencias):
 *  - Programar cita: carga los médicos de la especialidad y los horarios libres (RF06).
 *  - Formulario de usuario: muestra el campo médico o paciente según el rol.
 */
document.addEventListener('DOMContentLoaded', () => {
    const raiz = document.getElementById('programar-cita');
    if (raiz) iniciarProgramarCita(raiz);

    const formUsuario = document.getElementById('form-usuario');
    if (formUsuario) iniciarFormUsuario(formUsuario);
});

function iniciarProgramarCita(raiz) {
    const base = raiz.dataset.base;
    const $ = (id) => document.getElementById(id);
    const especialidad = $('especialidad_id');
    const medico = $('medico_id');
    const fecha = $('fecha');
    const horaInicio = $('hora_inicio');
    const horaFin = $('hora_fin');
    const contenedor = $('bloques');
    const guardar = $('btn-guardar');

    const mostrar = (id, ver) => $(id).classList.toggle('hidden', !ver);

    function limpiarHora() {
        horaInicio.value = '';
        horaFin.value = '';
        guardar.disabled = true;
        contenedor.innerHTML = '';
        mostrar('msg-vacio', false);
        mostrar('msg-leyenda', false);
    }

    async function cargarMedicos() {
        medico.innerHTML = '<option value="">Seleccione...</option>';
        limpiarHora();
        if (!especialidad.value) return;
        const r = await fetch(`${base}/medicos/${especialidad.value}`, { headers: { Accept: 'application/json' } });
        (await r.json()).forEach((m) => medico.add(new Option(m.nombre, m.id)));
    }

    let peticion = 0; // evita que una respuesta antigua pise a una más reciente

    async function cargarHoras() {
        const actual = ++peticion;
        limpiarHora();
        const listo = medico.value && fecha.value;
        mostrar('msg-seleccione', !listo);
        if (!listo) return;

        mostrar('msg-cargando', true);
        let bloques = [];
        try {
            const r = await fetch(`${base}/disponibilidad/${medico.value}?fecha=${fecha.value}`, { headers: { Accept: 'application/json' } });
            bloques = r.ok ? await r.json() : [];
        } finally {
            mostrar('msg-cargando', false);
        }
        if (actual !== peticion) return;

        mostrar('msg-vacio', bloques.length === 0);
        mostrar('msg-leyenda', bloques.length > 0);
        bloques.forEach((b) => {
            const boton = document.createElement('button');
            boton.type = 'button';
            boton.textContent = b.inicio;
            boton.disabled = !b.libre;
            boton.className = 'w-24 py-2 text-center rounded-md border text-sm font-semibold ' + (b.libre
                ? 'border-green-600 text-green-700 hover:bg-green-50'
                : 'border-gray-300 text-gray-400 bg-gray-100 line-through cursor-not-allowed');
            boton.addEventListener('click', () => {
                contenedor.querySelectorAll('button').forEach((x) => x.classList.remove('bg-green-600', 'text-white'));
                boton.classList.remove('hover:bg-green-50');
                boton.classList.add('bg-green-600', 'text-white');
                horaInicio.value = b.inicio;
                horaFin.value = b.fin;
                guardar.disabled = false;
            });
            contenedor.appendChild(boton);
        });
    }

    especialidad.addEventListener('change', cargarMedicos);
    medico.addEventListener('change', cargarHoras);
    fecha.addEventListener('change', cargarHoras);
}

function iniciarFormUsuario(raiz) {
    const rol = raiz.querySelector('#rol_id');
    const actualizar = () => {
        const nombre = rol.selectedOptions[0]?.dataset.nombre;
        document.getElementById('campo-medico').classList.toggle('hidden', nombre !== 'Médico');
        document.getElementById('campo-paciente').classList.toggle('hidden', nombre !== 'Paciente');
    };
    rol.addEventListener('change', actualizar);
    actualizar();
}
