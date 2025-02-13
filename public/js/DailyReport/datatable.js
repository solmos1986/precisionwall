$(document).on('click', '#open_modal_options', function () {
    $('#formModalTable').modal('show');
});

$(document).on('click', '.add_button', function () {
    $('input[class=check]:checked').each(function () {
        console.log($(this).val())

    });
    let ids = [];
    $('#lista').children().each(function (i, data) {
        console.log($(data).data('id'))
        ids.push($(data).data('id'))
    });

    $('#lista').append(add);
    $('#formModalTable').modal('hide');
});
let add = `
    <div class="p-3 m-2" style="background: rgb(243, 243, 243)">
        <div class="row d-flex justify-content-between pr-3 pl-3">
            <p class="ms-directions">DEMO</p>
            <button type="button" style="width: 30px;height: 30px;"
                class="ms-btn-icon btn-square btn-sm btn-danger ml-2 delete">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="row">
            <div class="col-md-12">
                <ul class="ms-list ms-list-display">
                        <li>
                            <label class="ms-checkbox-wrap ms-checkbox-primary">
                                <input type="checkbox" value="{{ $opcion->id }}"
                                    checked="">
                                <i class="ms-checkbox-check"></i>
                            </label>
                            <span> <strong>{{ $opcion->opcion }}</strong> <i> (
                                    
                                        {{ $valor->valor }},
                                    
                                    )<i>
                            </span>
                        </li>
                </ul>
            </div>
        </div>
    </div>
`;
$(document).on('click', '.delete', function () {
    $(this).parent().parent().remove();
});
