/**
 * Validaciones del lado del cliente (JavaScript).
 * Mejoran la experiencia del usuario, pero el servidor (PHP / Form Requests)
 * vuelve a validar siempre todos los datos.
 */
const reglas = {
    ci: { regex: /^[0-9A-Za-z-]+$/, mensaje: 'El CI solo puede contener letras, números y guiones.' },
    telefono: { regex: /^[0-9+\s-]{6,20}$/, mensaje: 'Ingrese un teléfono válido (6 a 20 dígitos).' },
};

function mostrarError(campo, mensaje) {
    let aviso = campo.parentElement.querySelector('.error-js');
    if (!mensaje) {
        aviso?.remove();
        campo.classList.remove('border-red-500');
        return;
    }
    if (!aviso) {
        aviso = document.createElement('p');
        aviso.className = 'error-js text-sm text-red-600 mt-1';
        campo.insertAdjacentElement('afterend', aviso);
    }
    aviso.textContent = mensaje;
    campo.classList.add('border-red-500');
}

function validarCampo(campo) {
    const valor = campo.value.trim();
    let mensaje = '';

    if (campo.required && !valor) {
        mensaje = 'Este campo es obligatorio.';
    } else if (valor) {
        const regla = reglas[campo.dataset.tipo];
        if (regla && !regla.regex.test(valor)) mensaje = regla.mensaje;
        if (campo.type === 'email' && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(valor)) mensaje = 'Ingrese un correo válido.';
        if (campo.type === 'date' && campo.max && valor > campo.max) mensaje = 'La fecha no puede ser futura.';
        if (campo.type === 'date' && campo.min && valor < campo.min) mensaje = 'La fecha no puede ser pasada.';
        if (campo.type === 'time' && campo.name === 'hora_fin') {
            const ini = campo.form.querySelector('[name="hora_inicio"]');
            if (ini && ini.value && valor <= ini.value) mensaje = 'La hora final debe ser posterior a la inicial.';
        }
        if (campo.minLength > 0 && valor.length < campo.minLength) mensaje = `Debe tener al menos ${campo.minLength} caracteres.`;
    }
    mostrarError(campo, mensaje);
    return !mensaje;
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[data-validar]').forEach((form) => {
        form.setAttribute('novalidate', '');
        const campos = () => form.querySelectorAll('input:not([type=hidden]):not([type=checkbox]), select, textarea');

        campos().forEach((c) => c.addEventListener('blur', () => validarCampo(c)));

        form.addEventListener('submit', (e) => {
            let ok = true;
            campos().forEach((c) => { if (!validarCampo(c)) ok = false; });

            // Grupos de casillas obligatorias (p. ej. especialidades del médico)
            form.querySelectorAll('[data-grupo-requerido]').forEach((g) => {
                const marcado = g.querySelector('input:checked');
                let aviso = g.parentElement.querySelector('.error-grupo');
                if (!marcado) {
                    ok = false;
                    if (!aviso) {
                        aviso = document.createElement('p');
                        aviso.className = 'error-grupo text-sm text-red-600 mt-1';
                        g.insertAdjacentElement('afterend', aviso);
                    }
                    aviso.textContent = 'Seleccione al menos una opción.';
                } else aviso?.remove();
            });

            if (!ok) {
                e.preventDefault();
                form.querySelector('.border-red-500')?.focus();
            }
        });
    });
});
