<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>reporte de Tickets</title>
</head>
<style>
    .font {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
    }

    .header-izq {
        float: left;
    }

    .header-der {
        float: right;
    }

    .center {
        text-align: center;
    }

    .container {
        background: #b4b3b3;
        margin: 0 0 1rem;
        height: 10rem;
        display: flex;
        /* align-items por defecto tiene el valor `stretch` */
        align-items: start;
    }

    table,
    td,
    th {
        border: 1px solid #ddd;
        text-align: center;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        padding: 3px;
    }

    @page {
        margin: 65px 50px;
    }

    footer .page:after {
        content: counter(page);

    }

    .total {
        background: #f5f3f3
    }

    .corte-control {
        float: left;
        margin-left: 5px;
    }

</style>

<body>
    <div class=" font">
        <div class="header-izq">
            <img
                src="data:image/jpeg;base64,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">
            <div style="font-size: 10px;position:absolute" class="">
                <strong>
                    User:
                </strong>
                hector <br>
                administrador
            </div>
        </div>
        <div class="header-der">
            <div><strong>Report date: </strong>{{ date('Y-m-d') }}</div>
        </div>

        <br>
        <br>
        <h3 class="center">Reporte Tickets</h3>
    </div>
    @if ($reporte_tickets)
        <p class="center font"><strong>Summary total ticket:</strong> {{ $total }} <strong>Date:
            </strong>{{ $fecha_inicio }}<strong> to </strong>{{ $fecha_fin }}</p>
            <!--table>
                <td colspan="5">
                    <Strong>Ticket summary </Strong>
                </td>
                <td>
                    Total ticket:<br>
                    {{ $total }}
                </td>
                <td>
                    total workers:<br>
                    {{ $total_trabajadores }}
                </td>
                <td>
                    Total out hours:<br>
                    {{ $total_out_hours }}
                </td>
                <td>
                    Total reg hours:<br>
                    {{ $total_reg_hours }}
                </td>
                <td>
                    Total premium hours:<br>
                    {{ $total_premium_hours }}
                </td>
            </table-->
        @foreach ($reporte_tickets as $report)
            <table style=”width: 100%” class="font">
                <tr style="background: #cccccc">
                    <td style="text-align: start" colspan="5">
                        <strong>Id:</strong>{{ $report['Pro_ID'] }}&nbsp;&nbsp;
                        <strong>Name Proyect:</strong>{{ $report['Nombre'] }}&nbsp;&nbsp;&nbsp;
                        <strong>Status:</strong>{{ $report['estado'] }}&nbsp;&nbsp;
                        <strong>Code Company:</strong>{{ $report['Codigo'] }}&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
                @if ($report['tickets'])
                    @foreach ($report['tickets'] as $ticket)
                        <colgroup>
                            <col style="width: 20%" />
                            <col style="width: 40%" />
                            <col style="width: 40%" />
                        </colgroup>
                        <thead>
                            <tr>
                                <td rowspan="3"><strong>Num Ticket: </strong>{{ $ticket['num'] }}</td>
                                <td colspan="4"><strong>Foreman name: </strong>{{ $ticket['foreman_name'] }} </td>

                            </tr>
                            <tr>
                                <td><strong>Status: </strong>{{ $ticket['estado'] }}</td>
                                <td><strong>Turn: </strong>{{ $ticket['horario'] }}</td>
                                <td><strong>Start date: </strong><br>{{ $ticket['fecha_ticket'] }}</td>
                                <td><strong>Start date: </strong><br>{{ $ticket['fecha_ticket'] }}</td>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td colspan="4"><strong>Description :</strong>{{ $ticket['descripcion'] }}</td>
                            </tr>
                        </tfoot>
                        <tbody class="total">
                            @if ($ticket['tickets_trabajadores'])

                                <tr>
                                    <th><strong>Num worker</strong></th>
                                    <th><strong>Profession</strong></th>
                                    <th><strong>total out hours</strong></th>
                                    <th><strong>total reg hours</strong></th>
                                    <th><strong>total premium hours</strong></th>
                                </tr>
                                @foreach ($ticket['tickets_trabajadores'] as $tickets_trabajadores)
                                    <tr>
                                        <td>{{ $tickets_trabajadores->n_worker }}</td>
                                        <td>{{ $tickets_trabajadores->nombre_tipo }}</td>
                                        <td>{{ $tickets_trabajadores->total_out_hours }}</td>
                                        <td>{{ $tickets_trabajadores->total_reg_hours }}</td>
                                        <td>{{ $tickets_trabajadores->total_premium_hours }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="5"><strong>Summary total</strong></td>
                                </tr>
                                <tr>

                                    <td>{{ $ticket['totales']->total_trabajadores }}</td>
                                    <td>-----------</td>
                                    <td>{{ $ticket['totales']->total_out_hours }}</td>
                                    <td>{{ $ticket['totales']->total_reg_hours }}</td>
                                    <td>{{ $ticket['totales']->total_premium_hours }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="5">there is no associated worker</td>
                                </tr>
                            @endif
                        </tbody>
                    @endforeach
                @else
                    <thead>
                        <td>there is no registered ticket</td>
                    </thead>
                @endif
            </table>
            <br>
        @endforeach
    @else
        <p class="center font" style="text-align: center">no registered ticket from {{ $fecha_inicio }} to
            {{ $fecha_fin }}
        </p>
    @endif

</body>
<!--footer>
    <p class="header-der page">
        Página
    </p>
</footer-->

</html>
