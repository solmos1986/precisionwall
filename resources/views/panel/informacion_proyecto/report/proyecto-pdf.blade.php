<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Order report</title>
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
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
        }

        .clearfix_in {
            padding: 2px 0;
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

        h1 {
            border-top: 1px solid #5d6975;
            border-bottom: 1px solid #5d6975;
            color: #5d6975;
            font-size: 2.0em;
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
            color: #5d6975;
            text-align: left;
            width: 130px;
            margin-right: 10px;
            display: inline-block;
            font-size: 0.8em;
        }

        #project span {
            color: #5d6975;
            text-align: right;
            width: 175px;
            margin-right: 10px;
            display: inline-block;
            font-size: 0.8em;
        }

        #company {
            float: right;
        }

        #project div,
        #company div {
            white-space: nowrap;
        }

        p {
            margin-top: 0;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 15px;
        }

        table tr:nth-child(2n-1) td {
            background: #f5f5f5;
        }

        table th,
        table td {
            text-align: center;
        }

        table th {
            padding: 15px 15px;
            color: #000000;
            border-bottom: 1.0px solid #c1ced9;
            white-space: nowrap;
            text-transform: uppercase;
            font-weight: bolder;
        }

        table .service,
        table .desc {
            text-align: left;
        }

        table td {
            padding: 0px;
            text-align: right;
            color: #494949
        }

        table td.service,
        table td.desc {
            vertical-align: top;
        }

        table td.unit,
        table td.qty,
        table td.total {
            text-align: center;
            font-size: 1em;
        }

        table td.grand {
            border-top: 1px solid #5d6975;
        }

        #notices .notice {
            color: #5d6975;
            font-size: 1.1em;
        }

        footer {
            color: #5d6975;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #c1ced9;
            padding: 8px 0;
            text-align: center;
        }

        @page {
            margin: 0cm 0cm;
            font-size: 1em;
        }

        body {
            margin: 2.5cm 1.3cm 1cm;
        }

        strong {
            color: rgb(65, 65, 65)
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

    </style>
</head>

<body>
    <x-pdf.header />
    <x-pdf.footer />
    <div class="clearfix_in">
        <h1>Job Reports</h1>
    </div>

    @foreach (array_chunk($resultado, 3) as $agrupados)
        <main>
            @foreach ($agrupados as $proyecto)
                <table>
                    <thead>
                        <tr>
                            <th width="160" class="desc" Colspan="1" style="background: white; padding: 0px">
                                Job Information
                            </th>
                            <th width="380" class="desc" colspan="3" style="background: white; padding: 0px">
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td colspan="1" class="desc" style="background: white; padding: 0px">
                                <strong>GC-Company:</strong> {{ $proyecto->proyectos->nombre_empresa }}
                            </td>
                            <td colspan="1" class="desc" style="background: white; padding: 0px">
                                <strong>Cod:</strong> {{ $proyecto->proyectos->Codigo }}
                            </td>
                            <td colspan="1" class="desc" style="background: white; padding: 0px">
                                <strong>Name:</strong> {{ $proyecto->proyectos->Nombre }}
                            </td>
                            <td colspan="1" class="desc" style="background: white; padding: 0px">
                                <strong>Street:</strong> {{ $proyecto->proyectos->Calle }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="1" class="desc" style="background: white; padding: 0px">
                                <strong>City:</strong> {{ $proyecto->proyectos->Ciudad }}
                            </td>
                            <td colspan="1" class="desc" style="background: white; padding: 0px">
                                <strong>State:</strong> {{ $proyecto->proyectos->Estado }}
                            </td>
                            <td colspan="1" class="desc" style="background: white; padding: 0px">
                                <strong>Zip Code:</strong> {{ $proyecto->proyectos->Zip_Code }}
                            </td>
                            <td colspan="1" class="desc" style="background: white; padding: 0px">
                                <strong>Status:</strong> {{ $proyecto->proyectos->nombre_status }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="1" class="desc" style="background: white; padding: 0px">
                                <strong>Type:</strong> {{ $proyecto->proyectos->nombre_tipo }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="desc" style="background: white; padding: 0px">
                                <strong>GC-PM:</strong> {{ $proyecto->proyectos->Project_Manager }}
                                {{ $proyecto->proyectos->Project_Manager_celular }}
                                {{ $proyecto->proyectos->Project_Manager_email }}
                            </td>
                            <td colspan="2" class="desc" style="background: white; padding: 0px">
                                <strong>GC-Superintendent:</strong> {{ $proyecto->proyectos->Coordinador_Obra }}
                                {{ $proyecto->proyectos->Coordinador_Obra_celular }}
                                {{ $proyecto->proyectos->Coordinador_Obra_email }}
                            </td>
                        </tr>
                        <tr>
                            <th class="desc" style="background: white; padding: 0px" colspan="4">
                                Contacts
                            </th>
                        </tr>
                        <tr>
                            <td colspan="1" class="desc" style="background: white; padding: 0px">
                                <strong> PM: </strong>{{ $proyecto->proyectos->Manager }} <br>
                                {{ $proyecto->proyectos->Manager_celular }}
                                {{ $proyecto->proyectos->Manager_email }}
                            </td>
                            <td colspan="1" class="desc" style="background: white; padding: 0px">
                                <strong>Field Superintendent:</strong>
                                {{ $proyecto->proyectos->field_superintendent }}
                                <br>
                                {{ $proyecto->proyectos->field_superintendent_celular }}
                                {{ $proyecto->proyectos->field_superintendent_email }}
                            </td>
                            <td colspan="1" class="desc" style="background: white; padding: 0px">
                                <strong>Foreman:</strong> {{ $proyecto->proyectos->Foreman }} <br>
                                {{ $proyecto->proyectos->Foreman_celular }}
                                {{ $proyecto->proyectos->Foreman_email }}
                            </td>
                            <td colspan="1" class="desc" style="background: white; padding: 0px">
                                <strong>Lead:</strong> {{ $proyecto->proyectos->lead_proyecto }}
                                {{ $proyecto->proyectos->lead_proyecto_celular }}
                                {{ $proyecto->proyectos->lead_proyecto_email }}
                            </td>
                        </tr>
                        <tr>
                            <td style="background: white; margin: 0px 0px 0px; text-align: left" rowspan="13">
                                <img style="width:210px;" src="{{ $proyecto->view_graficos }}">
                            </td>
                            <td colspan="3" style="background: white; padding: 0px; text-align: left;">
                                <strong>Asistant Project Manager:</strong>
                                {{ $proyecto->proyectos->asistente_proyecto }}
                                {{ $proyecto->proyectos->asistente_proyecto_celular }}
                                {{ $proyecto->proyectos->asistente_proyecto_email }}
                            </td>
                        </tr>
                        <tr>
                            <th class="desc" style="background: white; padding: 0px" colspan="3">
                                Project Dates
                            </th>
                        </tr>
                        <tr>
                            <td colspan="1" style="background: white; padding: 0px; text-align: left;">
                                <strong>Start Date:</strong> {{ $proyecto->proyectos->Fecha_Inicio }}
                            </td>
                            <td colspan="1" style="background: white; padding: 0px; text-align: left;">
                                <strong>End Date :</strong> {{ $proyecto->proyectos->Fecha_Fin }}
                            </td>
                            <td colspan="1" style="background: white; padding: 0px; text-align: left;">
                                <strong> Total Hours Contract:</strong>
                                {{ $proyecto->graficos->proyectos->horas_estimadas }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="1" style="background: white; padding: 0px; text-align: left;">
                                <strong>Q. Employees:</strong> {{ $proyecto->graficos->personal }}
                            </td>
                            <td colspan="1" style="background: white; padding: 0px; text-align: left;">
                                <strong>Days Aprox.:</strong> {{ $proyecto->graficos->dias }}
                            </td>
                        </tr>
                        <tr>
                            <th class="desc" style="background: white; padding: 0px" colspan="4">
                                Info
                            </th>
                        </tr>
                        @if ($proyecto->info != null)
                            <tr>
                                <td colspan="1" style="background: white; padding: 0px; text-align: left;">
                                    <strong>Contact:</strong>
                                    {{ $proyecto->info->contacto_status == null ? 'no record' : $proyecto->info->contacto_status }}
                                </td>
                                <td colspan="1" style="background: white; padding: 0px; text-align: left;">
                                    <strong>Submittals:</strong>
                                    {{ $proyecto->info->submittals_id == null ? 'no record' : $proyecto->info->submittals_id }}
                                </td>
                                <td colspan="1" style="background: white; padding: 0px; text-align: left;">
                                    <strong>Plans:</strong>
                                    {{ $proyecto->info->plans_status == null ? 'no record' : $proyecto->info->plans_status }}
                                </td>

                            </tr>
                            <tr>
                                <td colspan="1" style="background: white; padding: 0px; text-align: left;">
                                    <strong> Vendor:</strong>
                                    {{ $proyecto->info->vendor_id == null ? 'no record' : $proyecto->info->vendor_id }}
                                </td>
                                <td colspan="1" style="background: white; padding: 0px; text-align: left;">
                                    <strong>Const. Schedule:</strong>
                                    {{ $proyecto->info->const_schedule_status == null ? 'no record' : $proyecto->info->const_schedule_status }}
                                </td>
                                <td colspan="1" style="background: white; padding: 0px; text-align: left;">
                                    <strong>Field Folder:</strong>
                                    {{ $proyecto->info->field_folder_status == null ? 'no record' : $proyecto->info->field_folder_status }}
                                </td>

                            </tr>
                            <tr>
                                <td colspan="1" style="background: white; padding: 0px; text-align: left;">
                                    <strong>Brake down:</strong>
                                    {{ $proyecto->info->brake_down_status == null ? 'no record' : $proyecto->info->brake_down_status }}
                                </td>
                                <td colspan="1" style="background: white; padding: 0px; text-align: left;">
                                    <strong> Badges:</strong>
                                    {{ $proyecto->info->badges_id == null ? 'no record' : $proyecto->info->badges_id }}
                                </td>
                                <td colspan="" style="background: white; padding: 0px; text-align: left;">
                                    <strong> Special Material:</strong>
                                    {{ $proyecto->info->special_material_id == null ? 'no record' : $proyecto->info->special_material_id }}
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="4" style="background: white; padding: 0px; text-align: center;">
                                    no record
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <th class="desc" style="background: white; padding: 0px" colspan="2">
                                Weekly Report:
                            </th>
                            <th class="desc" style="background: white; padding: 0px" colspan="2">
                                Action for the Week:
                            </th>
                        </tr>
                        @if ($proyecto->actions != null)
                            <tr>
                                <td colspan="2" class="desc" style="background: white; padding: 0px">
                                    {{ $proyecto->actions->report_weekly }}
                                </td>
                                <td colspan="2" class="desc" style="background: white; padding: 0px">
                                    {{ $proyecto->actions->action_for_week }}
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="2" class="desc" style="background: white; padding: 0px;">
                                    no record
                                </td>
                                <td colspan="2" class="desc" style="background: white; padding: 0px;">
                                    no record
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            @endforeach
        </main>
        @if (!$loop->last)
            <div style="page-break-after: always;"></div>
        @endif
       
    @endforeach
</body>

</html>
