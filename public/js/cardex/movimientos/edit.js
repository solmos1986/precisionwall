//edit event
$(document).on('click', '.edit_evento', function () {
    var id = $(this).data('id')
    $('#formEditModalEvent #edit_event_form').trigger('reset');
    $('#edit_docs_name').text('Choose file');
    $.ajax({
        url: `${base_url}/edit-movimento-cardex/${id}`,
        dataType: "json",
        success: function (response) {
            console.log(response.data);
            //preselect
            var newOption = new Option(response.data.movimiento.nombre, response.data.movimiento.cod_evento, true, true);
            // Append it to the select
            $('#edit_event').append(newOption).trigger('change');
            $('#edit_movimientos_eventos').val(response.data.movimiento.movimientos_eventos_id);
            $('#edit_fecha_inicio').val(response.data.movimiento.start_date);
            $('#edit_duracion_evento').val(response.data.movimiento.duracion_day);
            //variable global para editar los dias
            dias = response.data.movimiento.duracion_day;
            $('#edit_report_alert').val(response.data.movimiento.report_alert);
            $('#edit_fecha_fin').val(response.data.movimiento.exp_date);
            $('#edit_note').val(response.data.movimiento.note);
            $('#edit_tipo_evento').val(response.data.movimiento.tipo_evento);
            $('#formEditModalEvent .modal-title').text('Edit event');
            $('#action').val('Edit');
            $('#formEditModalEvent').modal('show');

            var $el4 = $('#update_input_images'), initPlugin = function () {
            };
            var data = [];
            response.data.files.initialPreviewConfig.forEach(file => {
                var ext = (file.caption).split(".");
                var ext = ext[1];
                if (ext == 'pdf' || ext == 'xlsx' || ext == 'docx' || ext == 'doc' || ext == 'xls' || ext == 'csv') {
                    data.push(
                        {
                            type: ext,
                            description: '',
                            size: file.size,
                            caption: ext,
                            downloadUrl: file.downloadUrl,
                            key: file.key,
                            url: file.url
                        }
                    )
                } else {
                    data.push(
                        {
                            caption: file.caption,
                            description: '',
                            size: file.size,
                            downloadUrl: file.downloadUrl,
                            key: file.key,
                            url: file.url
                        }
                    )
                }
            });
            $el4.fileinput('destroy');

            var $el4 = $('#update_input_images'), initPlugin = function () {
                $el4.fileinput({
                    /* theme: "fas",
                    allowedFileExtensions: ['jpg', 'png', 'jpeg', 'pdf', 'docx', 'doc', 'xlsx', 'xls', 'csv'],
                    uploadUrl: `${base_url}/upload_image/${response.data.movimiento.movimientos_eventos_id}/input_images/files/cardex`,
                    uploadAsync: true,
                    showUpload: false,
                    overwriteInitial: false,
                    minFileCount: 1,
                    maxFileCount: 4,
                    browseOnZoneClick: true,
                    initialPreviewAsData: true,
                    showRemove: true,
                    showClose: false,
                    browseClass: "btn btn-sm btn-success",
                    initialPreview: response.data.files.initialPreview,
                    initialPreviewConfig: response.data.files.initialPreviewConfig, */


                    theme: "fas",
                    pdfRendererUrl: 'https://plugins.krajee.com/pdfjs/web/viewer.html',
                    allowedFileExtensions: ['jpg', 'png', 'jpeg', 'pdf', 'docx', 'doc', 'xlsx', 'xls', 'csv','heic'],
                    uploadUrl: `${base_url}/upload_image/${response.data.movimiento.movimientos_eventos_id}/input_images/files/cardex`,
                    uploadAsync: true,
                    showUpload: false,
                    overwriteInitial: false,
                    minFileCount: 1,
                    maxFileCount: 4,
                    browseOnZoneClick: true,
                    initialPreviewAsData: true,
                    showRemove: true,
                    showClose: false,
                    showCancel: false,
                    browseClass: "btn btn-sm btn-success",
                    initialPreviewDownloadUrl: response.data.files.initialPreview,
                    initialPreview: response.data.files.initialPreview,
                    initialPreviewConfig: data

                });
            };
            initPlugin();

        },
        error: function (jqXHR, textStatus, errorThrown) {
            error_status(jqXHR)
        },
        fail: function () {
            fail()
        }
    })
});