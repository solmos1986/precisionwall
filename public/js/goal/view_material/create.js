/* create */
$(document).on('click', '.view_report_superficio', function () {
    //reset
    $('#fromVisitReportSuperficie').trigger('reset');
    const proyecto_id = $(this).data('proyecto_id');
    $.ajax({
        type: "GET",
        url: `${base_url}/goal-project/report-visit-superficies/${proyecto_id}`,
        dataType: "json",
        success: function (response) {
            /* dato proyecto*/
            $('#title_modal').text(`Project - ${response.data.proyecto.Nombre}`);

            $('#proyecto_id').val(proyecto_id);
            superficies(response.data.sugerencia);

            //options
            data=[];
            response.data.superficies.forEach(superficie => {
                data.push(superficie.id)
            });
            $('#superficie_id').val(data).trigger('change');

            $('#modalCreateViewProjectMateriales').modal('show');

            /*datatable*/
            var list_table_materiales = $('#orden_material').DataTable().clear();
            list_table_materiales.destroy();
            list_table_materiales = $('#orden_material').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: `${base_url}/goal-project/data-table-orden/${proyecto_id}`,
                order: [],
                columns: [
                    { data: "nombre_proyecto", name: "nombre_proyecto" },
                    { data: "num", name: "num" },
                    { data: "nombre_estatus", name: "nombre_estatus" },
                    { data: "fecha_order", name: "fecha_order" },
                    { data: "nota", name: "nota" },
                    { data: "username", name: "username" },
                    /* { data: "acciones", name: "acciones" }, */
                ],
                columnDefs: [
                    {
                        width: "100px",
                        targets: 0
                    },
                ],
            });
        }
    });
});

function superficies(superficies) {
    $('#superficie_id').html('');
    superficieHTML=``;
    superficies.forEach(superficie => {
        superficieHTML+=`
        <option value="${superficie.id}">${superficie.codigo} | ${superficie.nombre}</option>
        `;
    });
    $('#superficie_id').append(superficieHTML);
}

$('#superficie_id').select2({
    theme: "bootstrap4",
    dropdownParent: $('#fromVisitReportSuperficie')
});

$(document).on('click', '#save_visit_report_superficie', function () {
    $.ajax({
        type: "POST",
        url: `${base_url}/goal-project/report-visit-superficies`,
        data: $('#fromVisitReportSuperficie').serialize(),
        dataType: "json",
        success: function (response) {
            if (response.status == 'ok') {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#modalCreateViewProjectMateriales').modal('hide');
            } else {
                $alert = "";
                response.message.forEach(function (error) {
                    $alert += `* ${error}<br>`;
                });
                Swal.fire({
                    icon: 'error',
                    title: 'complete the following fields to continue:',
                    html: $alert,
                })
            }
        }
    });
});



