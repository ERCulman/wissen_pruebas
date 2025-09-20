<?php

require_once "conexion.php";

class ModeloEstructuraCurricular {

    /*=============================================
    MOSTRAR ÁREAS
    =============================================*/
    static public function mdlMostrarAreas($tabla, $item, $valor) {
        if($item != null) {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
            $stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY nombre ASC");
            $stmt->execute();
            return $stmt->fetchAll();
        }
        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    REGISTRAR ÁREA
    =============================================*/
    static public function mdlIngresarArea($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombre) VALUES (:nombre)");
        $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
        
        if($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    EDITAR ÁREA
    =============================================*/
    static public function mdlEditarArea($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre = :nombre WHERE id = :id");
        $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
        
        if($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    BORRAR ÁREA
    =============================================*/
    static public function mdlBorrarArea($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");
        $stmt->bindParam(":id", $datos, PDO::PARAM_INT);
        
        if($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    MOSTRAR ASIGNATURAS
    =============================================*/
    static public function mdlMostrarAsignaturas($tabla, $item, $valor) {
        if($item != null) {
            $stmt = Conexion::conectar()->prepare("SELECT a.*, ar.nombre as nombre_area FROM $tabla a 
                                                   LEFT JOIN area ar ON a.area_id = ar.id 
                                                   WHERE a.$item = :$item");
            $stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll();
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT a.*, ar.nombre as nombre_area FROM $tabla a 
                                                   LEFT JOIN area ar ON a.area_id = ar.id 
                                                   ORDER BY a.nombre ASC");
            $stmt->execute();
            return $stmt->fetchAll();
        }
        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    REGISTRAR ASIGNATURA
    =============================================*/
    static public function mdlIngresarAsignatura($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombre, area_id) VALUES (:nombre, :area_id)");
        $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(":area_id", $datos["area_id"], PDO::PARAM_INT);
        
        if($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    EDITAR ASIGNATURA
    =============================================*/
    static public function mdlEditarAsignatura($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre = :nombre, area_id = :area_id WHERE id = :id");
        $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(":area_id", $datos["area_id"], PDO::PARAM_INT);
        $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
        
        if($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    BORRAR ASIGNATURA
    =============================================*/
    static public function mdlBorrarAsignatura($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");
        $stmt->bindParam(":id", $datos, PDO::PARAM_INT);
        
        if($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    GUARDAR ESTRUCTURA CURRICULAR
    =============================================*/
    static public function mdlGuardarEstructuraCurricular($asignaturas, $grados) {
        
        try {
            $conexion = Conexion::conectar();
            $conexion->beginTransaction();
            
            foreach($grados as $gradoId) {
                foreach($asignaturas as $asignatura) {
                    
                    // Verificar si ya existe
                    $stmt = $conexion->prepare("SELECT id FROM estructura_curricular WHERE oferta_academica_id = :oferta_id AND asignatura_id = :asignatura_id");
                    $stmt->bindParam(":oferta_id", $gradoId, PDO::PARAM_INT);
                    $stmt->bindParam(":asignatura_id", $asignatura['id'], PDO::PARAM_INT);
                    $stmt->execute();
                    
                    if($stmt->fetch()) {
                        // Actualizar
                        $stmt = $conexion->prepare("UPDATE estructura_curricular SET intensidad_horaria_semanal = :intensidad WHERE oferta_academica_id = :oferta_id AND asignatura_id = :asignatura_id");
                        $stmt->bindParam(":intensidad", $asignatura['intensidad'], PDO::PARAM_INT);
                        $stmt->bindParam(":oferta_id", $gradoId, PDO::PARAM_INT);
                        $stmt->bindParam(":asignatura_id", $asignatura['id'], PDO::PARAM_INT);
                        $stmt->execute();
                    } else {
                        // Insertar
                        $stmt = $conexion->prepare("INSERT INTO estructura_curricular (intensidad_horaria_semanal, oferta_academica_id, asignatura_id) VALUES (:intensidad, :oferta_id, :asignatura_id)");
                        $stmt->bindParam(":intensidad", $asignatura['intensidad'], PDO::PARAM_INT);
                        $stmt->bindParam(":oferta_id", $gradoId, PDO::PARAM_INT);
                        $stmt->bindParam(":asignatura_id", $asignatura['id'], PDO::PARAM_INT);
                        $stmt->execute();
                    }
                }
            }
            
            $conexion->commit();
            return "ok";
            
        } catch(Exception $e) {
            $conexion->rollback();
            return "error: " . $e->getMessage();
        }
    }

    /*=============================================
    MOSTRAR ESTRUCTURA CURRICULAR
    =============================================*/
    static public function mdlMostrarEstructuraCurricular($idUsuario) {
        $stmt = Conexion::conectar()->prepare("SELECT ec.*, 
                                               g.nombre as nombre_grado, 
                                               ne.nombre as nombre_nivel,
                                               a.nombre as nombre_area,
                                               asig.nombre as nombre_asignatura
                                               FROM estructura_curricular ec
                                               LEFT JOIN oferta_academica oa ON ec.oferta_academica_id = oa.id
                                               LEFT JOIN grado g ON oa.grado_id = g.id
                                               LEFT JOIN nivel_educativo ne ON g.nivel_educativo_id = ne.id
                                               LEFT JOIN asignatura asig ON ec.asignatura_id = asig.id
                                               LEFT JOIN area a ON asig.area_id = a.id
                                               LEFT JOIN sede_jornada sj ON oa.sede_jornada_id = sj.id
                                               LEFT JOIN sede s ON sj.sede_id = s.id
                                               LEFT JOIN institucion i ON s.institucion_id = i.id
                                               WHERE i.id_usuario_representante = :id_usuario
                                               ORDER BY g.numero, a.nombre, asig.nombre");
        $stmt->bindParam(":id_usuario", $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    BORRAR ESTRUCTURA CURRICULAR
    =============================================*/
    static public function mdlBorrarEstructuraCurricular($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");
        $stmt->bindParam(":id", $datos, PDO::PARAM_INT);
        
        if($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    OBTENER CURRÍCULO POR GRADOS
    =============================================*/
    static public function mdlObtenerCurriculoPorGrados($grados) {
        $placeholders = str_repeat('?,', count($grados) - 1) . '?';
        
        $stmt = Conexion::conectar()->prepare("SELECT ec.*, 
                                               g.nombre as nombre_grado, 
                                               ne.nombre as nombre_nivel,
                                               a.nombre as nombre_area,
                                               asig.nombre as nombre_asignatura
                                               FROM estructura_curricular ec
                                               LEFT JOIN oferta_academica oa ON ec.oferta_academica_id = oa.id
                                               LEFT JOIN grado g ON oa.grado_id = g.id
                                               LEFT JOIN nivel_educativo ne ON g.nivel_educativo_id = ne.id
                                               LEFT JOIN asignatura asig ON ec.asignatura_id = asig.id
                                               LEFT JOIN area a ON asig.area_id = a.id
                                               WHERE oa.id IN ($placeholders)
                                               ORDER BY g.numero, a.nombre, asig.nombre");
        $stmt->execute($grados);
        return $stmt->fetchAll();
        $stmt->close();
        $stmt = null;
    }
}

?>