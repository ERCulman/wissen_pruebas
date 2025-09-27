<!-- PAGINA CABECERA O BARRA DE NAVEGACION, LOGO, PERFIL USUARIO Y BOTON SALIR -->

<header class="main-header">

<!-- =======================================
  LOGOTIPO
=======================================-->
<a href="inicio" class="logo">

	<!-- LOGO MINI -->

	<span class="logo-mini">
		<img src="vistas/img/plantilla/logo_vertical.png" class="img-responsive" style="padding:3px">
	</span>

	<!-- LOGO NORMAL -->

	<span class="logo-lg">
		<!--<img src="vistas/img/plantilla/logo_horizontal.png" class="img-responsive" style="padding: 3px 0px 3px 0px">-->
		Wissen System
	</span>
	
</a>

<!-- =======================================
  BARRA DE NAVEGACION
=======================================-->

<nav class="navbar navbar-static-top" role="navigation">

	<!-- BOTON DE NAVEGACION -->

	<a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
		
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>

	</a>

	<!-- NOMBRE DE LA SEDE ACTIVA -->
	<div class="navbar-header" style="position: absolute; left: 50%; transform: translateX(-50%); top: 0;">
		<span id="sedeActiva" style="color: white; font-weight: bold; font-size: 20px; line-height: 50px; text-transform: uppercase; white-space: nowrap;">
			<?php
			if(isset($_SESSION['rol_activo'])){
				$rolesUsuario = ControladorAuth::ctrObtenerRolesUsuario();
				foreach($rolesUsuario as $rol){
					$valorRol = $rol['tipo'] . '_' . ($rol['tipo'] == 'institucional' ? 'sede_' . ($rol['sede_id'] ?? 'unknown') : 'sistema');
					if($_SESSION['rol_activo'] == $valorRol){
						echo strtoupper($rol['nombre_sede']);
						break;
					}
				}
			} else {
				echo "SISTEMA";
			}
			?>
		</span>
	</div>

	<!-- PERFIL DE USUARIO -->

	<div class="navbar-custom-menu">
		<ul class="nav navbar-nav">			
			<li class="dropdown user user-menu">				
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" style="padding: 15px; display: block; width: 100%;">					
					<img src="vistas/img/usuarios/default/anonymous.png" class="user-image">
					<span class="hidden-xs">
						<?php

						echo $_SESSION["nombres_usuario"]. " ";
						echo $_SESSION["apellidos_usuario"];



						?>						
					</span>
				</a>

				<!-- DROPDOWN - TOGGLE -->

				<ul class="dropdown-menu" style="width: 100%; left: 0; background-color: white; border: 1px solid #ddd; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
					<!-- Header del usuario -->
					<li class="user-header" style="background-color: #3c8dbc; color: white; padding: 15px; text-align: center;">
						<img src="vistas/img/usuarios/default/anonymous.png" class="img-circle" style="width: 50px; height: 50px; margin-bottom: 10px;">
						<p style="margin: 0; font-weight: bold;">
							<?php echo $_SESSION["nombres_usuario"] . " " . $_SESSION["apellidos_usuario"]; ?>
							<small style="display: block; margin-top: 5px; opacity: 0.8;">Miembro desde <?php echo date('M Y'); ?></small>
						</p>
					</li>
					
					<!-- Opciones del menú -->
					<li class="user-body" style="padding: 0;">
						<div style="padding: 10px 0;">
							<a href="#" class="dropdown-item" style="display: block; padding: 10px 20px; color: #333; text-decoration: none; border-bottom: 1px solid #f0f0f0;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='white'">
								<i class="fa fa-user" style="margin-right: 10px; color: #3c8dbc;"></i>
								<span>Mi Perfil</span>
							</a>
							<a href="#" class="dropdown-item" style="display: block; padding: 10px 20px; color: #333; text-decoration: none; border-bottom: 1px solid #f0f0f0;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='white'">
								<i class="fa fa-key" style="margin-right: 10px; color: #f39c12;"></i>
								<span>Cambiar Clave</span>
							</a>
							<a href="salir" class="dropdown-item" style="display: block; padding: 10px 20px; color: #333; text-decoration: none;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='white'">
								<i class="fa fa-sign-out" style="margin-right: 10px; color: #dd4b39;"></i>
								<span>Cerrar Sesión</span>
							</a>
						</div>
					</li>
				</ul>
			</li>
		</ul>
	</div>
</nav>	
</header>