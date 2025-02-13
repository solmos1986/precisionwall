<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/invoice.css') }}" media="all" />
    <link href="{{ asset('css/tokenfield-typeahead.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-tokenfield.min.css') }}" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
    <title>Work Ticket #{{ $ticket->num }}</title>
    <style>
        #signature-pad {
            min-height: 200px;
            border: 1px solid #000;
        }

        #signature-pad canvas {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: #fff;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<style>
    /*flexbox*/
    .box {
        display: flex;
    }

    .push {
        margin-left: auto;
    }

    /*<a>*/
    .not-active {
        pointer-events: none;
        cursor: default;
    }
</style>

<body>
    <div class="page">
        <div class="box">
            <div>
                <a type="button" class="btn btn-pill btn-primary btn-sm m-1" id="preview">Preview</a>
            </div>
            <div class="push">
                <a type="button" class="btn btn-pill btn-primary btn-sm m-1" id="next">Next</a>
            </div>
        </div>
        <header class="clearfix_in">
            <!--header css-->
            <div id="logo" style="float: left;">
                <img src="{{ asset('img/logo.png') }}">
            </div>
            <div id="logo" style="float: right">
                <!--div><strong>Date: </strong>{{ date('m-d-Y', strtotime(date('Y-m-d'))) }}</div-->
            </div>
            <br><br><br>
            <!--end header css-->
            <h1>Work Ticket #{{ $ticket->num }}</h1>
            <div id="company" class="clearfix_in">
                <div><span>Ticket Number:</span>{{ $ticket->num }}</div>
                <div><span>DATE OF WORK</span>
                    @if ($ticket->fecha_ticket)
                        {{ date('m-d-Y', strtotime($ticket->fecha_ticket)) }}
                    @endif
                </div>
                <div><span>FOREMAN NAME</span> {{ $ticket->foreman_name }}</div>
                <div><span>SUPERINTENDENT'S NAME</span> {{ $ticket->superintendent_name }}</div>
                <div><span>SCHEDULE HOURS</span> {{ $ticket->horario }}</div>
            </div>
            <div id="project">
                <div><span>GENERAL CONTRACTOR</span> {{ $ticket->empresa }}</div>
                <div><span>PRECISION WALL TECH PROJECT</span> {{ $ticket->Codigo }}</div>
                <div><span>PROJECT NAME</span> {{ $ticket->Nombre }}</div>
                <div><span>PROJECT ADDRESS</span> {{ $address }}</div>
            </div>
        </header>
        <!--
        <p>Total hours before this ticket</p>
        <p>Total Regular hours: {{ $t_reg_hours }} | Total Premium Hours: {{ $t_premium_hours }} | Total Over Time
            Hours: {{ $t_out_hours }} | Total Allowance Hours: {{ $t_prepaid_hours }}</p>-->
        <main>
            <table>
                <thead>
                    <tr>
                        <th class="desc">DESCRIPTION</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="desc">{!! str_replace("\n", '<br>', $ticket->descripcion) !!}</td>
                    </tr>
                </tbody>
            </table>
            <table>
                <thead>
                    <tr>
                        <th>QUANTITY</th>
                        <th>UNIT</th>
                        <th class="desc">MATERIAL AND/OR EQUIPMENT</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($materiales as $val)
                        <tr>
                            <td class="qty">{{ $val->cantidad }}</td>
                            <td class="qty">{{ $val->Unidad_Medida }}</td>
                            <td class="desc">{{ $val->Denominacion }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="total">I don't add anything</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <table>
                <thead>
                    <tr>
                        <th>N° workers</th>
                        <th class="service">Position</th>
                        <th>Reg Hrs</th>
                        <th>T. Reg<br>Hrs</th>
                        <th>Premium<br>Hrs</th>
                        <th>T. Premium<br>Hrs</th>
                        <th>Over<br>Time<br>Hours</th>
                        <th>Total<br>Over Time<br>Hours</th>
                        <th>Allowance<br>Hours</th>
                        <th>Total<br>Allowance<br>Hours</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($trabajadores as $val)
                        <tr>
                            <td class="qty">{{ $val->n_worker }}</td>
                            <td class="desc">{{ $val->nombre }}</td>
                            <td class="total">{{ $val->reg_hours }}</td>
                            <td class="total">{{ $val->reg_hours * $val->n_worker }}</td>
                            <td class="total">{{ $val->premium_hours }}</td>
                            <td class="total">{{ $val->premium_hours * $val->n_worker }}</td>
                            <td class="total">{{ $val->out_hours }}</td>
                            <td class="total">{{ $val->out_hours * $val->n_worker }}</td>
                            <td class="total">{{ $val->prepaid_hours }}</td>
                            <td class="total">{{ $val->prepaid_hours * $val->n_worker }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="total">I don't add anything</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="notice"> Date signed and submitted:
                @if ($ticket->fecha_finalizado)
                    {{ date('m-d-Y', strtotime($ticket->fecha_finalizado)) }}
                @endif
            </div>
            <div id="notices">
                <div>NOTICE:</div>
                <div class="notice">Signer verifies Precision Wall Tech, has completed the work stated above
                    under my
                    supervision. Time
                    and material listed above are accurate and approved</div>
            </div>
            <div class="cols">
                <div style="text-align: center;">
                    @if ($ticket->firma_foreman)
                        <img style="width: 2.5cm" src="{{ asset('signatures/empleoye/' . $ticket->firma_foreman) }}"
                            id="img_signature_fore">
                    @else
                        <img style="width: 2.5cm" src="{{ asset('signatures/no-signature.jpg') }}"
                            id="img_signature_fore">
                    @endif
                    <p>Foreman Signature</p>
                    <p>{{ $ticket->foreman_name }}</p>
                    <button class="btn btn-pill btn-primary mt-0 btn-sm signature" data-title="Foreman Signature"
                        data-table="empleoye" data-id_img="img_signature_fore"
                        data-id_img_input="input_signature_fore">Add Signature</button>
                </div>
                <div style="text-align: center;">
                    @if ($ticket->firma_cliente)
                        <img style="width: 2.5cm" src="{{ asset('signatures/client/' . $ticket->firma_cliente) }}"
                            id="img_signature_super">
                    @else
                        <img style="width: 2.5cm" src="{{ asset('signatures/no-signature.jpg') }}"
                            id="img_signature_super">
                    @endif
                    <p>Superintendent's Signature</p>
                    <p>{{ $ticket->superintendent_name }}</p>
                    <button class="btn btn-pill btn-primary mt-0 btn-sm signature"
                        data-title="Surperintendent's Signature" data-table="client" data-id_img="img_signature_super"
                        data-id_img_input="input_signature_super">Add Signature</button>
                </div>
                <div style="text-align: center;">
                    <a href="{{ route('listar.mis.tickets') }}" class="btn btn-pill btn-primary mt-0 btn-sm">Go
                        back</a><br>
                    <button class="btn btn-pill btn-primary mt-2 btn-sm send-mail">Send by e-mail</button>
                </div>
            </div>
    </div>
    </main>
    </div>
    <div class="page">
        <h5 style="text-align:center">PREVIOUS PICTURES</h5>
        <div class="row">
            @forelse ($img_start as $val)
                <div class="column">
                    <img src='{{ asset("uploads/$val->imagen") }}'>
                </div>
            @empty
                <h6 style="text-align:center">There is nothing inserted</h6>
            @endforelse
        </div>
    </div>
    <div class="page">
        <h5 style="text-align:center">FINAL PICTURES</h5>
        <div class="row">
            @forelse ($img_final as $val)
                <div class="column">
                    <img src='{{ asset("uploads/$val->imagen") }}'>
                </div>
            @empty
                <h6 style="text-align:center">There is nothing inserted</h6>
            @endforelse
        </div>
    </div>
    <div id="modal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-dark">
                    <input type="hidden" id="id_signature">
                    <input type="hidden" id="id_signature_input">
                    <input type="hidden" id="signature_table">
                    <div id="signature-pad"><canvas style="border:1px solid #000" id="sign"></canvas></div>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control form-control-sm" id="name_signature"
                        placeholder="Please enter your name">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" id="guardar_firma">Save Signature</button>
                    <button type="button" class="btn btn-success btn-sm" id="limpiar">Clear</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <x-components.mail-modal title="tickets" />

    <script src="{{ asset('assets/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-tokenfield.min.js') }}" charset="UTF-8"></script>
    <script type="text/javascript" src="{{ asset('js/typeahead.bundle.min.js') }}" charset="UTF-8"></script>
    <script src="{{ asset('js/taginput_custom.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script>
        var base_url = "{{ url('/') }}";

        function getParameterByName(name) {
            name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                results = regex.exec(location.search);
            return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
    </script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var wrapper = document.getElementById("signature-pad");
        var canvas = wrapper.querySelector("canvas");
        var signaturePad;

        function resizeCanvas() {
            var ratio = Math.max(window.devicePixelRatio || 1, 1);

            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
        }
        window.onresize = resizeCanvas;
        $(document).on('click', '.signature', function() {
            $('#modal').modal('show');
            $("#mailModal").removeAttr("tabindex");
            $('.modal-title').text($(this).data("title"));
            $("#id_signature").val($(this).data("id_img"))
            $("#id_signature_input").val($(this).data("id_img_input"))
            $("#signature_table").val($(this).data("table"))
            resizeCanvas();
            signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)', // necessary for saving image as JPEG; can be removed is only saving as PNG or SVG
                penColor: 'rgb(0, 0, 0)'
            });
        });
        $(document).on('click', '#guardar_firma', function() {
            if (signaturePad.isEmpty()) {
                return alert("Please provide a signature first.");
            }
            var data = signaturePad.toDataURL('image/jpeg');
            $(`#${$("#id_signature_input").val()}`).val(data);
            var type = $(`#signature_table`).val();
            var nombre = $('#name_signature').val();
            $.ajax({
                type: "POST",
                url: "{{ route('update_signature.ticket', ['id' => $id]) }}",
                data: {
                    signature: data,
                    type: type,
                    nombre: nombre
                },
                dataType: "json",
                success: function(response) {
                    location.reload();
                    $("#modal").modal("hide");
                }
            });
        });
        $(document).on('click', '#limpiar', function() {
            signaturePad.clear();
        });

        $(document).on('click', '.send-mail', function() {
            $("#mailModal").removeAttr("tabindex");
            $('#mailModal #to').attr('name', 'to').tokenfield('setTokens', []);
            $('#mailModal #cc').attr('name', 'cc').tokenfield('setTokens', []);
            var $icon = $(this);
            var $button = $("#send_mail");
            $button.html("Send Mail");
            $button.prop("disabled", false);

            $.ajax({
                url: `{{ url('/') }}/get_config_mail/{{ $ticket->proyecto_id }}/ticket`,
                dataType: "json",
                async: false,
                success: function(response) {
                    $("#mailModal #title_m").attr('name', 'title_m').val(
                        `${response.config.title_ticket_email} {{ $ticket->subempresa }}`
                    );
                    $("#mailModal #body_m").attr('name', 'body_m').text(response.config
                        .body_ticket_email);
                    $("#mailModal #row_id").val({{ $ticket->ticket_id }});
                    var emails = [];
                    $.each(response.email_contac, function(index, value) {
                        emails.push(value.email);
                    });
                    emails.push(response.emails.email_contac);
                    emails.push(response.emails.Coordinador_Obra_mail);
                    emails.push(response.emails.Lead_mail);
                    emails.push(response.emails.Pwtsuper_mail);
                    emails.push(response.emails.Foreman_mail);
                    
                    data = emails.filter(function(element) {
                        return element !== undefined && element !== null;
                    });

                    $('#to').attr('name', 'to').tokenfield('setTokens', data);
                    $('#cc').attr('name', 'cc').tokenfield('setTokens', []);
                }
            });
            $("#mailModal").modal("show");
        });

        $(document).on('click', '#send_mail', function() {
            var $button = $(this);
            var $button = $("#send_mail");
            $button.html("Wait.....");
            $button.prop("disabled", true);
            $.ajax({
                type: "post",
                url: `{{ url('/') }}/send/${$("#mailModal #row_id").val()}/ticket`,
                data: $("#mailModal #mail").serialize(),
                dataType: "json",
                success: function(data) {
                    var html = '';

                    if (data.errors) {
                        data.errors.forEach(function(error) {
                            alert(error);
                        });
                        html += '</div>';
                        $('#form_result').html(html);
                        $button.html("Send Mail");
                        $button.prop("disabled", false);
                    }
                    if (data.success) {
                        alert(data.success);
                        $('#mailModal #mail').trigger("reset");
                        $('#mailModal').modal('hide');
                        $button.html("Send Mail");
                    }
                },
                fail: function(xhr, textStatus, errorThrown) {
                    alert('ocurrio un error en la peticion por favor actualize la pagina');
                    $button.html("Send Mail");
                    $button.prop("disabled", false);
                }
            });
        });
        $(document).ready(function() {
            var tickets = getParameterByName('tickets').split(',');
            var view = getParameterByName('view');
            tickets.forEach(function(val, index) {
                if (val == view) {
                    if (index == 0) {
                        $('#preview').hide();
                        $('#next').attr('href',
                            `${base_url}/show-btn/ticket?tickets=${tickets}&view=${tickets[index+1]}`);
                    }
                    if (index == (tickets.length - 1)) {
                        $('#next').hide();
                        $('#preview').attr('href',
                            `${base_url}/show-btn/ticket?tickets=${tickets}&view=${tickets[index-1]}`);
                    }
                    $('#next').attr('href',
                        `${base_url}/show-btn/ticket?tickets=${tickets}&view=${tickets[index+1]}`);
                    $('#preview').attr('href',
                        `${base_url}/show-btn/ticket?tickets=${tickets}&view=${tickets[index-1]}`);
                }
            });

        });
        $("#all_email_to").select2({
                theme: "bootstrap4",
                width: '100%',
                ajax: {
                    url: `${base_url}/get-all-email/{{ $ticket->proyecto_id }}`,
                    type: "GET",
                    dataType: "json",
                    delay: 250,
                    data: function(params) {
                        return {
                            searchTerm: params.term, // search term
                        };
                    },
                    processResults: function(response) {
                        return {
                            results: response,
                        };
                    },
                    cache: true,
                },
            })
            .on("select2:select", function(e) {
                data.push(e.params.data.email)
                $('#to').tokenfield('setTokens', data);
                $("#all_email_to").val('').change();
                $("#to").trigger("enterKey");
                $('#to').focus();
            });

        let cc=[];
        $("#all_email_cc").select2({
                theme: "bootstrap4",
                width: '100%',
                ajax: {
                    url: `${base_url}/get-all-email/{{ $ticket->proyecto_id }}`,
                    type: "GET",
                    dataType: "json",
                    delay: 250,
                    data: function(params) {
                        return {
                            searchTerm: params.term, // search term
                        };
                    },
                    processResults: function(response) {
                        return {
                            results: response,
                        };
                    },
                    cache: true,
                },
            })
            .on("select2:select", function(e) {
                cc.push(e.params.data.email)
                $('#cc').tokenfield('setTokens', cc);
                $("#all_email_cc").val('').change();
                $("#cc").trigger("enterKey");
                $('#cc').focus();
            });
    </script>
</body>

</html>
