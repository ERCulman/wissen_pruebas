<!-- MENU LATERAL -->

<aside class="main-sidebar">
	
	<section class="sidebar">

		<ul class="sidebar-menu">

			<!-- SELECTOR DE ROLES -->
			<?php
			$rolesUsuario = ControladorAuth::ctrObtenerRolesUsuario();
			
			// Establecer rol activo automáticamente si no existe
			if(!isset($_SESSION['rol_activo']) && !empty($rolesUsuario)){
				$primerRol = $rolesUsuario[0];
				$_SESSION['rol_activo'] = $primerRol['tipo'] . '_' . ($primerRol['tipo'] == 'institucional' ? 'sede_' . ($primerRol['sede_id'] ?? 'unknown') : 'sistema');
			}
			
			if(count($rolesUsuario) > 1): // Solo mostrar si tiene múltiples roles
			?>
			<li class="dropdown" id="rolSelectorContainer">
				<!-- Icono para menu colapsado -->
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" id="iconoRolColapsado" style="display: none;">
					<i class="fa fa-user-circle"></i>
				</a>
				
				<!-- Selector para menu expandido -->
				<div id="selectorRolExpandido" style="background: #2c3e50; padding: 10px 15px; margin: 0;">
					<div style="display: flex; align-items: center;">
						<i class="fa fa-user-circle" style="color: white; margin-right: 10px; font-size: 16px;"></i>
						<select class="form-control" id="selectorRolActivo" style="background: #f8f9fa; color: black; border: none; font-weight: bold; flex: 1;">
							<?php foreach($rolesUsuario as $index => $rol): ?>
							<?php 
							$valorRol = $rol['tipo'] . '_' . ($rol['tipo'] == 'institucional' ? 'sede_' . ($rol['sede_id'] ?? 'unknown') : 'sistema');
							$esSeleccionado = (isset($_SESSION['rol_activo']) && $_SESSION['rol_activo'] == $valorRol);
							?>
							<option value="<?php echo $valorRol; ?>" <?php echo $esSeleccionado ? 'selected' : ''; ?>>
								<?php echo $rol['nombre_rol']; ?>
							</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div id="loadingRol" style="display: none; text-align: center; margin-top: 5px; color: white;">
						<i class="fa fa-spinner fa-spin"></i> Cambiando rol...
					</div>
				</div>
				
				<!-- Dropdown menu para cuando está colapsado -->
				<ul class="dropdown-menu" style="background: #2c3e50; border: none; padding: 10px;">
					<li style="padding: 5px;">
						<select class="form-control" id="selectorRolColapsado" style="background: #f8f9fa; color: black; border: none; font-weight: bold;">
							<?php foreach($rolesUsuario as $index => $rol): ?>
							<?php 
							$valorRol = $rol['tipo'] . '_' . ($rol['tipo'] == 'institucional' ? 'sede_' . ($rol['sede_id'] ?? 'unknown') : 'sistema');
							$esSeleccionado = (isset($_SESSION['rol_activo']) && $_SESSION['rol_activo'] == $valorRol);
							?>
							<option value="<?php echo $valorRol; ?>" <?php echo $esSeleccionado ? 'selected' : ''; ?>>
								<?php echo $rol['nombre_rol']; ?>
							</option>
							<?php endforeach; ?>
						</select>
					</li>
				</ul>
			</li>
			<?php elseif(count($rolesUsuario) == 1): // Si solo tiene un rol, mostrarlo como información ?>
			<li id="rolUnicoContainer">
				<!-- Icono para menu colapsado -->
				<a href="#" id="iconoRolUnicoColapsado" style="display: none;">
					<i class="fa fa-user-circle"></i>
				</a>
				
				<!-- Texto para menu expandido -->
				<div id="rolUnicoExpandido" style="background: #2c3e50; padding: 10px 15px; margin: 0;">
					<div style="display: flex; align-items: center;">
						<i class="fa fa-user-circle" style="color: white; margin-right: 10px; font-size: 16px;"></i>
						<span style="color: white; font-weight: bold;"><?php echo $rolesUsuario[0]['nombre_rol']; ?></span>
					</div>
				</div>
			</li>
			<?php endif; ?>

			<!-- BOTON INICIO -->
			
			<li>				
				<a href="inicio">					
					<i class="fa fa-home"></i>
					<span>Inicio</span>
				</a>
			</li>

			<!-- BOTON USUARIOS Y ROLES -->

			<li class="treeview">				
				<a href="">					
					<i class="fa fa-user"></i>
					<span>Usuarios y Roles</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>						
					</span>
				</a>

				<!-- SUBBOTONES USUARIOS Y ROLES -->

				<ul class="treeview-menu">
					<li>						
						<a href="usuarios">							
							<i class="fa fa-circle-o"></i>
							<span>Gestionar Usuarios</span>
						</a>
					</li>

					<li>						
						<a href="gestionar-acciones">							
							<i class="fa fa-circle-o"></i>
							<span>Gestionar Acciones</span>
						</a>
					</li>

					<li>						
						<a href="gestionar-permisos">							
							<i class="fa fa-circle-o"></i>
							<span>Gestionar Permisos</span>
						</a>
					</li>

					<li>						
						<a href="asignar-roles">							
							<i class="fa fa-circle-o"></i>
							<span>Asignar Roles</span>
						</a>
					</li>
				</ul>
			</li>

			<!-- BOTON PERFIL LABORAL -->

			<li>				
				<a href="perfil-laboral">					
					<i class="fa fa-users"></i>
					<span>Perfil laboral</span>
				</a>
			</li>

			<!-- BOTON MODULO ESTRUCTURA ORGANIZACIONAL EN ARBOL O JERARQUICO -->

			<li class="treeview">				
				<a href="">					
					<i class="fa fa-sitemap"></i>
					<span>Estructura Organizacional</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>						
					</span>
				</a>

				<!-- SUBBOTONES ESTRUCUTRA ORGANIZACIONAL EN ARBOL O JERARQUICO -->

				<ul class="treeview-menu">
                    <li>
                        <a href="institucion">
                            <i class="fa fa-circle-o"></i>
                            <span>Institucion</span>
                        </a>
                    </li>

					<li>						
						<a href="sedes">							
							<i class="fa fa-circle-o"></i>
							<span>Sedes</span>
						</a>
					</li>

                    <li>
                        <a href="niveleducativo">
                            <i class="fa fa-circle-o"></i>
                            <span>Nivel Educativo</span>
                        </a>
                    </li>

					<li>						
						<a href="jornadas">
							<i class="fa fa-circle-o"></i>
							<span>Jornadas</span>
						</a>
					</li>

					<li>						
						<a href="grados">							
							<i class="fa fa-circle-o"></i>
							<span>Grados</span>
						</a>
					</li>

					<li>						
						<a href="periodos">							
							<i class="fa fa-circle-o"></i>
							<span>Periodo Académico</span>
						</a>
					</li>

					<li>						
						<a href="cursos">							
							<i class="fa fa-circle-o"></i>
							<span>Cursos</span>
						</a>
					</li>

					<li>						
						<a href="oferta">
							<i class="fa fa-circle-o"></i>
							<span>Oferta Educativa</span>
						</a>
					</li>
				</ul>
			</li>

			<!-- BOTON MATRICULACION -->

			<li class="treeview">				
				<a href="">					
					<i class="fa fa-graduation-cap"></i>
					<span>Matriculas</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>						
					</span>
				</a>

				<!-- SUBBOTON MATRICULACION EN ARBOL O JERARQUICO -->

				<ul class="treeview-menu">					
					<li>						
						<a href="matricula">							
							<i class="fa fa-circle-o"></i>
							<span>Matricula</span>
						</a>
					</li>

					<li>						
						<a href="estudiantes">							
							<i class="fa fa-circle-o"></i>
							<span>Estudiantes</span>
						</a>
					</li>

					<li>						
						<a href="acudientes">							
							<i class="fa fa-circle-o"></i>
							<span>Acudientes</span>
						</a>
					</li>

					<li>						
						<a href="pension-escolar">							
							<i class="fa fa-circle-o"></i>
							<span>Pension Escolar</span>
						</a>
					</li>
				</ul>


				<!-- BOTON SEGUIMIENTO ACADEMICO -->

			<li class="treeview">				
				<a href="">					
					<i class="fa fa-pencil"></i>
					<span>Seguimiento Academico</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>						
					</span>
				</a>

				<!-- SUBBOTON SEGUIMIENTO ACADEMICO EN ARBOL O JERARQUICO -->

				<ul class="treeview-menu">

                    <li>
                        <a href="estructura-curricular">
                            <i class="fa fa-circle-o"></i>
                            <span>Estructura Curricular</span>
                        </a>
                    </li>

					<li>						
						<a href="asistencia">							
							<i class="fa fa-circle-o"></i>
							<span>Asistencia</span>
						</a>
					</li>

					<li>						
						<a href="calificaciones">							
							<i class="fa fa-circle-o"></i>
							<span>Calificaciones</span>
						</a>
					</li>

					<li>						
						<a href="observaciones-academicas">							
							<i class="fa fa-circle-o"></i>
							<span>Observaciones Academicas</span>
						</a>
					</li>

					<li>						
						<a href="observaciones-disciplinarias">							
							<i class="fa fa-circle-o"></i>
							<span>Observaciones Disciplinarias</span>
						</a>
					</li>

					<li>						
						<a href="horarios">							
							<i class="fa fa-circle-o"></i>
							<span>Horarios de Clase</span>
						</a>
					</li>
				</ul>
			</li>

			<!-- BOTON ARCHIVO ACADEMICO 

			<li class="treeview">				
				<a href="">					
					<i class="fa fa-book"></i>
					<span>Archivo Academico</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>						
					</span>
				</a> -->

				<!-- SUBBOTON ARCHIVO ACADEMICO EN ARBOL O JERARQUICO 

				<ul class="treeview-menu">					
					<li>						
						<a href="">							
							<i class="fa fa-circle-o"></i>
							<span>Boletin de Calificaciones</span>
						</a>
					</li>

					<li>						
						<a href="">							
							<i class="fa fa-circle-o"></i>
							<span>Certificados</span>
						</a>
					</li>

					<li>						
						<a href="">							
							<i class="fa fa-circle-o"></i>
							<span>Constancias</span>
						</a>
					</li>
				</ul>
			</li> -->			
		</ul>
	</section>
</aside>

<script>
$(document).ready(function(){
	// Detectar cambio de estado del menu (expandido/colapsado)
	function toggleRolSelector() {
		if($('body').hasClass('sidebar-collapse')) {
			// Menu colapsado - mostrar solo icono
			$('#iconoRolColapsado, #iconoRolUnicoColapsado').show();
			$('#selectorRolExpandido, #rolUnicoExpandido').hide();
		} else {
			// Menu expandido - mostrar selector completo
			$('#iconoRolColapsado, #iconoRolUnicoColapsado').hide();
			$('#selectorRolExpandido, #rolUnicoExpandido').show();
		}
	}
	
	// Ejecutar al cargar y cuando cambie el estado del menu
	toggleRolSelector();
	$('.sidebar-toggle').on('click', function(){
		setTimeout(toggleRolSelector, 300); // Delay para esperar la animación
	});
	
	// Sincronizar ambos selectores
	function sincronizarSelectores(valor) {
		$('#selectorRolActivo, #selectorRolColapsado').val(valor);
	}
	
	// Manejar cambio de rol (ambos selectores)
	$('#selectorRolActivo, #selectorRolColapsado').on('change', function(){
		var rolSeleccionado = $(this).val();
		
		if(rolSeleccionado){
			$('#loadingRol').show();
			$(this).prop('disabled', true);
			
			$.ajax({
				url: 'ajax/cambiar-rol.ajax.php',
				method: 'POST',
				data: {
					rolSeleccionado: rolSeleccionado
				},
				success: function(respuesta){
					if(respuesta.trim() === 'ok'){
						// Sincronizar ambos selectores
						sincronizarSelectores(rolSeleccionado);
						
						// Actualizar el nombre de la sede en el cabezote
						$.ajax({
							url: 'ajax/obtener-sede-rol.ajax.php',
							method: 'POST',
							data: {
								rolSeleccionado: rolSeleccionado
							},
							success: function(sede){
								$('#sedeActiva').text(sede.toUpperCase());
							}
						});
						
						$('#loadingRol').hide();
						$('#selectorRolActivo, #selectorRolColapsado').prop('disabled', false);
					} else {
						alert('Error al cambiar el rol');
						$('#loadingRol').hide();
						$('#selectorRolActivo, #selectorRolColapsado').prop('disabled', false);
					}
				},
				error: function(){
					alert('Error de conexión');
					$('#loadingRol').hide();
					$('#selectorRolActivo, #selectorRolColapsado').prop('disabled', false);
				}
			});
		}
	});
});
</script>