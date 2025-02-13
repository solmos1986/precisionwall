$('#crear_evento').click(function () {
    $('#formNewModalEvent .modal-title').text('Create event');
    $('#formNewModalEvent #new_event_form').trigger('reset');
    $('#new_docs_name').text('');
    $('#formNewModalEvent').modal('show');
    $('#new_event').val(null).trigger('change');
    $("#formNewModalEvent").removeAttr("tabindex");

    var $el4 = $('#new_input_images'), initPlugin = function () {  
    };
 
    $el4.fileinput('destroy');
    var $el4 = $('#new_input_images'), initPlugin = function () {
        $el4.fileinput({
            /*             theme: "fas",
                        allowedFileExtensions: ['jpg', 'png', 'jpeg', 'pdf', 'docx', 'doc', 'xlsx', 'xls', 'csv'],
                        //uploadUrl: `${base_url}/upload_image/${response.data.movimiento.movimientos_eventos_id}/input_images/files/cardex`,
                        uploadAsync: false,
                        showUpload: false,
                        overwriteInitial: false,
                        minFileCount: 1,
                        maxFileCount: 4,
                        browseOnZoneClick: true,
                        initialPreviewAsData: true,
                        showRemove: true,
                        showClose: false,
                        browseClass: "btn btn-sm btn-success",
                        initialPreview: [],
                        initialPreviewConfig: [],
                        pdfRendererUrl: 'https://plugins.krajee.com/pdfjs/web/viewer.html',
             */
            theme: "fas",
            allowedFileExtensions: ['jpg', 'png', 'jpeg', 'pdf', 'docx', 'doc', 'xlsx', 'xls', 'csv','heic'],
            //uploadUrl: `${base_url}/upload_image/${response.data.movimiento.movimientos_eventos_id}/input_images/files/cardex`,
            uploadAsync: false,
            showUpload: false,
            overwriteInitial: false,
            minFileCount: 1,
            maxFileCount: 4,
            browseOnZoneClick: true,
            initialPreviewAsData: false,
            showRemove: true,
            removeFromPreviewOnError:false,
            showClose: false,
            showCancel: false,
            validateInitialCount: false,
            browseClass: "btn btn-sm btn-success",

        });
    };
    initPlugin();
})