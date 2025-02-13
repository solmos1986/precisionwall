$('#create_nota').click(function () {
    $("#ModalNota").removeAttr("tabindex");
    $('#ModalNota .modal-title').text('Create Note');
    $('#ModalNota #form_nota').trigger('reset');
    $('#ModalNota').modal('show');
    $('#proyecto_id').val(null).trigger('change');
    $('#foreman_id').prop('checked', true)
    limpiar_campos_Text();
    var $el4 = $('#nota_files'), initPlugin = function () {
    };
    $el4.fileinput('destroy');
    var $el4 = $('#nota_files'), initPlugin = function () {
        $el4.fileinput({
            theme: "fas",
            pdfRendererUrl: 'https://plugins.krajee.com/pdfjs/web/viewer.html',
            allowedFileExtensions: ['jpg', 'png', 'jpeg', 'pdf', 'docx', 'doc', 'xlsx', 'xls', 'csv','heic'],
            uploadUrl: `${base_url}/project-notas/upload_image/${0}`,
            uploadAsync: true,
            showUpload: true,
            overwriteInitial: false,
            minFileCount: 1,
            maxFileCount: 4,
            browseOnZoneClick: true,
            initialPreviewAsData: true,
            showRemove: true,
            showClose: false,
            showCancel: false,
            browseClass: "btn btn-sm btn-success",
            initialPreviewDownloadUrl: [],
            initialPreview: [],
        });
    };
    initPlugin();

    $('#save_nota').removeClass('store_nota update_nota');
    $('#save_nota').addClass('store_nota');
})
