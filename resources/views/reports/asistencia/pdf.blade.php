<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{ public_path('css/pdf/hoja_horizontal.css') }}">
</head>

<body>
    <!--header -->
    <x-pdf.horizontal.header />
    <!--footer-->
    <x-pdf.horizontal.footer />
    <!--contenido-->
    <div class="clearfix_in">
        <h1>Report of Attendance from {{ date('m-d-Y', strtotime($fecha_inicio)) }} to
            {{ date('m-d-Y', strtotime($fecha_fin)) }} </h1>
        <div id="company" class="clearfix_in">
            <div><span>CODIGO</span> {{ $empresa->Codigo }} </div>
        </div>
        <div id="project">
            <div><span>TOTAL RECORDS</span> {{ $total_registros }}</div>
            <div><span>COMPANY</span> {{ $empresa->Nombre }} </div>
        </div>
    </div>
    <main>
        <table>
            <thead>
                <tr>
                    <th colspan="21"></th>
                </tr>
            </thead>
            <thead>
                <tr>
                    <th style="border-bottom: 1px solid white;" colspan="1" class="desc"></th>
                    <th style="border-bottom: 1px solid white;" colspan="1" class="desc"></th>
                    <th style="border-bottom: 1px solid white;" colspan="1" class="desc"></th>
                    <th style="border-bottom: 1px solid white;" colspan="2" class="desc">Potential days <br> to work
                    </th>
                    <th style="border-bottom: 1px solid white;" colspan="2" class="desc">Days Worked</th>
                    <th style="border-bottom: 1px solid white;" colspan="2" class="desc">Off due no show <br> up day
                        before</th>
                    <th style="border-bottom: 1px solid white;" colspan="2" class="desc">Days no <br> show up</th>
                    <th style="border-bottom: 1px solid white;" colspan="2" class="desc">Days asked <br> be off</th>
                    <th style="border-bottom: 1px solid white;" colspan="2" class="desc">Off due no<br>work
                        available
                    </th>
                    <th style="border-bottom: 1px solid white;" colspan="2" class="desc">Off due suspended <br> by
                        management</th>
                    <th style="border-bottom: 1px solid white;" colspan="2" class="desc">Days work <br> on weekend
                    </th>
                    <th style="border-bottom: 1px solid white;" colspan="2" class="desc">Days check<br> in late</th>
                </tr>
            </thead>
            <thead>
                <tr>
                    <th>Date of hire</th>
                    <th>Employee #</th>
                    <th>Nick Name</th>
                    <th>#</th>
                    <th>%</th>
                    <th>#</th>
                    <th>%</th>
                    <th>#</th>
                    <th>%</th>
                    <th>#</th>
                    <th>%</th>
                    <th>#</th>
                    <th>%</th>
                    <th>#</th>
                    <th>%</th>
                    <th>#</th>
                    <th>%</th>
                    <th>#</th>
                    <th>%</th>
                    <th>#</th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody>
                @if (count($lista_personal) > 0)
                    @foreach ($lista_personal as $persona)
                        <tr>
                            <td style="text-align: center;">
                                {{ date('m-d-Y', strtotime($persona['fecha_ingreso'])) }}
                            </td>
                            <td style="text-align: center;">{{ $persona['Empleado_ID'] }}</td>
                            <td style="text-align: center;">{{ $persona['Nombre'] }}</td>
                            <td style="text-align: center;">{{ $persona['dias_laborables'] }}</td>
                            <td style="text-align: center;">100%</td>
                            <td style="text-align: center;">{{ $persona['dias_trabajados'] }}</td>
                            <td style="text-align: center;">
                                {{ round(((float) $persona['dias_trabajados'] * 100) / $persona['dias_laborables']) }}%
                            </td>
                            <td style="text-align: center;">{{ $persona['castigo'] }}</td>
                            <td style="text-align: center;">
                                {{ round(((float) $persona['castigo'] * 100) / $persona['dias_laborables']) }}%
                            </td>
                            <td style="text-align: center;">{{ $persona['dias_no_trabajados'] }}</td>
                            <td style="text-align: center;">
                                {{ round(((float) $persona['dias_no_trabajados'] * 100) / $persona['dias_laborables']) }}%
                            </td>
                            <td style="text-align: center;">{{ $persona['permiso'] }}</td>
                            <td style="text-align: center;">
                                {{ round(((float) $persona['permiso'] * 100) / $persona['dias_laborables']) }}%
                            </td>
                            <td style="text-align: center;">{{ $persona['sin_trabajo'] }}</td>
                            <td style="text-align: center;">
                                {{ round(((float) $persona['sin_trabajo'] * 100) / $persona['dias_laborables']) }}%
                            </td>
                            <td style="text-align: center;">{{ $persona['suspendido'] }}</td>
                            <td style="text-align: center;">
                                {{ round(((float) $persona['suspendido'] * 100) / $persona['dias_laborables']) }}%
                            </td>
                            <td style="text-align: center;">{{ $persona['fin_semanas'] }}</td>
                            <td style="text-align: center;">
                                {{ round(((float) $persona['fin_semanas'] * 100) / $persona['dias_laborables']) }}%
                            </td>
                            <td style="text-align: center;">{{ $persona['dias_retraso'] }}</td>
                            <td style="text-align: center;">
                                @if ($persona['dias_trabajados'] > 0)
                                    {{ round(((float) $persona['dias_retraso'] * 100) / $persona['dias_trabajados']) }}%
                                @else
                                    0%
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="21" style="text-align: center;">there is no staff in this range dates</td>
                    </tr>
                @endif
            </tbody>
        </table>
        @if ($detalle === 'true')
            @foreach ($detalle_personal as $persona)
                <div style="page-break-after: always;"></div>
                <div class="clearfix_in">
                    <h1>Report of Attendance from {{ date('m-d-Y', strtotime($fecha_inicio)) }} to
                        {{ date('m-d-Y', strtotime($fecha_fin)) }} </h1>
                    <div id="company" class="clearfix_in">
                        <div><span>DAYS NO SHOW UP</span>{{ $persona['dias_no_trabajados'] }}</div>
                        <div><span>DAYS CHECK IN LATE</span>{{ $persona['dias_retraso'] }}</div>
                    </div>
                    <div id="project">
                        <div><span>EMPLOYEE #</span> {{ $persona['Empleado_ID'] }}</div>
                        <div><span>NICK</span>{{ $persona['Nombre'] }}</div>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th colspan="21"></th>
                        </tr>
                    </thead>
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Proyect</th>
                            <th>Direction</th>
                            <th>Code</th>
                            <th>Work date</th>
                            <th>Start Time</th>
                            <th>Check in</th>
                            <th>Entry status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($persona['registros'] as $datos)
                            <tr>
                                <td style="text-align: center;">
                                    {{ $datos['empresa'] }}
                                </td>
                                <td style="text-align: center;">
                                    {{ $datos['Nombre'] }}
                                </td>
                                <td style="text-align: center;">
                                    {{ $datos['Calle'] }}
                                </td>
                                <td style="text-align: center;">
                                    {{ $datos['Codigo'] }}
                                </td>
                                <td style="text-align: center;">
                                    {{ date('m-d-Y', strtotime($datos['Fecha'])) }}
                                </td>
                                <td style="text-align: center;">{{ $datos['Hora'] }} </td>
                                <td style="text-align: center;">
                                    {{ $datos['Hora_Ingreso'] != '00:00:00' ? $datos['Hora_Ingreso'] : 'no check in' }}
                                </td>
                                <td style="text-align: center;">
                                    {{ $datos['status_entrada'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        @endif
    </main>
</body>

</html>
