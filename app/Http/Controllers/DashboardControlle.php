<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DashboardControlle extends Controller
{
    //
    public function index()
    {

        return view('layouts.principal');
    }

    public function Ordenes()
    {
        
        $totalOrdenes = DB::table('ordenfabricacion')->count();
        if ($totalOrdenes === 0) {
            return response()->json([
                'retrabajo' => 0
            ]);
        }
        
        $ordenesRetrabajo = DB::table('ordenfabricacion')
            ->where('EstatusEntrega', 1)
            ->count();
       
        $retrabajoPorcentaje = round(($ordenesRetrabajo / $totalOrdenes) * 100, 2);

        return response()->json([
            'retrabajo' => $retrabajoPorcentaje
        ]);
        
    }
  
    public function cerradas()
    {
        // Definir correctamente la variable $totalOrdenes
        $totalOrdenes = DB::table('ordenfabricacion')->count();
    
        // Obtener las órdenes cerradas
        $ordenes = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id')
            ->join('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id')
            ->where('partidas_areas.Areas_id', 9)
            ->select(
                'ordenfabricacion.OrdenFabricacion',
                'ordenfabricacion.Articulo',
                'ordenfabricacion.Descripcion',
                'ordenfabricacion.CantidadTotal',
                'partidasof.cantidad_partida'
            )
            ->distinct()
            ->get();
    
        // Obtener los tiempos de las etapas
        $tiempos = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id')
            ->join('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id')
            ->select(
                'ordenfabricacion.OrdenFabricacion',
                //fecha de inicio
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 2 THEN partidas_areas.FechaComienzo END) as TiempoCorte"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 3 THEN partidas_areas.FechaComienzo END) as TiempoSuministro"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 4 THEN partidas_areas.FechaComienzo END) as TiempoPreparado"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 5 THEN partidas_areas.FechaComienzo END) as TiempoEnsamble"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 6 THEN partidas_areas.FechaComienzo END) as TiempoPulido"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 7 THEN partidas_areas.FechaComienzo END) as TiempoMedicion"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 8 THEN partidas_areas.FechaComienzo END) as TiempoVisualizacion"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 9 THEN partidas_areas.FechaComienzo END) as TiempoAbierto"),
                // Fecha final
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 2 THEN partidas_areas.FechaTermina END) as FinCorte"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 3 THEN partidas_areas.FechaTermina END) as FinSuministro"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 4 THEN partidas_areas.FechaTermina END) as FinPreparado"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 5 THEN partidas_areas.FechaTermina END) as FinEnsamble"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 6 THEN partidas_areas.FechaTermina END) as FinPulido"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 7 THEN partidas_areas.FechaTermina END) as FinMedicion"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 8 THEN partidas_areas.FechaTermina END) as FinVisualizacion"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 9 THEN partidas_areas.FechaTermina END) as FinAbierto")
            )
            ->groupBy('ordenfabricacion.OrdenFabricacion')
            ->get();

           
    
        // Combina los resultados de las órdenes con los tiempos
        $ordenesConTiempos = $ordenes->map(function($orden) use ($tiempos) {
            
            $tiempo = $tiempos->firstWhere('OrdenFabricacion', $orden->OrdenFabricacion);
            //inicio de tiempos 
            $orden->TiempoCorte = $tiempo ? $tiempo->TiempoCorte : "";
            $orden->TiempoSuministro = $tiempo ? $tiempo->TiempoSuministro : "";
            $orden->TiempoPreparado = $tiempo ? $tiempo->TiempoPreparado : "";
            $orden->TiempoEnsamble = $tiempo ? $tiempo->TiempoEnsamble : "";
            $orden->TiempoPulido = $tiempo ? $tiempo->TiempoPulido : "";
            $orden->TiempoMedicion = $tiempo ? $tiempo->TiempoMedicion : "";
            $orden->TiempoVisualizacion = $tiempo ? $tiempo->TiempoVisualizacion : "";
            $orden->TiempoAbierto = $tiempo ? $tiempo->TiempoAbierto : "";
            
           //final de tiempos
            $orden->FinCorte = $tiempo ? $tiempo->FinCorte : "";
            $orden->FinSuministro = $tiempo ? $tiempo->FinSuministro : "";
            $orden->FinPreparado = $tiempo ? $tiempo->FinPreparado : "";
            $orden->FinEnsamble = $tiempo ? $tiempo->FinEnsamble : "";
            $orden->FinPulido = $tiempo ? $tiempo->FinPulido : "";
            $orden->FinMedicion = $tiempo ? $tiempo->FinMedicion : "";
            $orden->FinVisualizacion = $tiempo ? $tiempo->FinVisualizacion : "";
            $orden->FinAbierto = $tiempo ? $tiempo->FinAbierto : "";
            
            return $orden;
        });
        
    
        // Calcular el porcentaje de órdenes cerradas
        $ordenesCerradasCount = $ordenesConTiempos->count();
        $porcentajeCerradas = $totalOrdenes > 0 ? ($ordenesCerradasCount / $totalOrdenes) * 100 : 0;
    
        // Retornar los datos en formato JSON
        return response()->json([
            'retrabajo' => round($porcentajeCerradas, 2),
            'ordenes' => $ordenesConTiempos
        ]);
    }
    
    public function abiertas()
    {
        $totalOrdenes = DB::table('ordenfabricacion')->count();
        
        $ordenesAbiertas = DB::table('ordenfabricacion')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                ->join('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id')
                ->join('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id')
                ->where('partidas_areas.Areas_id', 2)
                ->select('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.Articulo', 'ordenfabricacion.Descripcion', 'ordenfabricacion.CantidadTotal', 'partidasof.cantidad_partida')
                ->distinct()
                ->get(); // Obtiene los datos

        // Para contar el número de resultados
        $ordenesAbiertasCount = $ordenesAbiertas->count();
        
        $porcentajeAbiertas = $totalOrdenes > 0 ? ($ordenesAbiertasCount / $totalOrdenes) * 100 : 0;
        
        return response()->json([
            'retrabajo' => round($porcentajeAbiertas, 2),
            'ordenes' => $ordenesAbiertas
        ]);
    }

    public function graficas()
    {
        // Órdenes por día
        $ordenesPorDia = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id')
            ->join('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id')
            ->where('partidas_areas.Areas_id', 9)
            ->selectRaw('DATE_FORMAT(ordenfabricacion.created_at, "%Y-%m-%d") as dia, COUNT(DISTINCT ordenfabricacion.id) as total')
            ->groupBy('dia')
            ->orderBy('dia', 'asc')
            ->get();
    
        // Órdenes por semana
        $ordenesPorSemana = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id')
            ->join('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id')
            ->where('partidas_areas.Areas_id', 9)
            ->selectRaw('YEARWEEK(ordenfabricacion.created_at) as semana, COUNT(DISTINCT ordenfabricacion.id) as total')
            ->groupBy('semana')
            ->orderBy('semana', 'asc')
            ->get();
    
        // Órdenes por mes
        $ordenesPorMes = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id')
            ->join('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id')
            ->where('partidas_areas.Areas_id', 9)
            ->selectRaw('DATE_FORMAT(ordenfabricacion.created_at, "%Y-%m") as mes, COUNT(DISTINCT ordenfabricacion.id) as total')
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();
    
        return response()->json([
            'ordenesPorDia' => $ordenesPorDia,
            'ordenesPorSemana' => $ordenesPorSemana,
            'ordenesPorMes' => $ordenesPorMes,
        ]);
    }

    public function progreso()
    {
        $totalCantidad = DB::table('ordenfabricacion')
            ->leftJoin('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->leftJoin('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id')
            ->leftJoin('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id')
            ->select('ordenfabricacion.CantidadTotal')
            ->distinct()  
            ->sum('ordenfabricacion.CantidadTotal');  

        // Áreas que estás utilizando
        $areas = ['2', '3', '4', '5', '6', '7', '8', '9'];

        $progreso = [];

        foreach ($areas as $area) {
            
            $cantidadPorArea = DB::table('partidas_areas')
                ->join('partidas', 'partidas_areas.Partidas_id', '=', 'partidas.id')
                ->join('partidasof', 'partidas.PartidasOf_id', '=', 'partidasof.id')
                ->join('ordenfabricacion', 'partidasof.OrdenFabricacion_id', '=', 'ordenfabricacion.id')
                ->where('partidas_areas.Areas_id', $area)  
                ->sum('partidas_areas.Cantidad');  
            $porcentaje = ($totalCantidad > 0) ? ($cantidadPorArea / $totalCantidad) * 100 : 0;
            $progreso[$area] = number_format($porcentaje, 2, '.', '');  
        }
        
        return response()->json([
            'progreso' => $progreso
        ]);
    }
    public function progresoof()
    {
        // Obtener todas las órdenes de fabricación distintas
        $ordenes = DB::table('ordenfabricacion')
            ->leftJoin('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->leftJoin('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id')
            ->leftJoin('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id')
            ->select(
                'ordenfabricacion.id',
                'ordenfabricacion.OrdenFabricacion',
                'ordenfabricacion.CantidadTotal'
            )
            ->groupBy('ordenfabricacion.id', 'ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal')
            ->get();
    
        $areas = ['2', '3', '4', '5', '6', '7', '8', '9'];
        $progreso = [];
        $totalProgresoAcumulado = 0;
        $contadorOrdenesConDatos = 0;  // Para contar cuántas órdenes tienen progreso
    
        foreach ($ordenes as $orden) {
            $totalCantidad = $orden->CantidadTotal;
            $ordenId = $orden->id;
            $progresoOrden = [];
            $sumaPorcentajes = 0;
            $cantidadAreasConDatos = 0;
            $avancesArea9 = 0; // Variable para verificar si el área 9 está al 100%
    
            foreach ($areas as $area) {
                // Calcular la cantidad sumada en esta área para la orden específica
                $cantidadPorArea = DB::table('partidas_areas')
                    ->join('partidas', 'partidas_areas.Partidas_id', '=', 'partidas.id')
                    ->join('partidasof', 'partidas.PartidasOf_id', '=', 'partidasof.id')
                    ->join('ordenfabricacion', 'partidasof.OrdenFabricacion_id', '=', 'ordenfabricacion.id')
                    ->where('partidas_areas.Areas_id', $area)
                    ->where('ordenfabricacion.id', $ordenId)
                    ->sum('partidas_areas.Cantidad');
    
                // Calcular porcentaje por área
                $porcentaje = ($totalCantidad > 0) ? ($cantidadPorArea / $totalCantidad) * 100 : 0;
    
                // Guardar el porcentaje en el progreso de la orden
                $progresoOrden[$area] = number_format($porcentaje, 2, '.', '');
    
                // Sumar los porcentajes solo si hay datos en la cantidad
                if ($porcentaje > 0) {
                    $sumaPorcentajes += $porcentaje;
                    $cantidadAreasConDatos++;
                }
    
                // Si el área 9 tiene datos, guardamos el progreso específico
                if ($area == '9' && $porcentaje > 0) {
                    $avancesArea9 = $porcentaje;
                }
            }
    
            // Si el área 9 está al 100%, consideramos el progreso total como 100%, de lo contrario calculamos el promedio
            if ($avancesArea9 == 100) {
                $progresoTotal = 100; // Si el área 9 llegó a 100%, el progreso es 100%
            } else {
                // Si el área 9 no está completo, calculamos el progreso basado en las áreas con datos
                $progresoTotal = ($cantidadAreasConDatos > 0) ? ($sumaPorcentajes / $cantidadAreasConDatos) : 0;
            }
    
            // Incrementar el progreso acumulado con el progreso de la orden
            $totalProgresoAcumulado += $progresoTotal;
            $contadorOrdenesConDatos++;
    
            $progreso[$orden->OrdenFabricacion] = [
                'progreso_orden' => number_format($progresoTotal, 2, '.', ''), // Progreso total
                'Cantidad_total' => number_format($totalCantidad, 2, '.', ''),
                'detalle' => $progresoOrden // Detalle por áreas
            ];
        }
    
        // Calcular el progreso total acumulado para todas las órdenes
        $progresoTotalFinal = ($contadorOrdenesConDatos > 0) ? ($totalProgresoAcumulado / $contadorOrdenesConDatos) : 0;
    
        return response()->json([
            'progreso' => $progreso,
            'progreso_total_final' => number_format($progresoTotalFinal, 2, '.', '')
        ]);
    }
    


    
    

    
        /*

    // Contar el total de órdenes de fabricación
    $totalOf = $of->count();

    // Lista de áreas
    $areas = ['2', '3', '4', '5', '6', '7', '8', '9'];
    $progreso = [];

    foreach ($areas as $area) {
        // Obtener la cantidad total para el área
        $cantidadPorArea = DB::table('partidas_areas')
            ->join('partidas', 'partidas_areas.Partidas_id', '=', 'partidas.id')
            ->join('partidasof', 'partidas.PartidasOf_id', '=', 'partidasof.id')
            ->join('ordenfabricacion', 'partidasof.OrdenFabricacion_id', '=', 'ordenfabricacion.id')
            ->where('partidas_areas.Areas_id', $area)
            ->sum('partidas_areas.Cantidad');

        // Calcular el porcentaje
        $porcentaje = ($totalOf > 0) ? ($cantidadPorArea / $totalOf) * 100 : 0;
        $progreso[$area] = number_format($porcentaje, 2, '.', '');
    }
    dd($progreso);
    return response()->json([
        'progreso' => $progreso
    ]);*/
}




