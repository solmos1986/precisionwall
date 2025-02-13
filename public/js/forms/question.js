//variables para crear
const campo = `
<div class="row">
<div class="col-md-12">
    <div class="jumbotron" style="padding: 1rem 1rem">
        <div class="row">
            <div class="col-md-9">
                <div class="input-group">
                    <input type="text" class="form-control"
                        aria-label="Sizing example input"
                        aria-describedby="inputGroup-sizing-default" placeholder="question">
                </div>
            </div>
            <div class="col-md-3 pb-3">
                <div class="dropdown in-line">
                    <button class="btn btn-primary btn-sm dropdown-toggle mt-0"
                        type="button" id="dropdownMenu2" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        Type of question
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                        <button class="dropdown-item button_check_box" type="button"><i
                                class="fas fa-check-square"></i>
                            Check box</button>
                        <button class="dropdown-item button_box" type="button"><i
                                class="fas fa-dot-circle"></i>
                            Box</button>
                        <button class="dropdown-item button_paragraph" type="button"><i
                                class="fas fa-align-left"></i>
                            Paragraph</button>
                        <button class="dropdown-item button_lineal_scale" type="button"><i
                                class="fas fa-ellipsis-h"></i>
                            Linear scale</button>
                        <button class="dropdown-item delete_pregunta" type="button"><i
                                class="far fa-window-close"></i>
                            Delete all</button>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
            <div class="input-group input-group-sm mb-3 option_box">
            <span class="fas fa-dot-circle  pt-2 pr-1"></span>
            <input type="text" class="form-control" aria-label="Sizing example input"
                aria-describedby="inputGroup-sizing-sm" value="Never" placeholder="description">
            <button type="button" style="width: 30px;height: 30px;"
                class="ms-btn-icon btn-square btn-sm btn-danger ml-2 delete_option">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="input-group input-group-sm mb-3 option_box">
            <span class="fas fa-dot-circle  pt-2 pr-1"></span>
            <input type="text" class="form-control" aria-label="Sizing example input"
                aria-describedby="inputGroup-sizing-sm" value="Sometimes" placeholder="description">
            <button type="button" style="width: 30px;height: 30px;"
                class="ms-btn-icon btn-square btn-sm btn-danger ml-2 delete_option">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="input-group input-group-sm mb-3 option_box">
            <span class="fas fa-dot-circle  pt-2 pr-1"></span>
            <input type="text" class="form-control" value="Often" aria-label="Sizing example input"
                aria-describedby="inputGroup-sizing-sm" placeholder="description">
            <button type="button" style="width: 30px;height: 30px;"
                class="ms-btn-icon btn-square btn-sm btn-danger ml-2 delete_option">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="input-group input-group-sm mb-3 option_box">
            <span class="fas fa-dot-circle  pt-2 pr-1"></span>
            <input type="text" class="form-control" value="Always" aria-label="Sizing example input"
                aria-describedby="inputGroup-sizing-sm" placeholder="description">
            <button type="button" style="width: 30px;height: 30px;"
                class="ms-btn-icon btn-square btn-sm btn-danger ml-2 delete_option">
                <i class="fas fa-times"></i>
            </button>
        </div>
            </div>
            <div class="col-md-12">
                <div class="input-group">
                    <button class="btn btn-primary btn-sm add_options">
                        Add options
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
`;
const description = `
<div class="col-md-10 question">
                <div class="ms-panel">
                    <div class="ms-panel-header ">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input type="text" class="form-control descripcion"
                                        aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default"
                                        placeholder="SUBTITLE">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-group input-group-sm ">
                                    <input type="text" class="form-control" aria-label="Sizing example input"
                                        aria-describedby="inputGroup-sizing-sm" placeholder="optional description">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ms-panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="jumbotron" style="padding: 1rem 1rem">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <div class="input-group">
                                                <input type="text" class="form-control"
                                                    aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-default" placeholder="question">
                                            </div>
                                        </div>
                                        <div class="col-md-3 pb-3">
                                            <div class="dropdown in-line">
                                                <button class="btn btn-primary btn-sm dropdown-toggle mt-0"
                                                    type="button" id="dropdownMenu2" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    Type of question
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                                    <button class="dropdown-item button_check_box" type="button"><i
                                                            class="fas fa-check-square"></i>
                                                        Check box</button>
                                                    <button class="dropdown-item button_box" type="button"><i
                                                            class="fas fa-dot-circle"></i>
                                                        Box</button>
                                                    <button class="dropdown-item button_paragraph" type="button"><i
                                                            class="fas fa-align-left"></i>
                                                        Paragraph</button>
                                                    <button class="dropdown-item button_lineal_scale" type="button"><i
                                                            class="fas fa-ellipsis-h"></i>
                                                        Linear scale</button>
                                                    <button class="dropdown-item delete_pregunta" type="button"><i
                                                            class="far fa-window-close"></i>
                                                        Delete all</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            
                                        </div>
                                        <div class="col-md-12">
                                            <div class="input-group">
                                                <button class="btn btn-primary btn-sm add_options">
                                                    Add options
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" href="#"
                                    class="ms-btn-icon btn-pill btn-danger float-right eliminar_question">
                                    <i class="fas fa-trash-alt"></i></button>
                                <a type="button" href="#controls" class="ms-btn-icon btn-pill btn-success  agregar"><i
                                        class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
`;
const checkBox = `
  <div class="input-group input-group-sm mb-3 option_check_box">
                                        <span class="fa fa-check-square pt-2 pr-1"></span>
                                        <input type="text" class="form-control" aria-label="Sizing example input"
                                            aria-describedby="inputGroup-sizing-sm" placeholder="description"
                                            value="">
                                        <button type="button" style="width: 30px;height: 30px;"
                                            class="ms-btn-icon btn-square btn-sm btn-danger ml-2 delete_option">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
`;
const box = `
  <div class="input-group input-group-sm mb-3 option_box">
                                        <span class="fas fa-dot-circle  pt-2 pr-1"></span>
                                        <input type="text" class="form-control" aria-label="Sizing example input"
                                            aria-describedby="inputGroup-sizing-sm" placeholder="description">
                                        <button type="button" style="width: 30px;height: 30px;"
                                            class="ms-btn-icon btn-square btn-sm btn-danger ml-2 delete_option">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
`;
const preBox=`
                                        <div class="input-group input-group-sm mb-3 option_box">
                                                <span class="fas fa-dot-circle  pt-2 pr-1"></span>
                                                <input type="text" class="form-control" aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm" value="Never" placeholder="description">
                                                <button type="button" style="width: 30px;height: 30px;"
                                                    class="ms-btn-icon btn-square btn-sm btn-danger ml-2 delete_option">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <div class="input-group input-group-sm mb-3 option_box">
                                                <span class="fas fa-dot-circle  pt-2 pr-1"></span>
                                                <input type="text" class="form-control" aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm" value="Sometimes" placeholder="description">
                                                <button type="button" style="width: 30px;height: 30px;"
                                                    class="ms-btn-icon btn-square btn-sm btn-danger ml-2 delete_option">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <div class="input-group input-group-sm mb-3 option_box">
                                                <span class="fas fa-dot-circle  pt-2 pr-1"></span>
                                                <input type="text" class="form-control" value="Often" aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm" placeholder="description">
                                                <button type="button" style="width: 30px;height: 30px;"
                                                    class="ms-btn-icon btn-square btn-sm btn-danger ml-2 delete_option">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <div class="input-group input-group-sm mb-3 option_box">
                                                <span class="fas fa-dot-circle  pt-2 pr-1"></span>
                                                <input type="text" class="form-control" value="Always" aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm" placeholder="description">
                                                <button type="button" style="width: 30px;height: 30px;"
                                                    class="ms-btn-icon btn-square btn-sm btn-danger ml-2 delete_option">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
`;
const preCheckBox =`
<div class="input-group input-group-sm mb-3 option_check_box">
                                                <span class="fa fa-check-square pt-2 pr-1"></span>
                                                <input type="text" class="form-control" aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm" value="Never" placeholder="description">
                                                <button type="button" style="width: 30px;height: 30px;"
                                                    class="ms-btn-icon btn-square btn-sm btn-danger ml-2 delete_option">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <div class="input-group input-group-sm mb-3 option_check_box">
                                                <span class="fa fa-check-square pt-2 pr-1"></span>
                                                <input type="text" class="form-control" aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm" value="Sometimes" placeholder="description">
                                                <button type="button" style="width: 30px;height: 30px;"
                                                    class="ms-btn-icon btn-square btn-sm btn-danger ml-2 delete_option">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <div class="input-group input-group-sm mb-3 option_check_box">
                                                <span class="fa fa-check-square pt-2 pr-1"></span>
                                                <input type="text" class="form-control" aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm" value="Often" placeholder="description">
                                                <button type="button" style="width: 30px;height: 30px;"
                                                    class="ms-btn-icon btn-square btn-sm btn-danger ml-2 delete_option">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <div class="input-group input-group-sm mb-3 option_check_box">
                                                <span class="fa fa-check-square pt-2 pr-1"></span>
                                                <input type="text" class="form-control" aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm" value="Always" placeholder="description">
                                                <button type="button" style="width: 30px;height: 30px;"
                                                    class="ms-btn-icon btn-square btn-sm btn-danger ml-2 delete_option">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
`;
const lineas = `
<div class="input-group option_paragraph">
    <textarea class="form-control text option_paragraph" rows="3"></textarea>
</div>
`;

const escalaLineal = ` 
    <div class="input-group input-group-sm mb-3 option_scale">
    <span class="pt-2 pr-1"><strong> 1 </strong>&nbsp; </span>
    <input line type="text" class="form-control col-md-6" aria-label="Sizing example input"
        aria-describedby="inputGroup-sizing-sm" value="Poor" disabled placeholder="optional">
    <span class="pt-2 pr-1"> &nbsp; to <strong> 10 </strong>&nbsp; </span>
    <input line type="text" class="form-control col-md-6" aria-label="Sizing example input"
        aria-describedby="inputGroup-sizing-sm" value="Excellent" disabled placeholder="optional">
    </div>
`;