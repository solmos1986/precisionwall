<div id="ModalRegisterActividad" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Request</h5>
                <button type="button" class="close" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="ms-panel">
                            <div class="ms-panel-header ms-panel-custome">
                                <h6>Register activities:</h6>
                            </div>
                            <div class="ms-panel-body">
                                <input type="text" hidden id="tipo_usuario" name="tipo_usuario"
                                    value="{{ Auth::user()->verificarRol([1]) }}">
                                <input type="text" hidden id="empleado_id" name="empleado_id"
                                    value="{{ auth()->user()->Empleado_ID }}">
                                <input type="text" hidden id="nickname" name="nickname"
                                    value="{{ auth()->user()->Nick_Name }}">
                                <div class="row">
                                    @if (Auth::user()->verificarRol([1]))
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="buton">From Date:</label>
                                                        <input type="text" name="from_date" id="from_date"
                                                            class="form-control form-control-sm datepicke"
                                                            placeholder="From Date" value="{{ date('m/d/Y') }}"
                                                            autocomplete="off" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="buton">To Date:</label>
                                                        <input type="text" name="to_date" id="to_date"
                                                            class="form-control form-control-sm datepicke"
                                                            placeholder="From Date" value="{{ date('m/d/Y') }}"
                                                            autocomplete="off" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="buton">Nick name:</label>
                                                        <input type="text" name="nick_name" id="nick_name"
                                                            class="form-control form-control-sm" placeholder="Nick name"
                                                            value="" autocomplete="off" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="buton">Job:</label>
                                                        <input type="text" name="job" id="job"
                                                            class="form-control form-control-sm" placeholder="Job"
                                                            value="" autocomplete="off" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="buton">Option:</label>
                                                        <br>
                                                        <label class="ms-checkbox-wrap">
                                                            <input type="checkbox" name="no_cost_code" id="no_cost_code"
                                                                value="1">
                                                            <i class="ms-checkbox-check"></i>
                                                        </label>
                                                        <span> Records with No Cost Code and Hours=8 </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="buton">Hours > 8 or < 0 :</label>
                                                                <input type="text" name="horas_trabajo"
                                                                    id="horas_trabajo"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="Hours" value=""
                                                                    autocomplete="off" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="buton">Area cost code:</label>
                                                        <input type="text" name="cost_code" id="cost_code"
                                                            class="form-control form-control-sm"
                                                            placeholder="Area cost code:" value=""
                                                            autocomplete="off" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">

                                                </div>
                                                <div class="col-md-12">
                                                    <div class="d-flex justify-content-between">
                                                        <button class="btn btn-pill btn-primary d-block"
                                                            style="padding: 0.2rem 0.5rem;" type="button"
                                                            id="buscar"><i class="fa fa-search"></i>
                                                            Search</button>
                                                        <button class="btn btn-pill btn-warning d-block"
                                                            style="padding: 0.2rem 0.5rem" type="button"
                                                            id="limpiar"><i class="fas fa-trash"></i> Clean</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-6">
                                                </div>
                                                <div class="col-md-6 d-flex flex-row-reverse bd-highlight">
                                                    <div class="form-group">
                                                        <button type="button" id="crear_registro"
                                                            class="btn btn-primary btn-sm mt-0 has-icon">
                                                            <i class="fa fa-plus"></i>
                                                            Add Records
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="buton">From Date:</label>
                                                        <input type="text" name="from_date" id="from_date"
                                                            class="form-control form-control-sm datepicke"
                                                            placeholder="From Date" value="{{ date('m/d/Y') }}"
                                                            autocomplete="off" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="buton">To Date:</label>
                                                        <input type="text" name="to_date" id="to_date"
                                                            class="form-control form-control-sm datepicke"
                                                            placeholder="From Date" value="{{ date('m/d/Y') }}"
                                                            autocomplete="off" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="d-flex justify-content-between">
                                                        <button class="btn btn-pill btn-primary d-block"
                                                            style="padding: 0.2rem 0.5rem;" type="button"
                                                            id="buscar"><i class="fa fa-search"></i>
                                                            Search</button>
                                                        <button class="btn btn-pill btn-warning d-block"
                                                            style="padding: 0.2rem 0.5rem" type="button"
                                                            id="limpiar"><i class="fas fa-trash"></i> Clean</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-6">
                                                </div>
                                                <div class="col-md-6 d-flex flex-row-reverse bd-highlight">
                                                    <div class="form-group">
                                                        <button type="button" id="crear_registro"
                                                            class="btn btn-primary btn-sm mt-0 has-icon">
                                                            <i class="fa fa-plus"></i>
                                                            Add Records
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-12">
                                        <form action="" id="form_actividades" method="post">
                                            <table class="table thead-primary no-footer w-100" id="lista_actividades">
                                                <thead>
                                                    <tr>
                                                        <th>&nbsp;&nbsp;&nbsp;Day&nbsp;&nbsp;&nbsp;</th>
                                                        <th>Line&nbsp;/&nbsp;date&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                                        <th>Employee&nbsp;/&nbsp;Nick&nbsp;name&nbsp;&nbsp;&nbsp;&nbsp;
                                                        </th>
                                                        <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Job&nbsp;name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        </th>
                                                        <th>check&nbsp;in&nbsp;</th>
                                                        <th>check&nbsp;out&nbsp;</th>
                                                        <th>Title&nbsp;leven&nbsp;0&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                                        <th>Building&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                                        <th>Code&nbsp;=&nbsp;floor&nbsp;or&nbsp;area&nbsp;&nbsp;</th>
                                                        <th>Code&nbsp;=&nbsp;area&nbsp;or&nbsp;task</th>
                                                        <th>Hours&nbsp;Worked&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                                        <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Notes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        </th>
                                                        <th>Check by foreman&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </form>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-center">
                                            <button type="button" id="registrar_temporal"
                                                class="btn btn-success btn-sm mt-0 has-icon">
                                                <i class="fa fa-save"></i>
                                                Register
                                            </button>
                                            <button type="button" id="registrar" hidden
                                            class="btn btn-success btn-sm mt-0 has-icon">
                                            <i class="fa fa-save"></i>
                                            Register
                                        </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>
