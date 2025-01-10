@extends('layouts.menu')

@section('title', 'Roles & Permisos')

@section('styles')
    <!-- Meta CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Estilos específicos para los botones de cambio de estado -->
    <style>
        /* Estilo para el botón */
        .btn.toggle-status {
            border: none; /* Sin borde */
            background-color: transparent; /* Fondo transparente */
            padding: 10px;
            cursor: pointer;
        }

        /* Estilo para el ícono */
        .btn.toggle-status i {
            font-size: 1.5rem; /* Tamaño del ícono */
            transition: color 0.3s ease;
        }

        /* Estilos para cuando el estado está activo o inactivo */
        .btn.toggle-status.active i {
            color: #28a745; /* Verde */
        }

        .btn.toggle-status.inactive i {
            color: #dc3545; /* Rojo */
        }

        /* Estilo de hover para hacer crecer el ícono */
        .btn.toggle-status:hover i {
            transform: scale(1.2);
        }
    </style>
@endsection
@section('content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>Roles & Permisos</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="#">Dashboard</a></li>
                                <li><a href="#">Roles & Permisos</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Contenido principal -->
    <div class="container my-4">
        <a href="{{ route('RolesPermisos.create') }}" class="btn btn-outline-info mb-3">Agregar Rol</a>
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="table-responsive">
            <table id="roles-table" class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Nombre del Rol</th>
                        <th>Permisos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            <td>
                                <ul>
                                    @foreach ($role->permissions as $permissions)
                                        <li>{{ $permissions->name }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <!-- Botón para editar los permisos del rol -->
                                <button class="btn btn-outline-warning btn-sm btn-edit" data-id="{{ $role->id }}"  data-toggle="modal"  data-target="#roleModal">Editar</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
        <!-- Modal para editar el rol -->
    <div class="modal fade" id="roleModal" tabindex="-1" role="dialog" aria-labelledby="roleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalLabel">Editar Rol</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="roleEditForm" method="POST" >
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="roleName">Nombre del Rol</label>
                            <input type="text" class="form-control" id="roleName" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="rolePermissions">Permisos</label>
                            <div id="rolePermissions" class="form-check">
                                <!-- Los checkboxes se generarán aquí dinámicamente -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function () {
    // Inicialización de DataTables con idioma en español
    $('#roles-table').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
        }
    });

    // Configuración global de CSRF para solicitudes AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});

// Evento para cargar datos del rol al abrir el modal
$(document).on('click', '.btn-edit', function () {
    var roleId = $(this).data('id'); // Obtener el ID del rol

    // Realizar solicitud AJAX para obtener datos del rol
    $.ajax({
        url: `/RolesPermisos/${roleId}/edit`,
        method: 'GET',
        success: function (data) {
            // Llenar los campos del modal con los datos recibidos
            $('#roleName').val(data.name);

            // Limpiar y generar checkboxes dinámicamente
            var permissionsContainer = $('#rolePermissions');
            permissionsContainer.empty();
            data.available_permissions.forEach(permission => {
                permissionsContainer.append(`
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="permission-${permission.id}" 
                               name="permissions[]" value="${permission.id}" 
                               ${data.permissions.includes(permission.id) ? 'checked' : ''}>
                        <label class="form-check-label" for="permission-${permission.id}">
                            ${permission.name}
                        </label>
                    </div>
                `);
            });

            // Actualizar acción del formulario dinámicamente
            var formAction = `/RolesPermisos/${data.id}`;
            $('#roleEditForm').attr('action', formAction);
        },
        error: function () {
            Swal.fire('Error', 'No se pudieron cargar los datos del rol.', 'error');
        }
    });
});

// Evento para enviar el formulario de edición
$(document).on('submit', '#roleEditForm', function (event) {
    event.preventDefault(); // Prevenir el envío tradicional del formulario

    var form = $(this);
    var formData = form.serialize();

    // Realizar la solicitud AJAX para actualizar el rol
    $.ajax({
        url: form.attr('action'),
        method: 'PUT',
        data: formData,
        success: function (response) {
            if (response.success) {
                Swal.fire('Éxito', response.message, 'success').then(() => {
                    $('#roleModal').modal('hide'); // Cerrar el modal
                    location.reload(); // Recargar la página para reflejar los cambios
                });
            } else {
                Swal.fire('Error', response.message || 'Ocurrió un problema', 'error');
            }
        },
        error: function () {
            Swal.fire('Error', 'No se pudo actualizar el rol.', 'error');
        }
    });
});


</script>
        
    
@endsection
