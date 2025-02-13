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
            /* background: #F5F5F5; */
        }

        table th,
        table td {
            padding: 0px;
            text-align: center;
        }

        table th {
            padding: 2px 5px;
            color: #5D6975;
            border-bottom: 1px solid #C1CED9;
            white-space: normal;
            font-weight: normal;
        }

        table .service,
        table .desc {
            text-align: left;
        }

        table .asc {
            text-align: right;
            padding-right: 10px;
        }

        table td {
            padding: 5px;
            text-align: right;
        }

        table td.service,
        table td.desc {
            padding-left: 10px;
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

        .subtotal {
            color: #5d6975;
            background: #ffffff;
            /* border-bottom: 1px solid #C1CED9; */
        }

        .total {
            /* color: #5d6975; */
            background: #ffffff;
            font-weight: bold;
            border-bottom: 1px 1px 1px 1px solid #C1CED9;
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
                        <th style="border-bottom: 1px solid white;" width="15%" class="desc">Denominacion</th>
                        <th style="border-bottom: 1px solid white;" width="10%" class="desc">Date
                        </th>
                        <th style="border-bottom: 1px solid white;" width="5%" class="desc">Quantity pre ordered
                        </th>
                        <th style="border-bottom: 1px solid white;" width="10%" class="desc">Status</th>
                        <th style="border-bottom: 1px solid white;" width="10%" class="desc">PO</th>
                        <th style="border-bottom: 1px solid white;" width="5%" class="desc">Quantity ordered</th>
                        <th style="border-bottom: 1px solid white;" width="5%" class="desc">Received Warehouse
                        </th>
                        <th style="border-bottom: 1px solid white;" width="5%" class="desc">Received at Project
                        </th>
                        <th style="border-bottom: 1px solid white;" width="15%" class="desc">From </th>
                        <th style="border-bottom: 1px solid white;" width="15%" class="desc">To </th>
                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th colspan="10"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($proyecto->materiales as $material)
                        @forelse ($material->pedidos as $pedido)
                            {{--     {{dd($pedido)}} --}}
                            <tr>
                                <td class="desc ">{{ $material->Denominacion }}</td>
                                <td class="desc ">
                                    {{ date('m/d/Y', strtotime($pedido->Fecha)) }}
                                </td>
                                <td class="desc ">
                                    {{ $pedido->pre_orden ? $pedido->pre_orden->cant_registrada : '' }}
                                </td>
                                <td class="desc ">
                                    {{ $pedido->pre_orden ? $pedido->pre_orden->nombre : '' }}
                                </td>
                                <td class="desc ">{{ $pedido->PO }}</td>
                                <td class="desc ">{{ $pedido->Cantidad }}</td>
                                <td class="desc "></td>
                                <td class="desc "></td>
                                <td class="desc "></td>
                                <td class="desc "></td>
                            </tr>
                            {{-- {{dd($pedido)}} --}}
                            @forelse ($pedido->movimientos as $key => $movimiento)
                                <tr>
                                    <td class="desc "></td>
                                    <td class="desc "></td>
                                    <td class="desc "></td>
                                    <td class="desc "></td>
                                    <td class="desc"></td>
                                    <td class="desc"></td>
                                    <td class="desc subtotal">
                                        {{ $movimiento->total_warehouse }}
                                    </td>
                                    <td class="desc subtotal">
                                        {{ $movimiento->total_proyecto }}
                                    </td>
                                    <td class="desc subtotal">
                                        {{ $movimiento->Pro_id_ubicacion != $pedido->Pro_ID ? $movimiento->Nombre : '' }}
                                    </td>
                                    <td class="desc subtotal">
                                        {{ $movimiento->Pro_id_ubicacion == $pedido->Pro_ID ? $movimiento->Nombre : '' }}
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        @empty
                        @endforelse
                        <tr>
                            <td colspan="10" class="desc total"></td>
                        </tr>
                        <tr>
                            <td class="desc total">{{ $material->Denominacion }}</td>
                            <td class="desc total">-</td>
                            <td class="desc total">-</td>
                            <td class="desc total">-</td>
                            <td class="desc total">-</td>
                            <td class="desc total">{{ $material->total_cantidad }}</td>
                            <td class="desc total">{{ $material->total_warehouse }}</td>
                            <td class="desc total">{{ $material->total_proyecto }}</td>
                            <td class="desc total">-</td>
                            <td class="desc total">-</td>
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </main>
        @if (!$loop->last)
            <div style="page-break-after: always;"></div>
        @endif

        {{-- <main>
                <table>
                    <thead>
                        <tr>
                            <th colspan="10"></th>
                        </tr>
                    </thead>
                    <thead>
                        <tr>
                            <th style="border-bottom: 1px solid white; width:10%" class="desc">Denominacion</th>
                            <th style="border-bottom: 1px solid white; width:3%" class="desc">Quantity <br> pre
                                ordered
                            </th>
                            <th style="border-bottom: 1px solid white; width:12%" class="desc">Date</th>
                            <th style="border-bottom: 1px solid white; width:8%" class="desc">PO</th>
                            <th style="border-bottom: 1px solid white; width:5%" class="desc">Status</th>
                            <th style="border-bottom: 1px solid white; width:1%" class="desc">Quantity<br>Ordered</th>
                            <th style="border-bottom: 1px solid white; width:5%" class="desc">Received<br>Warehouse
                            </th>
                            <th style="border-bottom: 1px solid white; width:5%" class="desc">Received <br> at Project
                            </th>
                            <th style="border-bottom: 1px solid white; width:15%" class="desc">From </th>
                            <th style="border-bottom: 1px solid white; width:15%" class="desc">To </th>
                        </tr>
                    </thead>
                    <thead>
                        <tr>
                            <th colspan="10"> </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="10" style="text-align: center">no record</td>
                        </tr>
                    </tbody>
                </table>
            </main>
            <div style="page-break-after: always;"></div> --}}

    @empty

    @endforelse
    {{-- @if (!$loop->last)
        <div style="page-break-after: always;"></div>
    @endif
    @empty
        <tbody>
            <tr>
                <td style="text-align: center" colspan="11">no records</td>
            </tr>
        </tbody>
        @if (!$loop->last)
            <div style="page-break-after: always;"></div>
        @endif
        @endforelse
        @if (!$loop->last)
            <div style="page-break-after: always;"></div>
        @endif
        @endforeach --}}
</body>

</html>
