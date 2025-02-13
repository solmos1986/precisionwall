//images
function fileinput_images(id, input, type, table) {
    var imagenes = function () {
        var tmp = null;
        $.ajax({
            url: `${base_url}/daily-report-detail/image/${id}/get`,
            dataType: "json",
            async: false,
            success: function (response) {
                tmp = response;
            }
        });
        return tmp;
    }();

    var $el1 = $(`.images`).each(function (i, data) {

        let referencia = $(data).data('referencia');
        let images = [];
        let imagenConfig = [];

        console.log(imagenes)

        if (imagenes.legth > 0 || imagenes.initialPreviewConfig) {
            imagenes.initialPreviewConfig.forEach(data => {
                if (data.referencia == referencia) {
                    images.push(data.downloadUrl)
                    imagenConfig.push(data);
                }
            });
        }

        $(data).fileinput({
            theme: "fas",
            allowedFileExtensions: ['jpg', 'png', 'jpeg'],
            uploadUrl: `${base_url}/daily-report-detail/image/${id}/${referencia}/upload`,
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
            initialPreview: images,
            initialPreviewConfig: imagenConfig

        }).on("filebatchselected", function (event, files) {
            console.log(data)
            $(data).fileinput("upload");
        }).on("filepredelete", function (jqXHR) {
            var abort = true;
            if (confirm("Are you sure you want to delete this image?")) {
                abort = false;
            }
            return abort;
        });
    });
}
