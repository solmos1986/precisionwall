<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{ asset('css/invoice-pdf.css') }}" media="all" />
    <title>Field Visit Report {{ $goal->Codigo}}</title>
    <style>
        td input[type="checkbox"] {
            float: none;
            margin: 0 auto;
            width: 10%;
        }

        type=checkbox]:after {
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

    </style>
    <style>
        [type=checkbox]:after {
            content: attr(value);
            margin: 7px 10px;
            vertical-align: top;
            white-space: nowrap;
            display: inline-block;
            font-size: 10px;
            font-family: 'Times New =0Roman', Times, serif;
            
          }
          [type=radio]:after {
            content: attr(value);
            margin: 6px 10px;
            vertical-align: top;
            white-space: nowrap;
            display: inline-block;
            font-size: 10px;
            font-family: 'Times New Roman', Times, serif;
            
          }
    </style>

</head>

<body>
    <x-pdf.header />
    <x-pdf.footer />
    <div class="clearfix_in">
        <div id="logo">
            <img src="{{ asset('img/logo.png') }}">
        </div>
        <h1>Superintendent Daily Field Report #{{ $goal->Informe_ID }}</h1>
        <div id="company" class="clearfix_in">
            <div><span>GENERAL CONTRACTOR</span> {{ $goal->nombre_empresa }}</div>
            <div><span>CODE</span> {{ $goal->Codigo }}</div>

        </div>
        <div id="project">
            <div><span>NAME PROYECT</span> {{ $goal->nombre_proyecto }}</div>
            <div><span>SUPERINTENDENT'S NAME</span> {{ $goal->nombre_empleado }}</div>
            <div><span>NUM REPORT</span> #{{ $goal->Informe_ID }}</div>
            <div><span>PROJECT ADDRESS</span> {{ $goal->dirrecion }}</div>
        </div>
    </div>
    <main>
      <table>
        <thead>
            <tr>
                <th colspan="3" class="desc">PWT WORK AND CRE:  </th>
            </tr>
        </thead>
        <thead>
            <tr>
                <th>Actual working areas </th>
                <th class="service">Quality</th>
                <th  >Perception of the production rate</th> 
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="desc">
                    {{ ($goal->pwt_actual != '' ) ? $goal->pwt_actual : '' }}
                </td>
                <td class="desc">
                    {{ ($goal->pwt_quality != '' ) ? $goal->pwt_quality : '' }}
                </td>
                <td class="desc">
                    {{ ($goal->pwt_production_rate) }}
                </td>
            </tr>
        </tbody>
        <thead>
            <tr>
                <th colspan="2">Comments</th>
                <th colspan="2">Actions taken or need to be take</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="2" class="desc">
                    {{ ($goal->pwt_comments != '' ) ? $goal->pwt_comments : '' }}
                </td>
                <td colspan="2" class="desc">
                    {{ ($goal->pwt_action != '' ) ? $goal->pwt_action : '' }}
                </td>
            </tr>
        </tbody>
        <thead>
            <tr>
                <th colspan="3">Miscellaneous (Deliveries, Pick‐Up of Equipment, Meetings Etc.)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="3" class="desc">
                    {{ ($goal->pwt_miscellaneous != '' ) ? $goal->pwt_miscellaneous : '' }}
                </td>
            </tr>
        </tbody>
      </table>
    </main>
    <br>
    <br>
    <main>
        <table>
            <thead>
                <tr>
                    <th colspan="2" class="desc">GC AND OTHER TRADES WORK </th>
                </tr>
            </thead>
            <thead>
                <tr>
                    <th>GC organization and sequencing</th>
                    <th >Actions taken or need to be taken</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="desc">
                        {{ ($goal->gc != '' ) ? $goal->gc : '' }}
                    </td>
                    <td class="desc">
                        {{ ($goal->gc_action != '' ) ? $goal->gc_action : '' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </main>
    <div style="page-break-after: always;"></div>
    <main>
        <table>
            <thead>
                <tr>
                    <th colspan="2" class="desc">OTHER TRADES WORK </th>
                </tr>
            </thead>
            <thead>
                <tr>
                    <th colspan="2">Quality of the substrates Drywall / wood / metals / concrete etc</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2" class="desc">
                        {{ ($goal->quality_comments != '' ) ? $goal->quality_comments : '' }}
                    </td>
                    
                </tr>
            </tbody>
            <thead>
                <th colspan="2">Actions taken or need to be taken</th>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2" class="desc">
                        {{ ($goal->quality_action_taken != '' ) ? $goal->quality_action_taken : '' }}
                    </td>
                </tr>
            </tbody>
        </table>
        @if(isset($goal->Drywall))
        <div style="margin: -10px 0px;">Drywall point up:</div><br>
        <input  type="radio" value="{{ ($goal->Drywall == 'Excessive' ) ? 'Excessive' : 'Acceptable 5% of the wall  ' }}" checked/>
        @else
            
        @endif
        <table>
            <thead>
                <tr>
                    <th>Comments</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="desc">
                        {{ ($goal->Drywall_comments != '' ) ? $goal->Drywall_comments : '' }}
                    </td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th>Actions taken or need to be take</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="desc">
                        {{ ($goal->Drywall_action_taken != '' ) ? $goal->Drywall_action_taken : '' }}
                    </td>
                </tr>
            </tbody>
        </table>
     
    </main>
    
    <div style="page-break-after: always;"></div>
    <h4 style="text-align:center">IMAGES</h4>
   
    <x-pdf.images :images="$images" />
    
</body>

</html>
