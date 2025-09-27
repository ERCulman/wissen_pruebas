<?php

require_once "config/permisos.config.php";

class ControladorSincronizacion {
    
    /*=============================================
    SINCRONIZAR ACCIONES CON MÓDULOS AUTOMÁTICAMENTE
    =============================================*/
    static public function ctrSincronizarAccionesModulos() {
        // Obtener configuración desde el archivo de configuración
        $accionesPlanas = PermisosConfig::getAccionesPlanas();
        
        return ModeloSincronizacion::mdlSincronizarAccionesModulos($accionesPlanas);
    }
    
    /*=============================================
    AGREGAR NUEVO MÓDULO AL SISTEMA
    =============================================*/
    static public function ctrAgregarModulo($nombreModulo, $acciones) {
        if(!is_array($acciones)) {
            return "error_formato_acciones";
        }
        
        return ModeloSincronizacion::mdlAgregarModulo($nombreModulo, $acciones);
    }
    
    /*=============================================
    VERIFICAR INTEGRIDAD DEL SISTEMA DE PERMISOS
    =============================================*/
    static public function ctrVerificarIntegridad() {
        return ModeloSincronizacion::mdlVerificarIntegridad();
    }
    
    /*=============================================
    REPARAR PERMISOS FALTANTES AUTOMÁTICAMENTE
    =============================================*/
    static public function ctrRepararPermisos() {
        // Primero sincronizar
        $resultadoSync = self::ctrSincronizarAccionesModulos();
        
        if($resultadoSync == "ok") {
            // Luego verificar integridad
            $integridad = self::ctrVerificarIntegridad();
            return $integridad;
        }
        
        return $resultadoSync;
    }
}