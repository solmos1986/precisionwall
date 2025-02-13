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
    <title>Activity {{ date('m-d-Y', strtotime(date('Y-m-d'))) }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<style>
    .center {
        text-align: center;
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
                <!--div><strong>Date: </strong></div-->
            </div>
            <br><br><br>
            <!--end header css-->
            <h1>Daily Report {{ $proyecto->actividad_fecha }}</h1>
            <div id="company" class="clearfix_in">

                <div><span>FOREMAN NAME</span> {{ $foreman_name }}</div>
                <div><span>SUPERINTENDENT'S NAME</span></div>
            </div>
            <div id="project">
                <div><span>GENERAL CONTRACTOR</span> {{ $proyecto->empresa }}</div>
                <div><span>PRECISION WALL TECH PROJECT</span> {{ $proyecto->Codigo }}</div>
                <div><span>PROJECT NAME</span> {{ $proyecto->Nombre }}</div>
                <div><span>PROJECT ADDRESS</span> {{ $address }}</div>
            </div>
        </header>
        <main>
            <table>
                <thead>
                    <tr>
                        <th class="desc">Area of Work</th>
                        <th class="desc">Task</th>
                        <th class="desc">#Workers</th>
                        <th class="desc">H.Worked</th>
                        <th class="desc">%Completed <br> at Today</th>
                        <th class="desc">Note:</th>
                        {{-- <th class="desc">H. TOTAL EST.</th>
                        <th class="desc">H. TOTAL USED</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse ($resumen as $task)
                        <tr>
                            <td class="desc" width="15%">{{ $task->nombre_area }}</td>
                            <td class="desc" width="35%">{{ $task->nombre_tarea }}</td>
                            <td class="center" width="5%">{{ $task->cantidad_personas }}</td>
                            <td class="center" width="5%">{{ number_format($task->Horas_Contract_total, 2) }}</td>
                            <td class="center" width="5%">{{ $task->porcentaje }}</td>
                            <td class="desc" width="35%">{{ $task->note }}</td>
                            {{-- <td class="desc">{{ $task->Horas_Estimadas }}</td>
                            <td class="desc">{{ $task->total_used }}</td> --}}
                        </tr>
                    @empty
                        <tr>
                            <td class="center" colspan="6">No Cost Codes</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
            <table>
                <thead>
                    <tr>
                        <th class="desc">DESCRIPTION</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($daily_report_detail->estado == 'pending')
                        <td style="text-align: center">No Daily Report</td>
                    @else
                        <td class="desc">{!! str_replace("\n", '<br>', $daily_report_detail->detalle) !!}</td>
                    @endif
                </tbody>
            </table>
        </main>
        <div class="cols">
            <div style="text-align: center;">
                <a href="{{ route('daily_report_detail.index', ['id' => $proyecto->Pro_ID]) }}"
                    class="btn btn-pill btn-primary mt-0 btn-sm">Go
                    back</a><br>
                <button class="btn btn-pill btn-primary mt-2 btn-sm send-mail">Send by e-mail</button>
            </div>
        </div>
    </div>
    <div class="page">
        <h5 style="text-align:center"> PICTURES</h5>
        <div class="row">
            @forelse ($img as $val)
                <div class="column">
                    <img src='{{ asset("uploads/$val->imagen") }}'>
                    <p style="text-align: center">{{ $val->referencia }}</p>
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

            </div>
        </div>
    </div>
    <x-components.mail-modal title="daily report" />

    <script src="{{ asset('assets/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-tokenfield.min.js') }}" charset="UTF-8"></script>
    <script type="text/javascript" src="{{ asset('js/typeahead.bundle.min.js') }}" charset="UTF-8"></script>
    <script src="{{ asset('js/taginput_custom.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script>
        var base_url = "{{ url('/') }}";
        $(document).on('click', '.send-mail', function() {
            $("#mailModal").removeAttr("tabindex");
            $('#mailModal #to').attr('name', 'to').tokenfield('setTokens', []);
            $('#mailModal #cc').attr('name', 'cc').tokenfield('setTokens', []);
            var $icon = $(this);
            var $button = $("#send_mail");
            $button.html("Send Mail");
            $button.prop("disabled", false);

            $.ajax({
                url: `{{ url('/') }}/get_config_mail/{{ $proyecto->Pro_ID }}/ticket`,
                dataType: "json",
                async: false,
                success: function(response) {
                    $("#mailModal #title_m").attr('name', 'title_m').val(
                        `Job: {{ $proyecto->empresa }} - Daily Report {{ $proyecto->fecha }} `
                    );
                    $("#mailModal #body_m").attr('name', 'body_m').text(
                        'Please find attached daily report for the project mention above');
                    $("#mailModal #row_id").val({{ $proyecto->Pro_ID }});
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
                type: "POST",
                url: `{{ url('/') }}/daily-report-detail/email/{{ $daily_report_detail->actividad_id }}/admin`,
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
            var reports = getParameterByName('reports').split(',');
            var view = getParameterByName('view');
            reports.forEach(function(val, index) {
                if (val == view) {

                    if (index == 0) {
                        $('#preview').hide();
                        $('#next').attr('href',
                            `${base_url}/daily-report-detail/view-admin?reports=${reports}&view=${reports[index+1]}`
                        );
                    }
                    if (index == (reports.length - 1)) {
                        $('#next').hide();
                        $('#preview').attr('href',
                            `${base_url}/daily-report-detail/view-admin?reports=${reports}&view=${reports[index-1]}`
                        );
                    }
                    $('#next').attr('href',
                        `${base_url}/daily-report-detail/view-admin?reports=${reports}&view=${reports[index+1]}`
                    );
                    $('#preview').attr('href',
                        `${base_url}/daily-report-detail/view-admin?reports=${reports}&view=${reports[index-1]}`
                    );
                }
            });
        });

        $("#all_email_to").select2({
                theme: "bootstrap4",
                width: '100%',
                ajax: {
                    url: `${base_url}/get-all-email/{{ $proyecto->Pro_ID }}`,
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
                    url: `${base_url}/get-all-email/{{ $proyecto->Pro_ID }}`,
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
    <script>
        var base_url = "{{ url('/') }}";

        function getParameterByName(name) {
            name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                results = regex.exec(location.search);
            return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
    </script>
</body>

</html>
