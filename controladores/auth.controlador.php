<?php

// Asegúrate de que el servicio de autorización esté disponible
require_once __DIR__ . '/../servicios/ServicioAutorizacion.php';

class ControladorAuth {

    /**
     * @deprecated Este método ahora es redundante. Usar ServicioAutorizacion::getInstance()->puede() directamente.
     * Se mantiene por compatibilidad con código antiguo.
     */
    static public function ctrVerificarPermiso($accion, $sedeId = null) {
        $auth = ServicioAutorizacion::getInstance();
        return $auth->puede($accion);
    }

    /**
     * @deprecated La lógica de "es admin" está centralizada en el servicio.
     * Se mantiene por compatibilidad. Para saber si es admin, la lógica ya está
     * incluida en el método puede() del servicio.
     */
    static public function ctrEsAdministradorSistema() {
        $usuarioId = $_SESSION["id_usuario"] ?? null;
        if (!$usuarioId) return false;

        // Delegamos al modelo, ya que el servicio no expone este método públicamente.
        // La decisión de autorización ya lo considera internamente.
        return ModeloAuth::mdlEsAdministradorSistema($usuarioId);
    }

    /**
     * @deprecated Es un alias de ctrVerificarPermiso. Usar ServicioAutorizacion::getInstance()->puede() directamente.
     */
    static public function ctrTienePermiso($accion, $sedeId = null) {
        return self::ctrVerificarPermiso($accion, $sedeId);
    }

    // =============================================
    // MÉTODOS QUE SE MANTIENEN POR SU UTILIDAD ACTUAL
    // =============================================

    static public function ctrVerificarAccesoModulo($modulo) {
        if (!isset($_SESSION["id_usuario"])) {
            return false;
        }
        $usuarioId = $_SESSION["id_usuario"];

        // Un administrador siempre tiene acceso. La llamada a "puede" lo resolvería,
        // pero para ser explícitos, lo verificamos aquí también.
        if (self::ctrEsAdministradorSistema()) {
            return true;
        }

        return ModeloAuth::mdlVerificarAccesoModulo($usuarioId, $modulo);
    }

    static public function ctrProtegerRutaModal($modulo) {
        if (!self::ctrVerificarAccesoModulo($modulo)) {
            echo '<script>
                swal({
                    type: "error",
                    title: "Acceso Denegado",
                    text: "No tienes los permisos necesarios para acceder a este módulo.",
                    showConfirmButton: true,
                    confirmButtonText: "Entendido"
                }).then(function(result){
                    if (result.value) { window.location = "inicio"; }
                })
            </script>';
            // Detiene la renderización posterior de la página.
            exit();
        }
        return true;
    }

    // El resto de métodos que obtienen DATOS (no que verifican permisos) se mantienen igual.

    static public function ctrObtenerPermisosUsuario($sedeId = null) {
        if (!isset($_SESSION["id_usuario"])) return [];
        return ModeloAuth::mdlObtenerPermisosUsuario($_SESSION["id_usuario"], $sedeId);
    }

    static public function ctrObtenerRolesUsuario() {
        if (!isset($_SESSION["id_usuario"])) return [];
        return ModeloAuth::mdlObtenerRolesUsuario($_SESSION["id_usuario"]);
    }

    static public function ctrTieneCualquierPermiso($acciones, $sedeId = null){
        $auth = ServicioAutorizacion::getInstance();
        foreach($acciones as $accion){
            if($auth->puede($accion)){
                return true;
            }
        }
        return false;
    }

    static public function ctrTieneTodosPermisos($acciones, $sedeId = null){
        $auth = ServicioAutorizacion::getInstance();
        foreach($acciones as $accion){
            if($auth->noPuede($accion)){
                return false;
            }
        }
        return true;
    }
}