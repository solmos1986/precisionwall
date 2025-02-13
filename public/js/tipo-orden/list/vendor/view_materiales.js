//global
var new_table_materiales=$('#list-materiales').DataTable().clear();
var edit_table_materiales=$('#edit-list-materiales').DataTable().clear();

$(document).on("click", "#view_new_materiales", function () {
    if ($('#ocultar_new_materiales').is(":visible")) {
        $('#ocultar_new_materiales').hide()
    } else {
        $('#ocultar_new_materiales').show();
        new_table_materiales.destroy();
        new_table_materiales = $('#new-list-materiales').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: `${base_url}/materials/data-table?proyecto=${$('#new_proyecto').val()}&material=${$('#new_material').val()}`,
            order: [],
            columns: [
                { data: "Denominacion", name: "Denominacion" },
                { data: "Unidad_Medida", name: "Unidad_Medida" },
                { data: "proyecto_nombre", name: "proyecto_nombre" },
                { data: "total_ordenada", name: "total_ordenada" },
                { data: "total", name: "total" },
                { data: "ubicacion_proyecto", name: "ubicacion_proyecto" },
            ],
        });
    }
    var i = $(this).find('i');
    /* console.log(i.hasClass('fa-eye-slash') ); */
    i.attr('class', i.hasClass('fa-eye-slash') ? 'fas fa-eye' : 'fas fa-eye-slash');
});

 $('#ocultar_new_materiales').hide();

$('#new_proyecto, #new_material').change(function () {
    new_table_materiales.ajax.url(`${base_url}/materials/data-table?proyecto=${$('#new_proyecto').val()}&material=${$('#new_material').val()}`).load();
    var rows = new_table_materiales.rows().data().toArray();
});
$('#new_refresh').click(function (e) {
    $("#new_proyecto").val("");
    $("#new_material").val("");
    new_table_materiales.ajax.url(`${base_url}/materials/data-table`).load();
});

/* table edit */
$(document).on("click", "#view_edit_materiales", function () {
    if ($('#ocultar_edit_materiales').is(":visible")) {
        $('#ocultar_edit_materiales').hide()
    } else {
        $('#ocultar_edit_materiales').show();
    }
    edit_table_materiales.destroy();
    edit_table_materiales = $('#edit-list-materiales').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: `${base_url}/materials/data-table?proyecto=${$('#edit_proyecto').val()}&material=${$('#edit_material').val()}`,
        order: [],
        columns: [
            { data: "Denominacion", name: "Denominacion" },
            { data: "Unidad_Medida", name: "Unidad_Medida" },
            { data: "proyecto_nombre", name: "proyecto_nombre" },
            { data: "total_ordenada", name: "total_ordenada" },
            { data: "total", name: "total" },
            { data: "ubicacion_proyecto", name: "ubicacion_proyecto" },
        ],
    });
    var i = $(this).find('i');
    i.attr('class', i.hasClass('fa-eye-slash') ? 'fas fa-eye' : 'fas fa-eye-slash');
});

$('#ocultar_edit_materiales').hide()

$('#edit_proyecto, #edit_material').change(function () {
    edit_table_materiales.ajax.url(`${base_url}/materials/data-table?proyecto=${$('#edit_proyecto').val()}&material=${$('#edit_material').val()}`).load();
    var rows = edit_table_materiales.rows().data().toArray();
});
$('#edit_refresh').click(function (e) {
    $("#edit_proyecto").val("");
    $("#edit_material").val("");
    edit_table_materiales.ajax.url(`${base_url}/materials/data-table`).load();
});
/* table create orden */
var create_orden_table_materiales = $('#create-orden-list-materiales').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    ajax: `${base_url}/materials/data-table`,
    order: [],
    columns: [
        { data: "Denominacion", name: "Denominacion" },
        { data: "Unidad_Medida", name: "Unidad_Medida" },
        { data: "proyecto_nombre", name: "proyecto_nombre" },
        { data: "total_ordenada", name: "total_ordenada" },
        { data: "total", name: "total" },
        { data: "ubicacion_proyecto", name: "ubicacion_proyecto" },
    ],
    columnDefs: [
        {
            width: "100px",
            targets: 3
        },
    ],
});
$('#create_orden_proyecto, #create_orden_material').change(function () {
    create_orden_table_materiales.ajax.url(`${base_url}/materials/data-table?proyecto=${$('#create_orden_proyecto').val()}&material=${$('#create_orden_material').val()}`).load();
    var rows = create_orden_table_materiales.rows().data().toArray();
});
$('#create_orden_refresh').click(function (e) {
    $("#create_orden_proyecto").val("");
    $("#create_orden_material").val("");
    create_orden_table_materiales.ajax.url(`${base_url}/materials/data-table`).load();
});