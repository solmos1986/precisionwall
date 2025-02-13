<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Report Daily</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .clearfix_in:after {
            content: "";
            display: table;
            clear: both;
        }

        body {
            position: relative;
            width: 19cm;
            height: 25.7cm;
            margin: 0 auto;
            color: #001028;
            background: #ffffff;
            font-family: Arial, sans-serif;
            font-size: 11px;
            font-family: Arial;
        }

        .clearfix_in {
            padding: 0px 0;
            margin-bottom: 2px;
        }

        #logo {
            text-align: center;
            margin-bottom: 10px;
        }

        #logo img {
            width: 90px;
        }

        .column img {
            width: 6.5cm;
        }

        .column {
            text-align: center;
            padding-bottom: 10px;
        }

        .cols {
            text-align: center;
            display: table;
            width: 100%;
            padding-top: 50px;
        }

        .cols div {
            display: table-cell;
        }

        .cols div img,
        p {
            width: 6cm;
            text-align: center;
        }

        h1 {
            border-top: 1px solid #5D6975;
            border-bottom: 1px solid #5D6975;
            color: #5D6975;
            font-size: 1.5em;
            line-height: 1.4em;
            font-weight: normal;
            text-align: center;
            margin: 0 0 20px 0;
            background: url("https://s3-eu-west-1.amazonaws.com/htmlpdfapi.production/free_html5_invoice_templates/example1/dimension.png");
        }

        #project {
            float: left;
        }

        #company span {
            color: #5D6975;
            text-align: left;
            width: auto;
            margin-left: 2px;
            display: inline;
            font-size: 12px;
        }

        #project span {
            color: #5D6975;
            text-align: left;
            width: auto;
            margin-right: 10px;
            display: inline;
            font-size: 12px;
        }

        #company {
            float: right;
        }

        #project div,
        #company div {
            text-align: left;
            white-space: nowrap;
            margin-bottom: 0px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        table tr:nth-child(2n-1) td {
            background: #F5F5F5;
        }

        table th,
        table td {
            padding: 0px;
            text-align: center;
        }

        table th {
            padding: 5px 10px;
            color: #5D6975;
            border-bottom: 1px solid #C1CED9;
            white-space: nowrap;
            font-weight: normal;
        }

        table .service,
        table .desc {
            text-align: left;
        }

        table td {
            padding: 5px;
            text-align: right;
        }

        table td.service,
        table td.desc {
            vertical-align: top;
        }

        table td.unit,
        table td.qty,
        table td.total {
            font-size: 12px;
        }

        table td.grand {
            border-top: 1px solid #5D6975;
            ;
        }

        #notices .notice {
            color: #5D6975;
            font-size: 12px;
        }

        footer {
            color: #5D6975;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #C1CED9;
            padding: 8px 0;
            text-align: center;
        }

        @page {
            margin: 0cm 0cm;
            font-size: 10px;
        }

        body {
            margin: 2.5cm 1.3cm 1.5cm;
        }
    </style>
    <style>
        .border {
            border: solid 1px black;
            text-align: center;
        }

        .record_text {
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .center {
            text-align: center
        }
    </style>
</head>
<x-pdf.header />
<x-pdf.footer />

<body>
    <div class="clearfix_in">
        <h1>REPORT DAILY {{ $from_date }} to {{ $to_date }}</h1>
    </div>
    @forelse ($proyectos as $proyecto)
        <div class="clearfix_in">
            <div id="company" class="clearfix_in">
            </div>
            <div id="project">
                <div><span>GENERAL CONTRACTOR</span></div>
                <div><span>PRECISION WALL TECH PROJECT</span> {{ $proyecto->Codigo }}</div>
                <div><span>PROJECT NAME</span> {{ $proyecto->Nombre }}</div>
            </div>
        </div>
        @forelse ($proyecto->registro_diarios as $registro_diario)
            @if (count($registro_diario->registro_diario_actividad) > 0)
                <main>
                    <table>
                        <thead>
                            <tr>
                                <th colspan="7" class="desc" style="background: white; padding: 1px;">
                                    {{ $registro_diario->descripcion_fecha }}
                                </th>
                            </tr>
                        </thead>
                        <thead>
                            <tr>
                                <th width="20%" colspan="1" class="desc"
                                    style="background: white; padding: 1px; ">
                                    Area OF Work
                                </th>
                                <th width="30%" colspan="1" class="desc"
                                    style="background: white; padding: 1px; ">
                                    Cost Code/Task
                                </th>
                                <th width="20%" colspan="1" class="desc"
                                    style="background: white; padding: 1px; ">
                                    Nick Name
                                </th>
                                <th width="20%" colspan="1" class="desc"
                                    style="background: white; padding: 1px; ">
                                    Note
                                </th>
                                <th width="10%" colspan="1" class="desc"
                                    style="background: white; padding: 1px; ">
                                    H. Worked
                                </th>
                                {{-- <th width="8%" colspan="1" class="desc"
                                    style="background: white; padding: 1px; ">
                                    Completed
                                </th>
                                <th width="10%" colspan="1" class="desc"
                                    style="background: white; padding: 1px; ">
                                    H. Est.
                                </th>
                                <th width="9%" colspan="1" class="desc"
                                    style="background: white; padding: 1px; ">
                                    H. T. Used
                                </th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($registro_diario->registro_diario_actividad as $registro_diario_actividad)
                                <tr>
                                    <td class="desc">
                                        {{ $registro_diario_actividad->nombre_area }}
                                    </td>
                                    <td class="desc">
                                        {{ $registro_diario_actividad->nombre_tarea }}
                                    </td>
                                    <td class="desc">
                                        {{ $registro_diario_actividad->Nick_Name }}
                                    </td>
                                    <td class="desc">
                                        {{ $registro_diario_actividad->Detalles }}
                                    </td>
                                    <td class="center">
                                        {{ number_format($registro_diario_actividad->Horas_Contract, 2) }}
                                    </td>
                                    {{-- <td class="desc">
                                        {{ $registro_diario_actividad->Last_Per_Recorded }}%
                                    </td>
                                    <td class="desc">
                                        {{ $registro_diario_actividad->Horas_Estimadas }}
                                    </td>
                                    <td class="desc">
                                        {{ $registro_diario_actividad->total_used->Horas_Contract }}
                                    </td> --}}
                                </tr>
                            @empty
                                <tr>
                                    <td class="desc" colspan="7" style="text-align: center;">
                                        no records
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="7" class="desc" style="background: white; padding: 1px">

                                </th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="1" class="desc" style="background: white; padding: 1px">

                                </th>
                                <th colspan="1" class="desc" style="background: white; padding: 1px">

                                </th>
                                <th colspan="1" class="desc" style="background: white; padding: 1px">

                                </th>
                                <th colspan="1" class="desc" style="background: white; padding: 1px">

                                </th>
                                <th colspan="1" class="center" style="background: white; padding: 1px">
                                    {{ number_format($registro_diario_actividad->total_Horas_Contract, 2) }}
                                </th>
                                {{-- <th colspan="1" class="desc" style="background: white; padding: 1px">

                                </th>
                                <th colspan="1" class="desc" style="background: white; padding: 1px">

                                </th>
                                <th colspan="1" class="desc" style="background: white; padding: 1px">

                                </th> --}}
                            </tr>
                        </tfoot>
                </main>
                <main>
                    <table>
                        <thead>
                            <tr>
                                <th colspan="1" class="desc" style="background: white; padding: 1px;">

                                </th>
                            </tr>
                        </thead>
                        <thead>
                            <tr>
                                <th width="20%" colspan="1" class="desc"
                                    style="backgroun3d: white; padding: 1px; ">
                                    Daily Report Description
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @if ($registro_diario_actividad->daily_report != null)
                                    <td class="desc">{!! str_replace("\n", '<br>', $registro_diario_actividad->daily_report->detalle) !!}</td>
                                @else
                                    <td class="center">No Daily Report</td>
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </main>
                <h4 style="text-align:center">Pictures</h4>
                <main>
                    <table width="209">
                        @if ($registro_diario_actividad->daily_report!=null)
                            @forelse (array_chunk($registro_diario_actividad->daily_report->images , 3) as $row)
                                <tr>
                                    @foreach ($row as $value)
                                        <td width="25%" style="text-align: center; background:white ">
                                            <p style="text-align: center;margin:0px; padding:0px;">{{ $value->referencia }}
                                            </p>
                                            <img src='{{ public_path("uploads/$value->imagen") }}'
                                                style="width: 100%; height:auto; ">

                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td style="background: #fff;">
                                        <h3 style="text-align:center">-//-</h3>
                                    </td>
                                </tr>
                            @endforelse
                        @else
                            <tr>
                                <td style="background: #fff;">
                                    <h3 style="text-align:center">-//-</h3>
                                </td>
                            </tr>
                        @endif
                    </table>
                </main>
            @else
                <main>
                    <table>
                        <thead>
                            <tr>
                                <th colspan="7" class="desc" style="background: white; padding: 1px;">
                                    {{ $registro_diario->descripcion_fecha }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" class="desc" style="text-align: center;">
                                    No records
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </main>
                <main>
                    <table>
                        <thead>
                            <tr>
                                <th width="20%" colspan="1" class="desc"
                                    style="backgroun3d: white; padding: 1px; ">
                                    Daily Report Description
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <td class="center">No Daily Report</td>
                        </tbody>
                    </table>
                </main>
            @endif
        @empty
            <main>
                <table>

                </table>
            </main>
        @endforelse
        @if (!$loop->last)
            <div style="page-break-after: always;"></div>
        @endif
    @empty

    @endforelse
</body>

</html>
