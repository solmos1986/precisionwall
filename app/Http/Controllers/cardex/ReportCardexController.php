<?php

namespace App\Http\Controllers\cardex;

use App\Exports\resourceHuman\recursosSkills;
use App\Http\Controllers\Controller;
use App\Personal;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use PDF;
use \stdClass;

class ReportCardexController extends Controller
{
    public function __construct(Excel $excel)
    {
        $this->excel = $excel;
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $images = $request->query('images') == 'true' ? true : false;
        $personas = Personal::select(
            'personal.Empleado_ID',
            'personal.Numero',
            'personal.Nick_Name',
            'personal.Nombre',
            'personal.Apellido_Paterno',
            'personal.Apellido_Materno',
            'tipo_personal.nombre as nombre_tipo',
            'personal.Cargo',
            'personal.email',
            'personal.Fecha_Nacimiento',
            'personal.aux5',
            'empresas.Nombre as nombre_empresa'
        )
        //filtrando Company
            ->when(!empty(request()->companies), function ($q) {
                return $q->whereIn('empresas.Emp_ID', explode(',', request()->companies));
            })
        //filtrando tipos
            ->when(!empty(request()->tipos), function ($q) {
                return $q->whereIn('personal.tipo_personal_id', explode(',', request()->tipos));
            })
        //filtrando cargo
            ->when(!empty(request()->cargos), function ($q) {
                return $q->whereIn('personal.cargo_personal_id', explode(',', request()->cargos));
            })
        //filtrando nickname
            ->when(!empty(request()->personas), function ($q) {
                return $q->whereIn('personal.Empleado_ID', explode(',', request()->personas));
            })
        //filtrando nickname
            ->when(!empty(request()->eventos), function ($q) {
                return $q->leftjoin('movimientos_eventos', 'movimientos_eventos.Empleado_ID', 'personal.Empleado_ID')
                    ->leftjoin('evento', 'evento.cod_evento', 'movimientos_eventos.cod_evento')
                    ->where('movimientos_eventos.estado', '1')
                    ->where('evento.estado', '1')
                    ->whereIn('movimientos_eventos.cod_evento', explode(',', request()->eventos));
            })
            ->join('empresas', 'empresas.Emp_ID', 'personal.Emp_ID')
            ->leftjoin('tipo_personal', 'tipo_personal.id', 'personal.tipo_personal_id')
            ->leftjoin('cargo_personal', 'cargo_personal.id', 'personal.cargo_personal_id')
            ->where('personal.status', '1')
            ->groupBy('personal.Empleado_ID')
            ->orderBy('personal.Nombre', 'ASC')
            ->get();
        foreach ($personas as $key => $persona) {
            $persona->eventos = DB::table('movimientos_eventos')
                ->select(
                    'movimientos_eventos.movimientos_eventos_id',
                    'evento.nombre as nombre_evento',
                    'tipo_evento.nombre as tipo_evento',
                    DB::raw('DATE_FORMAT(movimientos_eventos.start_date, "%m/%d/%Y") as start_date'),
                    'movimientos_eventos.note as note',
                    DB::raw('DATE_FORMAT(movimientos_eventos.exp_date, "%m/%d/%Y") as exp_date'),
                    'evento.estado'
                )
                ->join('evento', 'evento.cod_evento', 'movimientos_eventos.cod_evento')
                ->join('tipo_evento', 'tipo_evento.tipo_evento_id', 'evento.tipo_evento_id')
                ->where('movimientos_eventos.Empleado_ID', $persona->Empleado_ID)
                ->where('movimientos_eventos.estado', '1')
                ->where('evento.estado', '1')
                ->orderBy('movimientos_eventos.movimientos_eventos_id', 'DESC')
                ->get();
            foreach ($persona->eventos as $key => $movimiento) {
                $movimiento->images = DB::table('movimientos_eventos_archivos')
                    ->where('movimientos_eventos_archivos.movimientos_eventos_id', $movimiento->movimientos_eventos_id)
                    ->get()->toArray();
            }
        }

        $pdf = PDF::loadView('panel.cardex_personal.report.personal', compact('personas', 'images'))->setPaper('letter')->setWarnings(false);
        return $pdf->stream("Report Employee DPF " . date('m-d-Y') . ".pdf");
    }
    public function reportSkillPdf(Request $request)
    {
        $images = $request->query('images') == 'true' ? true : false;
        $personas = Personal::select(
            'personal.Empleado_ID',
            'personal.Numero',
            DB::raw("CONCAT(COALESCE(personal.Nombre,''),' ',COALESCE(personal.Apellido_Paterno,''),' ',COALESCE(personal.Apellido_Materno,'')) as Nombre"),
            'personal.Nick_Name',
            'tipo_personal.nombre as nombre_tipo',
            'personal.Cargo',
            'personal.email',
            'empresas.Nombre as nombre_empresa'
        )
        //filtrando Company
            ->when(!empty(request()->companies), function ($q) {
                return $q->whereIn('empresas.Emp_ID', explode(',', request()->companies));
            })
        //filtrando tipos
            ->when(!empty(request()->tipos), function ($q) {
                return $q->whereIn('personal.tipo_personal_id', explode(',', request()->tipos));
            })
        //filtrando cargo
            ->when(!empty(request()->cargos), function ($q) {
                return $q->whereIn('personal.cargo_personal_id', explode(',', request()->cargos));
            })
        //filtrando nickname
            ->when(!empty(request()->personas), function ($q) {
                return $q->whereIn('personal.Empleado_ID', explode(',', request()->personas));
            })
        //filtrando nickname
            ->when(!empty(request()->eventos), function ($q) {
                return $q->leftjoin('movimientos_eventos', 'movimientos_eventos.Empleado_ID', 'personal.Empleado_ID')
                    ->leftjoin('evento', 'evento.cod_evento', 'movimientos_eventos.cod_evento')
                    ->where('movimientos_eventos.estado', '1')
                    ->where('evento.estado', '1')
                    ->whereIn('movimientos_eventos.cod_evento', explode(',', request()->eventos));
            })
            ->join('empresas', 'empresas.Emp_ID', 'personal.Emp_ID')
            ->leftjoin('tipo_personal', 'tipo_personal.id', 'personal.tipo_personal_id')
            ->leftjoin('cargo_personal', 'cargo_personal.id', 'personal.cargo_personal_id')
            ->where('personal.status', '1')
            ->groupBy('personal.Empleado_ID')
            ->orderBy('personal.Nombre', 'ASC')
            ->get();

        foreach ($personas as $key => $persona) {
            $eventos = DB::table('movimientos_eventos')
                ->select(
                    'movimientos_eventos.movimientos_eventos_id',
                    'evento.nombre as nombre_evento',
                    'tipo_evento.tipo_evento_id',
                    'tipo_evento.nombre as tipo_evento',
                    DB::raw('DATE_FORMAT(movimientos_eventos.start_date, "%m/%d/%Y") as start_date'),
                    'movimientos_eventos.note as note',
                    DB::raw('DATE_FORMAT(movimientos_eventos.exp_date, "%m/%d/%Y") as exp_date'),
                    'evento.estado'
                )
                ->join('evento', 'evento.cod_evento', 'movimientos_eventos.cod_evento')
                ->join('tipo_evento', 'tipo_evento.tipo_evento_id', 'evento.tipo_evento_id')
                ->where('movimientos_eventos.Empleado_ID', $persona->Empleado_ID)
                ->where('movimientos_eventos.estado', '1')
                ->where('evento.estado', '1')
                ->orderBy('movimientos_eventos.movimientos_eventos_id', 'DESC')
                ->get();
            $persona->eventos = $eventos;
            //filtrando por tipo evento algoritmo para evitar consultas
            $tipoEvento = [];
            foreach ($eventos as $key => $evento) {
                $tipoEvento[] = $evento->tipo_evento_id;
            }
            $tipoEventoFiltrado = array_unique($tipoEvento);
            //encapsular nombre evento o skill
            $skillPorTipoEvento = [];

            foreach ($tipoEventoFiltrado as $key => $value) {
                $skill = new stdClass;
                $tipo = '';
                $nombresEvento = [];
                foreach ($eventos as $key => $evento) {

                    if ($evento->tipo_evento_id == $value) {
                        $tipo = $evento->tipo_evento;
                        $nombresEvento[] = $evento->nombre_evento;
                    }
                }
                $skill->tipoEvento = $tipo;
                $skill->evento = implode(', ', $nombresEvento);
                $skillPorTipoEvento[] = $skill;
            }
            $persona->eventos = $skillPorTipoEvento;
        }
        $pdf = PDF::loadView('panel.cardex_personal.report.reportSkill', compact('personas', 'images'))->setPaper('letter')->setWarnings(false);
        return $pdf->stream("Report Employee DPF " . date('m-d-Y') . ".pdf");
    }
    public function reportSkillExcel(Request $request)
    {
        $images = $request->query('images') == 'true' ? true : false;
        $personas = Personal::select(
            'personal.Empleado_ID',
            'personal.Numero',
            'personal.Nick_Name',
            'personal.Cargo',
            'personal.email'
        )
        //filtrando Company
            ->when(!empty(request()->companies), function ($q) {
                return $q->whereIn('empresas.Emp_ID', explode(',', request()->companies));
            })
        //filtrando tipos
            ->when(!empty(request()->tipos), function ($q) {
                return $q->whereIn('personal.tipo_personal_id', explode(',', request()->tipos));
            })
        //filtrando cargo
            ->when(!empty(request()->cargos), function ($q) {
                return $q->whereIn('personal.cargo_personal_id', explode(',', request()->cargos));
            })
        //filtrando nickname
            ->when(!empty(request()->personas), function ($q) {
                return $q->whereIn('personal.Empleado_ID', explode(',', request()->personas));
            })
        //filtrando nickname
            ->when(!empty(request()->eventos), function ($q) {
                return $q->leftjoin('movimientos_eventos', 'movimientos_eventos.Empleado_ID', 'personal.Empleado_ID')
                    ->leftjoin('evento', 'evento.cod_evento', 'movimientos_eventos.cod_evento')
                    ->where('movimientos_eventos.estado', '1')
                    ->where('evento.estado', '1')
                    ->whereIn('movimientos_eventos.cod_evento', explode(',', request()->eventos));
            })
            ->join('empresas', 'empresas.Emp_ID', 'personal.Emp_ID')
            ->leftjoin('tipo_personal', 'tipo_personal.id', 'personal.tipo_personal_id')
            ->leftjoin('cargo_personal', 'cargo_personal.id', 'personal.cargo_personal_id')
            ->where('personal.status', '1')
            ->groupBy('personal.Empleado_ID')
            ->orderBy('personal.Nombre', 'ASC')
            ->get();

        $resultado = [];
        foreach ($personas as $i => $persona) {
            $data = new stdClass;
            $eventos = DB::table('movimientos_eventos')
                ->select(
                    'movimientos_eventos.movimientos_eventos_id',
                    'evento.nombre as nombre_evento',
                    'tipo_evento.tipo_evento_id',
                    'tipo_evento.nombre as tipo_evento',
                    DB::raw('DATE_FORMAT(movimientos_eventos.start_date, "%m/%d/%Y") as start_date'),
                    'movimientos_eventos.note as note',
                    DB::raw('DATE_FORMAT(movimientos_eventos.exp_date, "%m/%d/%Y") as exp_date'),
                    'evento.estado'
                )
                ->join('evento', 'evento.cod_evento', 'movimientos_eventos.cod_evento')
                ->join('tipo_evento', 'tipo_evento.tipo_evento_id', 'evento.tipo_evento_id')
                ->where('movimientos_eventos.Empleado_ID', $persona->Empleado_ID)
                ->where('movimientos_eventos.estado', '1')
                ->where('evento.estado', '1')
                ->orderBy('movimientos_eventos.movimientos_eventos_id', 'DESC')
                ->get();
            $persona->eventos = $eventos;
            //filtrando por tipo evento algoritmo para evitar consultas
            $tipoEvento = [];
            foreach ($eventos as $key => $evento) {
                $tipoEvento[] = $evento->tipo_evento_id;
            }
            $tipoEventoFiltrado = array_unique($tipoEvento);
            //encapsular nombre evento o skill
            $skillPorTipoEvento = [];

            foreach ($tipoEventoFiltrado as $key => $value) {
                $skill = new stdClass;
                $tipo = '';
                $nombresEvento = [];
                foreach ($eventos as $key => $evento) {

                    if ($evento->tipo_evento_id == $value) {
                        $tipo = $evento->tipo_evento;
                        $nombresEvento[] = $evento->nombre_evento;
                    }
                }
                $skill->tipoEvento = $tipo;
                $skill->evento = implode(', ', $nombresEvento);
                $skillPorTipoEvento[] = $skill;
            }
            unset($persona->Empleado_ID);
            //refractor para excel
            $persona->eventos = $skillPorTipoEvento;
            $eventosToString = "";
            foreach ($persona->eventos as $key => $string) {
                $eventosToString .= strtoupper($string->tipoEvento) . ': ' . $string->evento . ' ';
            }
            //reordenar
            $data->num = $i + 1;
            $data->Numero = $persona->Numero;
            $data->Nick_Name = $persona->Nick_Name;
            $data->Cargo = $persona->Cargo;
            $data->email = $persona->email;
            $data->eventos = $eventosToString;
            $resultado[]=$data;
        }
        return $this->excel->download(new recursosSkills($resultado), "Report employees by skills Excel " . date('m-d-Y') . ".xlsx");
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
