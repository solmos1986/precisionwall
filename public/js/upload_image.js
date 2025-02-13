function fileinput_images(id, input, type, table) {
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
        allowedFileExtensions: ['jpg', 'png', 'jpeg','heic'],
        uploadUrl: `${base_url}/upload_image/${id}/${type}/${table}/${input}`,
        uploadAsync: true,
        showUpload: false,
        overwriteInitial: false,
        minFileCount: 1,
        maxFileCount: 8,
        browseOnZoneClick: true,
        initialPreviewAsData: true,
        showRemove: false,
        showClose: false,
        browseClass: "btn btn-sm btn-success",
        initialPreview: imagenes.initialPreview,
        initialPreviewConfig: imagenes.initialPreviewConfig

    }).on("filebatchselected", function(event, files) {
        $el1.fileinput("upload");
    }).on("filepredelete", function(jqXHR) {
        var abort = true;
        if (confirm("Are you sure you want to delete this image?")) {
            abort = false;
        }
        return abort;
    });
}