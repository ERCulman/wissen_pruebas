/**
 * =================================================================
 * INICIALIZADOR DE LA APLICACIÓN (Inicializador.js)
 * =================================================================
 * Responsabilidad: Orquestar la aplicación. Busca elementos en el DOM
 * y "enciende" los módulos necesarios (validadores, helpers de UI, etc.).
 * Es el punto de entrada principal del comportamiento del frontend.
 */

// Importa los "cerebros" que vamos a utilizar.
import { ControladorFormulario } from './ControladorFormulario.js';
import { configurarCamposDeFecha } from './Utilidades.js';

// Se asegura de que todo el HTML esté cargado antes de intentar manipularlo.
document.addEventListener('DOMContentLoaded', () => {

    // --- SECCIÓN 1: INICIALIZACIÓN DE VALIDACIONES UNIVERSALES ---
    console.log('Buscando formularios para validar...');
    const formulariosParaValidar = document.querySelectorAll('form[data-validacion-universal]');

    formulariosParaValidar.forEach(form => {
        // Crea una instancia del controlador por cada formulario.
        // Esto mantiene el estado y la lógica de cada formulario completamente aislados.
        new ControladorFormulario(form);
        console.log(`Validador universal activado para el formulario: #${form.id}`);
    });

    // --- SECCIÓN 2: INICIALIZACIÓN DE COMPORTAMIENTOS DE UI ESPECIALES ---
    console.log('Configurando campos de fecha y edad...');

    // Configuración para el formulario de CREAR usuario.
    configurarCamposDeFecha({
        idFecha: 'nuevoFechaNacimiento',
        idEdad: 'nuevoEdadUsuario'
    });

    // Configuración para el formulario de EDITAR usuario.
    configurarCamposDeFecha({
        idFecha: 'editarFechaNacimiento',
        idEdad: 'editarEdadUsuario'
    });

    console.log('Inicialización completada.');
});

