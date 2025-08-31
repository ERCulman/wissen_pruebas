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

	<!-- PERFIL DE USUARIO -->

	<div class="navbar-custom-menu">
		<ul class="nav navbar-nav">			
			<li class="dropdown user user-menu">				
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">					
					<img src="vistas/img/usuarios/default/anonymous.png" class="user-image">
					<span class="hidden-xs">
						<?php

						echo $_SESSION["nombres_usuario"]. " ";
						echo $_SESSION["apellidos_usuario"]. "<br>";
                        echo "Rol: ". $_SESSION["id_rol"];


						?>						
					</span>
				</a>

				<!-- DROPDOWN - TOGGLE -->

				<ul class="dropdown-menu">
					<li class="user-body">			
						<div class="pull-right">				
							<a href="salir" class="btn btn-default btn-flat">Salir</a>
						</div>
					</li>
				</ul>
			</li>
		</ul>
	</div>
</nav>	
</header>