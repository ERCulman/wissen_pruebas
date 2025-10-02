<?php

require_once __DIR__ . '/../modelos/auth.modelo.php'; // Asegúrate que la ruta al modelo sea correcta

class ServicioAutorizacion {
    private $permisos = [];
    private $rolActivoEsAdmin = false;
    private static $instancia;

    // El constructor ahora carga los permisos del usuario activo desde la BD
    private function __construct() {
        if (isset($_SESSION["id_usuario"])) {
            $datosPermisos = ModeloAuth::mdlObtenerPermisosDelRolActivo($_SESSION["id_usuario"]);

            $this->permisos = $datosPermisos['permisos'];
            $this->rolActivoEsAdmin = $datosPermisos['esRolAdmin'];
        }
    }

    public static function getInstance(): ServicioAutorizacion {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }

    /**
     * Verifica si el usuario activo tiene un permiso específico.
     * Un rol de administrador siempre tendrá permiso.
     */
    public function puede(string $accion): bool {
        // Si el rol activo es Superadministrador o Administrador, siempre puede.
        if ($this->rolActivoEsAdmin) {
            return true;
        }

        // Si no es admin, verifica si la acción está en su lista de permisos.
        return in_array($accion, $this->permisos);
    }

    public function noPuede(string $accion): bool {
        return !$this->puede($accion);
    }

    public function debugInfo() {
        return [
            'usuario_id' => $_SESSION['id_usuario'] ?? 'No definido',
            'rol_activo' => $_SESSION['rol_activo'] ?? 'No definido',
            'rolActivoEsAdmin' => $this->rolActivoEsAdmin,
            'totalPermisosCargados' => count($this->permisos),
            'listaPermisos' => $this->permisos
        ];
    }
}
