<?php

use App\Http\Controllers\HomeControler;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlaneacionController;
use App\Http\Controllers\CorteController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\AreasController;
use App\Http\Controllers\RolesPermisoController;
use App\Http\Controllers\PreparadoController;
use GuzzleHttp\Promise\Coroutine;
use Illuminate\Routing\Route as RoutingRoute;


// Ruta para mostrar el formulario de login
Route::get('/login', [loginController::class, 'login_view'])->name('login_view');
Route::post('/login', [loginController::class, 'login'])->name('login_post');
Route::post('/logout', [loginController::class, 'logout'])->name('logout');
Route::post('/register', [loginController::class, 'register'])->name('register');

//Rutas Planeación
Route::get('/', [HomeControler::class,'Home'])->name('Home');
Route::get('/Planeacion', [PlaneacionController::class,'index'])->name('Planeacion');
Route::post('/Planeacion/Filtro/Fechas', [PlaneacionController::class,'PlaneacionFF'])->name('PlaneacionFF');
Route::post('/Planeacion/Filtro/OrdenVenta',[PlaneacionController::class,'PlaneacionFOV'])->name('PlaneacionFOV');
Route::get('/Planeacion/Filtro/OrdenFabricacion_OrdenVenta',[PlaneacionController::class,'PlaneacionFOFOV'])->name('PlaneacionFOFOV');
Route::get('/Planeacion/partidas', [PlaneacionController::class,'PartidasOF'])->name('PartidasOF');
Route::get('/Planeacion/partidas/vencidas', [PlaneacionController::class,'LlenarTablaVencidasOV'])->name('LlenarTablaVencidasOV');
Route::post('/Planeacion/partidas', [PlaneacionController::class,'PartidasOFGuardar'])->name('PartidasOFGuardar');
Route::delete('/Planeacion/partidas', [PlaneacionController::class,'PartidasOFRegresar'])->name('PartidasOFRegresar');
Route::post('/Planeacion/partidas/FiltroFechas', [PlaneacionController::class,'PartidasOFFiltroFechas_Tabla'])->name('PartidasOFFiltroFechas_Tabla');
Route::post('/Planeacion/partidas/EscanerEstatus', [PlaneacionController::class,'CambiarEstatusEscaner'])->name('CambiarEstatusEscaner');
Route::get('/Planeacion/detalles', [PlaneacionController::class,'PartidasOF_Detalles'])->name('PartidasOF_Detalles');

//Rutas Ares
Route::get('/Area/Corte', [AreasController::class,'Corte'])->name('Corte');
Route::get('/Area/Suministro', [AreasController::class,'Suministro'])->name('Suministro');
Route::get('/Area/Suministro/buscar', [AreasController::class,'SuministroBuscar'])->name('SuministroBuscar');
Route::post('/Area/Suministro/NoEscaner', [AreasController::class,'TipoNoEscaner'])->name('TipoNoEscaner');
Route::get('/Area/Preparado', [AreasController::class,'Preparado'])->name('Preparado');
Route::get('/Area/Ensamble', [AreasController::class,'Ensamble'])->name('Ensamble');
Route::get('/Area/Pulido', [AreasController::class,'Pulido'])->name('Pulido');
Route::get('/Area/Medicion', [AreasController::class,'Medicion'])->name('Medicion');
Route::get('/Area/Visualizacion', [AreasController::class,'Visualizacion'])->name('Visualizacion');
Route::get('/Area/Partidas', [AreasController::class,'AreaPartidas'])->name('AreaPartidas');

//Rutas cortes
Route::get('/cortes', [CorteController::class, 'index'])->name('corte.index');
    Route::get('/cortes/getData', [CorteController::class, 'getData'])->name('corte.getData');
    Route::get('/detalles', [CorteController::class, 'verDetalles'])->name('detalles');
    Route::get('/buscar-ordenes', [CorteController::class, 'buscarOrdenVenta'])->name('buscar.ordenes');
    Route::get('/corte/getDetalleOrden', [CorteController::class, 'getDetalleOrden'])->name('corte.getDetalleOrden');
    Route::get('corte/getCortes', [CorteController::class, 'getCortes'])->name('corte.getCortes');
    Route::post('corte/finalizar/corte', [CorteController::class, 'finalizarCorte'])->name('corte.finalizarCorte');
    Route::get('/orden-fabricacion/cantidad-total/{id}', [CorteController::class, 'getCantidadTotal'])->name('ordenFabricacion.getCantidadTotal');
    Route::post('/guardarpartida', [CorteController::class, 'guardarPartidasOF'])->name('guardar.partida');
    Route::get('/orden-fabricacion/{id}/cortes-info', [CorteController::class, 'getCortesInfo'])->name('ordenFabricacion.getCortesInfo');
    Route::get('/orden-fabricacion/{ordenFabricacionId}/cortes-info', [CorteController::class, 'getCortesInfo'])->name('orden-fabricacion.cortes-info');
    Route::post('/orden-fabricacion/update-status', [CorteController::class, 'updateStatus'])->name('orden-fabricacion.update-status');
Route::get('/ruta-para-actualizar-tabla', [CorteController::class, 'actualizarTabla'])->name('actualizar.tabla');

//rutas para generar etiquetas
Route::get('/generar-etiquetas/{corteId}', [CorteController::class, 'getDatosGenerarEtiquetas']);
    Route::post('/generar-etiquetas', [CorteController::class, 'generarEtiquetas'])->name('generar.etiquetas');
    Route::get('/mostrar/etiqueta', [CorteController::class, 'MostarInformacion'])->name('mostrar.etiqueta');
    Route::get('/generar-pdf', [CorteController::class, 'generarPDF'])->name('generar.pdf');
Route::post('/generar-pdf-rangos', [CorteController::class, 'PDFCondicion'])->name('pdfcondicion');

//ruta para el formulario de registro
Route::get('/registro', [RegistroController::class, 'index'])->name('registro.index');

    Route::post('/users/activar', [RegistroController::class, 'activar'])->name('users.activar');
    Route::post('/users/desactivar', [RegistroController::class, 'desactivar'])->name('users.desactivar');
    
    // Ruta para mostrar el formulario de creación
    Route::get('/registro/create', [RegistroController::class, 'create'])->name('registro.create');
    
    // Ruta para almacenar un nuevo rol o permiso
    Route::post('/registro/store', [RegistroController::class, 'store'])->name('registro.store');
    
    // Ruta para mostrar el formulario de edición
    Route::get('/registro/edit/{id}', [RegistroController::class, 'edit'])->name('registro.edit');

    Route::get('/registro/show/{id}',[RegistroController::class, 'show'])->name('registro.show');
    
    // Ruta para actualizar un rol o permiso
    Route::put('/registro/update/{id}', [RegistroController::class, 'update'])->name('registro.update');
    
    // Ruta para eliminar un rol o permiso
Route::delete('registro/{id}', [RegistroController::class, 'destroy'])->name('registro.destroy');

//rutas roles y permiso
Route::get('/RolesPermisos', [RolesPermisoController::class, 'index'])->name('RolesPermisos.index');
    
    // Ruta para mostrar el formulario de creación
    Route::get('/RolesPermisos/create', [RolesPermisoController::class, 'create'])->name('RolesPermisos.create');
    
    // Ruta para almacenar un nuevo rol o permiso
    Route::post('/RolesPermisos/store', [RolesPermisoController::class, 'store'])->name('RolesPermisos.store');
    
    // Ruta para mostrar el formulario de edición
    Route::get('/RolesPermisos/edit/{id}', [RolesPermisoController::class, 'edit'])->name('RolesPermisos.edit');
    
    // Ruta para actualizar un rol o permiso
    Route::put('/RolesPermisos/update/{id}', [RolesPermisoController::class, 'update'])->name('RolesPermisos.update');
    
    // Ruta para eliminar un rol o permiso
Route::delete('destroy/{id}', [RolesPermisoController::class, 'destroy'])->name('destroy');


Route::post('/filtrar-por-fecha', [CorteController::class, 'filtrarPorFecha'])->name('Fitrar.Fecha');
Route::post('/filtrar-por-fechaS', [CorteController::class, 'fechaCompletado'])->name('Fitrar.FechaS');

Route::get('/ordenes-filtradas', [CorteController::class, 'SinCortesProceso'])->name('ordenes.filtradas');
Route::get('/ordenes/completadas',[CorteController:: class, 'Completado'])->name('ordenes.completadas');




























//registro 
//Route::view('/registro', "usuarios.registro")->name('register');



// Grupo de rutas para la gestión de usuarios
/*Route::prefix('registro')->name('registro.')->group(function () {
    // Listar todos los usuarios
    Route::get('/index', [RegistroController::class, 'index'])->name('index');

    // Crear un nuevo usuario
    Route::get('/create', [RegistroController::class, 'create'])->name('create');
    Route::post('/store', [RegistroController::class, 'store'])->name('store');

    // Mostrar detalles de un usuario específico
    Route::get('/{registro}', [RegistroController::class, 'show'])->name('show');

    // Editar un usuario existente
    Route::get('/{registro}/edit', [RegistroController::class, 'edit'])->name('edit');
    Route::put('/{registro}', [RegistroController::class, 'update'])->name('update');

    // Eliminar un usuario
    Route::delete('/{registro}', [RegistroController::class, 'destroy'])->name('destroy');
});*/

/*Route::get('/registro', [RegistroController::class, 'index'])->name('registro.index');
Route::get('/registro/create', [RegistroController::class, 'create'])->name('registro.create');
Route::post('/registro', [RegistroController::class, 'store'])->name('create');
Route::get('/registro/{registro}', [RegistroController::class, 'show'])->name('registro.show');
Route::get('/registro/{registro}/edit', [RegistroController::class, 'edit'])->name('registro.edit');
Route::put('/registro/{registro}', [RegistroController::class, 'update'])->name('registro.update');
Route::delete('/registro/{registro}', [RegistroController::class, 'destroy'])->name('registro.destroy');*/



//Route::get('/', [HomeController::class,'Home'])->name('home');
/*use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\SapController;
use App\Http\Controllers\SuministrosController;
use App\Http\Controllers\OrdenesController;
use App\Http\Controllers\PrincipalController;
use App\Http\Controllers\PlaneacionController;
use App\Http\Controllers\OrdenFabricacionController;
use App\Http\Controllers\BarcodeController;

use App\Http\Controllers\CorteController;
use App\Http\Controllers\OrdenVentaController;

// Rutas de autenticación
Route::get('/login', [LoginController::class,'login_view'])->name('login');
Route::post('/login/inicio', [LoginController::class,'login'])->name('login_post');
//Route::view('/registro', "usuarios.registro")->name('register');
Route::view('/menu', "layouts.menu")->middleware('auth')->name('menu');
//Route::post('/registro', [LoginController::class,'register'])->name('validar-registro');
Route::post('/login', [LoginController::class,'login'])->name('inicia-sesion');
Route::get('/logout', [LoginController::class,'logout'])->name('logout');

Route::get('/panel-principal', [PrincipalController::class, 'index'])->name('panel.principal');


Route::resource('registro', RegistroController::class);


Route::get('/conexion-sap', [SapController::class, 'conexionSap']);
Route::get('/datos-sap', [SapController::class, 'obtenerDatosSap']);


Route::get('/suministros', [SuministrosController::class, 'index'])->name('suministros.index');
Route::post('/suministros/enviar', [SuministrosController::class, 'enviar'])->name('suministros.enviar');

Route::get('/ordenes', [OrdenesController::class, 'index'])->name('ordenes.index');
Route::post('/enviar', [OrdenesController::class, 'ordenes.enviar'])->name('ordenes.enviar');

Route::get('/orden-venta', [OrdenVentaController::class, 'index'])->name('ordenventa');
Route::post('/orden-venta/{id}/update-state', [OrdenVentaController::class, 'updateState'])->name('ordenventa.updateState');

Route::get('/leer-codigo-barra', [BarcodeController::class, 'index'])->name('leer.codigo.barra');

Route::get('/ordenes-fabricacion', [OrdenFabricacionController::class,'index'])->name('ordenes.indexx');
Route::get('/orders', [PlaneacionController::class, 'OrdenesVActual'])->name('orders');
Route::post('/partidas', [PlaneacionController::class, 'DatosDePartida'])->name('datospartida');
Route::post('/filtros', [PlaneacionController::class, 'filtros'])->name('filtros');
Route::post('/filtro', [PlaneacionController::class, 'filtro'])->name('filtro');
Route::post('/guardarDatos', [PlaneacionController::class, 'guardarDatos'])->name('guardarDatos');
Route::post('/eliminar-registro', [PlaneacionController::class, 'eliminarRegistro'])->name('eliminarRegistro');

Route::get('/cortes', [CorteController::class, 'index'])->name('cortes');
Route::get('/cortes/data', [CorteController::class, 'getData'])->name('corte.getData');

Route::post('/FiltroFecha', [CorteController::class, 'FiltroFecha'])->name('FiltroFecha');
Route::post('/FiltroOrden', [CorteController::class, 'FiltroOrden'])->name('FiltroOrden');


Route::get('/buscar-orden', [BarcodeController::class, 'searchOrder']);

Route::get('/orden-fabricacion', [CorteController::class, 'create']);
Route::post('/orden-fabricacion', [CorteController::class, 'store']);





use App\Http\Controllers\DetallesController;
*/
//Route::get('/orden/{id}', [DetallesController::class, 'show'])->name('orden.show');



 