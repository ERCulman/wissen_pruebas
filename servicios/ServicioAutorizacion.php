<?php

require_once __DIR__ . '/../modelos/auth.modelo.php';

class ServicioAutorizacion {
    private $permisos = [];
    private $rolActivoEsAdmin = false;
    private $nombreRolActivo = ''; // <-- 1. NUEVA PROPIEDAD
    private static $instancia;

    private function __construct() {
        if (isset($_SESSION["id_usuario"])) {
            // El modelo ahora debe devolver también el nombre del rol
            $datosPermisos = ModeloAuth::mdlObtenerPermisosDelRolActivo($_SESSION["id_usuario"]);

            $this->permisos = $datosPermisos['permisos'];
            $this->rolActivoEsAdmin = $datosPermisos['esRolAdmin'];
            $this->nombreRolActivo = $datosPermisos['nombre_rol'] ?? ''; // <-- 2. GUARDAMOS EL NOMBRE
        }
    }

    public static function getInstance(): ServicioAutorizacion {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }

    // ... (método puede() y noPuede() se mantienen igual)
    public function puede(string $accion): bool {
        return in_array($accion, $this->permisos);
    }

    public function noPuede(string $accion): bool {
        return !$this->puede($accion);
    }


    /**
     * Informa si el rol activo es de tipo Administrador global (Superadmin).
     */
    public function esRolAdmin(): bool {
        return $this->rolActivoEsAdmin;
    }

    /**
     * 3. NUEVO MÉTODO: Informa si el rol activo tiene alcance sobre toda una institución.
     */
    public function tieneAlcanceInstitucional(): bool {
        return in_array($this->nombreRolActivo, ['Rector', 'Administrativo']);
    }

    public function debugInfo() {
        return [
            'usuario_id' => $_SESSION['id_usuario'] ?? 'No definido',
            'rol_activo' => $_SESSION['rol_activo'] ?? 'No definido',
            'nombreRolActivo' => $this->nombreRolActivo, // <-- Útil para depurar
            'rolActivoEsAdmin' => $this->rolActivoEsAdmin,
            'totalPermisosCargados' => count($this->permisos),
            'listaPermisos' => $this->permisos
        ];
    }
}
