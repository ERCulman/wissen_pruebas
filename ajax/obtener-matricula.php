<?php
require_once "../controladores/matricula.controlador.php";
require_once "../modelos/matricula.modelo.php";
require_once "../controladores/usuarios.controlador.php";
require_once "../modelos/usuarios.modelo.php";

class AjaxMatricula {

    /*=============================================
    BUSCAR ESTUDIANTE
    =============================================*/
    public $criterio;
    public $valor;

    public function ajaxBuscarEstudiante(){
        $respuesta = ControladorMatricula::ctrBuscarEstudiante($this->criterio, $this->valor);
        echo json_encode($respuesta);
    }

    /*=============================================
    BUSCAR ACUDIENTE
    =============================================*/
    public $documento;

    public function ajaxBuscarAcudiente(){
        $respuesta = ControladorMatricula::ctrBuscarAcudiente($this->documento);
        echo json_encode($respuesta);
    }

    /*=============================================
    OBTENER MATRÍCULA
    =============================================*/
    public $idMatricula;

    public function ajaxObtenerMatricula(){
        $item = "id";
        $valor = $this->idMatricula;
        $respuesta = ControladorMatricula::ctrMostrarMatricula($item, $valor);
        echo json_encode($respuesta);
    }

    /*=============================================
    OBTENER GRADOS POR SEDE
    =============================================*/
    public $sedeId;

    public function ajaxObtenerGradosPorSede(){
        $respuesta = ControladorMatricula::ctrObtenerGradosPorSede($this->sedeId);
        echo json_encode($respuesta);
    }

    /*=============================================
    OBTENER GRUPOS POR GRADO
    =============================================*/
    public $gradoId;

    public function ajaxObtenerGruposPorGrado(){
        $respuesta = ControladorMatricula::ctrObtenerGruposPorGrado($this->gradoId, $this->sedeId);
        echo json_encode($respuesta);
    }

    /*=============================================
    OBTENER GRUPOS (MANTENIDO PARA COMPATIBILIDAD)
    =============================================*/
    public function ajaxObtenerGrupos(){
        $respuesta = ControladorMatricula::ctrObtenerGrupos();
        echo json_encode($respuesta);
    }

    /*=============================================
    REGISTRAR NUEVO ACUDIENTE (NUEVO MÉTODO AJAX)
    =============================================*/
    public function ajaxRegistrarAcudiente(){
        $controlador = new ControladorMatricula();
        $controlador->ajaxCrearAcudiente();
    }
}

/*=============================================
INSTANCIAS DE CLASE (ROUTER DE PETICIONES)
=============================================*/

// BUSCAR ESTUDIANTE POR DOCUMENTO
if(isset($_POST["buscarEstudianteDoc"])){
    $buscarEstudiante = new AjaxMatricula();
    $buscarEstudiante -> criterio = "documento";
    $buscarEstudiante -> valor = $_POST["buscarEstudianteDoc"];
    $buscarEstudiante -> ajaxBuscarEstudiante();
}

// BUSCAR ESTUDIANTE POR NOMBRES
if(isset($_POST["buscarEstudianteNombres"])){
    $buscarEstudiante = new AjaxMatricula();
    $buscarEstudiante -> criterio = "nombres";
    $buscarEstudiante -> valor = $_POST["buscarEstudianteNombres"];
    $buscarEstudiante -> ajaxBuscarEstudiante();
}

// BUSCAR ACUDIENTE
if(isset($_POST["buscarAcudiente"])){
    $buscarAcudiente = new AjaxMatricula();
    $buscarAcudiente -> documento = $_POST["buscarAcudiente"];
    $buscarAcudiente -> ajaxBuscarAcudiente();
}

// OBTENER GRADOS POR SEDE
if(isset($_POST["obtenerGradosPorSede"])){
    $obtenerGrados = new AjaxMatricula();
    $obtenerGrados -> sedeId = $_POST["obtenerGradosPorSede"];
    $obtenerGrados -> ajaxObtenerGradosPorSede();
}

// OBTENER GRUPOS POR GRADO
if(isset($_POST["obtenerGruposPorGrado"]) && isset($_POST["sedeId"])){
    $obtenerGrupos = new AjaxMatricula();
    $obtenerGrupos -> gradoId = $_POST["obtenerGruposPorGrado"];
    $obtenerGrupos -> sedeId = $_POST["sedeId"];
    $obtenerGrupos -> ajaxObtenerGruposPorGrado();
}

// OBTENER MATRÍCULA PARA VER O EDITAR
if(isset($_POST["idMatricula"])){
    $obtenerMatricula = new AjaxMatricula();
    $obtenerMatricula -> idMatricula = $_POST["idMatricula"];
    $obtenerMatricula -> ajaxObtenerMatricula();
}

// OBTENER GRUPOS (COMPATIBILIDAD)
if(isset($_POST["obtenerGrupos"])){
    $obtenerGrupos = new AjaxMatricula();
    $obtenerGrupos -> ajaxObtenerGrupos();
}

// ELIMINAR MATRÍCULA
if(isset($_POST["eliminarMatricula"])){
    header('Content-Type: application/json');
    try {
        $respuesta = ControladorMatricula::ctrBorrarMatriculaAjax($_POST["eliminarMatricula"]);
        echo json_encode($respuesta);
    } catch(Exception $e) {
        echo json_encode(["success" => false, "message" => "Error del servidor: " . $e->getMessage()]);
    }
    exit();
}

// OBTENER SEDE DE UN GRUPO ESPECÍFICO
if(isset($_POST["obtenerSedeDeGrupo"])){
    $grupoId = $_POST["obtenerSedeDeGrupo"];
    try {
        $stmt = Conexion::conectar()->prepare("
            SELECT s.id as sede_id, s.nombre_sede, gr.id as grado_id
            FROM grupo g
            INNER JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id
            INNER JOIN grado gr ON oa.grado_id = gr.id  
            INNER JOIN sede_jornada sj ON oa.sede_jornada_id = sj.id
            INNER JOIN sede s ON sj.sede_id = s.id
            WHERE g.id = :grupo_id
        ");
        $stmt->bindParam(":grupo_id", $grupoId, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($resultado);
    } catch(Exception $e) {
        echo json_encode(array("error" => $e->getMessage()));
    }
}

// REGISTRAR NUEVO ACUDIENTE VIA AJAX (NUEVO HANDLER)
if(isset($_POST["accion"]) && $_POST["accion"] == "registrarAcudienteNuevo"){
    $registro = new AjaxMatricula();
    $registro -> ajaxRegistrarAcudiente();
}

?>