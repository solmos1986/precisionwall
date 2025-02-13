<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/invoice.css') }}" media="all" />
    <link href="{{ asset('css/tokenfield-typeahead.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-tokenfield.min.css') }}" type="text/css" rel="stylesheet">
    <title>Project Note {{$proyecto->codigo}}</title>
    <style>
        td input[type="checkbox"] {
            float: none;
            margin: 0 auto;
            width: 10%;
        }

        type=checkbox]:after {
            color: #001028;
            background: #FFFFFF;
            font-family: Arial, sans-serif;
            font-size: 12px;
            font-family: Arial;
            content: attr(value);
            margin: -3px 15px;
            vertical-align: top;
            white-space: nowrap;
            display: inline-block;
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

</head>

<body>
    <div class="page">
        {{-- <div class="box">
            <div>
                <a type="button" class="btn btn-pill btn-primary btn-sm m-1" id="preview">Preview</a>
            </div>
            <div class="push">
                <a type="button" class="btn btn-pill btn-primary btn-sm m-1" id="next">Next</a>
            </div>
        </div> --}}
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
            <h1>Project note : {{$proyecto->codigo}}</h1>
            <div id="company" class="clearfix_in">
                <div><span>NOTE NUMBER:</span>{{$proyecto->codigo}}</div>
                <div><span>DATE OF REPORT</span>
                    {{ date('m/d/Y', strtotime($proyecto->fecha_entrega)) }}
                </div>
                <div><span>CREATE BY</span> {{ $proyecto->nombre_empleado }}</div>
            </div>
            <div id="project">
                <div><span>GENERAL CONTRACTOR</span> {{ $proyecto->nombre_empresa }}</div>
                <div><span>PRECISION WALL TECH PROJECT</span> {{ $proyecto->codigo_proyecto }}</div>
                <div><span>PROJECT NAME</span> {{ $proyecto->nombre_proyecto }}</div>
                <div><span>PROJECT ADDRESS</span> {{ $proyecto->dirrecion }}</div>
            </div>
        </header>

        <main>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="desc">
                            {!! str_replace("\n", '<br>', $proyecto->nota) !!}
                        </td>
                    </tr>
                </tbody>
            </table>
        </main>
        <br>
        <br>
        <br>
        <br>
       {{--  <div style="text-align: center;">
            <a href="{{ route('list.goal') }}" class="btn btn-pill btn-primary mt-0 btn-sm">Go back</a><br>
            <button class="btn btn-pill btn-primary mt-2 btn-sm send-mail">Send by e-mail</button>
        </div> --}}
    </div>
    @forelse (array_chunk($images, 4) as $image)
        <div class="page">
            <h5 style="text-align:center">Pictures</h5>
            <div class="row">
                @forelse ($image as $val)
                    <div class="column">
                        <img src='{{ asset("docs/$val->nombre") }}'>
                    </div>
                @empty
                    <h6 style="text-align:center">There is nothing inserted</h6>
                @endforelse
            </div>
        </div>
    @empty
    <div class="page">
        <h5 style="text-align:center">Pictures</h5>
        <h6 style="text-align:center">There is nothing inserted</h6>
    </div>
    @endforelse

    </div>
    <x-components.mail-modal title="visit report" />
</body>
<script src="{{ asset('assets/js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap-tokenfield.min.js') }}" charset="UTF-8"></script>
<script type="text/javascript" src="{{ asset('js/typeahead.bundle.min.js') }}" charset="UTF-8"></script>
<script src="{{ asset('js/taginput_custom.js') }}"></script>
{{-- <script>
    var base_url = "{{ url('/') }}";

    function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }

    $(document).ready(function() {
        var goals = getParameterByName('goals').split(',');
        var view = getParameterByName('view');
        goals.forEach(function(val, index) {
            if (val == view) {
                if (index == 0) {
                    $('#preview').hide();
                    $('#next').attr('href',
                        `${base_url}/show-btn/goals?goals=${goals}&view=${goals[index+1]}`);
                }
                if (index == (goals.length - 1)) {
                    $('#next').hide();
                    $('#preview').attr('href',
                        `${base_url}/show-btn/ticket?goals=${goals}&view=${goals[index-1]}`);
                }
                $('#next').attr('href',
                    `${base_url}/show-btn/goals?goals=${goals}&view=${goals[index+1]}`);
                $('#preview').attr('href',
                    `${base_url}/show-btn/goals?goals=${goals}&view=${goals[index-1]}`);
            }
        });
    });

    /*  envio de email */
    $(document).on('click', '.send-mail', function() {
        $('#mailModal #to').attr('name', 'to').tokenfield('setTokens', []);
        $('#mailModal #cc').attr('name', 'cc').tokenfield('setTokens', []);
        var $icon = $(this);
        var $button = $("#send_mail");
        $button.html("Send Mail");
        $button.prop("disabled", false);

        $.ajax({
            url: `{{ url('/') }}/get_config_mail/{{ $goal->Informe_ID }}/goal/{{ $goal->Pro_ID }}`,
            dataType: "json",
            async: false,
            success: function(response) {
                $("#mailModal #title_m").attr('name', 'title_m').val(
                    `${response.config.title_ticket_email} {{ $goal->subempresa }}`
                );
                $("#mailModal #body_m").attr('name', 'body_m').text(
                    'Please find attached visit report for the project mentioned above');
                $("#mailModal #row_id").val({{ $goal->Informe_ID }});
                var emails = [];
                $.each(response.email_contac, function(index, value) {
                    emails.push(value.email);
                });
                emails.push(response.emails.email_contac);
                emails.push(response.emails.Coordinador_Obra_mail);
                emails.push(response.emails.Lead_mail);
                emails.push(response.emails.Pwtsuper_mail);
                data = emails.filter(function(element) {
                    return element !== undefined && element !== null;
                });

                $('#to').attr('name', 'to').tokenfield('setTokens', response.emails.Foreman_mail);
                $('#cc').attr('name', 'cc').tokenfield('setTokens', data);

            }
        });
        $("#mailModal").modal("show");
    });

    $(document).on('click', '#send_mail', function() {
        var $button = $(this);
        var $text = $button.text();
        $button.html("Wait.....", true);
        $button.prop("disabled", true);

        $.ajax({
            type: "post",
            url: `${base_url}/send/{{ $goal->Informe_ID }}/all/goal`,
            data: $("#mailModal #mail").serialize(),
            dataType: "json",
            success: function(data) {
                var html = '';
                if (data.errors) {
                    for (var count = 0; count < data.errors.length; count++) {
                        alert(`${data.errors[count]}`);
                    }
                    html += '</div>';
                    $('#mailModal #form_result').html(html);
                    $button.html($text);
                    $button.prop("disabled", false);
                    button.html("Wait.....", false);
                }
                if (data.success) {
                    alert(data.success);
                    $('#mailModal #mail').trigger("reset");
                    $('#mailModal').modal('hide');
                    $button.html($text);
                    $button.prop("disabled", false);
                    button.html("Wait.....", false);

                }
            },
            fail: function(xhr, textStatus, errorThrown) {
                alert('ocurrio un error en la peticion por favor actualize la pagina');
                $button.html($text);
            }
        });
    });
</script> --}}


</html>
