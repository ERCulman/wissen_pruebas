<?php

/**
 * =================================================================
 * REGLAS DE VALIDACIÓN PARA USUARIOS (ReglasUsuario.php)
 * =================================================================
 * Responsabilidad Única: Centralizar las reglas de validación específicas para el módulo de Usuarios.
 */
class ReglasUsuario
{
    // Devuelve las reglas de validación para el formulario de creación de un nuevo usuario.
    public static function reglasCreacion(): array
    {
        return [
            'numeroDocumento' => 'requerido|numeros|min:7|max:15',
            'tipoDocumento'   => 'requerido',
            'nombreUsuario'   => 'requerido|texto|min:3|max:20',
            'apellidoUsuario' => 'requerido|texto|min:3|max:20',
            'sexoUsuario'     => 'requerido',
            'rhUsuario'       => 'requerido',
            'fechaNacimiento' => 'requerido|fechaUsuario',
            'edadUsuario'     => 'requerido|minValor:1|maxValor:120',
            'telefonoUsuario' => 'requerido|numeros|min:10|max:15',
            'emailUsuario'    => 'requerido|email',
            'loginUsuario'    => 'requerido|min:5|max:20',
            'password'        => 'requerido|passwordFuerte',
        ];
    }

    //Devuelve las reglas de validación para el formulario de edición de un usuario.
    public static function reglasEdicion(): array
    {
        return [
            // 'editarNumeroDocumento' es readonly, no se valida del lado del servidor.
            'editarTipoDocumento'   => 'requerido',
            'editarNombreUsuario'   => 'requerido|texto|min:3|max:20',
            'editarApellidoUsuario' => 'requerido|texto|min:3|max:20',
            'editarSexoUsuario'     => 'requerido',
            'editarRhUsuario'       => 'requerido',
            'editarFechaNacimiento' => 'requerido|fechaUsuario',
            'editarEdadUsuario'     => 'requerido|minValor:1|maxValor:120',
            'editarTelefonoUsuario' => 'requerido|numeros|min:10|max:15',
            'editarEmailUsuario'    => 'requerido|email',
            // 'editarLoginUsuario' es readonly, no se valida.
            'editarPassword'        => 'passwordFuerte', // No es 'requerido'
            'editarEstadoUsuario'   => 'requerido',
        ];
    }
}
