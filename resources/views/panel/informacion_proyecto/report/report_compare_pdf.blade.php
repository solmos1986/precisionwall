<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Production analysis</title>
    <!--link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"-->

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
            background: #f5f5f5;
        }

        table th,
        table td {
            text-align: center;
        }

        table th {
            padding: 5px 10px;
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
            padding: 10px;
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
    </style>

</head>

<body>
    <x-pdf.header />
    <x-pdf.footer />
    <div class="clearfix_in">
        <h1>Production analysis</h1>
        <div id="company" class="clearfix_in">
            <div><span>GENERATED:</span> {{ date('m-d-Y') }}</div>
        </div>
    </div>
    <main>
        <table>
            <thead>
                <tr>
                    <th colspan="9">
                    </th>
                </tr>
            </thead>
            <thead>
                <tr>
                    <th width="20%" class="desc">
                        Project
                    </th>
                    <th class="desc">
                        Cod cost
                    </th>
                    <th width="25%" class="desc">
                        Name task
                    </th>
                    <th class="desc">
                        # items
                    </th>
                    <th class="desc">
                        Hrs. Estimated
                    </th>
                    <th class="desc">
                        Hrs. Used
                    </th>
                    <th class="desc">
                        Estimate quantity
                    </th>
                    <th class="desc">
                        Estimate production rate
                    </th>
                    <th class="desc">
                        Actual production rate
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($proyectos as $proyecto)
                    @if ($proyecto->nombre_proyecto == 'Total')
                        <tr>
                            <td class="desc" style="color:#000FFF">{{ $proyecto->nombre_proyecto }}</td>
                            <td class="desc" style="color:#000FFF">{{ $proyecto->Tas_IDT }}</td>
                            <td class="desc" style="color:#000FFF">{{ $proyecto->Nombre }}</td>
                            <td class="desc" style="color:#000FFF">{{ $proyecto->cantidad }}</td>
                            <td class="desc" style="color:#000FFF">{{ number_format($proyecto->horas_estimadas, 2)  }}</td>
                            <td class="desc" style="color:#000FFF">{{ number_format($proyecto->horas_usadas, 2) }}</td>
                            <td class="desc" style="color:#000FFF">
                                {{ number_format($proyecto->horas_cc_butdget_qty, 2) }}</td>
                            <td class="desc" style="color:#000FFF">{{ number_format($proyecto->estimate_producction_rate, 2)  }}</td>
                            <td class="desc" style="color:#000FFF">{{ number_format($proyecto->actual_porcentaje_rate, 2) }}</td>
                        </tr>
                        </tr>
                        <tr>
                            <td colspan="9"></td>
                        </tr>
                    @else
                        <tr>
                            <td class="desc"s tyle='color:#000FFF'>{{ $proyecto->nombre_proyecto }}</td>
                            <td class="desc">{{ $proyecto->Tas_IDT }}</td>
                            <td class="desc">{{ $proyecto->Nombre }}</td>
                            <td class="desc">{{ $proyecto->cantidad }}</td>
                            <td class="desc">{{ number_format($proyecto->horas_estimadas, 2)  }}</td>
                            <td class="desc">{{ number_format($proyecto->horas_usadas, 2) }}</td>
                            <td class="desc">{{ number_format($proyecto->horas_cc_butdget_qty, 2) }}</td>
                            <td class="desc">{{ number_format($proyecto->estimate_producction_rate, 2)  }}</td>
                            <td class="desc">{{ number_format($proyecto->actual_porcentaje_rate, 2)  }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </main>
</body>

</html>
