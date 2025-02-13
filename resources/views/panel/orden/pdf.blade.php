<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Example 1</title>
    <link rel="stylesheet" href="{{ public_path('css/invoice-pdf.css') }}" media="all" />
</head>

<body>
    <x-pdf.header />
    <x-pdf.footer />
    <div class="clearfix_in">
        <h1>ORDER WC INSTALLATION #{{ $orden->num }}</h1>
        <div id="company" class="clearfix_in">
            <div><span>ORDER DATE</span> 
                @if($orden->date_order)
                {{ date("m-d-Y", strtotime($orden->date_order))}}
                @endif</div>
            <div><span>DATE SCHEDULE</span>
                @if($orden->date_work)
                {{ date("m-d-Y", strtotime($orden->date_work))}}
                @endif
            </div>
            <div><span>NAME BY</span> {{ $orden->creator }}</div>
        </div>
        <div id="project">
            <div><span>JOB NAME</span> {{ $orden->job_name }}</div>
            <div><span>SUB CONTRACTOR</span> {{ $orden->empresa }}</div>
            <div><span>NAME SUB C. EMPLEOYEE</span> {{ $orden->sub_employe }}</div>
        </div>
    </div>
    <main>
        <table>
            <thead>
                <tr>
                    <th class="desc">MATERIAL</th>
                    <th>QUANTITY <br> ORDERED</th>
                    <th>Q. TO <br>THE JOB SITE</th>
                    <th>QUANTITY<br> INSTALLED</th>
                    <th>DATE<br> INSTALLED</th>
                    <th>Q. REMAINING<br> WC</th>
                    <th>REMAINING <br>WC STORED AT</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($materiales as $val)
                    <tr>
                        <td class="desc">{{ $val->Denominacion }}</td>
                        <td class="qty">{{ $val->q_ordered }}</td>
                        <td class="qty">{{ $val->q_job_site }}</td>
                        <td class="qty">{{ $val->q_installed }}</td>
                        <td class="qty">
                            @if($val->d_installed)
                                {{ date("m-d-Y", strtotime($val->d_installed))}}
                            @endif
                        </td>
                        <td class="qty">{{ $val->q_remaining_wc }}</td>
                        <td class="qty">{{ $val->remaining_wc_stored }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">I don't add anything</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div id="notices">
            <div>NOTICE:</div>
            <div class="notice">Signer verifies PWT, has completed the work stated above under my supervision. Time and
                material listed above are accurate and approved</div>
        </div>
        <!--x-pdf.firma :firma_installer="$orden->firma_installer" :firma_foreman="$orden->firma_foreman"  /-->
        <div class="cols">
            <div style="text-align: center;">
                <img style="width: 2.5cm"
                    src="{{ $orden->firma_installer ? public_path('signatures/install/' . $orden->firma_installer) : public_path('signatures/no-signature.jpg') }}">
                <p>Installer Signature</p>
            </div>
            <div style="text-align: center;">
                <img style="width: 2.5cm"
                    src="{{ $orden->firma_foreman ? public_path('signatures/empleoye/' . $orden->firma_foreman) : public_path('signatures/no-signature.jpg') }}">
                <p>Superintendent's Signature</p>
            </div>
        </div>
    </main>
    <div style="page-break-after: always;"></div>
    <h4 style="text-align:center">STARTUP IMAGES</h4>
    <table>
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
                    <h3 style="text-align:center">There is nothing inserted</h3>
                </td>
            </tr>
        @endforelse
    </table>
    <div style="page-break-after: always;"></div>
    <h4 style="text-align:center">FINAL IMAGES</h4>
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
                    <h3 style="text-align:center">There is nothing inserted</h3>
                </td>
            </tr>
        @endforelse
    </table>
</body>

</html>
