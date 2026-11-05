//Validación en el navegador, contraseñas y confirmaciones del módulo de usuarios y del login
(function () {
    'use strict';

    const reglasClave = {
        longitud: function (valor) { return /^\S{8,64}$/u.test(valor); },
        mayuscula: function (valor) { return /\p{Lu}/u.test(valor); },
        minuscula: function (valor) { return /\p{Ll}/u.test(valor); },
        numero: function (valor) { return /\d/.test(valor); },
        simbolo: function (valor) { return /[^\p{L}\d\s]/u.test(valor); }
    };

    function claveSegura(valor) {
        return Object.keys(reglasClave).every(function (regla) {
            return reglasClave[regla](valor);
        });
    }

    function mensajeDe(campo) {
        const estado = campo.validity;

        if (estado.valid) return '';
        if (estado.valueMissing) return campo.dataset.msgRequerido || 'Este campo es obligatorio.';
        if (estado.typeMismatch || estado.patternMismatch) return campo.dataset.msgFormato || 'El formato no es válido.';
        if (estado.tooShort) return 'Debe tener al menos ' + campo.minLength + ' caracteres.';
        if (estado.tooLong) return 'No debe superar ' + campo.maxLength + ' caracteres.';

        return campo.validationMessage;
    }

    function contenedorMensaje(campo) {
        let destino = document.getElementById(campo.id + '-error');

        if (!destino) {
            destino = document.createElement('div');
            destino.id = campo.id + '-error';
            destino.className = 'invalid-feedback';
            (campo.closest('.input-group') || campo.parentElement).appendChild(destino);
        }

        return destino;
    }

    function actualizarRequisitos(campo) {
        const lista = document.querySelector('[data-requisitos-de="' + campo.id + '"]');

        if (!lista) return;

        lista.querySelectorAll('[data-regla]').forEach(function (requisito) {
            const cumple = reglasClave[requisito.dataset.regla](campo.value);
            const icono = requisito.querySelector('.bi');

            requisito.classList.toggle('cumple', cumple);
            if (icono) icono.className = 'bi ' + (cumple ? 'bi-check-circle-fill' : 'bi-circle');
        });
    }

    function validarCampo(campo) {
        if (campo.hasAttribute('data-clave-segura')) {
            const valida = campo.value === '' || claveSegura(campo.value);
            campo.setCustomValidity(valida ? '' : 'La contraseña no cumple todos los requisitos.');
        }

        if (campo.dataset.igualA) {
            const original = document.getElementById(campo.dataset.igualA);
            const coincide = !original || campo.value === original.value;
            campo.setCustomValidity(coincide ? '' : 'La confirmación no coincide con la contraseña.');
        }

        const mensaje = mensajeDe(campo);

        campo.classList.toggle('is-invalid', mensaje !== '');
        campo.setAttribute('aria-invalid', mensaje !== '' ? 'true' : 'false');
        contenedorMensaje(campo).textContent = mensaje;

        return mensaje === '';
    }

    function prepararFormulario(formulario) {
        const campos = Array.from(formulario.querySelectorAll('input:not([type=hidden]), select, textarea'));

        campos.forEach(function (campo) {
            campo.addEventListener('blur', function () {
                if (campo.value !== '' || campo.classList.contains('is-invalid')) validarCampo(campo);
            });

            campo.addEventListener('input', function () {
                if (campo.hasAttribute('data-clave-segura')) actualizarRequisitos(campo);
                if (campo.classList.contains('is-invalid')) validarCampo(campo);

                //La confirmación depende de lo que se escriba en la contraseña principal
                formulario.querySelectorAll('[data-igual-a="' + campo.id + '"]').forEach(function (confirmacion) {
                    if (confirmacion.value !== '') validarCampo(confirmacion);
                });
            });

            if (campo.hasAttribute('data-clave-segura')) actualizarRequisitos(campo);
        });

        formulario.addEventListener('submit', function (evento) {
            const invalidos = campos.filter(function (campo) {
                return !campo.disabled && !validarCampo(campo);
            });

            if (invalidos.length > 0) {
                evento.preventDefault();
                invalidos[0].focus();
                return;
            }

            const boton = formulario.querySelector('button[type=submit][data-texto-envio]');

            if (boton) {
                boton.dataset.textoOriginal = boton.innerHTML;
                boton.disabled = true;
                boton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>' + boton.dataset.textoEnvio;
            }
        });
    }

    document.querySelectorAll('form[data-validar]').forEach(prepararFormulario);

    document.querySelectorAll('[data-mostrar-clave]').forEach(function (boton) {
        boton.addEventListener('click', function () {
            const campo = document.getElementById(boton.dataset.mostrarClave);
            const mostrar = campo.type === 'password';

            campo.type = mostrar ? 'text' : 'password';
            boton.setAttribute('aria-pressed', mostrar ? 'true' : 'false');
            boton.setAttribute('aria-label', mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña');
            boton.querySelector('.bi').className = mostrar ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });

    document.querySelectorAll('[data-aviso-mayus]').forEach(function (campo) {
        const aviso = document.getElementById(campo.dataset.avisoMayus);
        const revisar = function (evento) {
            if (aviso && evento.getModifierState) aviso.classList.toggle('d-none', !evento.getModifierState('CapsLock'));
        };

        campo.addEventListener('keydown', revisar);
        campo.addEventListener('keyup', revisar);
        campo.addEventListener('blur', function () {
            if (aviso) aviso.classList.add('d-none');
        });
    });

    //Muestra un grupo de campos solo cuando un selector tiene cierto valor, por ejemplo rol=ODONTOLOGO
    document.querySelectorAll('[data-muestra-si]').forEach(function (grupo) {
        const partes = grupo.dataset.muestraSi.split('=');
        const selector = document.getElementById(partes[0]);

        if (!selector) return;

        const actualizar = function () {
            const visible = selector.value === partes[1];

            grupo.hidden = !visible;
            grupo.querySelectorAll('input, select, textarea').forEach(function (campo) {
                campo.disabled = !visible;
            });
        };

        selector.addEventListener('change', actualizar);
        actualizar();
    });

    document.querySelectorAll('form[data-confirmar]').forEach(function (formulario) {
        formulario.addEventListener('submit', function (evento) {
            if (!window.confirm(formulario.dataset.confirmar)) evento.preventDefault();
        });
    });

    document.querySelectorAll('select[data-autoenviar]').forEach(function (selector) {
        selector.addEventListener('change', function () {
            if (selector.form.requestSubmit) selector.form.requestSubmit();
            else selector.form.submit();
        });
    });

    //Al volver con el botón Atrás, los botones de envío deben quedar habilitados
    window.addEventListener('pageshow', function () {
        document.querySelectorAll('button[type=submit][data-texto-original]').forEach(function (boton) {
            boton.disabled = false;
            boton.innerHTML = boton.dataset.textoOriginal;
        });
    });
})();
