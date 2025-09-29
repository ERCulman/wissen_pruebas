/**
 * =================================================================
 * MÓDULO DE UTILIDADES (Utilidades.js)
 * =================================================================
 * Responsabilidad: Contener funciones puras, genéricas y reutilizables
 * para tareas comunes como manipulación de fechas.
 * Este módulo está diseñado para ser usado en toda la aplicación.
 */

// Convierte una fecha de formato DB (AAAA-MM-DD) a formato de Vista (DD/MM/AAAA).
export function formatearFecha(fechaDB) {
    // Retorna vacío si la fecha de entrada no es válida.
    if (!fechaDB || fechaDB === '0000-00-00' || fechaDB.trim() === '') {
        return '';
    }

    // Separa la fecha de la hora, si existe.
    const soloFecha = fechaDB.split(' ')[0];
    const partes = soloFecha.split('-');

    // Si el formato no es el esperado, retorna el valor original.
    if (partes.length !== 3 || soloFecha.length !== 10) {
        return fechaDB;
    }

    // Reordena las partes de la fecha para mostrarla al usuario.
    const [anio, mes, dia] = partes;
    return `${dia}/${mes}/${anio}`;
}


// Convierte una fecha en un FormData de formato Vista (DD/MM/AAAA) a formato DB (AAAA-MM-DD).
export function formatearFechaParaDB(formData, nombreCampoFecha) {
    // Obtiene la fecha en formato DD/MM/AAAA del FormData.
    const fechaVista = formData.get(nombreCampoFecha);

    // Procede solo si la fecha existe y parece tener el formato correcto.
    if (fechaVista && fechaVista.includes('/')) {
        const partes = fechaVista.split('/');

        if (partes.length === 3) {
            // Reordena las partes al formato AAAA-MM-DD.
            const fechaParaDB = `${partes[2]}-${partes[1]}-${partes[0]}`;

            // Actualiza el FormData con la fecha ya convertida.
            formData.set(nombreCampoFecha, fechaParaDB);
        }
    }

    // Devuelve el objeto FormData, modificado o no.
    return formData;
}


//Calcula la edad a partir de una fecha de nacimiento en formato "DD/MM/YYYY".
export function calcularEdad(fechaVista) {
    // Se valida el formato estricto DD/MM/YYYY.
    if (!fechaVista || !/^\d{2}\/\d{2}\/\d{4}$/.test(fechaVista)) {
        return null;
    }

    const [dia, mes, anio] = fechaVista.split('/').map(Number);

    // Se valida que los componentes de la fecha sean números válidos.
    if (isNaN(dia) || isNaN(mes) || isNaN(anio) || anio < 1900) {
        return null;
    }

    const fechaNacimiento = new Date(anio, mes - 1, dia); // Meses en JS son 0-11
    const hoy = new Date();

    // Se asegura que la fecha de nacimiento no sea futura y que sea una fecha real
    // (Ej: previene que 31/02/2024 se convierta en 02/03/2024).
    if (fechaNacimiento > hoy || fechaNacimiento.getDate() !== dia) {
        return null;
    }

    let edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
    const diferenciaMeses = hoy.getMonth() - fechaNacimiento.getMonth();

    if (diferenciaMeses < 0 || (diferenciaMeses === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
        edad--;
    }

    return edad;
}


//Aplica formato automático de fecha (DD/MM/YYYY) y cálculo de edad a campos de un formulario.
export function configurarCamposDeFecha({ idFecha, idEdad }) {
    const campoFecha = document.getElementById(idFecha);
    const campoEdad = document.getElementById(idEdad);

    if (!campoFecha || !campoEdad) return;

    campoFecha.addEventListener('input', (e) => {
        // Auto-formateo de la fecha mientras se escribe para mejorar la UX.
        let valor = e.target.value.replace(/\D/g, ''); // Elimina todo lo que no sea dígito.
        if (valor.length > 2) valor = `${valor.slice(0, 2)}/${valor.slice(2)}`;
        if (valor.length > 5) valor = `${valor.slice(0, 5)}/${valor.slice(5, 9)}`;
        e.target.value = valor;

        // Cálculo automático de la edad cuando la fecha está completa.
        const edad = calcularEdad(valor);
        campoEdad.value = (edad !== null && edad >= 0) ? edad : '';

        // Notifica al sistema que el valor del campo de edad ha cambiado (útil para validadores).
        campoEdad.dispatchEvent(new Event('input'));
    });
}

// =================================================================
// ZONA DE COMPATIBILIDAD CON SCRIPTS ANTIGUOS (NO MÓDULOS)
// =================================================================
// Objetivo: Permitir que archivos JS antiguos que no usan 'import'
// puedan acceder a funciones clave.
if (typeof window !== 'undefined') {
    window.formatearFecha = formatearFecha;
    window.calcularEdad = calcularEdad;
}

