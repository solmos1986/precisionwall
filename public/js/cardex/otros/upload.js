function fileinput_images(id, input, type, table) {
    console.log('run')
    var $el1 = $(`#${input}`);
    var imagenes = function() {
        var tmp = null;
        $.ajax({
            url: `${base_url}/get_images/${id}/${type}/${table}`,
            dataType: "json",
            async: false,
            success: function(response) {
                tmp = response;
            }
        });
        return tmp;
    }();
    $el1.fileinput({
        theme: "fas",
        allowedFileExtensions: ['jpg', 'png', 'jpeg','csv','pdf'],
        uploadUrl: `${base_url}/upload_image/${id}/${type}/${table}/${input}`,
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
        initialPreview: imagenes.initialPreview,
        initialPreviewConfig: imagenes.initialPreviewConfig

    }).on("filebatchselected", function(event, files) {
        $el1.fileinput("upload");
    }).on("filepredelete", function(jqXHR) {
        var abort = true;
        if (confirm("Are you sure you want to delete this file?")) {
            abort = false;
        }
        return abort;
    }).on('filebatchuploadcomplete', function(event, preview, config, tags, extraData) {
        console.log('File batch upload complete', preview, config, tags, extraData);
    });

    $el1.fileinput({
        initialPreview: [],
        initialPreviewConfig: []
    });
}