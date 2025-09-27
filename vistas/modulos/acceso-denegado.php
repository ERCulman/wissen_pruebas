<div class="content-wrapper">
  <section class="content-header">
    <h1>Acceso Denegado</h1>
  </section>

  <section class="content">
    <div class="error-page">
      <h2 class="headline text-red">403</h2>
      <div class="error-content">
        <h3><i class="fa fa-warning text-red"></i> ¡Acceso Denegado!</h3>
        <p>
          No tienes los permisos necesarios para acceder a este módulo del sistema.
          <br>
          Por favor, comunícate con el administrador del sistema para solicitar los permisos requeridos.
        </p>
        <div style="margin-top: 20px;">
          <a href="inicio" class="btn btn-primary">
            <i class="fa fa-home"></i> Volver al Inicio
          </a>
          <button onclick="history.back()" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Regresar
          </button>
        </div>
        
        <div class="alert alert-info" style="margin-top: 20px;">
          <h4><i class="fa fa-info"></i> Información:</h4>
          <ul>
            <li>Si crees que deberías tener acceso a este módulo, contacta al administrador</li>
            <li>Los permisos se asignan por roles y pueden variar según la sede</li>
            <li>Tu sesión está activa, solo necesitas los permisos adecuados</li>
          </ul>
        </div>
      </div>
    </div>
  </section>
</div>

<style>
.error-page {
  max-width: 600px;
  margin: 20px auto;
  text-align: center;
}

.error-page .headline {
  float: left;
  font-size: 100px;
  font-weight: 300;
  margin-right: 20px;
}

.error-page .error-content {
  margin-left: 120px;
  text-align: left;
}

.error-page .error-content h3 {
  font-weight: 300;
  font-size: 25px;
}

@media (max-width: 767px) {
  .error-page .headline {
    float: none;
    text-align: center;
  }
  .error-page .error-content {
    margin-left: 0;
    text-align: center;
  }
}
</style>