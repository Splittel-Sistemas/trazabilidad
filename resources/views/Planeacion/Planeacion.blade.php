@extends('layouts.menu')
@section('title', 'Planeacion')
@section('styles')
<link rel="stylesheet" href="{{asset('css/Planecion.css')}}">
@endsection
@section('content')
<div class="row mb-2">
    <div class="breadcrumbs col-12">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>Planeaci&oacute;n</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="#">Dashboard</a></li>
                                <li><a href="#">Planeaci&oacute;n</a></li>
                                <li class="active">Planeaci&oacute;n Fabricaci&oacute;n</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 ">
        <div class="card">
            <div class="card-header">
                <strong>Filtrar Órdenes de Venta</strong>
                <button id="filtro_ov" type="button" class="btn btn-link float-end collapsed" draggable="true" data-bs-toggle="collapse" data-bs-target="#filtro" aria-expanded="true" aria-controls="filtro">
                    <i class="fa fa-chevron-up"></i>
                </button>
            </div>
            <div class="card-body card-block collapsed show" id="filtro">
                <form id="filtroForm" method="post" class="form-horizontal" action="{{--route('filtros')--}}">
                    @csrf
                    <div class="row">
                        <!-- Filtro por fecha -->
                        <div class="col-md-6 ">
                            <label for="" class="form-control-label me-2 col-12">
                                <strong>Filtro por Fecha</strong>
                            </label>
                            <div class="input-group">
                                <div class="col-4">
                                    <label for="startDate" class="form-control-label me-2 ">Fecha inicio:</label>
                                </div>
                                <div class="col-8">
                                    <input type="date" name="startDate" id="startDate" class="form-control form-control-sm w-autoborder-primary" value="{{$FechaFin }}">
                                    <input type="hidden" name="startDate_filtroantnext" id="startDate_filtroantnext" class="form-control form-control-sm w-autoborder-primary" value="{{$FechaFin }}">
                                </div>
                            </div>
                            <div class="input-group pt-3">
                                <div class="col-4">
                                    <label for="endDate" class="form-control-label ">Fecha fin:</label>
                                </div>
                                <div class="col-8">
                                    <input type="date" name="endDate" id="endDate" class="form-control form-control-sm w-autoborder-primary" value="{{$FechaInicio}}">
                                    <input type="hidden" name="endDate_filtroantnext" id="endDate_filtroantnext" class="form-control form-control-sm w-autoborder-primary" value="{{$FechaInicio}}">
                                    <p id="error_endDate" class="text-danger fs-sm"></p>
                                </div>
                            </div>
                            <div class="row form-group pt-3">
                                <button type="button" class="btn btn-primary btn-sm float-end" id="Filtro_fecha-btn">
                                    <i class="fa fa-search"></i> Filtrar
                                </button>
                            </div>
                        </div>
                        <!-- Filtro por Orden de Venta -->
                        <div class="col-md-6">
                            <label for="" class="form-control-label me-2 col-12">
                                <strong>Filtro por Orden de Venta</strong>
                            </label>
                            <div class="input-group">
                                <input type="text" placeholder="Ingresa una Orden de Venta" name="Filtro_buscarOV" id="Filtro_buscarOV" class="form-control form-control-sm w-autoborder-primary col-9">
                                <div class="input-group-btn">
                                    <button id="buscarOV" class="btn btn-primary btn-sm">
                                        <i class="fa fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                        </div> 
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="">
    <!--<h1 class="text-primary mb-4 text-center">Gestión de Órdenes de Venta</h1>-->
    <!-- Buscador -->
   <!-- Buscador -->
    <div class="row mb-2">
    </div>
</div>

    <!-- Contenedor de las tablas -->
    <div class="card p-3">
        <div class="row mb-5">
            <!-- Columna 1: Tabla de Órdenes de Venta -->
            <div class="col-md-6 mb-2">
                <div id="container_table_OV" class="table-responsive">
                    @if ($status=="empty")
                        <br>
                        <br>
                        <br>
                        <p class="text-center mt-4">No existen partidas <br> para el periodo</p>
                        <p class="text-center">{{$FechaFin}} - {{$FechaInicio}}</p>
                    @elseif($status=="success")
                        <table id="table_OV" class="table table-striped table-bordered" >
                            <thead class="table-primary text-center">
                                <tr>
                                    <th class="fw-bold">
                                        <span id="filtro-fecha-Ov">Órdenes de Venta <br> <p>{{$FechaInicio}} - {{$FechaFin}}</p></span>
                                        <div class="input-group ">
                                            <input type="text" placeholder="Ingresa una Orden de Venta" name="filtro_ov_tabla" oninput="filtro_ov_tabla(this.value);" id="filtro_ov_tabla" class="form-control form-control-sm   w-autoborder-primary col-12">
                                            <div class="input-group-btn">
                                                <button class="btn btn-primary btn-sm">
                                                    <i class="fa fa-search"></i> buscar
                                                </button>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="table_OV_body">
                                @foreach ($datos as $orden)
                                <tr class="table-light" id="details{{ $loop->index }}cerrar" style="cursor: pointer;" draggable="true" data-bs-toggle="collapse" data-bs-target="#details{{ $loop->index }}" aria-expanded="false" aria-controls="details{{ $loop->index }}">
                                    <td onclick="loadContent('details{{ $loop->index }}', {{ $orden['OV'] }}, '{{ $orden['Cliente'] }}')">
                                        {{ $orden['OV']." - ".$orden['Cliente']}}
                                    </td>
                                </tr>
                                <tr id="details{{ $loop->index }}" class="collapse">
                                    <td class="table-border" id="details{{ $loop->index . 'llenar' }}">
                                        <!-- Aquí se llenarán los detalles de la orden cuando el usuario haga clic -->
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-center mt-4">
                            <br>
                            <br>
                            <br>
                            Ocurrio un error!, no fue posible cargar los datos
                            <br>
                            <br>
                            <br>
                            <i class="fa fa-gears" style="transform: scale(4)"></i>
                        </p>
                    @endif
                </div>
                <div class="col-12">
                    <!-- Navegación de Fechas -->
                    <div class="d-flex justify-content-center align-items-center mb-4">
                        <button type="button" id="back_filterBtn" class="btn btn-link"><i class="fa fa-arrow-left"></i> Anterior</button>
                        <button type="button" id="next_filterBtn" class="btn btn-link">Siguiente <i class="fa fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>
            <!-- Columna 2: Dropzone y Tabla de Migrados -->
            <div class="col-md-6 mb-2">
                <!-- Área de Dropzone -->
                <div class="col 12 mb-2 pt-1">
                    <div class="form-row">
                        <div class="col-12 mb-3">
                            <label for="Filtrofecha_table2">Selecciona una fecha:</label>
                            <div class="input-group ">
                                <input type="date" name="FiltroOV_table2"  id="FiltroOV_table2" class="form-control form-control-sm   w-autoborder-primary col-12" placeholder="Ingresa Orden de fabricación">
                                <div class="input-group-btn">
                                    <button id="buscarOV" class="btn btn-primary btn-sm">
                                        Guardar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="dropzone" 
                    class="dropzone-area border-dashed border-primary p-4 text-center mb-4"
                    style="border: 2px dashed #007bff; padding: 20px; text-align: center; min-height: 120px;">
                    <h4>Arrastra aquí los datos</h4>
                    <p class="text-muted">Suelta los artículos que deseas migrar aquí</p>
                </div>

                <!-- Tabla de Migrados -->
                <div id="container_table_OF_migrados" class="table-responsive">
                    <table class="table table-striped table-bordered" id="table_OF_migrados">
                        <thead class="table-primary text-center">
                            <tr>
                                <th colspan="6" class="fw-bold">
                                    <p style="color: black" id="filtro-fecha-Ov">Órden de Fabricación <br> 2234</p>
                                    <div class="input-group ">
                                        <input type="text" name="FiltroOF_table2"  id="FiltroOF_table2" class="form-control form-control-sm   w-autoborder-primary col-12" placeholder="Buscar Orden de fabricación">
                                        <div class="input-group-btn">
                                            <button id="buscarOV" class="btn btn-primary btn-sm">
                                                <i class="fa fa-search"></i> buscar
                                            </button>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th>Orden Fab.</th>
                                <th>Artículo</th>
                                <th>Descripción</th>
                                <th>Cantidad</th>
                                <th>Fecha Entrega</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="table-2-content">
                            <!-- Aquí se añadirán las filas movidas -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/ordenesv.js') }}"></script>

@endsection
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
    $('#Filtro_fecha-btn').click(function() {
        var startDate = $('#startDate').val();  
        var endDate = $('#endDate').val(); 
        if(CompararFechas(startDate,endDate)){
            $('#error_endDate').html('');
        }else{
            $('#error_endDate').html('*Fecha fin tiene que ser menor  a Fecha inicio');
            return 0;
        }
        $.ajax({
            url: "{{route('PlaneacionFF')}}", 
            type: 'POST',
            data: {
                startDate: startDate, 
                endDate:endDate,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
                $('#table_OV_body').html("<p align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /></p>")
                // You can display a loading spinner here
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#table_OV_body').html(response.data);  
                    $('#filtro-fecha-Ov').html('Órdenes de Venta<br><p>'+response.fechaHoy+' - '+response.fechaAyer+'</p>');
                } else if(response.status==="empty") {
                    $('#table_OV_body').html('<p>No existen registros para el periodo '+fechaHoy+' - '+fechaAyer+'</p>');
                }else{
                    error("Ocurrio un error!....","los datos no pudieron ser procesados correctamente");
                }
            },
            error: function(xhr, status, error) {
                errorBD();
            }
        });
    });
    $('#Filtro_buscarOV').on('input',function() {
        OV=$('#Filtro_buscarOV').val();
        if(CadenaVacia(OV)){
            return 0;
        }
        if(OV.length<4){
            return 0;
        }
        $.ajax({
            url: "{{route('PlaneacionFOV')}}", 
            type: 'POST',
            data: {
                OV: OV,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
                $('#table_OV_body').html("<p align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /></p>")
                // You can display a loading spinner here
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#filtro-fecha-Ov').html('Órdenes de Venta<br><p>Filtro: '+OV+'</p>');
                    $('#table_OV_body').html(response.data);
                } else if(response.status==="empty") {
                    $('#table_OV_body').html('<p>No existen registros para lo orden de venta '+OV+'</p>');
                }else{
                    error("Ocurrio un error!....","los datos no pudieron ser procesados correctamente");
                }
            },
            error: function(xhr, status, error) {
                errorBD();
            }
        });
    });
    $('#back_filterBtn').click(function() {
        var startDate =$('#startDate_filtroantnext').val();  
        startDate=RestarDia(startDate)
        var endDate = $('#endDate_filtroantnext').val(); 
        endDate=RestarDia(endDate);
        $.ajax({
            url: "{{route('PlaneacionFF')}}", 
            type: 'POST',
            data: {
                startDate: startDate, 
                endDate:endDate,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
                $('#table_OV_body').html("<p align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /></p>")
                // You can display a loading spinner here
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#table_OV_body').html(response.data);  
                    $('#filtro-fecha-Ov').html('Órdenes de Venta<br><p>'+response.fechaHoy+' - '+response.fechaAyer+'</p>');
                } else if(response.status==="empty") {
                    $('#table_OV_body').html('<p class="text-center">No existen registros para el periodo <br>'+startDate+' - '+endDate+'</p>');
                }else{
                    error("Ocurrio un error!....","los datos no pudieron ser procesados correctamente");
                }
                $('#startDate_filtroantnext').val(startDate);  
                $('#endDate_filtroantnext').val(endDate); 
            },
            error: function(xhr, status, error) {
                errorBD();
            }
        });
    });
    $('#next_filterBtn').click(function() {
        var startDate =$('#startDate_filtroantnext').val();  
        startDate=SumarDia(startDate);
        var endDate = $('#endDate_filtroantnext').val(); 
        endDate=SumarDia(endDate);
        $.ajax({
            url: "{{route('PlaneacionFF')}}", 
            type: 'POST',
            data: {
                startDate: startDate, 
                endDate:endDate,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
                $('#table_OV_body').html("<p align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /></p>")
                // You can display a loading spinner here
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#table_OV_body').html(response.data);  
                    $('#filtro-fecha-Ov').html('Órdenes de Venta<br><p>'+response.fechaHoy+' - '+response.fechaAyer+'</p>');
                } else if(response.status==="empty") {
                    $('#table_OV_body').html('<p class="text-center">No existen registros para el periodo <br> '+startDate+' - '+endDate+'</p>');
                }else{
                    error("Ocurrio un error!....","los datos no pudieron ser procesados correctamente");
                }
                $('#startDate_filtroantnext').val(startDate);  
                $('#endDate_filtroantnext').val(endDate); 
            },
            error: function(xhr, status, error) {
                errorBD();
            }
        });
    });
    });
    function loadContent(idcontenedor, docNum, cliente) {
        let elemento = document.getElementById(idcontenedor + "cerrar");
        if (!elemento.classList.contains('collapsed')) {
            $.ajax({
                url: "{{route('PartidasOF')}}",
                method: "POST",
                data: {
                    docNum: docNum, 
                    cliente:cliente,
                    _token: '{{ csrf_token() }}'
                },
                beforeSend: function () {
                    $('#' + idcontenedor + "llenar").html("<p align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /></p>");
                },
                success: function (response) {
                    if (response.status === 'success') {
                        $('#' + idcontenedor + "llenar").html(response.message);
                    } else {
                        $('#' + idcontenedor + "llenar").html('<p>Error al cargar el contenido.</p>');
                    }
                },
                error: function (xhr, status, error) {
                    $('#' + idcontenedor + "llenar").html('<p>Error al cargar el contenido.</p>');
                }
            });
        } else {
            $('#' + idcontenedor + "llenar").html('');
        }
    }
    function SeleccionaFilas() {
        const selectAllCheckbox = document.getElementById("selectAll");
        const checkboxes = document.querySelectorAll(".rowCheckbox");
        if(selectAllCheckbox.checked){
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
                SeleccionarFila(null, checkbox);  // Actualiza la clase "selected"
            });
        }else{
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
                SeleccionarFila(null, checkbox);  // Actualiza la clase "selected"
            });
        }
    }
    function SeleccionarFila(event, checkbox) {
        const row = checkbox.closest('tr');
        if (checkbox.checked) {
            row.classList.add("selected");
        } else {
            row.classList.remove("selected");
        }
    }
    // Permitir que el elemento sea soltado
    /*function allowDrop(event) {
        event.preventDefault();
    }
    // Al arrastrar uno o más elementos de la tabla
    function drag(event) {
        let selectedRows = document.querySelectorAll(".selected");
        let rowIds = [];
        
        selectedRows.forEach(row => {
            rowIds.push(row.id);  // Almacenamos los IDs de las filas seleccionadas
        });

        event.dataTransfer.setData("text", rowIds.join(","));
    }
    // Al soltar los elementos en el dropzone
    function drop(event) {
        event.preventDefault();
        const data = event.dataTransfer.getData("text");
        const rowIds = data.split(",");

        rowIds.forEach(id => {
            const droppedRow = document.getElementById(id);
            const cells = droppedRow.getElementsByTagName("td");
            const rowData = document.createElement("tr");

            // Crear una nueva fila en la tabla de datos arrastrados
            for (let i = 1; i < cells.length; i++) {  // i = 1 porque la primera celda es el checkbox
                const newCell = document.createElement("td");
                newCell.textContent = cells[i].textContent;
                rowData.appendChild(newCell);
            }

            // Añadir la fila a la tabla de datos arrastrados
            document.getElementById("droppedTable").querySelector("tbody").appendChild(rowData);

            // Opcional: Eliminar la fila de la tabla original si lo deseas
            droppedRow.remove();
        });
    }*/
</script>
<!---------------------------------------------------------------------------------->
<script>
    /*
 //let consultasMigradas = new Set(); // IDs de filas ya migradas
 let consultasMigradas = new Set(); // IDs de filas ya migradas

function drag(event) {
    const targetRow = event.target.closest('tr'); // Fila que se está arrastrando
    const uniqueId = targetRow.id; // ID único de la fila
    event.dataTransfer.setData("text", uniqueId); // Permitir el arrastre
}

function drop(event) {
    event.preventDefault();

    const draggedId = event.dataTransfer.getData("text"); // ID de la fila arrastrada
    const draggedRow = document.getElementById(draggedId); // Fila arrastrada

    if (draggedRow) {
        if (!consultasMigradas.has(draggedId)) {
            // Intentar guardar los datos automáticamente
            guardarRow(draggedRow, draggedId);
        } else {
            console.warn("La fila ya fue migrada previamente:", draggedId);
            alert("Esta fila ya fue migrada previamente.");
        }
    } else {
        console.error("Fila arrastrada no encontrada:", draggedId);
    }
}

function regresarRow(rowId) {
    const migratedRow = document.getElementById(`migrated-${rowId}`);

    if (migratedRow) {
        const originalRow = document.getElementById(rowId);
        if (originalRow) {
            originalRow.style.display = ""; // Mostrar la fila original
            consultasMigradas.delete(rowId);
        }

        migratedRow.remove(); // Eliminar la fila migrada de la tabla 2
    }
}

function allowDrop(event) {
    event.preventDefault(); // Permitir el drop
}

function guardarRow(row, draggedId) {
    console.log("Intentando guardar fila:", draggedId); // Log para depurar

    // Extraer datos de la fila
    const ordenFab = row.cells[0].innerText.trim();
    const articulo = row.cells[1].innerText.trim();
    const descripcion = row.cells[2].innerText.trim();
    const cantidadOf = parseFloat(row.cells[3].innerText.trim()) || null;
    const fechaEntrega = row.cells[4].innerText.trim() || null;

    // Enviar datos al servidor mediante AJAX
    $.ajax({
        url: "{{--route('guardarDatos')--}}", // Ruta del endpoint
        method: "POST",
        data: {
            orden_fab: ordenFab,
            articulo: articulo,
            descripcion: descripcion,
            cantidad_of: cantidadOf,
            fecha_entrega: fechaEntrega,
            _token: "{{ csrf_token() }}" // CSRF Token para Laravel
        },
        success: function (response) {
            if (response.status === "success") {
                console.log("Datos guardados correctamente:", response.data);
                alert("Fila guardada correctamente.");
                consultasMigradas.add(draggedId); // Marcar la fila como migrada

                // Agregar la fila a la tabla migrada
                const newRow = document.createElement("tr");
                newRow.innerHTML = row.innerHTML;

                // Agregar botón "Regresar"
                const regresarButton = `
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="regresarRow('${draggedId}')">Regresar</button>
                    </td>`;
                newRow.innerHTML += regresarButton;

                // Asignar un ID único a la nueva fila
                newRow.id = `migrated-${draggedId}`;
                document.getElementById("table-2-content").appendChild(newRow);

                // Ocultar la fila original en la tabla 1
                row.style.display = "none";
            } else {
                alert("Error al guardar los datos: " + response.message);
            }
        },
        error: function (xhr, status, error) {
            if (xhr.responseJSON && xhr.responseJSON.status === "exists") {
                alert("Esta fila ya existe en la base de datos.");
            } else {
                console.error("Error al guardar los datos:", xhr.responseText);
                alert("Hubo un error al guardar los datos.");
            }
        }
    });
}



//
 $(document).ready(function() {
    $('#botonBuscar').click(function() {
        var docNum = $('#docNum').val();  
        $.ajax({
            url: "{{--route('datospartida')--}}", 
            type: 'POST',
            data: {
                docNum: docNum,  
                _token: '{{ csrf_token() }}'  
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#resultado').html(response.message);  
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', status, error);
                alert('Hubo un error al obtener las partidas. Intenta nuevamente.');
            }
        });
    });
});

 $(document).ready(function () {
    $('#buscarOV').on('click', function (e) {
        e.preventDefault();

        var query = $('#query').val().trim(); 

        if (!query) {
            alert("Por favor, ingresa una Orden de Venta.");
            return;
        }

        $.ajax({
            url: "{{--route('filtro')--}}", 
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            },
            data: {
                query: query, 
            },
            success: function (response) {
                if (response.status === 'success') {
                    $('#table_OV_body').html(response.data); 
                } else {
                    $('#table_OV_body').html(response.message); 
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                alert('Ocurrió un error. Por favor, intenta nuevamente.');
            }
        });
    });
});

 $(document).ready(function() {
    $('#filtroForm').on('submit', function(e) {
        e.preventDefault(); 
        var startDate = $('#startDate').val();
        var endDate = $('#endDate').val();
        var query = $('#query').val() || ''; 
        if (new Date(startDate) > new Date(endDate)) {
            alert("La fecha de inicio no puede ser posterior a la fecha de fin.");
            return;
        }
        $.ajax({
            url: "{{--route('filtros')--}}", 
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                startDate: startDate,
                endDate: endDate,
                query: query
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#table_OV_body').html(response.data); 
                } else {
                    $('#table_OV_body').html('<p class="text-center m-4">'+ response.message+'</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Ocurrió un error. Por favor, intenta nuevamente.');
            }
        });
    });
});
 $(document).ready(function () {
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
let currentDate = moment();
const datePicker = $('#datePicker');
datePicker.val(currentDate.format('YYYY-MM-DD'));

function filterOrdersByDate(date) {
    let foundAnyOrder = false;

    $('.order-row').each(function () {
        const rowDate = $(this).data('date');
        if (rowDate === date) {
            $(this).show();
            foundAnyOrder = true;
        } else {
            $(this).hide();
        }
    });

    $('#noOrdersRow').toggleClass('d-none', foundAnyOrder);
}

datePicker.on('change', function () {
    filterOrdersByDate($(this).val());
});

$('#prevDayBtn').on('click', function (e) {
    e.preventDefault();
    currentDate.subtract(1, 'days');
    datePicker.val(currentDate.format('YYYY-MM-DD'));
    filterOrdersByDate(currentDate.format('YYYY-MM-DD'));
});

$('#todayBtn').on('click', function (e) {
    e.preventDefault();
    currentDate = moment();
    datePicker.val(currentDate.format('YYYY-MM-DD'));
    filterOrdersByDate(currentDate.format('YYYY-MM-DD'));
});

$('#searchForm').on('submit', function (e) {
    e.preventDefault();
    const docNum = $('#ordenSearch').val();
    loadContent(docNum);
});
});
function loadContent(idcontenedor, docNum, cliente) {
    let elemento = document.getElementById(idcontenedor + "cerrar");
    if (!elemento.classList.contains('collapsed')) {
        $.ajax({
            url: "{{route('PartidasOF')}}",
            method: "POST",
            data: {
                docNum: docNum, 
                cliente:cliente,
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function () {
                $('#' + idcontenedor + "llenar").html("<p align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /></p>");
            },
            success: function (response) {
                if (response.status === 'success') {
                    $('#' + idcontenedor + "llenar").html(response.message);
                } else {
                    $('#' + idcontenedor + "llenar").html('<p>Error al cargar el contenido.</p>');
                }
            },
            error: function (xhr, status, error) {
                $('#' + idcontenedor + "llenar").html('<p>Error al cargar el contenido.</p>');
            }
        });
    } else {
        $('#' + idcontenedor + "llenar").html('');
    }
    //
    //function drag(event) {
    //    event.dataTransfer.setData("text", event.target.id);
    //}
}
document.getElementById('filtro_ov').addEventListener('click', function(event) {
    btn=document.getElementById('filtro_ov');
    if (!btn.classList.contains('collapsed')) {
        btn.innerHTML='<i class="fa fa-chevron-up"></i>';
    }else{
        btn.innerHTML='<i class="fa fa-chevron-down"></i>';
    }
});
function filtro_ov_tabla(ov){
    campo=0;
    let filas = document.querySelectorAll("#table_OV tbody tr");
    $('#table_OV .collapse').collapse('hide');
    filas.forEach(fila => {  
    let valorCelda = fila.cells[campo].innerText.trim();
    if(fila.id.includes("cerrar")){
        if (valorCelda.includes(ov)) {
        fila.style.display = ""; 
        } else {
        fila.style.display = "none"; 
        }
    }
  });
}
function filtro_fecha(startDate,endDate,query){
    e.preventDefault(); 
        if (new Date(startDate) > new Date(endDate)) {
            alert("La fecha de inicio no puede ser posterior a la fecha de fin.");
            return;
        }
        $.ajax({
            url: "{{--route('filtros')--}}", 
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                startDate: startDate,
                endDate: endDate,
                query: query
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#table_OV_body').html(response.data); 
                } else {
                    $('#table_OV_body').html('<p class="text-center m-4">'+ response.message+'</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Ocurrió un error. Por favor, intenta nuevamente.');
            }
        });
}*/
</script>
@endsection
