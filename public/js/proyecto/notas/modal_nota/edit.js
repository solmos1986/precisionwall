//edit event
$(document).on('click', '.edit_nota', function () {
    var id = $(this).data('id')
    $('#ModalNota #form_nota').trigger('reset');
    $('#proyecto_id').val(null).trigger('change');
    limpiar_campos_Text();

    $.ajax({
        type: 'GET',
        url: `${base_url}/project-notas/edit/${id}`,
        dataType: "json",
        success: function (response) {
            //preselect
            var newOption = new Option(response.data.nota.Nombre, response.data.nota.Pro_ID, true, true);
            // Append it to the select
            $('#proyecto_id').append(newOption).trigger('change');

            $('#nota_id').val(response.data.nota.id);
            $('#fecha_entrega').val(response.data.nota.fecha_entrega);
            $('#empresa').val(response.data.nota.nombre_empresa);
            $('#codigo').val(response.data.nota.Codigo);
            $('#estado').val(response.data.nota.estado);
            $('#note').val(response.data.nota.nota);

            $('#proyecto_manager_id').val(response.data.nota.project_manager_id);
            if (response.data.nota.project_manager_id != 0) {
                $('#proyecto_manager_id').prop('checked', true)
            }
            $('#proyecto_manager').text(response.data.nota.project_manager);

            $('#asistente_proyecto_manager_id').val(response.data.nota.asistente_project_manager_id);
            if (response.data.nota.asistente_project_manager_id != 0) {
                alert("es diferente de 0")
                $('#asistente_proyecto_manager_id').prop('checked', true)
            }
            $('#asistente_proyecto_manager').text(response.data.nota.asistente_project_manager);

            $('#foreman_id').val(response.data.nota.foreman_id);
            if (response.data.nota.foreman_id != 0) {
                $('#foreman_id').prop('checked', true)
            }
            $('#foreman').text(response.data.nota.foreman);

            $('#lead_id').val(response.data.nota.lead_id);
            if (response.data.nota.lead_id != 0) {
                $('#lead_id').prop('checked', true)
            }
            $('#lead').text(response.data.nota.lead);

            if (response.data.admin == "no") {
                $('#fecha_entrega').prop('disabled', true);
                $('#proyecto_id').prop('disabled', true);
                $('#lead_id').prop('disabled', true);
                $('#proyecto_manager_id').prop('disabled', true);
                $('#asistente_proyecto_manager_id').prop('disabled', true);
                $('#foreman_id').prop('disabled', true);
            }

            $('#ModalNota .modal-title').text('Edit Note');
            $('#ModalNota').modal('show');

            $('#save_nota').removeClass('store_nota update_nota');
            $('#save_nota').addClass('update_nota');
            var $el4 = $('#nota_files'), initPlugin = function () {
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

            var $el4 = $('#nota_files'), initPlugin = function () {
                $el4.fileinput({
                    theme: "fas",
                    pdfRendererUrl: 'https://plugins.krajee.com/pdfjs/web/viewer.html',
                    allowedFileExtensions: ['jpg', 'png', 'jpeg', 'pdf', 'docx', 'doc', 'xlsx', 'xls', 'csv','heic'],
                    uploadUrl: `${base_url}/upload_image/${response.data.nota.id}`,
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