<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\FuncionesGeneralesController;
use Illuminate\Support\Facades\Log;
use App\Models\OrdenVenta;
use App\Models\OrdenFabricacion;

class PlaneacionController extends Controller
{
    protected $funcionesGenerales;

    public function __construct(FuncionesGeneralesController $funcionesGenerales)
    {
        $this->funcionesGenerales = $funcionesGenerales;
    }
    public function index(){
        $FechaInicio=date('Ymd', strtotime('-1 day'));
        $FechaFin=date('Ymd');
        $NumOV="";
        $message="";
        $datos=$this->OrdenesVenta($FechaFin,$FechaInicio,$NumOV);
        if($datos!=0){
            if(empty($datos)){
                $status="empty";
            }else{
                $status="success";
            }
        }else{
            $status="error";
        }
        $FechaInicio=date('Y-m-d', strtotime('-1 day'));
        $FechaFin=date('Y-m-d');
        return view('Planeacion.Planeacion', compact('datos', 'FechaInicio', 'FechaFin','status'));
    }
    public function PartidasOF(Request $request){
        //datos para la consulta
        $schema = 'HN_OPTRONICS';
        $ordenventa = $request->input('docNum');
        $cliente = $request->input('cliente');
        if (empty($ordenventa)) {
            return response()->json([
                'status' => 'error',
                'message' => 'El número de orden no fue proporcionado.'
            ]);
        }
        //Consulta a SAP para traer las partidas de una OV
        $sql = "SELECT T1.\"ItemCode\" AS \"Articulo\", 
                    T1.\"Dscription\" AS \"Descripcion\", 
                    ROUND(T2.\"PlannedQty\", 0) AS \"Cantidad OF\", 
                    T2.\"DueDate\" AS \"Fecha entrega OF\", 
                    T1.\"PoTrgNum\" AS \"Orden de F.\" 
                FROM {$schema}.\"ORDR\" T0
                INNER JOIN {$schema}.\"RDR1\" T1 ON T0.\"DocEntry\" = T1.\"DocEntry\"
                LEFT JOIN {$schema}.\"OWOR\" T2 ON T1.\"PoTrgNum\" = T2.\"DocNum\"
                WHERE T0.\"DocNum\" = '{$ordenventa}'  
                ORDER BY T1.\"VisOrder\"";
        //Ejecucion de la consulta
        $partidas = $this->funcionesGenerales->ejecutarConsulta($sql);
        
        if ($partidas === false) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al ejecutar la consulta. Verifique los parámetros.'
            ]);
        }
        if (empty($partidas)) {
            //Log::warning("No se encontraron partidas para la orden: $ordenventa");
            return response()->json([
                'status' => 'error',
                'message' => 'No se encontraron partidas para esta orden.'
            ]);
        }
        $html = '<div class="table-responsive table-partidas" style="width:100%;">';
        $html .= '<table class="table-sm" id="table_OF'.$ordenventa.'" style="width:100%;">';
        $html .= '<thead>
                    <tr>
                        <th class="text-center">Todo <input type="checkbox" id="selectAll'.$ordenventa.'" onclick="SeleccionaFilas(this)"></th>
                        <th>Orden Fab.</th>
                        <th>Artículo</th>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Fecha entrega</th>
                        <th style="display:none;"></th>
                        <th style="display:none;"></th>
                        <th>Escáner</th>
                    </tr>
                </thead>
                <tbody>';
        $bandera_tabla_mostrar=0;
        foreach ($partidas as $index => $partida) {
            //Valida que la Orden de Fabricacion no se encuentre registrada
            $respuesta=$this->comprobar_existe_partida($ordenventa,$partida['Orden de F.']);
            //echo "Partida ".$partida['Orden de F.']." ".$respuesta." "."<br>";
            if($respuesta==0){
                $bandera_tabla_mostrar=1;
                $ordenFab = trim($partida['Orden de F.']); 
                $cantidadOF = is_numeric($partida['Cantidad OF']) 
                    ? number_format($partida['Cantidad OF'], 0, '.', '') 
                    : 'No disponible'; 
        
                $fechaEntrega = !empty($partida['Fecha entrega OF']) 
                    ? \Carbon\Carbon::parse($partida['Fecha entrega OF'])->format('d-m-Y') 
                    : 'No disponible'; 
                //$html .= '<tr id="row-' . $index . '" draggable="true" ondragstart="drag(event)" data-orden-fab="' . trim($partida['Orden de F.']) . '" data-articulo="' . $partida['Articulo'] . '" data-descripcion="' . $partida['Descripcion'] . '" data-cantidad="' . $cantidadOF . '" data-fecha-entrega="' . $fechaEntrega . '">
                $html .='<tr id="row' .$ordenventa.$index . '" draggable="true" ondragstart="drag(event)" class="draggable" >
                        <td class="text-center">';
                if($partida['Orden de F.'] != ""){
                    $html .='<input type="checkbox" class="selectAll'.$ordenventa.'rowCheckbox" onclick="SeleccionarFila(event, this)">';
                }
                $html .='</td>
                            <td>' . ($partida['Orden de F.'] ?? 'No disponible') . '</td>
                            <td>' . ($partida['Articulo'] ?? 'No disponible') . '</td>
                            <td>' . ($partida['Descripcion'] ?? 'No disponible') . '</td>
                            <td>' . ($cantidadOF ?: 'No disponible') . '</td>
                            <td>' . ($fechaEntrega ?: 'No disponible') . '</td>
                            <td style="display:none;">' . $ordenventa. '</td>
                            <td style="display:none;">' . $cliente. '</td>
                            <td class="text-center"><input type="checkbox" class="Escaner'.$ordenventa.'" onclick="SeleccionarFila(event, this)"></td>
                        </tr>';
            }
        }
        if($bandera_tabla_mostrar==0){
            return response()->json([
                'status' => 'success',
                'message' => '<p class="text-center" style="font-size:12px;">Todas las &Oacute;rdenes de fabricaci&oacute;n ya se encuentran asignadas</p>'
            ]);    
        }
            $html .= '</tbody></table></div>';
            return response()->json([
                'status' => 'success',
                'message' => $html
            ]);
    }
    public function  PlaneacionFF(Request $request){
        $FechaInicio=$request->input('startDate');
        $FechaInicio_consulta=str_replace("-","",$FechaInicio);
        $FechaFin=$request->input('endDate');
        $FechaFin_consulta=str_replace("-","",$FechaFin);
        $NumOV="";
        $tablaOrdenes="";
            $datos=$this->OrdenesVenta($FechaFin_consulta,$FechaInicio_consulta,$NumOV);
            if($datos!=0){
                if(empty($datos)){
                    $status="empty";
                }else{
                    $status="success";
                    foreach ($datos as $index => $orden) {
                        $tablaOrdenes .= '<tr class="table-light" id="details' . $index . 'cerrar" style="cursor: pointer;" draggable="true" ondragstart="drag(event)" data-bs-toggle="collapse" data-bs-target="#details' . $index . '" aria-expanded="false" aria-controls="details' . $index . '">
                                            <td onclick="loadContent(\'details' . $index . '\', ' . $orden['OV'] .', `' . $orden['Cliente'] . '`)">
                                                ' . $orden['OV'] . " - " . $orden['Cliente'] . '
                                            </td>
                                        </tr>
                                        <tr id="details' . $index . '" class="collapse">
                                            <td class="table-border" id="details' . $index . 'llenar">
                                                <!-- Aquí se llenarán los detalles de la orden cuando el usuario haga clic -->
                                            </td>
                                            <td style="display:none"> ' . $orden['Cliente']. '</td>
                                            <td style="display:none"> ' . $orden['OV']. '</td>
                                        </tr>';
                    }
                }
            }else{
                $status="error";
            }
            return response()->json([
                'status' => $status,
                'data' => $tablaOrdenes,
                'fechaHoy' => $FechaInicio,
                'fechaAyer' => $FechaFin
            ]);
    }
    public function  PlaneacionFOV(Request $request){
        $NumOV=$request->input('OV');
        $tablaOrdenes="";
            $datos=$this->OrdenesVenta("","",$NumOV);
            if($datos!=0){
                if(empty($datos)){
                    $status="empty";
                }else{
                    $status="success";
                    foreach ($datos as $index => $orden) {
                        $tablaOrdenes .= '<tr class="table-light" id="details' . $index . 'cerrar" style="cursor: pointer;" draggable="true" ondragstart="drag(event)" data-bs-toggle="collapse" data-bs-target="#details' . $index . '" aria-expanded="false" aria-controls="details' . $index . '">
                                            <td onclick="loadContent(\'details' . $index . '\', ' . $orden['OV'] .', `' . $orden['Cliente'] . '`)">
                                                ' . $orden['OV'] . " - " . $orden['Cliente'] . '
                                            </td>
                                        </tr>
                                        <tr id="details' . $index . '" class="collapse">
                                            <td class="table-border" id="details' . $index . 'llenar">
                                                <!-- Aquí se llenarán los detalles de la orden cuando el usuario haga clic -->
                                            </td>
                                            <td style="display:none"> ' . $orden['Cliente']. '</td>
                                            <td style="display:none"> ' . $orden['OV']. '</td>
                                        </tr>';
                    }
                }
            }else{
                $status="error";
            }
            return response()->json([
                'status' => $status,
                'data' => $tablaOrdenes,
                'NumOV' => $NumOV
            ]);
    }
    public function PartidasOFGuardar(Request $request){
        $DatosPlaneacion=json_decode($request->input('DatosPlaneacion'));
        $bandera="";
        $NumOV="";
        $NumOF=[];
        for($i=0;$i<count($DatosPlaneacion);$i++){
            $respuesta=0;
            $bandera_existe="";
            $respuesta=$this->comprobar_OV($DatosPlaneacion[$i]->OV);
            $NumOV=$DatosPlaneacion[$i]->OV;
            $respuestaOF= new Ordenfabricacion();

            if($respuesta==0){
                $respuestaOV= new OrdenVenta();
                $respuestaOV->OrdenVenta=$DatosPlaneacion[$i]->OV;
                $respuestaOV->NombreCliente=$DatosPlaneacion[$i]->Cliente;
                $bandera=$respuestaOV->save();
                if($bandera==1){
                    $NumOF[]=$DatosPlaneacion[$i]->OF;
                    $Fecha_entrega=isset($DatosPlaneacion[$i]->Fecha_entrega)?Carbon::createFromFormat('d-m-Y', $DatosPlaneacion[$i]->Fecha_entrega)->format('Y-m-d'):null;
                    $respuestaOF->OrdenVenta_id=$respuestaOV->id;
                    $respuestaOF->OrdenFabricacion=$DatosPlaneacion[$i]->OF;
                    $respuestaOF->Articulo=$DatosPlaneacion[$i]->Articulo;
                    $respuestaOF->Descripcion=$DatosPlaneacion[$i]->Descripcion;
                    $respuestaOF->CantidadTotal=$DatosPlaneacion[$i]->Cantidad;
                    $respuestaOF->FechaEntregaSAP=$Fecha_entrega;
                    $respuestaOF->FechaEntrega=$DatosPlaneacion[$i]->Fecha_planeada;
                    $respuestaOF->Escaner=$DatosPlaneacion[$i]->Escanner;
                    $respuestaOF->save();
                }
                else{
                    return response()->json([
                        'status' => "error",
                        'NumOV' => $NumOF,
                        'NumOV' => $NumOV
                    ]);
                }
            }else{
                $datos=OrdenVenta:: where('id','=',$respuesta)->first();
                $comprobar_existe_partida=$this->comprobar_existe_partida($datos->id, $DatosPlaneacion[$i]->OF);
                if($comprobar_existe_partida==0){
                    $NumOF[]=$DatosPlaneacion[$i]->OF;
                    $Fecha_entrega=isset($DatosPlaneacion[$i]->Fecha_entrega)?Carbon::createFromFormat('d-m-Y', $DatosPlaneacion[$i]->Fecha_entrega)->format('Y-m-d'):null;
                    $respuestaOF->OrdenVenta_id=$datos->id;
                    $respuestaOF->OrdenFabricacion=$DatosPlaneacion[$i]->OF;
                    $respuestaOF->Articulo=$DatosPlaneacion[$i]->Articulo;
                    $respuestaOF->Descripcion=$DatosPlaneacion[$i]->Descripcion;
                    $respuestaOF->CantidadTotal=$DatosPlaneacion[$i]->Cantidad;
                    $respuestaOF->FechaEntregaSAP=$Fecha_entrega;
                    $respuestaOF->FechaEntrega=$DatosPlaneacion[$i]->Fecha_planeada;
                    $respuestaOF->save();
                }
            }
        }
        if (!empty($NumOF)) {
            return response()->json([
                'status' => "success",
                'NumOF' => $NumOF,
                'NumOV' => $NumOV
            ]);
        }else{
            return response()->json([
                'status' => "empty",
                'NumOF' => $NumOF,
                'NumOV' => $NumOV
            ]);
        }
    }
    public function PartidasOFFiltroFechas_Tabla(Request $request){
        $Fecha=$request->input('fecha');
        $datos=$this->PartidasOFFiltroFechas($Fecha);
        //return $datos;
        $tabla="";
        if(count($datos)>0){
            for ($i=0; $i < count($datos); $i++) { 
                $tabla.='<tr>
                            <td class="text-center">'.$datos[$i]['OrdenVenta'].'</td>
                            <td class="text-center">'.$datos[$i]['OrdenFabricacion'].'</td>
                            <td class="text-center">'.'<button type="button" onclick="RegresarOrdenFabricacion(\''.$this->funcionesGenerales->encrypt($datos[$i]['ordenfabricacion_id']).'\')" class="btn btn-link"><i class="fa fa-arrow-left"></i> Regresar</button>'.'</td>
                            <td class="text-center">'.'<button type="button" onclick="DetallesOrdenFabricacion(\''.$this->funcionesGenerales->encrypt($datos[$i]['ordenfabricacion_id']).'\')" class="btn-sm btn-primary"><i class="fa fa-eye"></i> Ver</button>'.'</td>
                        </tr>';
            }
            return response()->json([
                'status' => "success",
                'tabla' => $tabla
            ]);
        }else{
            return response()->json([
                'status' => "empty",
                'tabla' => '<tr><td colspan="100%"" align="center">No existen registros</td></tr>'
            ]);
        }

    }
    //Funcion para eliminar Orden de Fabricacion planeadas
    public function PartidasOFRegresar(Request $request){
        try{
            $NumOF_id=$this->funcionesGenerales->decrypt($request->input('NumOF'));
            $OF = OrdenFabricacion::where('id','=',$NumOF_id)->first();
            $OF_OrdenFabricacion=$OF->OrdenFabricacion;
            //Comprobar si ya esta iniciada
            if (!empty($OF)) {
            $bandera_borrar=$OF->delete();
                if ($bandera_borrar) {
                    return response()->json([
                        'status' => "success",
                        'OF' => $OF_OrdenFabricacion
                    ]);
                } else {
                    return response()->json([
                        'status' => "error",
                        'OF' => ""
                    ]);
                }
            }else{ return "empty";}
        }catch(Exception $e){
            return response()->json([
                'status' => "error",
                'OF' => ""
            ]);
        }
    }
     //Funcion para cambiar estutus de si se escanea o no
     public function CambiarEstatusEscaner(Request $request){
        try{
            $NumOF_id=$this->funcionesGenerales->decrypt($request['Id']);
            $OF = OrdenFabricacion::where('id','=',$NumOF_id)->first();
            $escaner=$request['Escanear'];
            if($escaner=="true"){
                $OF->Escaner=1;
                $OF->save();
                return response()->json([
                    'status' => "success",
                    'valor' => "true"
                ]);
            }else{
                $OF->Escaner=0;
                $OF->save();
                return response()->json([
                    'status' => "success",
                    'valor' => "false"
                ]);
            }
        }catch(Exception $e){
            return response()->json([
                'status' => "error",
            ]);
        }
    }
    //Funcion para ver las Ordenes de venta de  fecha inicio a fecha fin y por numero de OV
    public function OrdenesVenta($FechaInicio,$FechaFin,$NumOV){
        $schema = 'HN_OPTRONICS';
        $where="";
        $datos="";
        if($NumOV==""){
            $where='T0."DocDate" BETWEEN \'' . $FechaFin . '\' AND \'' . $FechaInicio . '\'';
        }else{
            $where = 'T0."DocNum" LIKE \'%' . $NumOV . '%\'';
        }
        $sql = 'SELECT T0."DocNum" AS "OV", T0."CardName" AS "Cliente", T0."DocDate" AS "Fecha", 
                T0."DocStatus" AS "Estado", T0."DocTotal" AS "Total" FROM ' . $schema . '.ORDR T0 
                WHERE '.$where;
        try {
            $datos = $this->funcionesGenerales->ejecutarConsulta($sql);
        } catch (\Exception $e) {
            return $datos=0;
        }
        return $datos;
    }
    //Funcion para comprobar si existe una Orden de Fabricacion de una OV
    public function comprobar_existe_partida($OrdenVenta, $Ordenfabricacion){
        $datos=OrdenVenta:: where('OrdenVenta','=',$OrdenVenta)->first();
        if($datos){
            $datos=OrdenFabricacion::where('OrdenVenta_id','=',$datos->id)
            ->where('OrdenFabricacion','=',$Ordenfabricacion)                        
            ->count();
        }else{
            $datos=0;
        }
        return $datos;
    }
    //Funcion para comprobar si existe una OV
    public function comprobar_OV($DocNumOv){
        $datos=OrdenVenta:: where('OrdenVenta','=',$DocNumOv)->first();
        if(!$datos){
            $datos=0;
        }else{
            $datos=$datos->id;
        }
        return $datos;
    }
    //Funcion para filtrar partidas de Orden de fabricacion por fecha
    public function PartidasOFFiltroFechas($Fecha){
        $datos=OrdenFabricacion::join('ordenventa', 'ordenfabricacion.OrdenVenta_id', '=', 'ordenventa.id')
                                ->where('FechaEntrega','=',$Fecha)
                                ->select('ordenfabricacion.id as ordenfabricacion_id', 'ordenventa.id as ordenventa_id','OrdenVenta','OrdenFabricacion') 
                                ->orderBy('ordenfabricacion_id', 'desc') // Orden descendente
                                ->get();
        return $datos->toArray();
    }
    //Funcion para ver detalles
    public function PartidasOF_Detalles(Request $request){
        $NumOF_id=$this->funcionesGenerales->decrypt($request->input('NumOF'));
        $datos=OrdenFabricacion:: join('ordenventa', 'ordenfabricacion.OrdenVenta_id', '=', 'ordenventa.id')
                                ->select(
                                    'ordenfabricacion.id as ordenfabricacionid', 
                                    'ordenfabricacion.OrdenVenta_id', 
                                    'ordenventa.OrdenVenta', 
                                    'ordenventa.NombreCliente', 
                                    'ordenfabricacion.OrdenFabricacion', 
                                    'ordenfabricacion.Articulo', 
                                    'ordenfabricacion.Descripcion', 
                                    'ordenfabricacion.CantidadTotal', 
                                    'ordenfabricacion.FechaEntrega', 
                                    'ordenfabricacion.FechaEntregaSAP', 
                                    'ordenfabricacion.Escaner'  // Aquí se selecciona la columna Escaner para el checkbox
                                )
                                ->where('ordenfabricacion.id','=',$NumOF_id)->first();
        if($datos){
            $cadena='<table class="table-sm table-bordered table-striped table-text-responsive" style="width:100%">
                        <thead>
                        </thead>
                        <tbody>
                            <tr>
                                <th>Orden de Venta</th>
                                <td class="text-center">'.$datos->OrdenVenta.'</td>
                            </tr>
                            <tr>
                                <th>Cliente</th>
                                <td class="text-center">'.$datos->NombreCliente.'</td>
                            </tr>
                            <tr>
                                <th>Orden de Fabricación</th>
                                <td class="text-center">'.$datos->OrdenFabricacion.'</td>
                            </tr>
                            <tr>
                                <th>Articulo</th>
                                <td class="text-center">'.$datos->Articulo.'</td>
                            </tr>
                            <tr>
                                <th>Descripción</th>
                                <td>'.$datos->Descripcion.'</td>
                            </tr>
                            <tr>
                                <th>Cantidad Total</th>
                                <td class="text-center">'.$datos->CantidadTotal.'</td>
                            </tr>
                            <tr>
                                <th>Fecha Planeación</th>
                                <td class="text-center">'.Carbon::parse($datos->FechaEntrega)->format('d/m/Y').'</td>
                            </tr>
                            <tr>
                                <th>Fecha Entrega</th>
                                <td class="text-center">'.Carbon::parse($datos->FechaEntregaSAP)->format('d/m/Y').'</td>
                            </tr>
                            <tr>
                                <th>Escánear</th>
                                <td class="text-center"><input type="checkbox" style="transform:scale(1.2)" class="Escaner'.$datos->Escaner.'" onclick="CambiarEscaner(this,\''.$this->funcionesGenerales->encrypt($datos->ordenfabricacionid).'\')" ';
            if($datos->Escaner){
                $cadena.='checked';                
            }
            $cadena.='></td>
                        </tr>
                        </tbody></table>';
                return response()->json([
                    'status' => "success",
                    'tabla' => $cadena,
                    'OF'=>$datos->OrdenFabricacion
                ]);
        }else{
            return response()->json([
                'status' => "empty",
                'tabla' => '<p class="text-center">No existen información para esta Orden de Fabricación</p>'
            ]);
        }
    }
    public function PlaneacionFOFOV(Request $request){
        $FiltroOF_table2=$request->input('FiltroOF_table2');
        $datos=OrdenFabricacion::join('ordenventa', 'ordenfabricacion.OrdenVenta_id', '=', 'ordenventa.id')
        ->where('ordenfabricacion.OrdenFabricacion', 'like', '%'.$FiltroOF_table2. '%')
        ->orWhere('ordenventa.OrdenVenta', 'like', '%'.$FiltroOF_table2. '%')
        ->select('ordenfabricacion.id as ordenfabricacion_id', 'ordenventa.id as ordenventa_id','OrdenVenta','OrdenFabricacion') 
        ->orderBy('ordenfabricacion_id', 'desc') // Orden descendente
        ->get();
        $tabla="";
        if(count($datos)>0){
            for ($i=0; $i < count($datos); $i++) { 
                $tabla.='<tr>
                            <td class="text-center">'.$datos[$i]['OrdenVenta'].'</td>
                            <td class="text-center">'.$datos[$i]['OrdenFabricacion'].'</td>
                            <td class="text-center">'.'<button type="button" onclick="RegresarOrdenFabricacion(\''.$this->funcionesGenerales->encrypt($datos[$i]['ordenfabricacion_id']).'\')" class="btn btn-link"><i class="fa fa-arrow-left"></i> Regresar</button>'.'</td>
                            <td class="text-center">'.'<button type="button" onclick="DetallesOrdenFabricacion(\''.$this->funcionesGenerales->encrypt($datos[$i]['ordenfabricacion_id']).'\')" class="btn-sm btn-primary"><i class="fa fa-eye"></i> Ver</button>'.'</td>
                        </tr>';
            }
            return response()->json([
                'status' => "success",
                'tabla' => $tabla
            ]);
        }else{
            return response()->json([
                'status' => "empty",
                'tabla' => '<tr><td colspan="100%"" align="center">No existen registros para '.$FiltroOF_table2.'</td></tr>'
            ]);
        }
    }

}    