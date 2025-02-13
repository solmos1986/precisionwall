/* modal view pdf */
$(document).on('click', '.descarga_pdf', function () {
    var options = {
        url: `${base_url}/goal-reports/view-pdf?proyectos=${$(this).data('proyecto_id')}&imagen=false`,
        title: 'Preview',
        size: eModal.size.lg,
        buttons: [{
            text: 'ok',
            style: 'info',
            close: true
        }],
    };
    eModal.iframe(options);
});
$(document).on('click', '.descarga_pdf_image', function () {
    var options = {
        url: `${base_url}/goal-reports/view-pdf?proyectos=${$(this).data('proyecto_id')}&imagen=true`,
        title: 'Preview',
        size: eModal.size.lg,
        buttons: [{
            text: 'ok',
            style: 'info',
            close: true
        }],
    };
    eModal.iframe(options);
});
/* modal view pdf */
$("#descarga_pdf").on('click', function (evt) {
    var proyectos_id = [];
    $('.proyectos[type="checkbox"]').each(function () {
        if (this.checked) {
            proyectos_id.push(this.value);
        }
    });
    console.log(proyectos_id)
    if (proyectos_id.length == 0) {
        Swal.fire({
            position: 'center',
            icon: 'error',
            title: 'selection one',
            showConfirmButton: false,
            timer: 1500
        });
    } else {
        var options = {
            url: `${base_url}/goal-reports/view-pdf?proyectos=${proyectos_id}&imagen=false`,
            title: 'Preview',
            size: eModal.size.lg,
            buttons: [{
                text: 'ok',
                style: 'info',
                close: true
            }],
        };
        eModal.iframe(options);
    }
});
$("#descarga_pdf_imagen").on('click', function (evt) {
    var proyectos_id = [];
    $('.proyectos[type="checkbox"]').each(function () {
        if (this.checked) {
            proyectos_id.push(this.value);
        }
    });
    if (proyectos_id.length == 0) {
        Swal.fire({
            position: 'center',
            icon: 'error',
            title: 'selection one',
            showConfirmButton: false,
            timer: 1500
        });
    } else {
        var options = {
            url: `${base_url}/goal-reports/view-pdf?proyectos=${proyectos_id}&imagen=true`,
            title: 'Preview',
            size: eModal.size.lg,
            buttons: [{
                text: 'ok',
                style: 'info',
                close: true
            }],
        };
        eModal.iframe(options);
    }
});
/*
*descarga excel
 */
$("#descarga_excel").on('click', function (evt) {
    var proyectos_id = [];
    $('.proyectos[type="checkbox"]').each(function () {
        if (this.checked) {
            proyectos_id.push(this.value);
        }
    });
    if (proyectos_id.length == 0) {
        Swal.fire({
            position: 'center',
            icon: 'error',
            title: 'selection one',
            showConfirmButton: false,
            timer: 1500
        });
    } else {
        $('#descargar_excel').attr("action", `${base_url}/goal-reports/view-excel?proyectos=${proyectos_id}&imagen=false`);
        $("#descargar_excel").submit();
    }
});

$(document).on('click', '.descarga_pdf_multiple', function () {
    var proyectos_id = [];
    $('.proyectos[type="checkbox"]').each(function () {
        if (this.checked) {
            proyectos_id.push(this.value);
        }
    });
    if (proyectos_id.length == 0) {
        Swal.fire({
            position: 'center',
            icon: 'error',
            title: 'selection one',
            showConfirmButton: false,
            timer: 1500
        });
    } else {
        $('#descargar_excel').attr("action", `${base_url}/goal-reports/view-all-pdf?proyectos=${proyectos_id}&imagen=false`);
        $("#descargar_excel").submit();
    }
});


$(document).on('click', '.descarga_pdf_image_multiple', function () {
    var proyectos_id = [];
    $('.proyectos[type="checkbox"]').each(function () {
        if (this.checked) {
            proyectos_id.push(this.value);
        }
    });
    if (proyectos_id.length == 0) {
        Swal.fire({
            position: 'center',
            icon: 'error',
            title: 'selection one',
            showConfirmButton: false,
            timer: 1500
        });
    } else {
        $('#descargar_excel').attr("action", `${base_url}/goal-reports/view-all-pdf-images?proyectos=${proyectos_id}&imagen=true`);
        $("#descargar_excel").submit();
    }
});

