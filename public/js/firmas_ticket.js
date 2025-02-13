var wrapper = document.getElementById("signature-pad");
var canvas = wrapper.querySelector("canvas");

function resizeCanvas() {
    var ratio = Math.max(window.devicePixelRatio || 1, 1);

    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext("2d").scale(ratio, ratio);
}
window.onresize = resizeCanvas;
var signaturePad;

$(document).on('click', '.signature', function () {
    $('#modal').modal('show');
    $('.modal-title').text($(this).data("title"));
    $("#id_signature").val($(this).data("id_img"));
    $("#id_signature_input").val($(this).data("id_img_input"));
    $("#id_text").val($(this).data("input_text"));
    $("#name_signature").val(null);
    resizeCanvas();
    signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255, 255, 255)', // necessary for saving image as JPEG; can be removed is only saving as PNG or SVG
        penColor: 'rgb(0, 0, 0)'
    });
});
$(document).on('click', '#guardar_firma', function () {
    if (signaturePad.isEmpty()) {
        return alert("Please provide a signature first.");
    }
    var data = signaturePad.toDataURL('image/jpeg');
    console.log(data);
    $(`#${$("#id_signature").val()}`).attr('src', data);
    $(`#${$("#id_signature_input").val()}`).val(data);

    console.log($("#id_text").val());

    if ($(`#${$("#id_text").val()}`).val() == '') {
        $(`#${$("#id_text").val()}`).val($("#name_signature").val());
    }
    $("#modal").modal("hide");
});
$(document).on('click', '#limpiar', function () {
    signaturePad.clear();
});