<!-- MODAL AGREGAR ASIGNATURA -->
<div id="modalAgregarAsignatura" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" id="formAgregarAsignatura">
                <div class="modal-header" style="background: #3c8ebd; color: white;">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-book"></i> Agregar Asignatura</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre de la Asignatura:</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-book"></i></span>
                            <input type="text" class="form-control input-lg" name="nombreAsignatura" placeholder="Nombre de la asignatura" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Área:</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-folder"></i></span>
                            <select class="form-control input-lg" name="areaAsignatura" required>
                                <option value="">Seleccione un área...</option>
                                <?php
                                $areas = ControladorEstructuraCurricular::ctrMostrarAreas(null, null);
                                foreach ($areas as $area) {
                                    echo '<option value="'.$area["id"].'">'.$area["nombre"].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR ASIGNATURA -->
<div id="modalEditarAsignatura" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" method="post">
                <div class="modal-header" style="background: #3c8ebd; color: white;">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-pencil"></i> Editar Asignatura</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="idAsignatura" id="idAsignatura">
                    <div class="form-group">
                        <label>Nombre de la Asignatura:</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-book"></i></span>
                            <input type="text" class="form-control input-lg" name="editarNombreAsignatura" id="editarNombreAsignatura" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Área:</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-folder"></i></span>
                            <select class="form-control input-lg" name="editarAreaAsignatura" id="editarAreaAsignatura" required>
                                <option value="">Seleccione un área...</option>
                                <?php
                                $areas = ControladorEstructuraCurricular::ctrMostrarAreas(null, null);
                                foreach ($areas as $area) {
                                    echo '<option value="'.$area["id"].'">'.$area["nombre"].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
                <?php
                $editarAsignatura = new ControladorEstructuraCurricular();
                $editarAsignatura->ctrEditarAsignatura();
                ?>
            </form>
        </div>
    </div>
</div>

<!-- MODAL VER ASIGNATURA -->
<div id="modalVerAsignatura" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: #3c8ebd; color: white;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-eye"></i> Ver Asignatura</h4>
            </div>
            <div class="modal-body">
                <table style="border: none; width: 100%;">
                    <tr style="border: none;">
                        <td style="border: none; padding: 10px; font-weight: bold;">Nombre:</td>
                        <td style="border: none; padding: 10px;" id="verNombreAsignatura"></td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; padding: 10px; font-weight: bold;">Área:</td>
                        <td style="border: none; padding: 10px;" id="verAreaAsignatura"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning btnEditarAsignatura" data-dismiss="modal">
                    <i class="fa fa-pencil"></i> Editar
                </button>
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
            </div>
        </div>
    </div>
</div>