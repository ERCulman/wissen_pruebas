<!-- MODAL AGREGAR ÁREA -->
<div id="modalAgregarArea" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" method="post">
                <div class="modal-header" style="background: #3c8ebd; color: white;">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-folder"></i> Agregar Área</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre del Área:</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-folder"></i></span>
                            <input type="text" class="form-control input-lg" name="nombreArea" placeholder="Nombre del área" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
                <?php
                $crearArea = new ControladorEstructuraCurricular();
                $crearArea->ctrCrearArea();
                ?>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR ÁREA -->
<div id="modalEditarArea" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" method="post">
                <div class="modal-header" style="background: #3c8ebd; color: white;">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-pencil"></i> Editar Área</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="idArea" id="idArea">
                    <div class="form-group">
                        <label>Nombre del Área:</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-folder"></i></span>
                            <input type="text" class="form-control input-lg" name="editarNombreArea" id="editarNombreArea" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
                <?php
                $editarArea = new ControladorEstructuraCurricular();
                $editarArea->ctrEditarArea();
                ?>
            </form>
        </div>
    </div>
</div>

<!-- MODAL VER ÁREA -->
<div id="modalVerArea" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: #3c8ebd; color: white;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-eye"></i> Ver Área</h4>
            </div>
            <div class="modal-body">
                <table style="border: none; width: 100%;">
                    <tr style="border: none;">
                        <td style="border: none; padding: 10px; font-weight: bold;">Nombre:</td>
                        <td style="border: none; padding: 10px;" id="verNombreArea"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning btnEditarArea" data-dismiss="modal">
                    <i class="fa fa-pencil"></i> Editar
                </button>
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
            </div>
        </div>
    </div>
</div>