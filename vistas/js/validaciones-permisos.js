/**
 * SISTEMA DE VALIDACIONES DE PERMISOS - LADO CLIENTE
 * 
 * Este archivo proporciona funciones JavaScript para validar permisos
 * desde el lado del cliente antes de realizar operaciones.
 */

class ValidadorPermisos {
    
    constructor() {
        this.cache = new Map();
        this.cacheExpiry = 5 * 60 * 1000; // 5 minutos
    }
    
    /**
     * Validar un permiso específico
     */
    async validarPermiso(permiso, sedeId = null) {
        const cacheKey = `${permiso}_${sedeId || 'null'}`;
        
        // Verificar cache
        if (this.cache.has(cacheKey)) {
            const cached = this.cache.get(cacheKey);
            if (Date.now() - cached.timestamp < this.cacheExpiry) {
                return cached.data;
            }
        }
        
        try {
            const response = await $.ajax({
                url: 'ajax/validar-permisos.ajax.php',
                method: 'POST',
                data: {
                    accion: 'validarPermiso',
                    permiso: permiso,
                    sedeId: sedeId
                },
                dataType: 'json'
            });
            
            // Guardar en cache
            this.cache.set(cacheKey, {
                data: response,
                timestamp: Date.now()
            });
            
            return response;
            
        } catch (error) {
            console.error('Error validando permiso:', error);
            return { tienePermiso: false, esAdmin: false };
        }
    }
    
    /**
     * Validar acceso a un módulo
     */
    async validarModulo(modulo) {
        const cacheKey = `modulo_${modulo}`;
        
        // Verificar cache
        if (this.cache.has(cacheKey)) {
            const cached = this.cache.get(cacheKey);
            if (Date.now() - cached.timestamp < this.cacheExpiry) {
                return cached.data;
            }
        }
        
        try {
            const response = await $.ajax({
                url: 'ajax/validar-permisos.ajax.php',
                method: 'POST',
                data: {
                    accion: 'validarModulo',
                    modulo: modulo
                },
                dataType: 'json'
            });
            
            // Guardar en cache
            this.cache.set(cacheKey, {
                data: response,
                timestamp: Date.now()
            });
            
            return response;
            
        } catch (error) {
            console.error('Error validando módulo:', error);
            return { puedeAcceder: false, esAdmin: false };
        }
    }
    
    /**
     * Obtener todos los permisos del usuario actual
     */
    async obtenerPermisos(sedeId = null) {
        try {
            const response = await $.ajax({
                url: 'ajax/validar-permisos.ajax.php',
                method: 'POST',
                data: {
                    accion: 'obtenerPermisos',
                    sedeId: sedeId
                },
                dataType: 'json'
            });
            
            return response;
            
        } catch (error) {
            console.error('Error obteniendo permisos:', error);
            return { permisos: [], roles: [], esAdmin: false };
        }
    }
    
    /**
     * Validar múltiples permisos de una vez
     */
    async validarMultiples(permisos, sedeId = null) {
        try {
            const response = await $.ajax({
                url: 'ajax/validar-permisos.ajax.php',
                method: 'POST',
                data: {
                    accion: 'validarMultiples',
                    permisos: JSON.stringify(permisos),
                    sedeId: sedeId
                },
                dataType: 'json'
            });
            
            return response;
            
        } catch (error) {
            console.error('Error validando múltiples permisos:', error);
            return { resultados: {}, esAdmin: false };
        }
    }
    
    /**
     * Mostrar/ocultar elementos basado en permisos
     */
    async mostrarSiTienePermiso(selector, permiso, sedeId = null) {
        const resultado = await this.validarPermiso(permiso, sedeId);
        
        if (resultado.tienePermiso || resultado.esAdmin) {
            $(selector).show();
        } else {
            $(selector).hide();
        }
        
        return resultado.tienePermiso || resultado.esAdmin;
    }
    
    /**
     * Habilitar/deshabilitar elementos basado en permisos
     */
    async habilitarSiTienePermiso(selector, permiso, sedeId = null) {
        const resultado = await this.validarPermiso(permiso, sedeId);
        
        if (resultado.tienePermiso || resultado.esAdmin) {
            $(selector).prop('disabled', false);
        } else {
            $(selector).prop('disabled', true);
        }
        
        return resultado.tienePermiso || resultado.esAdmin;
    }
    
    /**
     * Ejecutar función solo si tiene permiso
     */
    async ejecutarSiTienePermiso(permiso, callback, sedeId = null) {
        const resultado = await this.validarPermiso(permiso, sedeId);
        
        if (resultado.tienePermiso || resultado.esAdmin) {
            if (typeof callback === 'function') {
                callback();
            }
            return true;
        } else {
            this.mostrarErrorPermiso(permiso);
            return false;
        }
    }
    
    /**
     * Mostrar error de permisos
     */
    mostrarErrorPermiso(permiso) {
        swal({
            type: 'error',
            title: 'Acceso Denegado',
            text: `No tienes permisos para realizar esta acción (${permiso}). Contacta al administrador del sistema.`,
            confirmButtonText: 'Entendido'
        });
    }
    
    /**
     * Limpiar cache de permisos
     */
    limpiarCache() {
        this.cache.clear();
    }
    
    /**
     * Configurar validaciones automáticas en elementos
     */
    configurarValidacionesAutomaticas() {
        // Botones con atributo data-permiso
        $('[data-permiso]').each(async (index, element) => {
            const $element = $(element);
            const permiso = $element.data('permiso');
            const sedeId = $element.data('sede-id') || null;
            
            const resultado = await this.validarPermiso(permiso, sedeId);
            
            if (!(resultado.tienePermiso || resultado.esAdmin)) {
                $element.prop('disabled', true);
                $element.addClass('sin-permisos');
                $element.attr('title', `Requiere permiso: ${permiso}`);
            }
        });
        
        // Enlaces con atributo data-modulo
        $('[data-modulo]').each(async (index, element) => {
            const $element = $(element);
            const modulo = $element.data('modulo');
            
            const resultado = await this.validarModulo(modulo);
            
            if (!(resultado.puedeAcceder || resultado.esAdmin)) {
                $element.addClass('sin-acceso');
                $element.click((e) => {
                    e.preventDefault();
                    this.mostrarErrorPermiso(`acceso a ${modulo}`);
                });
            }
        });
    }
}

// Instancia global del validador
const validadorPermisos = new ValidadorPermisos();

// Configurar validaciones automáticas cuando el documento esté listo
// $(document).ready(() => {
//     validadorPermisos.configurarValidacionesAutomaticas();
// });

// Funciones de conveniencia globales
window.validarPermiso = (permiso, sedeId) => validadorPermisos.validarPermiso(permiso, sedeId);
window.validarModulo = (modulo) => validadorPermisos.validarModulo(modulo);
window.ejecutarSiTienePermiso = (permiso, callback, sedeId) => validadorPermisos.ejecutarSiTienePermiso(permiso, callback, sedeId);