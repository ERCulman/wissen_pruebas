/**
 * =================================================================
 * MOTOR DE VALIDACIÓN (MotorValidacion.js)
 * =================================================================
 * Responsabilidad Única: Contener la lógica de validación pura.
 * Recibe un valor y unas reglas, y devuelve si es válido o no.
 */

// Define la biblioteca de todas las funciones de validación disponibles.
const REGLAS = {
    // Valida que el campo no esté vacío.
    requerido: (valor) => ({
        isValid: valor.trim() !== '',
        message: 'Este campo es obligatorio'
    }),
    // Valida la longitud mínima de una cadena de texto.
    min: (valor, limite) => ({
        isValid: valor.length >= Number(limite),
        message: `La longitud mínima es de ${limite} caracteres`
    }),
    // Valida la longitud máxima de una cadena de texto.
    max: (valor, limite) => ({
        isValid: valor.length <= Number(limite),
        message: `La longitud máxima es de ${limite} caracteres`
    }),
    // Valida que el valor numérico sea como mínimo el límite especificado.
    minValor: (valor, limite) => ({
        isValid: !isNaN(valor) && Number(valor) >= Number(limite),
        message: `El valor mínimo permitido es ${limite}`
    }),
    // Valida que el valor numérico sea como máximo el límite especificado.
    maxValor: (valor, limite) => ({
        isValid: !isNaN(valor) && Number(valor) <= Number(limite),
        message: `El valor máximo permitido es ${limite}`
    }),
    // Valida una cantidad exacta de dígitos numéricos.
    digitos: (valor, limite) => ({
        isValid: /^\d+$/.test(valor) && valor.length === Number(limite),
        message: `Debe contener exactamente ${limite} dígitos`
    }),
    // Valida la cantidad mínima de dígitos numéricos.
    minDigitos: (valor, limite) => ({
        isValid: /^\d+$/.test(valor) && valor.length >= Number(limite),
        message: `Debe contener como mínimo ${limite} dígitos`
    }),
    // Valida la cantidad máxima de dígitos numéricos.
    maxDigitos: (valor, limite) => ({
        isValid: /^\d+$/.test(valor) && valor.length <= Number(limite),
        message: `Debe contener como máximo ${limite} dígitos`
    }),
    // Valida que el texto siga un formato de correo electrónico.
    email: (valor) => ({
        isValid: /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor),
        message: 'Formato de email inválido (ej: usuario@dominio.com)'
    }),
    // Valida que el valor contenga únicamente dígitos numéricos.
    numeros: (valor) => ({
        isValid: /^\d+$/.test(valor),
        message: 'Solo se permiten números'
    }),
    // Valida que el valor sea solo texto (ideal para nombres, apellidos).
    texto: (valor) => ({
        isValid: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(valor),
        message: 'Solo se permiten letras y espacios'
    }),
    //Valida que la contraseña tenga minimo 8 caracteres ( Mínimo: 1 mayuscula, 1 minuscula, 1 numero, 1 caracter especial)
    passwordFuerte: (valor) => ({
        isValid: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,50}$/.test(valor),
        message: 'Debe tener entre 8-50 caracteres, e incluir al menos una mayúscula, minúscula, número y un símbolo (@$!%*?&)'
    }),
    // Valida texto general (párrafos), permitiendo letras, números, puntuación y arroba.
    textoGeneral: (valor) => ({
        isValid: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\d\s.,;:¡!¿?()\-'"@]+$/.test(valor),
        message: 'Se han detectado caracteres no permitidos'
    }),
    // Valida el valor contra una expresión regular personalizada.
    patron: (valor, expresion) => ({
        isValid: new RegExp(expresion).test(valor),
        message: 'El formato no es válido'
    }),
    // Valida formato de fecha DD/MM/AAAA
    fechaUsuario: (valor) => ({
        isValid: /^\d{2}\/\d{2}\/\d{4}$/.test(valor),
        message: 'Formato de fecha inválido. Use DD/MM/AAAA'
    }),
};

/**
 * Orquesta la validación de un valor contra un conjunto de reglas.
 * @param {string} valor - El valor del campo a validar.
 * @param {string} reglasStr - Cadena con reglas separadas por '|' (ej: "requerido|min:4|email").
 * @returns {Object} - Resultado de la validación con estado y mensaje.
 */
function validar(valor, reglasStr) {
    if (!reglasStr) return { isValid: true, message: '' };

    // Si no es requerido y está vacío, es válido automáticamente.
    if (!reglasStr.includes('requerido') && valor.trim() === '') {
        return { isValid: true, message: '' };
    }

    const reglas = reglasStr.split('|');

    for (const regla of reglas) {
        // Separa la regla de su parámetro, permitiendo ':' en el parámetro (para regex).
        const primeraSeparacion = regla.indexOf(':');
        const nombreRegla = primeraSeparacion === -1 ? regla : regla.substring(0, primeraSeparacion);
        const parametro = primeraSeparacion === -1 ? null : regla.substring(primeraSeparacion + 1);

        const funcionRegla = REGLAS[nombreRegla];

        if (typeof funcionRegla === 'function') {
            const resultado = funcionRegla(valor, parametro);
            if (!resultado.isValid) {
                // Si una regla falla, retornamos su resultado inmediatamente.
                return resultado;
            }
        }
    }

    // Si todas las reglas pasan, el campo se considera válido.
    return { isValid: true, message: '' };
}

// Exporta la función 'validar' para que otros módulos puedan importarla y usarla.
export { validar };

