<!-- MODAL EDITAR CURRÍCULO -->
<div class="modal fade" id="modalEditarCurriculo" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Editar Currículo</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3">
                        <h5>Áreas Disponibles</h5>
                        <div id="areasDisponiblesModal">
                            <?php
                            $areas = ControladorEstructuraCurricular::ctrMostrarAreas(null, null);
                            foreach ($areas as $area) {
                                echo '<div class="checkbox">
                                    <label>
                                        <input type="checkbox" class="areaCheckboxModal" value="'.$area["id"].'"> '.$area["nombre"].'
                                    </label>
                                </div>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h5>Asignaturas del Área</h5>
                        <div id="asignaturasAreaModal"></div>
                    </div>
                    <div class="col-md-6">
                        <h5>Asignaturas Asignadas</h5>
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row" style="font-weight: bold; border-bottom: 2px solid #ddd; margin-bottom: 10px; padding-bottom: 5px;">
                                    <div class="col-md-5">Asignatura</div>
                                    <div class="col-md-4">IHS</div>
                                    <div class="col-md-3">Acciones</div>
                                </div>
                                <div id="asignaturasAsignadasModal">
                                    <p class="text-muted" id="mensajeVacioModal">No hay asignaturas asignadas</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="gradoEditando" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnGuardarCambiosCurriculo">
                    <i class="fa fa-save"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>