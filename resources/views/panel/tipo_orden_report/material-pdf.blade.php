<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Order report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ public_path('css/invoice-pdf.css') }}" media="all" />
    <style>
        .border {
            border: solid 1px black;
            text-align: center;
        }

    </style>
</head>

<body>
    <x-pdf.header />
    <x-pdf.footer />
    <div class="clearfix_in">
        <h1>Material and Equipment Report from {{ $fecha_inicio }} to {{ $fecha_fin }}</h1>
        <div id="company">
            <div><span>Date: </span> {{ date('m-d-Y') }}</div>
           
        </div>
        <div id="project">
            <div><span>N. Material:</span>{{ count($resultado) }}</div>
           
        </div>
    </div>
    <main>
        {{-- en el caso q sea detallado --}}
        @if ($detalle == 'true')
            <table>
                <thead>
                    <tr>
                        <th class="desc">Material</th>
                        <th class="desc">Project</th>
                        <th class="desc">PO</th>
                        <th class="desc">Date</th>
                        <th class="desc">Status</th>
                        <th class="total">Income</th>
                        <th class="total">Output</th>
                        <th class="desc">From</th>
                        <th class="desc">To</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($resultado as $value)
                        <tr>
                            <th class="desc" colspan="9" style="font-size: 11px;">
                               {{ $value->Denominacion }}
                            </th>
                        </tr>
                        @forelse ($value->proyectos as $proyecto)
                            @forelse ($proyecto->detalle as $key => $movimiento)
                                <tr>
                                    <td class="desc"> </td>
                                    @if ($key == 0)
                                        <td class="desc"><strong>Job:</strong> {{ $proyecto->Nombre }}</td>
                                    @else
                                        <td class="desc"> </td>
                                    @endif
                                    <td class="desc"> {{ $movimiento->PO }}</td>
                                    <td class="desc"> {{ $movimiento->fecha }}</td>
                                    <td class="desc"> {{ $movimiento->nombre_status }}</td>
                                    <td class="total"> {{ $movimiento->ingreso }}</td>
                                    <td class="total"> {{ $movimiento->egreso }}</td>
                                    <td class="desc"> {{ $movimiento->nombre_vendor }}</td>
                                    <td class="desc"> {{ $movimiento->nombre_to }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="total">There is nothing inserted</td>
                                </tr>
                            @endforelse
                            <tr>
                                <th colspan="9" class="desc">
                                </th>
                            </tr>
                            <tr>
                                <th colspan="1" class="total">
                                    Sub total:
                                </th>
                                <th colspan="4" class="desc">
                                    {{ $proyecto->Nombre }}
                                </th>
                                <th colspan="1" class="total">
                                    {{ $proyecto->total_ingreso }}
                                </th>
                                <th colspan="1" class="total">
                                    {{ $proyecto->total_egreso }}
                                </th>
                                <th colspan="1" class="total">
                                    Total:
                                </th>
                                <th colspan="1" class="total">
                                    {{ $proyecto->total }}
                                </th>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="total">There is nothing inserted</td>
                            </tr>
                        @endforelse
                    @empty
                        <tr>
                            <td colspan="9" class="total">There is nothing inserted</td>
                        </tr>
                    @endforelse
                </tbody>
                {{-- <tfoot>
                            <tr>
                                <th colspan="7" class="total">
                                </th>
                            </tr>
                            <tr>
                                <th colspan="3" class="desc">
                                </th>
                                <th colspan="1" class="total">
                                    {{ $proyecto->total_ingreso }}
                                </th>
                                <th colspan="1" class="total">
                                    {{ $proyecto->total_egreso }}
                                </th>
                                <th colspan="1" class="desc">
                                </th>
                                <th colspan="" class="total">
                                    <strong>Total: </strong>{{ $proyecto->total }}
                                </th>
                            </tr>
                        </tfoot> --}}
            </table>
            {{-- @empty --}}
            {{-- <table>
                <tr>
                    <td class="total" colspan="3">
                        no records
                    </td>
                </tr>
            </table> --}}
            {{-- @endforelse --}}
        @else
            @forelse ($resultado as $value)
                <div id="notices">
                    <div>
                        <strong>Material/Equipment: </strong> {{ $value->Denominacion }} &nbsp; &nbsp;
                    </div>
                </div>
                {{-- lista totales --}}
                <table>
                    <thead>
                        <tr>
                            <th class="desc">Projects</th>
                            <th class="desc">Cod:</th>
                            <th class="total">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($value->proyectos as $proyecto)
                            <tr>
                                <td class="desc">
                                    {{ $proyecto->Nombre }}
                                </td>
                                <td class="desc">
                                    {{ $proyecto->Codigo }}
                                </td>
                                <td class="total">
                                    {{ $proyecto->total }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="total" colspan="3">
                                    no records
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                     {{--    <tr>
                            <th colspan="3" class="total">

                            </th>
                        </tr>
                        <tr>
                            <th colspan="1" class="desc">
                                Total:
                            </th>
                            <th colspan="1" class="total">

                            </th>
                            <th colspan="1" class="total">
                                {{dd($value)}}
                            </th>
                        </tr> --}}
                    </tfoot>
                </table>
            @empty
                <div class="notice">no related projects</div>
            @endforelse
        @endif
    </main>
</body>

</html>
