<?php

/**
 * CONFIGURACIÓN DEL SISTEMA DE PERMISOS ESCALABLE
 * 
 * Este archivo contiene la configuración central del sistema de permisos.
 * Permite agregar nuevos módulos y acciones de forma escalable.
 */

class PermisosConfig {
    
    /*=============================================
    CONFIGURACIÓN DE MÓDULOS DEL SISTEMA
    =============================================*/
    public static function getModulosDelSistema() {
        return array(
            'usuarios' => array(
                'nombre_display' => 'Gestión de Usuarios',
                'descripcion' => 'Módulo para administrar usuarios del sistema',
                'acciones' => array(
                    'usuarios_ver' => 'Ver lista de usuarios',
                    'usuarios_crear' => 'Crear nuevos usuarios', 
                    'usuarios_editar' => 'Editar datos de usuarios',
                    'usuarios_eliminar' => 'Eliminar usuarios',
                    'usuarios_cambiar_estado' => 'Activar/Desactivar usuarios'
                )
            ),
            
            'matricula' => array(
                'nombre_display' => 'Gestión de Matrículas',
                'descripcion' => 'Módulo para administrar matrículas de estudiantes',
                'acciones' => array(
                    'matricula_ver' => 'Ver matrículas registradas',
                    'matricula_crear' => 'Registrar nueva matrícula',
                    'matricula_editar' => 'Editar datos de matrícula',
                    'matricula_eliminar' => 'Eliminar matrícula',
                    'matricula_reportes' => 'Generar reportes de matrícula'
                )
            ),
            
            'institucion' => array(
                'nombre_display' => 'Datos Institucionales',
                'descripcion' => 'Módulo para gestionar información de la institución',
                'acciones' => array(
                    'institucion_ver' => 'Ver datos de institución',
                    'institucion_crear' => 'Crear nueva institución',
                    'institucion_editar' => 'Editar datos institucionales'
                )
            ),
            
            'sedes' => array(
                'nombre_display' => 'Gestión de Sedes',
                'descripcion' => 'Módulo para administrar sedes educativas',
                'acciones' => array(
                    'sedes_ver' => 'Ver lista de sedes',
                    'sedes_crear' => 'Crear nuevas sedes',
                    'sedes_editar' => 'Editar datos de sedes',
                    'sedes_eliminar' => 'Eliminar sedes'
                )
            ),
            
            'grados' => array(
                'nombre_display' => 'Gestión de Grados',
                'descripcion' => 'Módulo para administrar grados académicos',
                'acciones' => array(
                    'grados_ver' => 'Ver lista de grados',
                    'grados_crear' => 'Crear nuevos grados',
                    'grados_editar' => 'Editar grados',
                    'grados_eliminar' => 'Eliminar grados'
                )
            ),
            
            'cursos' => array(
                'nombre_display' => 'Gestión de Cursos',
                'descripcion' => 'Módulo para administrar cursos y grupos',
                'acciones' => array(
                    'cursos_ver' => 'Ver lista de cursos',
                    'cursos_crear' => 'Crear nuevos cursos',
                    'cursos_editar' => 'Editar cursos',
                    'cursos_eliminar' => 'Eliminar cursos'
                )
            ),
            
            'estructura-curricular' => array(
                'nombre_display' => 'Estructura Curricular',
                'descripcion' => 'Módulo para gestionar la estructura curricular',
                'acciones' => array(
                    'estructura-curricular_ver' => 'Ver estructura curricular',
                    'estructura-curricular_crear' => 'Crear estructura curricular',
                    'estructura-curricular_editar' => 'Editar estructura curricular',
                    'estructura-curricular_eliminar' => 'Eliminar estructura curricular',
                    'estructura-curricular_area_ver' => 'Ver areas en estructura curricular'
                )
            ),
            
            'niveleducativo' => array(
                'nombre_display' => 'Niveles Educativos',
                'descripcion' => 'Módulo para gestionar niveles educativos',
                'acciones' => array(
                    'niveles_ver' => 'Ver niveles educativos',
                    'niveles_crear' => 'Crear niveles educativos',
                    'niveles_editar' => 'Editar niveles educativos',
                    'niveles_eliminar' => 'Eliminar niveles educativos'
                )
            ),
            
            'jornadas' => array(
                'nombre_display' => 'Gestión de Jornadas',
                'descripcion' => 'Módulo para administrar jornadas académicas',
                'acciones' => array(
                    'jornadas_ver' => 'Ver lista de jornadas',
                    'jornadas_crear' => 'Crear nuevas jornadas',
                    'jornadas_editar' => 'Editar jornadas',
                    'jornadas_eliminar' => 'Eliminar jornadas'
                )
            ),
            
            'oferta' => array(
                'nombre_display' => 'Oferta Educativa',
                'descripcion' => 'Módulo para gestionar la oferta educativa',
                'acciones' => array(
                    'oferta_ver' => 'Ver oferta educativa',
                    'oferta_crear' => 'Crear oferta educativa',
                    'oferta_editar' => 'Editar oferta educativa',
                    'oferta_eliminar' => 'Eliminar oferta educativa'
                )
            ),
            
            'periodos' => array(
                'nombre_display' => 'Períodos Académicos',
                'descripcion' => 'Módulo para gestionar períodos académicos',
                'acciones' => array(
                    'periodos_ver' => 'Ver períodos académicos',
                    'periodos_crear' => 'Crear períodos académicos',
                    'periodos_editar' => 'Editar períodos académicos',
                    'periodos_eliminar' => 'Eliminar períodos académicos'
                )
            ),
            
            'gestionar-acciones' => array(
                'nombre_display' => 'Gestión de Acciones',
                'descripcion' => 'Módulo para administrar acciones del sistema',
                'acciones' => array(
                    'permisos_ver' => 'Ver permisos del sistema',
                    'permisos_crear' => 'Crear nuevos permisos',
                    'permisos_editar' => 'Editar permisos',
                    'permisos_eliminar' => 'Eliminar permisos'
                )
            ),
            
            'gestionar-permisos' => array(
                'nombre_display' => 'Asignación de Permisos',
                'descripcion' => 'Módulo para asignar permisos a roles',
                'acciones' => array(
                    'permisos_asignar' => 'Asignar permisos a roles',
                    'permisos_revocar' => 'Revocar permisos de roles'
                )
            ),
            
            'asignar-roles' => array(
                'nombre_display' => 'Gestión de Roles',
                'descripcion' => 'Módulo para administrar roles y asignaciones',
                'acciones' => array(
                    'roles_ver' => 'Ver roles del sistema',
                    'roles_crear' => 'Crear nuevos roles',
                    'roles_editar' => 'Editar roles',
                    'roles_asignar' => 'Asignar roles a usuarios',
                    'roles_eliminar' => 'Eliminar roles'
                )
            ),
            
            'sincronizar-permisos' => array(
                'nombre_display' => 'Sincronización del Sistema',
                'descripcion' => 'Herramientas de administración y sincronización',
                'acciones' => array(
                    'sistema_sincronizar' => 'Sincronizar sistema de permisos',
                    'sistema_diagnosticar' => 'Diagnosticar problemas del sistema',
                    'sistema_reparar' => 'Reparar sistema automáticamente'
                )
            ),
            
            'asignacion-docente-asignaturas' => array(
                'nombre_display' => 'Asignación Docente Asignaturas',
                'descripcion' => 'Módulo para asignar asignaturas a docentes',
                'acciones' => array(
                    'asignacion_docente_ver' => 'Ver asignaciones de docentes',
                    'asignacion_docente_crear' => 'Crear asignaciones de docentes',
                    'asignacion_docente_editar' => 'Editar horas semanales de docentes',
                    'asignacion_docente_eliminar' => 'Eliminar asignaciones de docentes'
                )
            )
        );
    }
    
    /*=============================================
    OBTENER ACCIONES PLANAS PARA SINCRONIZACIÓN
    =============================================*/
    public static function getAccionesPlanas() {
        $modulos = self::getModulosDelSistema();
        $accionesPlanas = array();
        
        foreach ($modulos as $moduloKey => $moduloData) {
            foreach ($moduloData['acciones'] as $accion => $descripcion) {
                $accionesPlanas[$accion] = array(
                    'nombre_accion' => $accion,
                    'descripcion' => $descripcion,
                    'modulo' => $moduloData['nombre_display'],
                    'modulo_asociado' => $moduloKey
                );
            }
        }
        
        return $accionesPlanas;
    }
    
    /*=============================================
    VERIFICAR SI UN MÓDULO EXISTE
    =============================================*/
    public static function existeModulo($modulo) {
        $modulos = self::getModulosDelSistema();
        return isset($modulos[$modulo]);
    }
    
    /*=============================================
    OBTENER INFORMACIÓN DE UN MÓDULO
    =============================================*/
    public static function getInfoModulo($modulo) {
        $modulos = self::getModulosDelSistema();
        return isset($modulos[$modulo]) ? $modulos[$modulo] : null;
    }
    
    /*=============================================
    AGREGAR NUEVO MÓDULO DINÁMICAMENTE
    =============================================*/
    public static function agregarModulo($moduloKey, $nombreDisplay, $descripcion, $acciones) {
        // Esta función permitiría agregar módulos dinámicamente
        // Por ahora, se debe hacer manualmente editando este archivo
        // En el futuro se podría implementar persistencia en base de datos
        
        return array(
            'modulo_key' => $moduloKey,
            'nombre_display' => $nombreDisplay,
            'descripcion' => $descripcion,
            'acciones' => $acciones
        );
    }
    
    /*=============================================
    CONFIGURACIÓN DE ROLES PREDETERMINADOS
    =============================================*/
    public static function getRolesPredeterminados() {
        return array(
            'Superadministrador' => array(
                'descripcion' => 'Acceso total al sistema',
                'tipo' => 'sistema',
                'acciones' => 'todas' // Indica que tiene todas las acciones
            ),
            'Administrador' => array(
                'descripcion' => 'Administrador del sistema con permisos amplios',
                'tipo' => 'sistema',
                'acciones' => 'todas'
            ),
            'Director' => array(
                'descripcion' => 'Director de institución educativa',
                'tipo' => 'institucional',
                'acciones' => array(
                    'usuarios_ver', 'matricula_ver', 'matricula_crear', 'matricula_editar',
                    'institucion_ver', 'sedes_ver', 'grados_ver', 'cursos_ver',
                    'estructura-curricular_ver', 'niveles_ver', 'jornadas_ver',
                    'oferta_ver', 'periodos_ver'
                )
            ),
            'Coordinador' => array(
                'descripcion' => 'Coordinador académico',
                'tipo' => 'institucional',
                'acciones' => array(
                    'matricula_ver', 'matricula_crear', 'matricula_editar',
                    'grados_ver', 'cursos_ver', 'estructura-curricular_ver'
                )
            ),
            'Secretario' => array(
                'descripcion' => 'Secretario académico',
                'tipo' => 'institucional',
                'acciones' => array(
                    'matricula_ver', 'matricula_crear', 'matricula_editar',
                    'usuarios_ver'
                )
            ),
            'Docente' => array(
                'descripcion' => 'Docente del sistema',
                'tipo' => 'institucional',
                'acciones' => array(
                    'matricula_ver', 'cursos_ver'
                )
            ),
            'Estudiante' => array(
                'descripcion' => 'Estudiante del sistema',
                'tipo' => 'institucional',
                'acciones' => array()
            )
        );
    }
}