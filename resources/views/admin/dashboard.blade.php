<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin - AlquilaFácil</title>
</head>
<body>
    <h1>¡Bienvenido, Administrador!</h1>
    <p>Has iniciado sesión correctamente.</p>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Cerrar sesión</button>
    </form>
</body>
</html>