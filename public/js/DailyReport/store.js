$("#enviar").click(function (e) {
    $(this).prop("disabled", true);
    e.preventDefault();
    let options = rastreo();
    resultado = {
        proyecto_id: $('#proyect_id').val(),
        detalle: $('#detalle').val(),
        note: ""
    }
    store(resultado);
});

function rastreo() {
    let resultados = [];
    $('#ociones .option').each(function (i, data) {
        if ($(data).is(':checked')) {
            resultados.push({
                option: $(this).data('value'),
                value: $(this).val(),
                note: ""
            });
        }
    });
    resultados.forEach(resultado => {
        $('#ociones .note').each(function (i, data) {
            console.log(data)
            if ($(data).data('value') == resultado.option) {
                resultado.note = $(data).val();
            }
        });
    });
    return resultados;
}
function store(data) {
    $.ajax({
        type: "POST",
        url: `${base_url}/daily-report-detail/store/${Actividad_ID}`,
        data: data,
        dataType: "json",
        success: function (data) {
            if (data.status == 'success') {

                $("#enviar").prop("disabled", false);
                if (auth == 1) {
                    window.location.href = `${base_url}/info-project`;
                }
                else {
                    $.ajax({
                        type: "POST",
                        url: `${base_url}/logout`,
                        data: data,
                        dataType: "json",
                        success: function (data) {

                        }
                    });
                    window.location.href = `${base_url}`;
                }

            }
        }
    });
}

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
            uploadUrl: `${base_url}/daily-report-detail/image/${id}/upload`,
            uploadAsync: true,
            showUpload: false,
            overwriteInitial: false,
            minFileCount: 1,
            maxFileCount: 8,
            browseOnZoneClick: true,
            initialPreviewAsData: true,
            showRemove: false,
            showClose: false,
            uploadExtraData: { 'referencia': referencia },
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
