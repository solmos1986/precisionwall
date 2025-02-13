function upload_datatable_break_down(imports, total) {
    $('#load-data-thead').html('');
    $('#load-data-thead').append(header_break_down());
    $('#load-data-tbody').html('');
    tbodyHTML = ``;
    imports.forEach((area, index) => {
        detailHTML = ``;
        if (area.superficies) {
            nombre_descripcion = ``;
            //superficies
            area.superficies.forEach(superficie => {
                superficie.tareas.forEach(tarea => {
                    metodosHTML = ``;
                    tarea.metodos.forEach(metodo => {
                        metodosHTML += `
                        <option value="${metodo.id}" ${metodo.id == tarea.estimado_metodo_id ? 'selected' : ''}>${metodo.nombre}</option>`;
                    });
                    detailHTML += `
                        <tr style="border-top: 1px solid #ffffff; background-color: ${verificar_procedimiento(superficie.procedimiento, superficie.miselaneo)};" data-estimado_use_metodo_id="${superficie.id}">
                            <td></td>
                            <td></td>
                            <td>${superficie.cost_code}</td>
                            <td colspan='1'>
                            ${superficie.cc_descripcion}
                            </td>
                            <td>${superficie.cc_butdget_qty}</td>
                            <td>${superficie.um}</td>
                            <td>${superficie.of_coast}</td>
                            <td>${superficie.pwt_prod_rate}</td>
                            <td>${superficie.estimate_hours}</td>
                            <td>${superficie.estimate_labor_cost}</td>
                            <td>${superficie.material_or_equipment_unit_cost}</td>
                            <td>${superficie.material_spread_rate_per_unit}</td>
                            <td>${superficie.mat_qty_or_galon}</td>
                            <td>${superficie.mat_um}</td> 
                            <td>${superficie.material_cost}</td>
                            <td>${superficie.price_total}</td>
                            <td>${superficie.buscontract_cost}</td> 
                            <td>${superficie.equipament_cost}</td>      
                            <td>${superficie.other_cost}</td>             
                        </tr>
                    `;
                });
            });
        } else {
            detailHTML += `
        <tr>
                <td> </td>
                <td></td>
                <td></td>
                <td colspan='1'></td>   
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan='5'>
                Data no encontrada
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        `;
        }
        //areass
        tbodyHTML += `
        <tr data-estimado_use_metodo_id="${area.id}">
            <td> 
            </td>
            <td colspan="5">${area.nombre_area}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
            ${detailHTML}
        `;
    });
    var totales = `
    <tr style="background:#fafbe3;">
        <td><strong>TOTAL:</strong></td>
        <td></td>
        <td></td>
        <td>Total cost: $${total.total_cost}</td>
        <td>Mark Up:${(total.mark_up)}%</td>
        <td></td>
        <td></td>
        <td></td>
        <td>${total.estimated_hours}</td>
        <td>${total.estimated_labor_hours}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>${total.material_cost}</td>
        <td>${total.price_total}</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    `;

    $('#load-data-tbody').append(totales + tbodyHTML);
}
$(document).on("click", "#view_brake_down", function () {
    $('#nombre_report_view').text('');
    $('#nombre_report_view').text('Report Break Down');
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/final-brake-down`,
        dataType: 'json',
        data:{
            'proyecto_id':$(this).data('proyecto_id'),
            'floor_id':$(this).data('floor_id')
        },
        async: true,
        success: function (response) {
            upload_datatable_break_down(response.data.imports, response.data.totales)
        }
    });
    console.log($(this).data('proyecto_id'),$(this).data('floor_id'));
});
function verificar_procedimiento(procedimiento, miselaneo) {
    var color;
    if (miselaneo == 'y') {
        color = '#f4f5d5';
    }
    else {
        color = '#f1f5fc';
    }
    switch (procedimiento) {
        case 'Only Material':
            color = '#b9f1ff';
            break;
        case 'Only Installation':
            color = '#b9f1ff';
            break;
        default:
            color = '#f1f5fc';
            break;
    }
    return color;
}
function header_break_down() {
    return header=`
        <tr>
            <th>&nbsp;&nbsp;&nbsp;&nbsp;ACTIONS&nbsp;&nbsp;&nbsp;&nbsp;</th>
            <th>AREA</th>
            <th>COST CODE</th>
            <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DESCRIPTION&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
            <th>CC&nbsp;BUTGET&nbsp;QTY</th>
            <th>&nbsp;UM&nbsp;</th>
            <th>OF COATS</th>
            <th>PWT PROD RATE</th>
            <th>ESTIMATED HOURS</th>
            <th>ESTIMATED LABOR COST </th>
            <th>MATERIAL OR EQUIPMENT UNIT COST</th>
            <th>MATERIAL SPREAD RATE PER UNIT</th>
            <th>MAT QTY OR GALLONS / UNIT</th>
            <th>MAT UM</th>
            <th>MATERIAL COST</th>
            <th>PRICE TOTAL</th>
            <th>SUBCONTRACT COST</th>
            <th>EQUIPMENT COST</th>
            <th>OTHER COST</th>
        </tr>
    `;
}