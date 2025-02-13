//pre renderizacion de  firmas
var wrapper1 = document.getElementById("signature-pad-install"),
	canvas1 = wrapper1.querySelector("canvas"),
	signaturePad1;

var wrapper2 = document.getElementById("signature-pad-foreman"),
	canvas2 = wrapper2.querySelector("canvas"),
	signaturePad2;


function resizeCanvas(canvas) {
	var ratio = 1;
	canvas.width = canvas.offsetWidth * ratio;
	canvas.height = canvas.offsetHeight * ratio;
	canvas.getContext("2d").scale(ratio, ratio);
}

/*Modales personal */
$(document).on('click', '.show_detail', function () {
	var id = $(this).data('id');
	$("#formModalShowOrder #show_order").trigger('reset');
	$('#table-material tbody').html("");

	$.ajax({
		type: 'GET',
		url: `${base_url}/order-delivery/show-detail/${id}`,
		dataType: 'json',
		success: function (data) {
			$('#address').val(`${data.address}`);
			$(`#status option`).each(function () {
				$(this).attr({ selected: false })
			});
			$(`#status option[value="${data.estatus_id}"]`).attr("selected", true);
			$('#tipo_tranferencia_envio_id').val(data.id);
			$('#order_id').val(data.order_id);
			$('#name_proyect').val(data.Nombre);
			$('#job_name').val(data.nombre_trabajo);
			$('#sub_contractor').val(data.empresa);
			$('#sub_empleoye_id').val(data.nombre_delivery);
			$('#pco_pedido').val(data.pco_pedido);
			$('#date_work').val(data.fecha_actividad);
			$('#created_by').val(data.creator);
			$("#formModalShowOrder .modal-title").text(`Show order #${data.num}`);
			$("#formModalShowOrder").modal("show");
			var trHTML = '';
			data.materiales.forEach(materiales => {
				var check = `<td>
				<input type="checkbox" name="entregado[]" value="${materiales.id}" checked disabled>
				</td>`;
				if (materiales.entregado == "no") {
					check = `<td>
					<input type="checkbox" name="entregado[]" value="${materiales.id} ">
					</td>`
				}
				trHTML +=
					`<tr>
                    <td>${materiales.Denominacion}</td>
					<td>${materiales.Unidad_Medida}</td>
					<td>${materiales.cant_ordenada}</td>
					<td>${materiales.cant_ordenada}</td>
                </tr>`;
			});

			//carga firmas
			console.log(data.firma_entrega, data.firma_foreman)
			if (data.firma_entrega != null && data.firma_foreman != null) {
				$("#show_signature-pad-install").attr("src", `${base_url}/signatures/install/${data.firma_entrega}`);
				$("#show_signature-pad-install").show();
				$("#show_signature-pad-foreman").attr("src", `${base_url}/signatures/install/${data.firma_foreman}`);
				$("#show_signature-pad-foreman").show();
				$("#signature-pad-install").hide();
				$("#signature-pad-foreman").hide();
				$("#limpiar_install").hide();
				$("#limpiar_foreman").hide();
			} else {

				$("#show_signature-pad-foreman").hide();
				$("#show_signature-pad-install").hide();

				$("#signature-pad-install").show();
				$("#signature-pad-foreman").show();
				$("#limpiar_install").show();
				$("#limpiar_foreman").show();
				resizeCanvas(canvas1);
				signaturePad1 = new SignaturePad(canvas1, {
					backgroundColor: 'rgb(255, 255, 255)', // necessary for saving image as JPEG; can be removed is only saving as PNG or SVG
					penColor: 'rgb(0, 0, 0)'
				});

				resizeCanvas(canvas2);
				signaturePad2 = new SignaturePad(canvas2, {
					backgroundColor: 'rgb(255, 255, 255)', // necessary for saving image as JPEG; can be removed is only saving as PNG or SVG
					penColor: 'rgb(0, 0, 0)'
				});

			}
			$('#table-material tbody').append(trHTML);
		},
	});

});

$(document).on('click', '#limpiar_install', function () {
	signaturePad1.clear();

});
$(document).on('click', '#limpiar_foreman', function () {
	signaturePad2.clear();

});


$(document).on('click', '.save_order_detail', function () {
	try {
		var data1 = signaturePad1.toDataURL('image/jpeg');
		$("#signature_install").val(data1);
		var data2 = signaturePad2.toDataURL('image/jpeg');
		$("#signature_foreman").val(data2);
	} catch (error) {
	}
	$("#fecha").val(moment().format('MM/DD/YYYY HH:mm:ss'));
	$.ajax({
		type: 'PUT',
		url: `${base_url}/order-delivery/update-detail/${$('#tipo_tranferencia_envio_id').val()}`,
		data: $('#formModalShowOrder #show_order_detail').serialize(),
		dataType: 'json',
		success: function (data) {
			if (data.status == 'errors') {
				$alert = "";
				data.message.forEach(function (error) {
					$alert += `* ${error}<br>`;
				});
				Swal.fire({
					icon: 'error',
					title: 'complete the following fields to continue:',
					html: $alert,
				})
			}
			if (data.status == 'ok') {
				Swal.fire(data.message, '', 'success').then((result) => {
					$("#formModalShowOrder").modal("hide");
					$("#signature_install").val('');
					$("#signature_foreman").val('');
					//table.draw();
					$('#ordenes').html('');
					load_ordenes();
					$('#formModalMovimientosProveedor').modal("hide");
				});
			}
		},
	});
});
function load_ordenes() {
	$.ajax({
		type: 'POST',
		url: `${base_url}/order-delivery/order-list?from_date=${$('#from_date').val()}&to_date=${$('#to_date').val()}&status=${$('#status').val()}`,
		dataType: 'json',
		data: { fecha: moment().format('MM/DD/YYYY HH:mm:ss') },
		success: function (data) {
			$('#ordenes').html('');
			data.forEach(orden => {
				var estatus = `
						<div class="p-2" >${orden.color}</div>
						<div class="ml-auto p-2" >${orden.fecha_actividad}</div>
						`;
				var cards = `
						<div class="col-lg-6 col-md-6 col-sm-12">
							<div class="ms-card">
								<div class="ms-card-header d-flex">
									${estatus}
								</div>
								<div class="ms-card-body">
									<h6>${orden.nombre_trabajo} <strong>Order #</strong>${orden.num}</h6>
									<h6><strong>PO:</strong>${orden.po}</h6>
									<p><strong>Address: </strong>${orden.address}</p>
									<p><strong>Note: </strong>${orden.nota}</p>
								</div>
								<div class="ms-card-footer text-disabled d-flex">
									<div class="ms-card-options">
									<button class="btn btn-pill mt-0 btn-success inline btn-sm has-icon entregado" ${orden.estatus_id == 6 ? 'disabled' : ''} data-id="${orden.id}" >
											<i class="flaticon-pencil">
											</i>
											Record Delivery Completed
										</button>	
									</div>
									<div class="ms-card-options">
										<button class="btn btn-pill mt-0 btn-primary inline btn-sm has-icon show_detail" data-id="${orden.pedido_id}" >
											<i class="flaticon-list">
											</i>
											Detail 
										</button>
									</div>
								</div>
							</div>
						</div>
					`;
				$('#ordenes').append(cards);
			});
		},
	});
}

$(document).on('click', '.entregado', function () {
	Swal.fire({
		title: 'Are you sure to record as delivered?',
		confirmButtonText: 'Save',
	}).then((result) => {
		/* Read more about isConfirmed, isDenied below */
		if (result.value) {
			$.ajax({
				type: 'PUT',
				url: `${base_url}/order-delivery/update-detail-express/${$(this).data('id')}`,
				data: {
					fecha: moment().format('MM/DD/YYYY HH:mm:ss')
				},
				dataType: 'json',
				success: function (data) {
					Swal.fire(data.message, '', 'success').then((result) => {
						$('#ordenes').html('');
						load_ordenes();
					});
				},
			});
		} else {
			Swal.fire('Changes are not saved', '', 'info')
		}
	})
});

$(document).ready(function () {
	$('#from_date').val(moment().subtract(1, 'days').format('MM/DD/YYYY'));
	$('#to_date').val(moment().add(1, 'days').format('MM/DD/YYYY'));
	load_ordenes();
	$('#status').select2();
});

$('#from_date, #to_date, #status').change(function() {
	load_ordenes();
});

$('#buscar').click(function() {
	load_ordenes();
});
