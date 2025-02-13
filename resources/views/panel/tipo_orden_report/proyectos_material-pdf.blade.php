<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Material and Equipment Report</title>
    <style>
        .clearfix_in:after {
            content: "";
            display: table;
            clear: both;
        }

        body {
            position: relative;
            width: 18.5cm;
            height: 25.7cm;
            margin: 0 auto;
            color: #001028;
            background: #FFFFFF;
            font-family: Arial, sans-serif;
            font-size: 11px;
            font-family: Arial;
        }

        .clearfix_in {
            padding: 10px 0;
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
            font-size: 2.4em;
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
            margin-bottom: 54px;
        }

        table tr:nth-child(2n-1) td {
            /* background: #f5f5f5; */
        }

        table th,
        table td {
            text-align: center;
        }

        table th {
            padding: 2px 5px;
            color: #5d6975;
            border-bottom: 1px solid #c1ced9;
            white-space: normal;
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
            text-align: center;
            font-size: 11px;
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

        .subtotal {
            color: #5d6975;
            background: #ffffff;
            border-bottom: 1px solid #C1CED9;
        }
    </style>
</head>

<body>
    <!--header -->
    <x-pdf.header />
    <!--footer-->
    <x-pdf.footer />
    <!--contenido-->
    <div class="clearfix_in">
        <h1 style="font-size: 18px;">Material and Equipment Report from {{ $fecha_inicio }} to {{ $fecha_fin }}</h1>
    </div>
    @forelse ($proyectos as $proyecto)
        <div class="clearfix_in">
            <div id="project">
                <div><span>GENERAL CONTRACTOR</span>{{ $proyecto->nombre_empresa }}</div>
                <div><span>PRECISION WALL TECH PROJECT</span> {{ $proyecto->Codigo }} / {{ $proyecto->Nombre }} /
                    {{ $proyecto->nombre_estatus }} / {{ $proyecto->Ciudad }} {{ $proyecto->Zip_Code }},
                    {{ $proyecto->Calle }} {{ $proyecto->Estado }} </div>
            </div>
        </div>
        <hr>
        <main>
            <table>
                <thead>
                    <tr>
                        <th style="border-bottom: 1px solid white;" width="40%" class="desc">Denominacion</th>
                        <th style="border-bottom: 1px solid white;" width="10%" class="desc">Unit<br>of<br>measure
                        </th>
                        <th style="border-bottom: 1px solid white;" width="10%" class="desc">Type material</th>
                        <th style="border-bottom: 1px solid white;" width="10%"class="desc">Received Warehouse
                        </th>
                        <th style="border-bottom: 1px solid white;" width="10%" class="desc">Received at Project
                        </th>
                        <th style="border-bottom: 1px solid white;" width="30%" class="desc">Projects
                        </th>
                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th colspan="8"></th>
                    </tr>
                </thead>
                @forelse ($proyecto->materiales as $material)
                    <tbody>
                        {{-- {{ dd($material) }} --}}
                        @forelse ($material->proyectos_total as $proyecto_total)
                            <tr>
                                <td class="desc" style="background: #ffffff;"> {{ $material->Denominacion }}</td>
                                <td class="desc" style="background: #ffffff;">{{ $material->Unidad_Medida }}</td>
                                <td class="desc" style="background: #ffffff;">{{ $material->Nombre }}</td>
                                <td class="desc" style="background: #ffffff;"></td>
                                <td class="desc" style="background: #ffffff; color:#6c6c6c">
                                    {{ $proyecto_total->por_proyecto }}
                                </td>
                                <td class="desc" style="background: #ffffff; color:#6c6c6c">
                                    {{ $proyecto_total->nombre_proyecto }}
                                </td>
                            </tr>
                        @empty
                        @endforelse
                        <tr>
                            <td class="desc"
                                style="background: #ffffff; border-bottom: 1px solid rgb(215, 215, 215);">
                                {{ $material->Denominacion }}
                            </td>
                            <td class="desc"
                                style="background: #ffffff; border-bottom: 1px solid rgb(215, 215, 215);">
                                {{ $material->Unidad_Medida }}
                            </td>
                            <td class="desc"
                                style="background: #ffffff; border-bottom: 1px solid rgb(215, 215, 215);">
                                {{ $material->Nombre }}
                            </td>
                            <td class="desc"
                                style="background: #ffffff; border-bottom: 1px solid rgb(215, 215, 215);">
                                {{ $material->total_warehouse }}
                            </td>
                            <td class="desc"
                                style="background: #ffffff; border-bottom: 1px solid rgb(215, 215, 215);">
                                {{ $material->total_proyecto }}
                            </td>
                            <td class="desc"
                                style="background: #ffffff; border-bottom: 1px solid rgb(215, 215, 215);">

                            </td>
                        </tr>
                    </tbody>
                @empty
                @endforelse

            </table>
        </main>
        @if (!$loop->last)
            <div style="page-break-after: always;"></div>
        @endif
    @empty
    @endforelse
</body>

</html>
