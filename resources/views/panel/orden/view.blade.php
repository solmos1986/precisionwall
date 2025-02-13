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

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

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
                <div><strong>Report date: </strong>{{ date('m-d-Y', strtotime(date('Y-m-d'))) }}</div>
            </div>
            <br><br><br>
            <!--end header css-->

            <h1>ORDER WC INSTALLATION #{{ $orden->num }}</h1>
            <div id="company" class="clearfix_in">
                <div><span>ORDER DATE</span>
                    @if ($orden->date_order)
                        {{ date('m-d-Y', strtotime($orden->date_order)) }}
                    @endif
                </div>
                <div><span>DATE SCHEDULE</span>
                    @if ($orden->date_work)
                        {{ date('m-d-Y', strtotime($orden->date_work)) }}
                    @endif
                </div>
                <div><span>NAME BY</span> {{ $orden->creator }}</div>
            </div>
            <div id="project">
                <div><span>JOB NAME</span> {{ $orden->job_name }}</div>
                <div><span>SUB CONTRACTOR</span> {{ $orden->empresa }}</div>
                <div><span>NAME SUB C. EMPLEOYEE</span> {{ $orden->sub_employe }}</div>
            </div>
        </header>
        <main>
            <table>
                <thead>
                    <tr>
                        <th class="desc">MATERIAL</th>
                        <th>QUANTITY <br> ORDERED</th>
                        <th>Q. TO <br>THE JOB SITE</th>
                        <th>QUANTITY<br> INSTALLED</th>
                        <th>DATE<br> INSTALLED</th>
                        <th>Q. REMAINING<br> WC</th>
                        <th>REMAINING <br>WC STORED AT</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($materiales as $val)
                        <tr>
                            <td class="desc">{{ $val->Denominacion }}</td>
                            <td class="qty">{{ $val->q_ordered }}</td>
                            <td class="qty">{{ $val->q_job_site }}</td>
                            <td class="qty">{{ $val->q_installed }}</td>
                            <td class="qty">
                                @if ($val->d_installed)
                                    {{ date('m-d-Y', strtotime($val->d_installed)) }}
                                @endif
                            </td>
                            <td class="qty">{{ $val->q_remaining_wc }}</td>
                            <td class="qty">{{ $val->remaining_wc_stored }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">I don't add anything</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="cols">
                <div style="text-align: center;">
                    <img id="img_signature_super" style="width: 3.5cm"
                        src="{{ $orden->firma_installer ? asset('signatures/install/' . $orden->firma_installer) : asset('signatures/no-signature.jpg') }}">

                    <p>Installer Signature</p>
                    <button class="btn btn-pill btn-primary mt-0 btn-sm signature" data-title="Installer Signature"
                        data-table="installer" data-id_img="img_signature_super"
                        data-id_img_input="input_signature_super">Add Signature</button>
                </div>
                <div style="text-align: center;">
                    <img id="img_signature_fore" style="width: 3.5cm"
                        src="{{ $orden->firma_foreman ? asset('signatures/empleoye/' . $orden->firma_foreman) : asset('signatures/no-signature.jpg') }}">
                    <p>Foreman Signature</p>
                    <button class="btn btn-pill btn-primary mt-0 btn-sm signature" data-title="Foreman Signature"
                        data-table="empleoye" data-id_img="img_signature_fore"
                        data-id_img_input="input_signature_fore">Add Signature</button>
                </div>
                <div style="text-align: center;">
                    <a href="{{ url()->previous() }}" class="btn btn-pill btn-primary mt-0 btn-sm">Go back</a><br>
                    <button class="btn btn-pill btn-primary mt-2 btn-sm send-mail">Send by e-mail</button>
                </div>
            </div>
        </main>
    </div>
    @forelse (array_chunk($img_start, 4) as $image)
        <div class="page">
            <h5 style="text-align:center">STARTUP IMAGES</h5>
            <div class="row">
                @forelse ($image as $val)
                    <div class="column">
                        <img src='{{ asset("uploads/$val->imagen") }}'>
                    </div>
                @empty
                @endforelse
            </div>
        </div>
    @empty
        <div class="page">
            <h5 style="text-align:center">STARTUP IMAGES</h5>
            <h6 style="text-align:center">There is nothing inserted</h6>
        </div>
    @endforelse
    @forelse (array_chunk($img_final, 4) as $image)
        <div class="page">
            <h5 style="text-align:center">FINAL IMAGES</h5>
            <div class="row">
                @forelse ($image as $val)
                    <div class="column">
                        <img src='{{ asset("uploads/$val->imagen") }}'>
                    </div>
                @empty
                @endforelse
            </div>
        </div>
    @empty
        <div class="page">
            <h5 style="text-align:center">FINAL IMAGES</h5>
            <h6 style="text-align:center">There is nothing inserted</h6>
        </div>
    @endforelse

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
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" id="guardar_firma">Save Signature</button>
                    <button type="button" class="btn btn-success btn-sm" id="limpiar">Clear</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <x-components.mail-modal title="orders" />
    <script src="{{ asset('assets/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-tokenfield.min.js') }}" charset="UTF-8"></script>
    <script type="text/javascript" src="{{ asset('js/typeahead.bundle.min.js') }}" charset="UTF-8"></script>
    <script src="{{ asset('js/taginput_custom.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
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
            console.log(data);

            $.ajax({
                type: "POST",
                url: "{{ route('update_signature.orden', ['id' => $id]) }}",
                data: {
                    signature: data,
                    type: type
                },
                dataType: "json",
                success: function(response) {
                    $(`#${$("#id_signature").val()}`).attr('src', data);
                    $("#modal").modal("hide");
                }
            });
        });
        $(document).on('click', '#limpiar', function() {
            signaturePad.clear();
        });

        $(document).on('click', '.send-mail', function() {
            var $icon = $(this);
            var $button = $("#send_mail");
            $button.html("Send Mail");
            $button.prop("disabled", false);

            console.log($icon.data('num'));
            $.ajax({
                url: `{{ url('/') }}/get_config_mail/{{ $orden->proyecto_id }}/orden`,
                dataType: "json",
                async: false,
                success: function(response) {
                    $("#mailModal").removeAttr("tabindex");
                    $("#mailModal #title_m").attr('name', 'title_m').val(
                        `Job: ${response.config.title_ticket_email}`);
                    $("#mailModal #body_m").attr('name', 'body_m').text(``);
                    $("#mailModal #row_id").val({{ $orden->id }});
                    var emails = [];
                    emails.push(response.emails.Coordinador_Obra_mail);
                    emails.push(response.emails.Lead_mail);
                    emails.push(response.emails.Pwtsuper_mail);
                    emails.push(response.emails.Foreman_mail);
                    data = emails.filter(function(element) {
                        return element !== undefined && element !== null;
                    });

                    $('#mailModal #to').attr('name', 'to').tokenfield('setTokens', data);
                    $('#mailModal #cc').attr('name', 'cc');
                    $.each(emails, function(index, value) {
                        if (value) {
                            $('#mailModal #cc').tokenfield('setTokens', []);
                        }
                    });
                    $.each(response.email_contac, function(index, value) {
                        if (value.email) {
                            $('#mailModal #cc').tokenfield('setTokens', value.email);
                        }
                    });
                }
            });


            $("#mailModal").modal("show");
        });
        $(document).on('click', '#send_mail', function() {
            var $button = $("#send_mail");
            $button.html("Wait.....");
            $button.prop("disabled", true);
            $.ajax({
                type: "post",
                url: `{{ url('/') }}/send/${$("#mailModal #row_id").val()}/orden`,
                data: $("#mailModal #mail").serialize(),
                dataType: "json",
                success: function(data) {
                    var html = '';

                    if (data.errors) {
                        html = '<div class="alert alert-danger">';
                        data.errors.forEach(function(error) {
                            html += `<p>${error}</p>`;
                        });
                        html += '</div>';
                        $('#form_result').html(html);
                        $button.html("Send Mail");
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
            var orders = getParameterByName('orders').split(',');
            var view = getParameterByName('view');
            orders.forEach(function(val, index) {
                if (val == view) {
                    if (index == 0) {
                        $('#preview').hide();
                        $('#next').attr('href',
                            `${base_url}/show/orden?orders=${orders}&view=${orders[index+1]}`);
                    }
                    if (index == (orders.length - 1)) {
                        $('#next').hide();
                        $('#preview').attr('href',
                            `${base_url}/show/orden?orders=${orders}&view=${orders[index-1]}`);
                    }
                    $('#next').attr('href',
                        `${base_url}/show/orden?orders=${orders}&view=${orders[index+1]}`);
                    $('#preview').attr('href',
                        `${base_url}/show/orden?orders=${orders}&view=${orders[index-1]}`);
                }
            });

        });
        var base_url = "{{ url('/') }}";

        function getParameterByName(name) {
            name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                results = regex.exec(location.search);
            return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }

        $("#all_email_to").select2({
                theme: "bootstrap4",
                width: '100%',
                ajax: {
                    url: `${base_url}/get-all-email/{{ $orden->proyecto_id }}`,
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
                console.log(e.params.data)
                data.push(e.params.data.email)
                $('#to').attr('name', 'to').tokenfield('setTokens', data);
                $("#all_email_to").val('').change();
                $("#to").trigger("enterKey");
                $('#to').focus();
            });
        let cc = [];
        $("#all_email_cc").select2({
                theme: "bootstrap4",
                width: '100%',
                ajax: {
                    url: `${base_url}/get-all-email/{{ $orden->proyecto_id }}`,
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
                $('#cc').attr('name', 'cc').tokenfield('setTokens', cc);
                $("#all_email_cc").val('').change();
                $("#cc").trigger("enterKey");
                $('#cc').focus();
            });
    </script>
</body>

</html>
