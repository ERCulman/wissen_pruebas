<?php

class ControladorRoles{

    /*=============================================
    MOSTRAR ROLES
    =============================================*/
    static public function ctrMostrarRoles($item, $valor){
        // PROTECCIÓN: Verificar permisos antes de mostrar roles
        if (!BackendProtector::protectController('roles_ver')) {
            return false;
        }
        
        $tabla = "roles";
        $respuesta = ModeloRoles::mdlMostrarRoles($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    MOSTRAR PERMISOS DE ROL
    =============================================*/
    static public function ctrMostrarPermisosRol($rolId){
        $respuesta = ModeloRoles::mdlMostrarPermisosRol($rolId);
        return $respuesta;
    }

    /*=============================================
    ACTUALIZAR PERMISOS DE ROL
    =============================================*/
    static public function ctrActualizarPermisosRol(){
        // PROTECCIÓN: Verificar permisos antes de actualizar
        if (!BackendProtector::protectController('roles_editar')) {
            return;
        }
        
        if(isset($_POST["rolId"])){
            $rolId = $_POST["rolId"];
            $acciones = isset($_POST["acciones"]) ? $_POST["acciones"] : array();
            
            $respuesta = ModeloRoles::mdlActualizarPermisosRol($rolId, $acciones);
            
            if($respuesta == "ok"){
                echo'<script>
                    swal({
                        type: "success",
                        title: "Los permisos han sido actualizados correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if (result.value) {
                            window.location = "gestionar-permisos";
                        }
                    })
                </script>';
            } else {
                echo'<script>
                    swal({
                        type: "error",
                        title: "Error al actualizar los permisos",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    })
                </script>';
            }
        }
    }

    /*=============================================
    MOSTRAR TODOS LOS USUARIOS
    =============================================*/
    static public function ctrMostrarTodosUsuarios(){
        $respuesta = ModeloRoles::mdlMostrarTodosUsuarios();
        return $respuesta;
    }

    /*=============================================
    MOSTRAR USUARIOS CON ROLES
    =============================================*/
    static public function ctrMostrarUsuariosConRoles(){
        $respuesta = ModeloRoles::mdlMostrarUsuariosConRoles();
        return $respuesta;
    }

    /*=============================================
    ASIGNAR ROL INSTITUCIONAL
    =============================================*/
    static public function ctrAsignarRolInstitucional(){
        // PROTECCIÓN: Verificar permisos antes de asignar rol
        if (!BackendProtector::protectController('roles_asignar')) {
            return;
        }
        
        if(isset($_POST["usuarioId"])){
            $datos = array(
                "usuario_id" => $_POST["usuarioId"],
                "rol_id" => $_POST["rolId"],
                "sede_id" => $_POST["sedeId"],
                "fecha_inicio" => $_POST["fechaInicio"],
                "fecha_fin" => $_POST["fechaFin"] ?: null,
                "estado" => $_POST["estado"]
            );
            
            $respuesta = ModeloRoles::mdlAsignarRolInstitucional($datos);
            
            if($respuesta == "ok"){
                echo'<script>
                    swal({
                        type: "success",
                        title: "El rol ha sido asignado correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if (result.value) {
                            window.location = "asignar-roles";
                        }
                    })
                </script>';
            }
        }
    }

    /*=============================================
    EDITAR ROL INSTITUCIONAL
    =============================================*/
    static public function ctrEditarRolInstitucional(){
        // PROTECCIÓN: Verificar permisos antes de editar rol
        if (!BackendProtector::protectController('roles_editar')) {
            return;
        }
        
        if(isset($_POST["editarRolId"])){
            $datos = array(
                "id" => $_POST["editarRolId"],
                "rol_id" => $_POST["editarRolSelect"],
                "sede_id" => $_POST["editarSedeSelect"],
                "fecha_inicio" => $_POST["editarFechaInicio"],
                "fecha_fin" => $_POST["editarFechaFin"] ?: null,
                "estado" => $_POST["editarEstado"]
            );
            
            $respuesta = ModeloRoles::mdlEditarRolInstitucional($datos);
            
            if($respuesta == "ok"){
                echo'<script>
                    swal({
                        type: "success",
                        title: "El rol ha sido actualizado correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if (result.value) {
                            window.location = "asignar-roles";
                        }
                    })
                </script>';
            }
        }
    }

    /*=============================================
    ELIMINAR O INACTIVAR ROL INSTITUCIONAL
    =============================================*/
    static public function ctrEliminarRolInstitucional(){
        // PROTECCIÓN: Verificar permisos antes de eliminar rol
        if (!BackendProtector::protectController('roles_eliminar')) {
            return;
        }
        
        if(isset($_GET["idRolInstitucional"])){
            $id = $_GET["idRolInstitucional"];
            $accion = $_GET["accion"] ?? "eliminar";
            
            if($accion == "inactivar"){
                $respuesta = ModeloRoles::mdlInactivarRolInstitucional($id);
                $mensaje = "El rol ha sido inactivado correctamente";
            } else {
                // Verificar si tiene relaciones activas
                $tieneRelaciones = ModeloRoles::mdlVerificarRelacionesActivas($id);
                
                if($tieneRelaciones){
                    echo'<script>
                        swal({
                            type: "error",
                            title: "No se puede eliminar",
                            text: "Este usuario tiene relaciones activas. Solo se puede inactivar.",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if (result.value) {
                                window.location = "asignar-roles";
                            }
                        })
                    </script>';
                    return;
                }
                
                $respuesta = ModeloRoles::mdlEliminarRolInstitucional($id);
                $mensaje = "El rol ha sido eliminado correctamente";
            }
            
            if($respuesta == "ok"){
                echo'<script>
                    swal({
                        type: "success",
                        title: "'.$mensaje.'",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if (result.value) {
                            window.location = "asignar-roles";
                        }
                    })
                </script>';
            }
        }
    }

    /*=============================================
    ASIGNAR ROLES MASIVAMENTE
    =============================================*/
    static public function ctrAsignarRolesMasivo(){
        // PROTECCIÓN: Verificar permisos antes de asignar masivamente
        if (!BackendProtector::protectController('roles_asignar')) {
            return;
        }
        
        if(isset($_POST["usuariosSeleccionados"])){
            $usuarios = json_decode($_POST["usuariosSeleccionados"], true);
            $rolId = $_POST["rolMasivoId"];
            $sedeId = $_POST["sedeMasivaId"];
            
            $respuesta = ModeloRoles::mdlAsignarRolesMasivo($usuarios, $rolId, $sedeId);
            
            if($respuesta == "ok"){
                echo'<script>
                    swal({
                        type: "success",
                        title: "Los roles han sido asignados correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if (result.value) {
                            window.location = "asignar-roles";
                        }
                    })
                </script>';
            }
        }
    }
}