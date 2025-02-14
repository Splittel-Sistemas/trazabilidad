<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="//code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <!--<link rel="stylesheet" href="{{ asset('css/login.css') }}">-->
    <style>
        @import url(https://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300);
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Source Sans Pro', sans-serif;
        }

        body {
            background: linear-gradient(to right, #42688a, #851400);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 80px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        .container h1 {
            color: #42688a;
            margin-bottom: 20px;
        }

        .button-group {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .button-group button {
            padding: 10px 20px;
            border: none;
            border-radius: 90px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
            background-color: #faf8f8;
            border: 3px solid #007bff;
            color: #007bff;
        }

        .button-group button.active {
            background-color: #007bff;
            color: white;
        }

        .button-group button:hover {
            opacity: 0.8;
        }

        .input-field {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 2px solid #7e0404;
            border-radius: 90px;
            font-size: 16px;
        }

        .input-field:focus {
            border-color: #007bff;
            outline: none;
        }

        #login-button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 90px;
            background-color: #0056b3;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        #login-button:hover {
            background-color: #003d82;
        }

        .alert {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }





        
       

        /* Burbujas de fondo */
       

        .bg-bubbles li {
            position: absolute;
            list-style: none;
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.2);
            bottom: -160px;
            animation: square 25s infinite linear; /* Animación de las burbujas */
        }

        @keyframes square {
            0% {
                transform: translateY(0);
            }
            100% {
                transform: translateY(-700px) rotate(600deg);
            }
        }

        /* Posiciones y tamaños de las burbujas */
        .bg-bubbles li:nth-child(1) { left: 10%; }
        .bg-bubbles li:nth-child(2) { left: 20%; width: 80px; height: 80px; animation-delay: 2s; animation-duration: 17s; }
        .bg-bubbles li:nth-child(3) { left: 25%; animation-delay: 4s; }
        .bg-bubbles li:nth-child(4) { left: 40%; width: 60px; height: 60px; animation-duration: 22s; background-color: rgba(255, 255, 255, 0.25); }
        .bg-bubbles li:nth-child(5) { left: 70%; }
        .bg-bubbles li:nth-child(6) { left: 80%; width: 120px; height: 120px; animation-delay: 3s; background-color: rgba(255, 255, 255, 0.2); }
        .bg-bubbles li:nth-child(7) { left: 32%; width: 160px; height: 160px; animation-delay: 7s; }
        .bg-bubbles li:nth-child(8) { left: 55%; width: 20px; height: 20px; animation-delay: 15s; animation-duration: 40s; }
        .bg-bubbles li:nth-child(9) { left: 25%; width: 10px; height: 10px; animation-delay: 2s; animation-duration: 40s; background-color: rgba(255, 255, 255, 0.3); }
        .bg-bubbles li:nth-child(10) { left: 90%; width: 160px; height: 160px; animation-delay: 11s; }


        .active {
            padding: 10px 20px;
            border: none;
            border-radius: 90px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
            background-color: #faf8f8;
            border: 3px solid #007bff;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
                <h1>Bienvenido</h1>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="text-center mb-4">
                    <span class="btn btn-outline-primary mx-2 active" id="toggleAdministrativo">Administrativo</span>
                    <span class="btn btn-outline-primary mx-2" id="toggleOperadores">Operadores</span>
                </div>

                <form method="POST" action="{{ route('login_post') }}" class="form">
                    @csrf
                    
                    <!-- Campos administrativos -->
                    <div id="administrativoFields">
                        <input name="email" type="text" placeholder="Correo Electrónico" value="{{ old('email') }}" required class="input-field">
                        <input name="password" type="password" placeholder="Contraseña" required class="input-field">
                    </div>
                    
                    <!-- Campos operadores (ocultos por defecto) -->
                    <div id="operadoresFields" style="display: none;">
                        <input name="clave" type="text" placeholder="Clave" required class="input-field">
                    </div>
                    
                    <button type="submit" id="login-button">Ingresar</button>
                </form>
            </div>
        </div>
    </div>

    <ul class="bg-bubbles">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>
</body>
<script>
     document.getElementById('toggleAdministrativo').addEventListener('click', function() {
        this.classList.add('active');
        document.getElementById('toggleOperadores').classList.remove('active');
        document.getElementById('administrativoFields').style.display = 'block';
        document.getElementById('operadoresFields').style.display = 'none';
    });

    document.getElementById('toggleOperadores').addEventListener('click', function() {
        this.classList.add('active');
        document.getElementById('toggleAdministrativo').classList.remove('active');
        document.getElementById('administrativoFields').style.display = 'none';
        document.getElementById('operadoresFields').style.display = 'block';
    });

</script>
</html>
