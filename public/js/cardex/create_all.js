//create event
$('#crear_evento').click(function () {
    $('#formModalEvent .modal-title').text('Add Event to one or multiple employees')
    $('#formModalEvent #event_form').trigger('reset')
    $('#formModalEvent').modal('show')
    $("#formModalEvent").removeAttr("tabindex");
    $('#company').multiselect('refresh');
    $('#company').multiselect('select', 6);
    $('#cargo').val([]).multiselect('refresh')//deselect
    $('#personal').val([]).multiselect('refresh')//deselect
    $('#users tbody').html("");//campos dinamicos
    $('#user_name').html("");//campos dinamicos
    $('#event').val(null).trigger('change');
    getAllUser();
    /*files */
    var $el4 = $('#docs'), initPlugin = function () {  
    };
 
    $el4.fileinput('destroy');
    var $el4 = $('#docs'), initPlugin = function () {
        $el4.fileinput({
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