$(document).on('click', '#open_modal_table_option', function () {
    $('#formModalTable').modal('show');
    table.draw();
});

var table = $('#list-option').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    ajax: `${base_url}/config-daily-report-detail/datatable-option`,
    order: [],
    columns: [{
        data: "id",
        render: function (data, type, row) {
            return `<input type="checkbox" class="check" name="daily_report" value="${data}" data-nombre="${row.nombre}" data-descripcion="${row.descripcion}" data-detalle="${row.detalle}">`;
        },
        orderable: false
    },
    {
        data: "nombre",
        name: "nombre"
    },
    {
        data: "descripcion",
        name: "descripcion"
    },
    {
        data: "detalle",
        name: "detalle"
    },
    {
        data: "actions",
        name: "actions",
        orderable: false
    }],

});

$(document).on('click', '.add_button', function () {
    let resultado = [];
    $('input[class=check]:checked').each(function (i, data) {
        if ($(data).is(':checked')) {
            resultado.push($(data).val())
        }
    });
    $.ajax({
        type: "POST",
        url: `${base_url}/config-daily-report-option/show-options`,
        data: { options: resultado },
        dataType: "json",
        success: function (response) {
            $('#lista').append(configOption(response.data));
        },
        error: function (jqXHR, textStatus, errorThrown) {
            error_status(jqXHR)
        },
        fail: function () {
            fail()
        }
    });
    $('#formModalTable').modal('hide');
});

function configOption(data) {
    let dailyreportHTML = ``;

    data.forEach(report => {
        let optionsHTML = ``;

        report.options.forEach(option => {
            let valores = ``;
            if (option.valores != "") {
                valores += `  <i> (
                    ${option.valores}
                )</i>`;
            }
            optionsHTML += `
            <li>
                <label class="ms-checkbox-wrap ms-checkbox-primary">
                    <input type="checkbox" value="${option.id}" >
                    <i class="ms-checkbox-check"></i>
                </label>
                <span> <strong>${option.opcion}</strong>
                    ${valores}
                </span>
            </li>`;
        });
        dailyreportHTML += `
        <div class="p-3 m-2" style="background: rgb(243, 243, 243)">
            <div class="row d-flex justify-content-between pr-3 pl-3">
                <p class="ms-directions">${report.nombre}</p>
                <button type="button" style="width: 30px;height: 30px;"
                    class="ms-btn-icon btn-square btn-sm btn-danger ml-2 delete">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <ul class="ms-list ms-list-display">
                        ${optionsHTML}
                    </ul>
                </div>
            </div>
        </div>
    `;
    });
    return dailyreportHTML;
}
$(document).on('click', '.delete', function () {
    $(this).parent().parent().remove();
});


