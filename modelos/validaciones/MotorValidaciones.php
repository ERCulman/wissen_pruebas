<?php

/**
 * =================================================================
 * MOTOR DE VALIDACIÓN (MotorValidaciones.php)
 * =================================================================
 * Responsabilidad Única: Contener la lógica de validación pura y reutilizable.
 */
class MotorValidaciones
{
    private $errores = [];

    //Gestiona la validación de un conjunto de datos contra un conjunto de reglas.
    public function validar(array $datos, array $reglasPorCampo): array
    {
        $this->errores = []; // Limpiar errores previos

        foreach ($reglasPorCampo as $campo => $reglasStr) {
            $valor = $datos[$campo] ?? '';
            $this->aplicarReglasParaCampo($campo, $valor, $reglasStr);
        }

        return $this->errores;
    }

    //Aplica una cadena de reglas a un valor de campo específico.
    private function aplicarReglasParaCampo(string $campo, $valor, string $reglasStr)
    {
        // Si el campo no es requerido y está vacío, no se valida más.
        if (!in_array('requerido', explode('|', $reglasStr)) && $valor === '') {
            return;
        }

        $reglas = explode('|', $reglasStr);

        foreach ($reglas as $regla) {
            $partes = explode(':', $regla, 2);
            $nombreRegla = 'validar' . ucfirst($partes[0]);
            $parametro = $partes[1] ?? null;

            if (method_exists($this, $nombreRegla)) {
                $resultado = $this->$nombreRegla($valor, $parametro);
                if ($resultado !== true) {
                    $this->errores[$campo] = $resultado;
                    break; // Detenerse en el primer error para este campo
                }
            }
        }
    }

    //MÉTODOS DE VALIDACIÓN INDIVIDUALES

    private function validarRequerido($valor)
    {
        return trim($valor) !== '' ? true : 'Este campo es obligatorio.';
    }

    private function validarMin($valor, $limite)
    {
        return mb_strlen($valor) >= (int)$limite ? true : "La longitud mínima es de {$limite} caracteres.";
    }

    private function validarMax($valor, $limite)
    {
        return mb_strlen($valor) <= (int)$limite ? true : "La longitud máxima es de {$limite} caracteres.";
    }

    private function validarMinValor($valor, $limite)
    {
        return is_numeric($valor) && (float)$valor >= (float)$limite ? true : "El valor mínimo permitido es {$limite}.";
    }

    private function validarMaxValor($valor, $limite)
    {
        return is_numeric($valor) && (float)$valor <= (float)$limite ? true : "El valor máximo permitido es {$limite}.";
    }

    private function validarNumeros($valor)
    {
        return ctype_digit($valor) ? true : 'Solo se permiten números.';
    }

    private function validarEmail($valor)
    {
        return filter_var($valor, FILTER_VALIDATE_EMAIL) ? true : 'Formato de email inválido (ej: usuario@dominio.com).';
    }

    private function validarTexto($valor)
    {
        return preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u', $valor) ? true : 'Solo se permiten letras y espacios.';
    }

    private function validarPasswordFuerte($valor)
    {
        $patron = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,50}$/';
        return preg_match($patron, $valor) ? true : 'La contraseña debe tener entre 8-50 caracteres, e incluir mayúscula, minúscula, número y un símbolo (@$!%*?&).';
    }
    
    private function validarFechaUsuario($valor)
    {
        $patron = '/^\d{4}-\d{2}-\d{2}$/';
        if (!preg_match($patron, $valor)) {
            return 'Formato de fecha inválido. El formato debe ser AAAA-MM-DD.';
        }
        $partes = explode('-', $valor);
        // checkdate ( int $month , int $day , int $year )
        return checkdate($partes[1], $partes[2], $partes[0]) ? true : 'La fecha proporcionada no es una fecha válida.';
    }
}
