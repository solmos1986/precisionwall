<?php

namespace App\Http\Controllers;

use App\Material;
use App\Order;
use App\Personal;
use DataTables;
use DB;
use File;
use Illuminate\Http\Request;
use Image;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $data = Order::select('orders.*', 'empresas.Codigo as empresa', 'personal.Usuario as username')->when(!auth()->user()->isAdmin(), function ($query) {
                return $query->where('empleado_id', auth()->user()->Empleado_ID);
            })
                ->join('empresas', 'empresas.Emp_ID', 'orders.sub_contractor')
                ->join('personal', 'personal.Empleado_ID', 'orders.created_by')
                ->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('firma_installer', function ($data) {
                    $html = ($data->firma_installer) ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
                    return $html;
                })
                ->addColumn('firma_foreman', function ($data) {
                    $html = ($data->firma_foreman) ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
                    return $html;
                })
                ->addColumn('acciones', function ($data) {
                    $button = "
                        <a href='" . route('show.order', ['id' => $data->id]) . "'><i class='fas fa-eye ms-text-primary'></i></a>
                        <a href='" . route('edit.order', ['id' => $data->id]) . "'><i class='fas fa-pencil-alt ms-text-warning'></i></a>
                        <i class='far fa-trash-alt ms-text-danger delete cursor-pointer' data-id='$data->id' title='Delete'></i>
                        <a href='#'><i class='fas fa-file-download ms-text-success'></i></a>
                        ";
                    return $button;
                })
                ->addColumn('inicio', function ($data) {
                    $button = "<i class='fas fa-images text-info upload_image cursor-pointer' data-image='inicio' data-id='$data->ticket_id'></i>";
                    return $button;
                })
                ->addColumn('final', function ($data) {
                    $button = "<i class='fas fa-images text-info upload_image cursor-pointer' data-image='final' data-id='$data->ticket_id'></i>";
                    return $button;
                })
                ->rawColumns(['acciones', 'firma_installer', 'firma_foreman', 'inicio', 'final'])
                ->make(true);
        }
        return view('panel.order.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $proyectos = DB::table('proyectos')->get();
        $n_order = Order::count() + 1;

        return view('panel.order.new', compact('proyectos', 'n_order'));
    }
    public function get_materiales(Request $request)
    {
        if (!isset($request->searchTerm)) {
            $materiales = Material::select('materiales.*')
                ->distinct('materiales.Mat_ID')
                ->get();
        } else {
            $materiales = Material::select('materiales.*')
                ->where('Denominacion', 'like', '%' . $request->searchTerm . '%')
                ->distinct('materiales.Mat_ID')
                ->get();
        }
        $data = [];
        foreach ($materiales as $row) {
            $data[] = array(
                "id" => $row->Mat_ID,
                "text" => $row->Denominacion,
            );
        }
        return response()->json($data);
    }

    public function get_proyects(Request $request)
    {
        if (!isset($request->searchTerm)) {
            $proyectos = DB::table('proyectos')
                ->select('proyectos.*', 'empresas.Codigo as empresa', 'empresas.Emp_ID')
                ->join('empresas', 'proyectos.Emp_ID', 'empresas.Emp_ID')
                ->distinct('proyectos.Pro_ID')
                ->get();
        } else {
            $proyectos = DB::table('proyectos')
                ->select('proyectos.*', 'empresas.Codigo as empresa', 'empresas.Emp_ID')
                ->where('proyectos.Nombre', 'like', '%' . $request->searchTerm . '%')
                ->join('empresas', 'proyectos.Emp_ID', 'empresas.Emp_ID')
                ->distinct('proyectos.Pro_ID')
                ->get();
        }
        $data = [];
        foreach ($proyectos as $row) {
            $data[] = array(
                "id" => $row->Pro_ID,
                "text" => "$row->Nombre - $row->empresa",
                "emp" => $row->empresa,
                "emp_id" => $row->Emp_ID,
            );
        }
        return response()->json($data);
    }

    public function get_employes(Request $request, $id)
    {
        if (!isset($request->searchTerm)) {
            $personal = Personal::where('Emp_ID', $id)
                ->get();
        } else {
            $personal = Personal::where('Emp_ID', $id)
                ->where('Nombre', 'like', '%' . $request->searchTerm . '%')
                ->get();
        }
        $data = [];
        foreach ($personal as $row) {
            $data[] = array(
                "id" => $row->Empleado_ID,
                "text" => trim("$row->Nombre $row->Apellido_Paterno $row->Apellido_Materno"),
            );
        }
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $name_img_insta = "";
        $name_img_foreman = "";
        $n_order = Order::count() + 1;

        $data = $request->validate([
            'proyect' => 'required|max:255',
            'job_name' => 'required|max:255',
            'sub_contractor' => 'required',
            'sub_empleoye_id' => 'required|max:255',
            'date_order' => 'nullable|date',
            'date_work' => 'nullable|date',
            'fecha_firm_installer' => 'nullable|date',
            'fecha_firm_foreman' => 'nullable|date',
            'input_signature_insta' => 'nullable',
            'input_signature_fore' => 'nullable',
            'material_id' => 'nullable|array',
            'q_ordered' => 'nullable|array',
            'q_job_site' => 'nullable|array',
            'q_installed' => 'nullable|array',
            'd_installed' => 'nullable|array',
            'q_remaining_wc' => 'nullable|array',
            'remaining_wc_stored' => 'nullable|array',
        ]);

        if (!empty($request->input_signature_fore)) {
            $name_img_foreman = "signature-foreman-" . time() . ".jpg";
            $path = public_path() . "/signatures/empleoye/$name_img_foreman";
            Image::make(file_get_contents($request->input_signature_fore))->save($path);
        }
        if (!empty($request->input_signature_insta)) {
            $name_img_insta = "signature-client-" . time() . ".jpg";
            $path = public_path() . "/signatures/install/$name_img_insta";
            Image::make(file_get_contents($request->input_signature_insta))->save($path);
        }

        $order = Order::insertGetId([
            'num' => $n_order,
            'job_name' => $data['job_name'],
            'sub_contractor' => $data['sub_contractor'],
            'proyecto_id' => $data['proyect'],
            'sub_empleoye_id' => $data['sub_empleoye_id'],
            'date_order' => $data['date_order'],
            'date_work' => $data['date_work'],
            'created_by' => auth()->user()->Empleado_ID,
            'fecha_firm_installer' => $data['fecha_firm_installer'],
            'fecha_firm_foreman' => $data['fecha_firm_foreman'],
            'firma_installer' => $name_img_insta,
            'firma_foreman' => $name_img_foreman,
        ]);

        if ($order) {
            if ($request->material_id) {
                foreach ($data['material_id'] as $key => $val) {
                    DB::table('order_material')->insert([
                        'material_id' => $val,
                        'q_ordered' => $data['q_ordered'][$key],
                        'q_job_site' => $data['q_job_site'][$key],
                        'q_installed' => $data['q_installed'][$key],
                        'd_installed' => $data['d_installed'][$key],
                        'q_remaining_wc' => $data['q_remaining_wc'][$key],
                        'remaining_wc_stored' => $data['remaining_wc_stored'][$key],
                        'order_id' => $order,
                    ]);
                }
            }
            return redirect(route('listar.orders'))->with('success', 'nuevo orden ha sido creada');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::selectRaw("orders.*,
        proyectos.*,
        empresas.Codigo as empresa,
        CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as sub_employe")->where('id', $id)
            ->join('proyectos', 'proyectos.Pro_ID', 'orders.proyecto_id')
            ->join('empresas', 'proyectos.Emp_ID', 'empresas.Emp_ID')
            ->join('personal', 'personal.Empleado_ID', 'orders.sub_empleoye_id')
            ->first();

        $materiales = DB::table('instalacion_material')
            ->where('id', $id)
            ->join('materiales', 'materiales.Mat_ID', 'instalacion_material.id')
            ->get();
        return view('panel.instalación_WC.view', compact('order', 'materiales'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $order = Order::selectRaw("orders.*,
        proyectos.*,
        empresas.Codigo as empresa,
        CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as sub_employe")->where('id', $id)
            ->join('empresas', 'empresas.Emp_ID', 'orders.sub_contractor')
            ->join('personal', 'personal.Empleado_ID', 'orders.sub_empleoye_id')
            ->join('proyectos', 'proyectos.Pro_ID', 'orders.proyecto_id')
            ->where('id', $id)
            ->first();
        $materiales = DB::table('order_material')
            ->where('order_id', $id)
            ->join('materiales', 'materiales.Mat_ID', 'order_material.material_id')
            ->get();

        return view('panel.order.edit', compact('order', 'materiales', 'id'));
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
        $order = Order::find($id);

        $name_img_insta = "";
        $name_img_foreman = "";

        $data = $request->validate([
            'proyect' => 'required|max:255',
            'job_name' => 'required|max:255',
            'sub_contractor' => 'required',
            'sub_empleoye_id' => 'required|max:255',
            'date_order' => 'nullable|date',
            'date_work' => 'nullable|date',
            'fecha_firm_installer' => 'nullable|date',
            'fecha_firm_foreman' => 'nullable|date',
            'input_signature_insta' => 'nullable',
            'input_signature_fore' => 'nullable',
            'material_id' => 'nullable|array',
            'q_ordered' => 'nullable|array',
            'q_job_site' => 'nullable|array',
            'q_installed' => 'nullable|array',
            'd_installed' => 'nullable|array',
            'q_remaining_wc' => 'nullable|array',
            'remaining_wc_stored' => 'nullable|array',
        ]);

        if ($request->input_signature_fore) {
            $image_path = public_path() . "/signatures/empleoye/$order->firma_foreman";
            if (File::exists($image_path) && $order->firma_foreman) {
                File::delete($image_path);
            }
            $name_img_foreman = "signature-foreman-" . time() . ".jpg";
            $path = public_path() . "/signatures/empleoye/$name_img_foreman";
            Image::make(file_get_contents($request->input_signature_fore))->save($path);
        } else {
            $name_img_foreman = ($order->firma_foreman) ? $order->firma_foreman : null;
        }
        if ($request->input_signature_insta) {
            $image_path = public_path() . "/signatures/install/$order->firma_installer";
            if (File::exists($image_path) && $order->firma_installer) {
                File::delete($image_path);
            }
            $name_img_insta = "signature-install-" . time() . ".jpg";
            $path = public_path() . '/signatures/install/' . $name_img_insta;
            Image::make(file_get_contents($request->input_signature_insta))->save($path);
        } else {
            $name_img_insta = ($order->firma_installer) ? $order->firma_installer : null;
        }

        $order->update([
            'job_name' => $data['job_name'],
            'sub_contractor' => $data['sub_contractor'],
            'proyecto_id' => $data['proyect'],
            'sub_empleoye_id' => $data['sub_empleoye_id'],
            'date_order' => $data['date_order'],
            'date_work' => $data['date_work'],
            'fecha_firm_installer' => $data['fecha_firm_installer'],
            'fecha_firm_foreman' => $data['fecha_firm_foreman'],
            'firma_installer' => $name_img_insta,
            'firma_foreman' => $name_img_foreman,
        ]);

        if ($order) {
            if ($request->material_id) {
                DB::table('order_material')->where('order_id', $id)->delete();
                foreach ($data['material_id'] as $key => $val) {
                    DB::table('order_material')->insert([
                        'material_id' => $val,
                        'q_ordered' => $data['q_ordered'][$key],
                        'q_job_site' => $data['q_job_site'][$key],
                        'q_installed' => $data['q_installed'][$key],
                        'd_installed' => $data['d_installed'][$key],
                        'q_remaining_wc' => $data['q_remaining_wc'][$key],
                        'remaining_wc_stored' => $data['remaining_wc_stored'][$key],
                        'order_id' => $order,
                    ]);
                }
            }
            return redirect(route('listar.orders'))->with('success', 'la order a sido actualizada');
        }
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
