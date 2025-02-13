<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Daily Report {{ $proyecto->actividad_fecha }}</title>
    <link rel="stylesheet" href="{{ public_path('css/invoice-pdf.css') }}" media="all" />
    <style>
        .center {
            text-align: center;
        }
    </style>
</head>

<body>
    <x-pdf.header />
    <x-pdf.footer />
    <div class="clearfix_in">
        <h1>Daily Report {{ $proyecto->actividad_fecha }}</h1>
        <div id="company">
            <div><span>FOREMAN NAME</span> {{ $foreman_name }}</div>
            <div><span>SUPERINTENDENT'S NAME</span></div>
        </div>
        <div id="project">
            <div><span>GENERAL CONTRACTOR</span> {{ $proyecto->empresa }}</div>
            <div><span>PRECISION WALL TECH PROJECT</span> {{ $proyecto->Codigo }}</div>
            <div><span>PROJECT NAME</span> {{ $proyecto->Nombre }}</div>
            <div><span>PROJECT ADDRESS</span> {{ $address }}</div>
        </div>
    </div>

    <main>
        <table>
            <thead>
                <tr>
                    <th class="desc">Area of Work</th>
                    <th class="desc">Task</th>
                    <th class="desc">%Completed at Today</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($resumen as $task)
                    <tr>
                        <td class="desc" width="20%">{{ $task->nombre_area }}</td>
                        <td class="desc" width="60%">{{ $task->nombre_tarea }}</td>
                        <td class="center" width="20%">{{ $task->porcentaje }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="center" colspan="3">No Cost Codes</td>
                    </tr>
                @endforelse

            </tbody>
        </table>
        <table>
            <thead>
                <tr>
                    <th class="desc">DESCRIPTION</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    @if ($daily_report_detail->estado == 'pending')
                        <td style="text-align: center">No Daily Report</td>
                    @else
                        <td class="desc">{!! str_replace("\n", '<br>', $daily_report_detail->detalle) !!}</td>
                    @endif
                </tr>
            </tbody>
        </table>
    </main>
    <div style="page-break-after: always;"></div>
    <h4 style="text-align:center">Pictures</h4>
    <table width="209">
        @forelse (array_chunk($img, 2) as $row)
            <tr style="height: 8cm">
                @foreach ($row as $value)
                    <td width="50%" style="text-align: center; padding:10px; background: #fff;">
                        <p style="text-align: center">{{ $value->referencia }}</p>
                        <img src='{{ public_path("uploads/$value->imagen") }}'
                            style="display:block; width: 95%; height:auto">
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
    </table>
</body>

</html>
