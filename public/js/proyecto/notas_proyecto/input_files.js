
$(document).on('click', '.files_nota', function () {
    const id = $(this).data("id");
    $.ajax({
        type: 'GET',
        url: `${base_url}/project-notas/edit/${id}`,
        dataType: "json",
        success: function (response) {
            $('#ModalNotaImages #form_nota_images').trigger('reset');
            $('#ModalNotaImages .modal-title').text('File notes');
            $('#ModalNotaImages').modal('show');
            var $el4 = $('#modal_nota_files'), initPlugin = function () {
            };
            var data = [];
            response.data.files.initialPreviewConfig.forEach(file => {
                var ext = (file.caption).split(".");
                var ext = ext[1];
                if (ext == 'pdf' || ext == 'xlsx' || ext == 'docx' || ext == 'doc' || ext == 'xls' || ext == 'csv' || ext == 'heic') {
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

            var $el4 = $('#modal_nota_files'), initPlugin = function () {
                $el4.fileinput({
                    theme: "fas",
                    pdfRendererUrl: 'https://plugins.krajee.com/pdfjs/web/viewer.html',
                    allowedFileExtensions: ['jpg', 'png', 'jpeg', 'pdf', 'docx', 'doc', 'xlsx', 'xls', 'csv', 'heic'],
                    uploadUrl: `${base_url}/project-notas/upload_image/${response.data.nota.id}`,
                    uploadAsync: false,
                    showUpload: false,
                    overwriteInitial: false,
                    minFileCount: 1,
                    maxFileCount: 4,
                    browseOnZoneClick: false,
                    showUpload: false,
                    initialPreviewAsData: true,
                    showRemove: false,
                    showClose: false,
                    showCancel: false,
                    browseClass: "btn btn-sm btn-success",
                    initialPreviewDownloadUrl: response.data.files.initialPreview,
                    initialPreview: response.data.files.initialPreview,
                    initialPreviewConfig: data
                }).on("filebatchselected", function (event, files) {
                    $el4.fileinput("upload");
                }).on("filepredelete", function (jqXHR) {
                    var abort = true;
                    if (alert("Cannot delete resources")) {
                        abort = true;
                    }
                    return abort;
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