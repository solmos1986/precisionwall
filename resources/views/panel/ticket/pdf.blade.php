<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Work Ticket #{{ $ticket->num }}</title>
    <link rel="stylesheet" href="{{ public_path('css/invoice-pdf.css') }}" media="all" />
</head>

<body>
    <x-pdf.header />
    <x-pdf.footer />
    <div class="clearfix_in">
        <h1>Work Ticket #{{ $ticket->num }}</h1>
        <div id="company">
            <div><span>Ticket Number:</span> {{ $ticket->num }}</div>
            <div><span> Date of Work:</span>
                @if ($ticket->fecha_ticket)
                    {{ date('m-d-Y', strtotime($ticket->fecha_ticket)) }}
                @endif
            </div>
            <div><span>Precision Wall Tech Project:</span>{{ $ticket->Codigo }}</div>
            <div><span>Foreman Name:</span>{{ $ticket->foreman_name }}</div>
            <div><span>Schedule Hours:</span> {{ $ticket->horario }}</div>
        </div>
        <div id="project">
            <div><span>General Contractor:</span> {{ $ticket->empresa }}</div>
            <div><span>Project Name:</span> {{ $ticket->Nombre }}</div>
            <div><span>Project Address:</span> {{ $address }}</div>
             <div><span>Superintendent Name:</span>{{ $ticket->superintendent_name }}</div>
        </div>
    </div>
    <!--
    <p>Total hours before this ticket</p>
    <p>Total Regular hours: {{ $t_reg_hours }} | Total Premium Hours: {{ $t_premium_hours }} | Total Over Time
        Hours: {{ $t_out_hours }} | Total Allowance Hours: {{ $t_prepaid_hours }}</p>-->
    <main>
        <table>
            <thead>
                <tr>
                    <th class="desc">DESCRIPTION</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="desc"><p>PCO:{{ $ticket->pco }}</p>
                    <p> {!! str_replace("\n", '<br>', $ticket->descripcion) !!}</p></td>
                </tr>
            </tbody>
        </table>
        <table>
            <thead>
                <tr>
                    <th>QUANTITY</th>
                    <th>UNIT</th>
                    <th class="desc">MATERIAL AND/OR EQUIPMENT</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($materiales as $val)
                    <tr>
                        <td class="qty">{{ $val->cantidad }}</td>
                        <td class="qty">{{ $val->Unidad_Medida }}</td>
                        <td class="desc">{{ $val->Denominacion }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="total">There is nothing inserted</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <table>
            <thead>
                <tr>
                    <th># workers</th>
                    <th class="service">Position</th>
                    <th>Reg Hrs</th>
                    <th>T. Reg<br>Hrs</th>
                    <th>Premium<br>Hrs</th>
                    <th>T. Premium<br>Hrs</th>
                    <th>Over<br>Time<br>Hours</th>
                    <th>Total<br>Over Time<br>Hours</th>
                    <th>Allowance<br>Hours</th>
                    <th>Total<br>Allowance<br>Hours</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($trabajadores as $val)
                    <tr>
                        <td class="qty">{{ $val->n_worker }}</td>
                        <td class="desc">{{ $val->nombre }}</td>
                        <td class="total">{{ $val->reg_hours }}</td>
                        <td class="qty">{{ $val->reg_hours * $val->n_worker }}</td>
                        <td class="total">{{ $val->premium_hours }}</td>
                        <td class="qty">{{ $val->premium_hours * $val->n_worker }}</td>
                        <td class="total">{{ $val->out_hours }}</td>
                        <td class="qty">{{ $val->out_hours * $val->n_worker }}</td>
                        <td class="total">{{ $val->prepaid_hours }}</td>
                        <td class="qty">{{ $val->prepaid_hours * $val->n_worker }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="total">There is nothing inserted</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="notice"> Date signed and submitted:
            @if ($ticket->fecha_finalizado)
                {{ date('m-d-Y', strtotime($ticket->fecha_finalizado)) }}
            @endif
        </div>
        <br>
        <div id="notices">
            <div>NOTICE:</div>
            <div class="notice">Signer verifies Precision Wall Tech, has completed the work stated above under my
                supervision. Time and
                material listed above are accurate and approved</div>
        </div>
        <div class="cols">
            <div style="text-align: center;">
                <img style="width: 1.5cm"
                    src="{{ $ticket->firma_foreman ? public_path('signatures/empleoye/' . $ticket->firma_foreman) : public_path('signatures/no-signature.jpg') }}">
              <p>Foreman Signature <br>
                {{ $ticket->foreman_name }}</p>
            </div>
            <div style="text-align: center;">
                <img style="width: 1.5cm"
                    src="{{ $ticket->firma_cliente ? public_path('signatures/client/' . $ticket->firma_cliente) : public_path('signatures/no-signature.jpg') }}">
                <p>Superintendent's Signature<br>
                {{ $ticket->superintendent_name }}</p>
            </div>
        </div>
        <!--div class="cols">
            <div>
                @if ($ticket->firma_foreman)
                    <img src="{{ public_path('signatures/empleoye/' . $ticket->firma_foreman) }}">
@else
                    <img src="{{ public_path('signatures/no-signature.jpg') }}">
                @endif
                <p>Foreman Signature</p>
            </div>
            <div>
                @if ($ticket->firma_cliente)
                    <img src="{{ public_path('signatures/client/' . $ticket->firma_cliente) }}">
@else
                    <img src="{{ public_path('signatures/no-signature.jpg') }}">
                @endif
                <p>Superintendent's Signature</p>
            </div
        </div>-->
    </main>
    <div style="page-break-after: always;"></div>
    <h4 style="text-align:center">Previous pictures</h4>
    <table width="209">
        @forelse (array_chunk($img_start, 2) as $row)
            <tr style="height: 8cm">
                @foreach ($row as $value)
                    <td width="50%" style="text-align: center; padding:10px; background: #fff;">
                        <img src='{{ public_path("uploads/$value->imagen") }}' style="display:block; width: 95%; height:auto">
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
    <div style="page-break-after: always;"></div>
    <h4 style="text-align:center">Final pictures</h4>
    <table style="width: 100%">
        @forelse (array_chunk($img_final, 2) as $row)
            <tr style="height: 8cm">
                @foreach ($row as $value)
                    <td width="50%" style="text-align: center; padding:10px; background: #fff;">
                        <img src='{{ public_path("uploads/$value->imagen") }}' style="display:block; width: 95%; height:auto">
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
