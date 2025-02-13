<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Report Employee</title>
    {{-- <link rel="stylesheet" href="{{ public_path('css/invoice-pdf.css') }}" media="all" /> --}}
    <!--link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"-->
    <style>
        td input[type="checkbox"] {
            float: none;
            margin: 0 auto;
            width: 10%;
        }

        [type=checkbox]:after {
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

        .header-izq {
            float: left;
        }

        .header-der {
            float: right;
        }
    </style>

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
            margin: 2.5cm 1.3cm 2.5cm;
            color: #001028;
            background: #FFFFFF;
            font-family: Arial, sans-serif;
            font-size: 11px;
            font-family: Arial, Helvetica, sans-serif;
        }

        .clearfix_in {
            padding: 0px 0;
            margin-bottom: 0px;
        }

        #logo {
            text-align: center;
            margin-bottom: 5px;
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
            font-size: 1.2em;
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
            font-size: 0.6rem;
        }

        #project span {
            color: #5D6975;
            text-align: left;
            width: auto;
            margin-right: 10px;
            display: inline;
            font-size: 0.6rem;
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
            margin-bottom: 5px;
        }

        .table tr:nth-child(2n-1) td {
            background-color: #f2f2f2;
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
            padding: 2px;
            text-align: right;
        }

        table td.service,
        table td.desc {
            vertical-align: top;
        }

        table td.unit,
        table td.qty,
        table td.total {
            font-size: 1em;
        }

        table td.grand {
            border-top: 1px solid #5D6975;
        }

        #notices .notice {
            color: #5D6975;
            font-size: 1.1em;
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
            font-size: 1em;
        }
    </style>
</head>

<body>
    <x-pdf.header />
    <x-pdf.footer />
    <div class="clearfix_in">
        <h1>Report Emplooyes</h1>
    </div>
    <div class="clearfix_in">
        <div id="company" class="clearfix_in">
            <div><span>Date of report:</span>
                {{ date('m/d/Y') }}
            </div>
        </div>
        <div id="project">
            <div><span>Total record</span>{{ count($personas) }}</div>
        </div>
    </div>
    @forelse ($personas as $persona)
        <main>
            <table>
                <thead>
                    <tr>
                        <th colspan="3" class="desc"></th>
                    </tr>
                    <tr>
                        <th colspan="1" width="33%"class="desc" style="font-size: 12px;">INFORMATION EMPLOYEE
                        </th>
                        <th colspan="1" width="33%" class="desc"></th>
                        <th colspan="1" width="33%" class="desc"></th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td class="desc">
                            <strong>Company:</strong> {{ $persona->nombre_empresa }} <br>
                        </td>
                        <td class="desc">
                            <strong>Num:</strong> {{ $persona->Numero }}
                        </td>
                        <td class="desc">
                            <strong>Nick Name:</strong> {{ $persona->Nick_Name }} <br>
                        </td>
                    </tr>
                    <tr>
                        <td class="desc">
                            <strong>Name:</strong> {{ $persona->Nombre }} <br>
                        </td>
                        <td class="desc">
                            <strong>Lastname:</strong> {{ $persona->Apellido_Paterno }} {{ $persona->Apellido_Materno }}
                            <br>
                        </td>
                        <td class="desc">
                            <strong>Birth date:</strong> {{ date('m/d/Y', strtotime($persona->Fecha_Nacimiento)) }} <br>
                        </td>
                    </tr>
                    <tr>

                        <td class="desc">
                            <strong>Phone:</strong> {{ $persona->Telefono }} <br>
                        </td>
                        <td class="desc">
                            <strong>Cell phone:</strong> {{ $persona->Celular }} <br>
                        </td>
                        <td class="desc">
                            <strong>Position:</strong> {{ $persona->Cargo }} <br>
                        </td>
                    </tr>
                    <tr>
                        <td class="desc">
                            <strong>Type of employee:</strong> {{ $persona->aux5 }} <br>
                        </td>
                        <td class="desc">
                            <strong>Email:</strong> {{ $persona->email }} <br>
                        </td>
                        <td class="desc">
                            <strong>Adress:</strong> {{ $persona->dirrecion }} <br>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table>
                <thead>
                    <tr>
                        <th colspan="1" width="33%" class="desc">EVENTS</th>
                        <th colspan="1" width="33%" class="desc"></th>
                        <th colspan="1" width="33%" class="desc"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($persona->eventos as $evento)
                        <tr>
                            <td class="desc">
                                <strong>Type:</strong> {{ $evento->tipo_evento }} <br>
                            </td>
                            <td class="desc">
                                <strong>Name:</strong> {{ $evento->nombre_evento }} <br>
                            </td>
                            <td colspan="2" class="desc">
                                <strong>Note:</strong> {{ $evento->note }} <br>
                            </td>
                        </tr>
                        <tr>
                            <td class="desc">
                                <strong>Start Date:</strong> {{ $evento->start_date }} <br>
                            </td>
                            <td class="desc">
                                <strong>Exp. Date:</strong> {{ $evento->exp_date }} <br>
                            </td>
                            <td class="desc">
                                <strong>Exp. Date:</strong> {{ $evento->exp_date }} <br>
                            </td>
                        </tr>
                        @if ($images == true)
                            <tr>
                                <td colspan="3" style="text-align: center;">
                                    Picture
                                </td>
                            </tr>
                            @forelse (array_chunk($evento->images, 3) as $row)
                                <tr>
                                    @foreach ($row as $image)
                                        <td colspan="1" style="text-align: center;">
                                            <img style=" border: 1px solid #ddd; border-radius: 4px; padding: 1px; width: 180px;"
                                                src="{{ url('/') }}/docs/{{ $image->imagen }}" alt="...">
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="background: #fff;text-align: center;">
                                        No records
                                    </td>
                                </tr>
                            @endforelse
                        @endif
                        <tr>
                            <td colspan="3">
                                <hr style="border: 0.5px solid rgb(211, 211, 211);">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="background: #fff;text-align: center;">
                                no record
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </main>

        <div style="page-break-after: always;"></div>

    @empty
        <tr>
            <td colspan="2" style="text-align: center;">
                Picture
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">
                No records
            </td>
        </tr>
    @endforelse
</body>

</html>
