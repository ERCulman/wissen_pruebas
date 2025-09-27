<?php

class ControladorAuth{

    /*=============================================
    VERIFICAR PERMISOS DE USUARIO
    =============================================*/
    static public function ctrVerificarPermiso($accion, $sedeId = null){
        if(!isset($_SESSION["id_usuario"])){
            return false;
        }
        
        $usuarioId = $_SESSION["id_usuario"];
        return ModeloAuth::mdlVerificarPermiso($usuarioId, $accion, $sedeId);
    }

    /*=============================================
    VERIFICAR ACCESO A MÓDULO
    =============================================*/
    static public function ctrVerificarAccesoModulo($modulo){
        if(!isset($_SESSION["id_usuario"])){
            return false;
        }
        
        $usuarioId = $_SESSION["id_usuario"];
        
        // Los administradores de sistema tienen acceso total
        if(ModeloAuth::mdlEsAdministradorSistema($usuarioId)){
            return true;
        }
        
        return ModeloAuth::mdlVerificarAccesoModulo($usuarioId, $modulo);
    }

    /*=============================================
    MIDDLEWARE - PROTEGER RUTA
    =============================================*/
    static public function ctrProtegerRuta($modulo){
        if(!self::ctrVerificarAccesoModulo($modulo)){
            // Redirigir a página de acceso denegado
            echo '<script>window.location = "acceso-denegado";</script>';
            exit();
        }
        return true;
    }

    /*=============================================
    PROTEGER RUTA CON MODAL (ALTERNATIVA)
    =============================================*/
    static public function ctrProtegerRutaModal($modulo){
        if(!self::ctrVerificarAccesoModulo($modulo)){
            echo '<script>
                swal({
                    type: "error",
                    title: "Acceso Denegado",
                    text: "No tienes los permisos necesarios para acceder a este módulo. Comunícate con el administrador del sistema para solicitar los permisos requeridos.",
                    showConfirmButton: true,
                    confirmButtonText: "Entendido"
                }).then(function(result){
                    if (result.value) {
                        window.location = "inicio";
                    }
                })
            </script>';
            return false;
        }
        return true;
    }

    /*=============================================
    OBTENER PERMISOS DEL USUARIO ACTUAL
    =============================================*/
    static public function ctrObtenerPermisosUsuario($sedeId = null){
        if(!isset($_SESSION["id_usuario"])){
            return array();
        }
        
        $usuarioId = $_SESSION["id_usuario"];
        return ModeloAuth::mdlObtenerPermisosUsuario($usuarioId, $sedeId);
    }

    /*=============================================
    VERIFICAR SI ES ADMINISTRADOR SISTEMA
    =============================================*/
    static public function ctrEsAdministradorSistema(){
        if(!isset($_SESSION["id_usuario"])){
            return false;
        }
        
        $usuarioId = $_SESSION["id_usuario"];
        return ModeloAuth::mdlEsAdministradorSistema($usuarioId);
    }

    /*=============================================
    HELPER - MOSTRAR/OCULTAR ELEMENTOS EN VISTAS
    =============================================*/
    static public function ctrTienePermiso($accion, $sedeId = null){
        return self::ctrVerificarPermiso($accion, $sedeId);
    }

    /*=============================================
    OBTENER ROLES DEL USUARIO ACTUAL
    =============================================*/
    static public function ctrObtenerRolesUsuario(){
        if(!isset($_SESSION["id_usuario"])){
            return array();
        }
        
        $usuarioId = $_SESSION["id_usuario"];
        return ModeloAuth::mdlObtenerRolesUsuario($usuarioId);
    }

    /*=============================================
    VERIFICAR MÚLTIPLES PERMISOS (OR)
    =============================================*/
    static public function ctrTieneCualquierPermiso($acciones, $sedeId = null){
        foreach($acciones as $accion){
            if(self::ctrVerificarPermiso($accion, $sedeId)){
                return true;
            }
        }
        return false;
    }

    /*=============================================
    VERIFICAR MÚLTIPLES PERMISOS (AND)
    =============================================*/
    static public function ctrTieneTodosPermisos($acciones, $sedeId = null){
        foreach($acciones as $accion){
            if(!self::ctrVerificarPermiso($accion, $sedeId)){
                return false;
            }
        }
        return true;
    }

    /*=============================================
    DEBUG - MOSTRAR INFORMACIÓN DE PERMISOS
    =============================================*/
    static public function ctrDebugPermisos($modulo = null){
        if(!isset($_SESSION["id_usuario"])){
            return "Usuario no logueado";
        }
        
        $usuarioId = $_SESSION["id_usuario"];
        $info = array();
        $info['usuario_id'] = $usuarioId;
        $info['es_admin_sistema'] = ModeloAuth::mdlEsAdministradorSistema($usuarioId);
        $info['roles'] = ModeloAuth::mdlObtenerRolesUsuario($usuarioId);
        $info['permisos'] = ModeloAuth::mdlObtenerPermisosUsuario($usuarioId);
        
        if($modulo){
            $info['acceso_modulo'] = ModeloAuth::mdlVerificarAccesoModulo($usuarioId, $modulo);
        }
        
        return $info;
    }
}