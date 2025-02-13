<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .clearfix_in:after {
            content: "";
            display: table;
            clear: both;
        }

        body {
            position: relative;
            width: 27.2cm;
            height: 25.7cm;
            margin: 0 auto;
            color: #001028;
            background: #FFFFFF;
            font-family: Arial, sans-serif;
            font-size: 12px;
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
            margin-bottom: 49px;
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
    <!--header -->
    <x-pdf.horizontal.header />
    <!--footer-->
    <x-pdf.horizontal.footer />
    <!--contenido-->
    <div class="clearfix_in">
        <h1>Field Visit Reports </h1>
    </div>
    @foreach ($proyectos as $proyecto)
        <div class="clearfix_in">
            <div id="company" class="clearfix_in">
                <div><span>DATE OF REPORT:</span>
                    {{ date('m-d-Y') }}
                </div>
            </div>
            <div id="project">
                <div><span>GENERAL CONTRACTOR</span>{{ $proyecto->nombre_empresa }}</div>
                <div><span>PRECISION WALL TECH PROJECT</span> {{ $proyecto->codigo_proyecto }}</div>
                <div><span>PROJECT NAME</span> {{ $proyecto->nombre_proyecto }}</div>
                <div><span>PROJECT ADDRESS</span> {{ $proyecto->dirrecion }}</div>
            </div>
        </div>
        <main>
            <table>
                <thead>
                    <tr>
                        <th colspan="6"></th>
                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th style="border-bottom: 1px solid white; width:5%" class="desc">Nro. VR</th>
                        <th style="border-bottom: 1px solid white; width:10%" class="desc">Date</th>
                        <th style="border-bottom: 1px solid white; width:10%" class="desc">Report By</th>
                        <th style="border-bottom: 1px solid white; width:5%" class="desc">Emailed</th>
                        <th style="border-bottom: 1px solid white; width:5%" class="desc">Downloaded</th>
                        <th style="border-bottom: 1px solid white; width:65%" class="desc">Coments</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($proyecto->visit_reports as $visit_report)
                        <tr>
                            <td class="desc">{{ $visit_report->Codigo }}</td>
                            <td class="desc">{{ $visit_report->Fecha }}</td>
                            <td class="desc">{{ $visit_report->nombre_empleado }}</td>
                            <td class="desc">{{ $visit_report->email_send }}</td>
                            <td class="desc">{{ $visit_report->descargas }}</td>
                            <td class="desc"> {!! str_replace("\n", '<br>', $visit_report->Drywall_comments) !!}</td>
                        </tr>
                        @if ($imagen == true)
                            <tr>
                                <td colspan="6">
                                    <table style="margin-bottom: 0px;">
                                            @forelse (array_chunk($visit_report->images, 4) as $row)
                                            <tr>
                                                @foreach ($row as $key => $value)
                                                    <td width="25%"
                                                        style="text-align: center; padding:2px; background: #fff;">
                                                        <img src='{{ public_path("uploads/$value->imagen") }}'
                                                            style="display:block; width: 55%; height:auto">
                                                    </td>
                                                @endforeach
                                            </tr>
                                            @empty
                                                <td style="background: #fff;">
                                                    <h3 style="text-align:center">-//-</h3>
                                                </td>
                                            @endforelse
                                    </table>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td style="text-align: center" colspan="6">no records</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </main>
        @if (!$loop->last)
        <div style="page-break-after: always;"></div>
    @endif
    @endforeach
</body>

</html>
