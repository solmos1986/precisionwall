

function autoInizializarInputFile(Ped_ID) {
    $(document).ready(function () {
        var imagenes = function () {
            var tmp = null;
            $.ajax({
                url: `${base_url}/sub-order/get-images/${Ped_ID}`,
                dataType: "json",
                async: false,
                success: function (response) {
                    tmp = response;
                }
            });
            return tmp;
        }();
        $('#load_images').html('');
        $('#load_images').html(`
            <p class="ms-directions text-center mb-1">FILES</p>
            <div class="file-loading">
                <input class="recibir" id="recibir-pedido" name="recibir[]"
                    type="file" multiple>
            </div>
        `
        );

        $(`#recibir-pedido`).fileinput({
            theme: "fas",
            allowedFileExtensions: ['jpg', 'png', 'jpeg'],
            uploadUrl: `${base_url}/sub-order/upload-images/${Ped_ID}`,
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

        }).on("filebatchselected", function (event, files) {
            $(`#recibir-pedido`).fileinput("upload");
        }).on("filepredelete", function (jqXHR) {
            var abort = true;
            if (confirm("Are you sure you want to delete this image?")) {
                abort = false;
            }
            return abort;
        });;
    });
}


function autoInizializarInputFileTraking(Ped_ID) {
    console.log('entrada')
    console.log(Ped_ID)
    $(document).ready(function () {
        var imagenes = function () {
            var tmp = null;
            $.ajax({
                url: `${base_url}/sub-order/get-images/${Ped_ID}`,
                dataType: "json",
                async: false,
                success: function (response) {
                    tmp = response;
                }
            });
            return tmp;
        }();
        $('#load_images_traking').html('');
        $('#load_images_traking').html(`
            <p class="ms-directions text-center mb-1">FILES</p>
            <div class="file-loading">
                <input class="recibir" id="recibir-pedido-traking" name="recibir[]"
                    type="file" multiple>
            </div>
        `);

        $(`#recibir-pedido-traking`).fileinput({
            theme: "fas",
            allowedFileExtensions: ['jpg', 'png', 'jpeg'],
            uploadUrl: `${base_url}/sub-order/upload-images/${Ped_ID}`,
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

        }).on("filebatchselected", function (event, files) {
            $(`#recibir-pedido-traking`).fileinput("upload");
        }).on("filepredelete", function (jqXHR) {
            var abort = true;
            if (confirm("Are you sure you want to delete this image?")) {
                abort = false;
            }
            return abort;
        });;
    });
}

function autoAlamacenadoInputFile(inputFile) {
    console.log('kraje', inputFile)
    $(document).ready(function () {
        var imagenes = function () {
            var tmp = null;
            $.ajax({
                url: `${base_url}/sub-order/get-images/${inputFile}`,
                dataType: "json",
                async: false,
                success: function (response) {
                    tmp = response;
                }
            });
            return tmp;
        }();
        $(`#almacenado-${inputFile}`).fileinput({
            theme: "fas",
            allowedFileExtensions: ['jpg', 'png', 'jpeg'],
            uploadUrl: `${base_url}/sub-order/upload-images/${inputFile}`,
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

        }).on("filebatchselected", function (event, files) {
            $(`#almacenado-${inputFile}`).fileinput("upload");
        }).on("filepredelete", function (jqXHR) {
            var abort = true;
            if (confirm("Are you sure you want to delete this image?")) {
                abort = false;
            }
            return abort;
        });;
    });
}