<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>document</title>
    <link rel="stylesheet" href="{{ public_path('css/invoice-pdf.css') }}" media="all" />
    <title>Field Visit Report {{ $proyecto->codigo }}</title>
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

</head>

<body>
    <x-pdf.header />
    <x-pdf.footer />
    <div class="clearfix_in">
        <h1>Field Visit Report {{ $proyecto->codigo }}</h1>
        <div id="company" class="clearfix_in">
            <div><span>NOTE NUMBER:</span>{{ $proyecto->codigo }}</div>
            <div><span>DATE OF REPORT</span>
                {{ date('m-d-Y', strtotime($proyecto->fecha_entrega)) }}
            </div>
            <div><span>CREATE BY</span> {{ $proyecto->nombre_empleado }}</div>
        </div>
        <div id="project">
            <div><span>GENERAL CONTRACTOR</span> {{ $proyecto->nombre_empresa }}</div>
            <div><span>PRECISION WALL TECH PROJECT</span> {{ $proyecto->codigo_proyecto }}</div>
            <div><span>PROJECT NAME</span> {{ $proyecto->nombre_proyecto }}</div>
            <div><span>PROJECT ADDRESS</span> {{ $proyecto->dirrecion }}</div>
        </div>
    </div>
    <main>
        <table>
            <thead>
                <tr>
                    <th>Comments</th>
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
    <div style="page-break-after: always;"></div>
    <h4 style="text-align:center">Pictures</h4>
    <x-pdf.doc-images :images="$images" />
</body>

</html>
