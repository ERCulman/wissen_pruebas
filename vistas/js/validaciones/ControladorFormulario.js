/**
 * =================================================================
 * CONTROLADOR DE FORMULARIO (ControladorFormulario.js)
 * =================================================================
 * Responsabilidad: Ser el puente entre el DOM (HTML) y el Motor de Validación.
 * Se instancia uno por cada formulario, gestionando eventos y actualizando la UI.
 */
import { validar } from './MotorValidacion.js'; // Asume que MotorValidacion.js existe

export class ControladorFormulario {
    constructor(formElement) {
        this.form = formElement;
        this.fields = this.form.querySelectorAll('[data-reglas]');
        this.submitButton = this.form.querySelector('button[type="submit"]');

        this.initialize();
    }

    initialize() {
        // Previene la validación nativa del navegador para tomar control total.
        this.form.setAttribute('novalidate', 'true');

        // Valida el formulario completo antes de permitir el envío.
        this.form.addEventListener('submit', (e) => {
            if (!this.validarFormularioCompleto()) {
                e.preventDefault();
                console.warn('Formulario inválido. Envío cancelado.');
                // Opcional: enfocar el primer campo con error para mejorar la accesibilidad.
                this.form.querySelector('[aria-invalid="true"]')?.focus();
            }
        });

        // Añade validación en tiempo real para una mejor experiencia de usuario.
        this.fields.forEach(field => {
            field.addEventListener('input', () => this.validarCampo(field));
        });

        // Actualiza el estado del botón de envío al cargar.
        this.actualizarEstadoBoton();
    }

    validarCampo(field) {
        const reglas = field.getAttribute('data-reglas');
        const valor = field.value;
        const resultado = validar(valor, reglas);

        this.actualizarUI(field, resultado);
        this.actualizarEstadoBoton();

        return resultado.isValid;
    }

    actualizarUI(field, resultado) {
        const formGroup = field.closest('.form-group');
        if (!formGroup) return;

        // **MEJORA CLAVE**: Busca un contenedor de errores dedicado fuera del form-group.
        // Esto permite un HTML más limpio y cumple la petición de diseño.
        const contenedorError = formGroup.nextElementSibling;

        // Limpia errores previos.
        formGroup.classList.remove('has-error');
        field.setAttribute('aria-invalid', 'false');
        if (contenedorError && contenedorError.classList.contains('validation-error-container')) {
            contenedorError.textContent = '';
        }

        // Si es inválido, muestra el error.
        if (!resultado.isValid) {
            formGroup.classList.add('has-error');
            field.setAttribute('aria-invalid', 'true');
            if (contenedorError && contenedorError.classList.contains('validation-error-container')) {
                contenedorError.textContent = resultado.message;
            }
        }
    }

    actualizarEstadoBoton() {
        if (!this.submitButton) return;
        // Habilita/deshabilita el botón basándose en la validez general del formulario.
        // El `false` previene que se muestren todos los mensajes de error cada vez que se teclea.
        this.submitButton.disabled = !this.validarFormularioCompleto(false);
    }

    validarFormularioCompleto(mostrarErrores = true) {
        // Utiliza `Array.every` para una validación más funcional y concisa.
        return Array.from(this.fields).every(field => {
            const resultado = validar(field.value, field.getAttribute('data-reglas'));
            if (mostrarErrores) {
                this.actualizarUI(field, resultado);
            }
            return resultado.isValid;
        });
    }
}