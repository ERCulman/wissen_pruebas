<?php

class ControladorAcciones{

    /*=============================================
    MOSTRAR ACCIONES
    =============================================*/
    static public function ctrMostrarAcciones($item, $valor){
        // PROTECCIÓN: Verificar permisos antes de mostrar acciones
        if (!BackendProtector::protectController('permisos_ver')) {
            return false;
        }
        
        $tabla = "acciones";
        $respuesta = ModeloAcciones::mdlMostrarAcciones($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    CREAR ACCION
    =============================================*/
    static public function ctrCrearAccion(){
        // PROTECCIÓN: Verificar permisos antes de crear acción
        if (!BackendProtector::protectController('permisos_crear')) {
            return;
        }
        
        if(isset($_POST["nuevoNombreAccion"])){
            if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ_\s]+$/', $_POST["nuevoNombreAccion"]) &&
               preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s]+$/', $_POST["nuevoModulo"])){
                
                $tabla = "acciones";
                $datos = array("nombre_accion" => $_POST["nuevoNombreAccion"],
                              "descripcion" => $_POST["nuevaDescripcion"],
                              "modulo" => $_POST["nuevoModulo"],
                              "modulo_asociado" => isset($_POST["nuevoModuloAsociado"]) ? $_POST["nuevoModuloAsociado"] : null);
                
                $respuesta = ModeloAcciones::mdlIngresarAccion($tabla, $datos);
                
                if($respuesta == "ok"){
                    echo'<script>
                        swal({
                            type: "success",
                            title: "La acción ha sido guardada correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if (result.value) {
                                window.location = "gestionar-acciones";
                            }
                        })
                    </script>';
                }
            }else{
                echo'<script>
                    swal({
                        type: "error",
                        title: "¡La acción no puede ir vacía o llevar caracteres especiales!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if (result.value) {
                            window.location = "gestionar-acciones";
                        }
                    })
                </script>';
            }
        }
    }

    /*=============================================
    EDITAR ACCION
    =============================================*/
    static public function ctrEditarAccion(){
        // PROTECCIÓN: Verificar permisos antes de editar acción
        if (!BackendProtector::protectController('permisos_editar')) {
            return;
        }
        
        if(isset($_POST["editarNombreAccion"])){
            if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ_\s]+$/', $_POST["editarNombreAccion"]) &&
               preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s]+$/', $_POST["editarModulo"])){
                
                $tabla = "acciones";
                $datos = array("id" => $_POST["idAccion"],
                              "nombre_accion" => $_POST["editarNombreAccion"],
                              "descripcion" => $_POST["editarDescripcion"],
                              "modulo" => $_POST["editarModulo"],
                              "modulo_asociado" => isset($_POST["editarModuloAsociado"]) ? $_POST["editarModuloAsociado"] : null);
                
                $respuesta = ModeloAcciones::mdlEditarAccion($tabla, $datos);
                
                if($respuesta == "ok"){
                    echo'<script>
                        swal({
                            type: "success",
                            title: "La acción ha sido editada correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if (result.value) {
                                window.location = "gestionar-acciones";
                            }
                        })
                    </script>';
                }
            }else{
                echo'<script>
                    swal({
                        type: "error",
                        title: "¡La acción no puede ir vacía o llevar caracteres especiales!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if (result.value) {
                            window.location = "gestionar-acciones";
                        }
                    })
                </script>';
            }
        }
    }

    /*=============================================
    BORRAR ACCION
    =============================================*/
    static public function ctrBorrarAccion(){
        // PROTECCIÓN: Verificar permisos antes de eliminar acción
        if (!BackendProtector::protectController('permisos_eliminar')) {
            return;
        }
        
        if(isset($_GET["idAccion"])){
            $tabla ="acciones";
            $datos = $_GET["idAccion"];
            $respuesta = ModeloAcciones::mdlBorrarAccion($tabla, $datos);
            if($respuesta == "ok"){
                echo'<script>
                    swal({
                        type: "success",
                        title: "La acción ha sido borrada correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if (result.value) {
                            window.location = "gestionar-acciones";
                        }
                    })
                </script>';
            }        
        }
    }

    /*=============================================
    INSERTAR ACCIONES PRECARGADAS
    =============================================*/
    static public function ctrInsertarAccionesPrecargadas(){
        // PROTECCIÓN: Verificar permisos antes de insertar acciones
        if (!BackendProtector::protectController('permisos_crear')) {
            return;
        }
        
        if(isset($_POST["insertarAccionesPrecargadas"])){
            $acciones = json_decode($_POST["accionesSeleccionadas"], true);
            
            if(!empty($acciones)){
                $tabla = "acciones";
                $respuesta = ModeloAcciones::mdlInsertarAccionesMasivo($tabla, $acciones);
                
                if($respuesta == "ok"){
                    echo'<script>
                        swal({
                            type: "success",
                            title: "Las acciones han sido insertadas correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if (result.value) {
                                window.location = "gestionar-acciones";
                            }
                        })
                    </script>';
                } else {
                    echo'<script>
                        swal({
                            type: "error",
                            title: "Error al insertar las acciones",
                            text: "Respuesta: ' . $respuesta . '",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        })
                    </script>';
                }
            } else {
                echo'<script>
                    swal({
                        type: "error",
                        title: "No se recibieron acciones para insertar",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    })
                </script>';
            }
        }
    }

    /*=============================================
    OBTENER ACCIONES PRECARGADAS
    =============================================*/
    static public function ctrObtenerAccionesPrecargadas(){
        $accionesPrecargadas = array(
            // USUARIOS
            array("nombre_accion" => "usuarios_crear", "modulo" => "Usuarios", "descripcion" => "Crear nuevos usuarios"),
            array("nombre_accion" => "usuarios_ver", "modulo" => "Usuarios", "descripcion" => "Ver lista de usuarios"),
            array("nombre_accion" => "usuarios_editar", "modulo" => "Usuarios", "descripcion" => "Editar datos de usuarios"),
            array("nombre_accion" => "usuarios_eliminar", "modulo" => "Usuarios", "descripcion" => "Eliminar usuarios"),
            
            // INSTITUCIÓN
            array("nombre_accion" => "institucion_crear", "modulo" => "Institución", "descripcion" => "Crear nueva institución"),
            array("nombre_accion" => "institucion_ver", "modulo" => "Institución", "descripcion" => "Ver datos de institución"),
            array("nombre_accion" => "institucion_editar", "modulo" => "Institución", "descripcion" => "Editar datos de institución"),
            
            // SEDES
            array("nombre_accion" => "sedes_crear", "modulo" => "Sedes", "descripcion" => "Crear nuevas sedes"),
            array("nombre_accion" => "sedes_ver", "modulo" => "Sedes", "descripcion" => "Ver lista de sedes"),
            array("nombre_accion" => "sedes_editar", "modulo" => "Sedes", "descripcion" => "Editar datos de sedes"),
            array("nombre_accion" => "sedes_eliminar", "modulo" => "Sedes", "descripcion" => "Eliminar sedes"),
            
            // JORNADAS
            array("nombre_accion" => "jornadas_crear", "modulo" => "Jornadas", "descripcion" => "Crear nuevas jornadas"),
            array("nombre_accion" => "jornadas_ver", "modulo" => "Jornadas", "descripcion" => "Ver lista de jornadas"),
            array("nombre_accion" => "jornadas_editar", "modulo" => "Jornadas", "descripcion" => "Editar jornadas"),
            array("nombre_accion" => "jornadas_eliminar", "modulo" => "Jornadas", "descripcion" => "Eliminar jornadas"),
            
            // NIVELES EDUCATIVOS
            array("nombre_accion" => "niveles_crear", "modulo" => "Niveles Educativos", "descripcion" => "Crear niveles educativos"),
            array("nombre_accion" => "niveles_ver", "modulo" => "Niveles Educativos", "descripcion" => "Ver niveles educativos"),
            array("nombre_accion" => "niveles_editar", "modulo" => "Niveles Educativos", "descripcion" => "Editar niveles educativos"),
            array("nombre_accion" => "niveles_eliminar", "modulo" => "Niveles Educativos", "descripcion" => "Eliminar niveles educativos"),
            
            // GRADOS
            array("nombre_accion" => "grados_crear", "modulo" => "Grados", "descripcion" => "Crear nuevos grados"),
            array("nombre_accion" => "grados_ver", "modulo" => "Grados", "descripcion" => "Ver lista de grados"),
            array("nombre_accion" => "grados_editar", "modulo" => "Grados", "descripcion" => "Editar grados"),
            array("nombre_accion" => "grados_eliminar", "modulo" => "Grados", "descripcion" => "Eliminar grados"),
            
            // CURSOS
            array("nombre_accion" => "cursos_crear", "modulo" => "Cursos", "descripcion" => "Crear nuevos cursos"),
            array("nombre_accion" => "cursos_ver", "modulo" => "Cursos", "descripcion" => "Ver lista de cursos"),
            array("nombre_accion" => "cursos_editar", "modulo" => "Cursos", "descripcion" => "Editar cursos"),
            array("nombre_accion" => "cursos_eliminar", "modulo" => "Cursos", "descripcion" => "Eliminar cursos"),
            
            // OFERTA EDUCATIVA
            array("nombre_accion" => "oferta_crear", "modulo" => "Oferta Educativa", "descripcion" => "Crear oferta educativa"),
            array("nombre_accion" => "oferta_ver", "modulo" => "Oferta Educativa", "descripcion" => "Ver oferta educativa"),
            array("nombre_accion" => "oferta_editar", "modulo" => "Oferta Educativa", "descripcion" => "Editar oferta educativa"),
            array("nombre_accion" => "oferta_eliminar", "modulo" => "Oferta Educativa", "descripcion" => "Eliminar oferta educativa"),
            
            // MATRÍCULA
            array("nombre_accion" => "matricula_crear", "modulo" => "Matrícula", "descripcion" => "Registrar nueva matrícula"),
            array("nombre_accion" => "matricula_ver", "modulo" => "Matrícula", "descripcion" => "Ver matrículas"),
            array("nombre_accion" => "matricula_editar", "modulo" => "Matrícula", "descripcion" => "Editar matrícula"),
            array("nombre_accion" => "matricula_eliminar", "modulo" => "Matrícula", "descripcion" => "Eliminar matrícula"),
            
            // ESTRUCTURA CURRICULAR
            array("nombre_accion" => "curriculo_crear", "modulo" => "Estructura Curricular", "descripcion" => "Crear estructura curricular"),
            array("nombre_accion" => "curriculo_ver", "modulo" => "Estructura Curricular", "descripcion" => "Ver estructura curricular"),
            array("nombre_accion" => "curriculo_editar", "modulo" => "Estructura Curricular", "descripcion" => "Editar estructura curricular"),
            array("nombre_accion" => "curriculo_eliminar", "modulo" => "Estructura Curricular", "descripcion" => "Eliminar estructura curricular"),
            
            // PERÍODOS
            array("nombre_accion" => "periodos_crear", "modulo" => "Períodos", "descripcion" => "Crear períodos académicos"),
            array("nombre_accion" => "periodos_ver", "modulo" => "Períodos", "descripcion" => "Ver períodos académicos"),
            array("nombre_accion" => "periodos_editar", "modulo" => "Períodos", "descripcion" => "Editar períodos académicos"),
            array("nombre_accion" => "periodos_eliminar", "modulo" => "Períodos", "descripcion" => "Eliminar períodos académicos"),
            
            // ROLES Y PERMISOS
            array("nombre_accion" => "roles_crear", "modulo" => "Roles y Permisos", "descripcion" => "Crear nuevos roles"),
            array("nombre_accion" => "roles_ver", "modulo" => "Roles y Permisos", "descripcion" => "Ver roles del sistema"),
            array("nombre_accion" => "roles_editar", "modulo" => "Roles y Permisos", "descripcion" => "Editar roles"),
            array("nombre_accion" => "permisos_asignar", "modulo" => "Roles y Permisos", "descripcion" => "Asignar permisos a roles"),
            array("nombre_accion" => "permisos_ver", "modulo" => "Roles y Permisos", "descripcion" => "Ver permisos del sistema"),
            
            // REPORTES
            array("nombre_accion" => "reportes_generar", "modulo" => "Reportes", "descripcion" => "Generar reportes"),
            array("nombre_accion" => "reportes_ver", "modulo" => "Reportes", "descripcion" => "Ver reportes generados"),
            array("nombre_accion" => "reportes_exportar", "modulo" => "Reportes", "descripcion" => "Exportar reportes"),
            
            // CONFIGURACIÓN
            array("nombre_accion" => "config_sistema", "modulo" => "Configuración", "descripcion" => "Configurar parámetros del sistema"),
            array("nombre_accion" => "config_backup", "modulo" => "Configuración", "descripcion" => "Realizar respaldos del sistema"),
            array("nombre_accion" => "config_logs", "modulo" => "Configuración", "descripcion" => "Ver logs del sistema")
        );
        
        // Verificar cuáles acciones ya existen en la base de datos
        $accionesExistentes = self::ctrMostrarAcciones(null, null);
        $nombresExistentes = array();
        if($accionesExistentes){
            foreach($accionesExistentes as $accion){
                $nombresExistentes[] = $accion['nombre_accion'];
            }
        }
        
        // Marcar las acciones que ya existen
        foreach($accionesPrecargadas as $key => $accion){
            $accionesPrecargadas[$key]['existe'] = in_array($accion['nombre_accion'], $nombresExistentes);
        }
        
        return $accionesPrecargadas;
    }
}