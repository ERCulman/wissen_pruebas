<?php

require_once "conexion.php";

class ModeloAcciones{

    /*=============================================
    MOSTRAR ACCIONES
    =============================================*/
    static public function mdlMostrarAcciones($tabla, $item, $valor){
        if($item != null){
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
            $stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
            $stmt -> execute();
            return $stmt -> fetch();
        }else{
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY modulo, nombre_accion");
            $stmt -> execute();
            return $stmt -> fetchAll();
        }
    }

    /*=============================================
    CREAR ACCION
    =============================================*/
    static public function mdlIngresarAccion($tabla, $datos){
        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombre_accion, descripcion, modulo, modulo_asociado, estado) VALUES (:nombre_accion, :descripcion, :modulo, :modulo_asociado, 'Activo')");
        $stmt->bindParam(":nombre_accion", $datos["nombre_accion"], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
        $stmt->bindParam(":modulo", $datos["modulo"], PDO::PARAM_STR);
        $stmt->bindParam(":modulo_asociado", $datos["modulo_asociado"], PDO::PARAM_STR);
        if($stmt->execute()){
            return "ok";
        }else{
            return "error";
        }
    }

    /*=============================================
    EDITAR ACCION
    =============================================*/
    static public function mdlEditarAccion($tabla, $datos){
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre_accion = :nombre_accion, descripcion = :descripcion, modulo = :modulo, modulo_asociado = :modulo_asociado WHERE id = :id");
        $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
        $stmt->bindParam(":nombre_accion", $datos["nombre_accion"], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
        $stmt->bindParam(":modulo", $datos["modulo"], PDO::PARAM_STR);
        $stmt->bindParam(":modulo_asociado", $datos["modulo_asociado"], PDO::PARAM_STR);
        if($stmt->execute()){
            return "ok";
        }else{
            return "error";
        }
    }

    /*=============================================
    ELIMINAR ACCION
    =============================================*/
    static public function mdlBorrarAccion($tabla, $datos){
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");
        $stmt -> bindParam(":id", $datos, PDO::PARAM_INT);
        if($stmt -> execute()){
            return "ok";
        }else{
            return "error";
        }
    }

    /*=============================================
    INSERTAR ACCIONES MASIVAMENTE
    =============================================*/
    static public function mdlInsertarAccionesMasivo($tabla, $acciones){
        $pdo = Conexion::conectar();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO $tabla(nombre_accion, descripcion, modulo) VALUES (:nombre_accion, :descripcion, :modulo)");
            foreach($acciones as $accion){
                $stmt->bindParam(":nombre_accion", $accion["nombre_accion"], PDO::PARAM_STR);
                $stmt->bindParam(":descripcion", $accion["descripcion"], PDO::PARAM_STR);
                $stmt->bindParam(":modulo", $accion["modulo"], PDO::PARAM_STR);
                $stmt->execute();
            }
            $pdo->commit();
            return "ok";
        } catch (Exception $e) {
            $pdo->rollback();
            return "error";
        }
    }
}