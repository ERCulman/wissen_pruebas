/**
 * Módulo PermisosVista
 * Controla la visibilidad de los elementos HTML basados en los permisos del usuario.
 * Utiliza el atributo `data-permiso` en los elementos HTML.
 */
const PermisosVista = {
    /**
     * Almacena los permisos del usuario actual para una búsqueda rápida.
     * Usamos un Set para un rendimiento óptimo (búsquedas en O(1)).
     * @type {Set<string>}
     */
    userPermissions: new Set(),

    /**
     * Inicializa el módulo con los permisos del usuario.
     * @param {string[]} permisos - Un array con los nombres de los permisos del usuario.
     */
    init(permisos = []) {
        // Convertimos el array de permisos en un Set para búsquedas más eficientes.
        this.userPermissions = new Set(permisos);
        // Una vez inicializado, aplicamos las reglas de visibilidad a toda la página.
        this.aplicarPermisos();
    },

    /**
     * Escanea todo el documento en busca de elementos con `data-permiso`
     * y los oculta si el usuario no tiene el permiso necesario.
     */
    aplicarPermisos() {
        // Seleccionamos todos los elementos que tengan el atributo 'data-permiso'.
        const elementosConPermiso = document.querySelectorAll('[data-permiso]');

        elementosConPermiso.forEach(elemento => {
            const permisoRequerido = elemento.dataset.permiso;

            // Si el elemento requiere un permiso y el usuario NO lo tiene, lo ocultamos.
            if (permisoRequerido && !this.userPermissions.has(permisoRequerido)) {
                elemento.style.display = 'none';
            }
        });
    },

    /**
     * Función de ayuda para verificar un solo permiso (opcional, pero útil).
     * @param {string} permiso - El nombre del permiso a verificar.
     * @returns {boolean} - True si el usuario tiene el permiso, false en caso contrario.
     */
    puede(permiso) {
        return this.userPermissions.has(permiso);
    }
};

export default PermisosVista;