<!-- =======================================
      MODAL AGREGAR USUARIO
    =======================================-->

<div id="modalAgregarUsuario" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form role="form" method="post" enctype="multipart/form-data" id="formAgregarUsuario" data-validacion-universal>
                <div class="modal-header" style="background: #3c8ebd; color: white;">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-user-plus"></i> Agregar Usuario</h4>
                </div>

                <div class="modal-body">
                    <div class="box-body">

                        <div class="row">
                            <!-- CAMPO NÚMERO DE DOCUMENTO -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Número de Documento:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-id-card"></i></span>
                                        <input type="text" class="form-control input-lg" name="numeroDocumento" placeholder="Número de Documento" data-reglas="requerido|numeros|min:7|max:15">
                                    </div>
                                </div>
                                <!-- Contenedor de error para este campo -->
                                <div class="validation-error-container"></div>
                            </div>
                            <!-- CAMPO TIPO DE DOCUMENTO -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tipo de Documento:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-list-alt"></i></span>
                                        <select class="form-control input-lg" name="tipoDocumento" data-reglas="requerido">
                                            <option value="">Seleccione Tipo de Documento</option>
                                            <option value="CC">CC - CÉDULA DE CIUDADANÍA</option>
                                            <option value="CE">CE - CÉDULA DE EXTRANJERÍA</option>
                                            <option value="TI">TI - TARJETA DE IDENTIDAD</option>
                                            <option value="RC">RC - REGISTRO CIVIL</option>
                                            <option value="PTE">PTE - PASAPORTE</option>
                                            <option value="TE">TE - TARJETA DE EXTRANJERÍA</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Contenedor de error para este campo -->
                                <div class="validation-error-container"></div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- CAMPO NOMBRES -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nombre(s):</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                        <input type="text" class="form-control input-lg" name="nombreUsuario" placeholder="Nombres" data-reglas="requerido|texto|min:3|max:20">
                                    </div>
                                </div>
                                <!-- Contenedor de error para este campo -->
                                <div class="validation-error-container"></div>
                            </div>
                            <!-- CAMPO APELLIDOS -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Apellidos:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                        <input type="text" class="form-control input-lg" name="apellidoUsuario" placeholder="Apellidos" data-reglas="requerido|texto|min:3|max:20">
                                    </div>
                                </div>
                                <!-- Contenedor de error para este campo -->
                                <div class="validation-error-container"></div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- CAMPO GÉNERO -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Género:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-venus-mars"></i></span>
                                        <select class="form-control input-lg" name="sexoUsuario" data-reglas="requerido">
                                            <option value="">Seleccione Género</option>
                                            <option value="Masculino">MASCULINO</option>
                                            <option value="Femenino">FEMENINO</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Contenedor de error para este campo -->
                                <div class="validation-error-container"></div>
                            </div>
                            <!-- CAMPO RH -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tipo de Sangre (RH):</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-tint"></i></span>
                                        <select class="form-control input-lg" name="rhUsuario" data-reglas="requerido">
                                            <option value="">Seleccione RH</option>
                                            <option value="A+">A+</option>
                                            <option value="AB+">AB+</option>
                                            <option value="B+">B+</option>
                                            <option value="O+">O+</option>
                                            <option value="A-">A-</option>
                                            <option value="AB-">AB-</option>
                                            <option value="B-">B-</option>
                                            <option value="O-">O-</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Contenedor de error para este campo -->
                                <div class="validation-error-container"></div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- CAMPO FECHA DE NACIMIENTO -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha de Nacimiento:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control input-lg has-datepicker" name="fechaNacimiento" id="fechaNacimiento" placeholder="dd/mm/aaaa" maxlength="10" data-reglas="requerido|fechaUsuario">
                                    </div>
                                </div>
                                <!-- Contenedor de error para este campo -->
                                <div class="validation-error-container"></div>
                            </div>
                            <!-- CAMPO EDAD -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Edad:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-birthday-cake"></i></span>
                                        <input type="number" class="form-control input-lg" name="edadUsuario" id="edadUsuario" placeholder="Edad" data-reglas="requerido|minValor:1|maxValor:120" readonly>
                                    </div>
                                </div>
                                <!-- Contenedor de error para este campo -->
                                <div class="validation-error-container"></div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- CAMPO TELÉFONO -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Teléfono:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                        <input type="text" class="form-control input-lg" name="telefonoUsuario" placeholder="Teléfono" data-reglas="requerido|numeros|min:10|max:15">
                                    </div>
                                </div>
                                <!-- Contenedor de error para este campo -->
                                <div class="validation-error-container"></div>
                            </div>
                            <!-- CAMPO EMAIL -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Correo Electrónico:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                        <input type="email" class="form-control input-lg" name="emailUsuario" placeholder="Correo Electrónico" data-reglas="requerido|email">
                                    </div>
                                </div>
                                <!-- Contenedor de error para este campo -->
                                <div class="validation-error-container"></div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- CAMPO USUARIO -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Usuario:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user-secret"></i></span>
                                        <input type="text" class="form-control input-lg" name="loginUsuario" placeholder="Usuario" data-reglas="requerido|min:5|max:20">
                                    </div>
                                </div>
                                <!-- Contenedor de error para este campo -->
                                <div class="validation-error-container"></div>
                            </div>
                            <!-- CAMPO CONTRASEÑA -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Contraseña:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                        <input type="password" class="form-control input-lg" name="password" placeholder="Contraseña" data-reglas="requerido|passwordFuerte">
                                    </div>
                                </div>
                                <!-- Contenedor de error para este campo -->
                                <div class="validation-error-container"></div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                    <button type="submit" class="btn btn-primary">Registrar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>