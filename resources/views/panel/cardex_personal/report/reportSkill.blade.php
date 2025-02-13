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
            margin: 0 auto;
            color: #001028;
            background: #FFFFFF;
            font-family: Arial, sans-serif;
            font-size: 11px;
            font-family: Arial, Helvetica, sans-serif;
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
            font-size: 0.7rem;
        }

        #project span {
            color: #5D6975;
            text-align: left;
            width: auto;
            margin-right: 10px;
            display: inline;
            font-size: 0.7rem;
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
            margin-bottom: 60px;
        }

        table tr:nth-child(2n-1) td {
            background-color: #f2f2f2;
        }

        table th,
        table td {
            padding: 0px;
            text-align: left;
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
            text-align: left;
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
            ;
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

        body {
            margin: 2.5cm 1.3cm 1cm;
        }
    </style>
</head>

<body>
    <x-pdf.header />
    <x-pdf.footer />
    <div class="clearfix_in">
        <h1>Report employees by skills</h1>
    </div>
    <div class="clearfix_in">
        <div id="company" class="clearfix_in">
            <div><span>Date of report:</span>
                {{ date('m-d-Y') }}
            </div>
        </div>
        <div id="project">
            <div><span>Total record</span>{{count($personas)}}</div>
        </div>
    </div>
    <main>
        <table>
            <thead>
                <tr>
                    <th width="3%">#</th>
                    <th width="5%">Num</th>
                    <th width="15%">NickName</th>
                    <th width="10%">Postion</th>
                    <th width="15%">Email</th>
                    <th width="40%">Skill</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($personas as $key => $persona)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $persona->Numero }}</td>
                        <td>{{ $persona->Nick_Name }}</td>
                        <td>{{ $persona->Cargo }}</td>
                        <td>{!! str_replace(',', '<br>', $persona->email) !!}</td>
                        <td>
                            @foreach ($persona->eventos as $evento)
                                <strong>{{ $evento->tipoEvento }} :</strong> {{ $evento->evento }} <br>
                            @endforeach
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No data</td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </main>
    {{-- @forelse ($personas as $persona)
        <main>
            <table>
                <thead>
                    <tr>
                        <th colspan="2" class="desc">INFORMATION EMPLOYEE</th>
                    </tr>
                </thead>
                <thead>

                </thead>
                <tbody>

                    <tr>
                        <td class="desc">
                            <strong>Company:</strong> {{ $persona->company }} <br>
                        </td>
                        <td class="desc">
                            <strong>Num:</strong> {{ $persona->Numero }}
                        </td>
                    </tr>
                    <tr>
                        <td class="desc">
                            <strong>Name:</strong> {{ $persona->Nombre }} <br>
                        </td>
                        <td class="desc">
                            <strong>Lastname:</strong> {{ $persona->apellidos }} <br>
                        </td>
                    </tr>
                    <tr>
                        <td class="desc">
                            <strong>Birth date:</strong> {{ $persona->Fecha_Nacimiento }} <br>
                        </td>
                        <td class="desc">
                            <strong>Phone:</strong> {{ $persona->Telefono }} <br>
                        </td>
                    </tr>
                    <tr>
                        <td class="desc">
                            <strong>Nick Name:</strong> {{ $persona->Nick_Name }} <br>
                        </td>
                        <td class="desc">
                            <strong>Cell phone:</strong> {{ $persona->Celular }} <br>
                        </td>
                    </tr>
                    <tr>
                        <td class="desc">
                            <strong>Position:</strong> {{ $persona->cargo_personal }} <br>
                        </td>
                        <td class="desc">
                            <strong>Type of employee:</strong> {{ $persona->tipo_personal }} <br>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="desc">
                            <strong>Email:</strong> {{ $persona->email }} <br>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="desc">
                            <strong>Adress:</strong> {{ $persona->dirrecion }} <br>
                        </td>
                    </tr>
                </tbody>
            </table>
            @forelse ($persona->eventos as $evento)
                <table>
                    <thead>
                        <tr>
                            <th colspan="1" class="desc">Event</th>
                            <th colspan="1" class="desc">{{ $evento->tipo_evento }} </th>
                        </tr>
                    </thead>
                    <thead>

                    </thead>
                    <thead>
                        <tr>
                            <td class="desc">
                                <strong>Name:</strong> {{ $evento->nombre_evento }} <br>
                            </td>
                            <td class="desc">
                                <strong>Type:</strong> {{ $evento->tipo_evento }} <br>
                            </td>
                        </tr>
                        <tr>
                            <td class="desc">
                                <strong>Start Date:</strong> {{ $evento->start_date }} <br>
                            </td>
                            <td class="desc">
                                <strong>Exp. Date:</strong> {{ $evento->exp_date }} <br>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="desc">
                                <strong>note:</strong> {{ $evento->note }} <br>
                            </td>
                        </tr>
                        @if ($images == true)
                            <tr>
                                <td colspan="2" style="text-align: center;">
                                    Picture
                                </td>
                            </tr>
                            @forelse (array_chunk($evento->images, 2) as $row)
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
                                    <td colspan="2" style="background: #fff;" style="text-align:center">
                                        <p style="text-align:center">No records</p>
                                    </td>
                                </tr>
                            @endforelse
                            <div style="page-break-after: always;"></div>
                        @endif
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                @if ($loop->last)
                    <div style="page-break-after: always;"></div>
                @endif
            @empty
                <table>
                    <thead>
                        <tr>
                            <th colspan="2" class="desc">Events</th>
                        </tr>
                    </thead>
                    <thead>

                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="2" style="text-align: center;">
                                Picture
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;">
                                <p style="text-align:center">No records</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div style="page-break-after: always;"></div>
            @endforelse
        @empty
            <tr>
                <td colspan="2" style="text-align: center;">
                    Picture
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <p style="text-align:center">No records</p>
                </td>
            </tr>
        </main>

    @endforelse --}}
</body>

</html>
