<!doctype html>
<html lang="es-MX">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="Last-Modified" content="0">
    <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="{{ url('/public/img/icon.png') }}" type="image/x-icon">
    <title>Propedéutico Medicina</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Zilla+Slab&display=swap" rel="stylesheet">
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-M43KHD2Q6C"></script>


</head>

<style>
    body.body-bg {
        min-height: 100vh;
        display: flex;
    }

    .body-bg {
        background-image: url("https://soyleonadmin.anahuacqro.edu.mx/public/img/campus_2023-15ffru.jpg");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .btn-primary {
        background: #ff5900;
        border-color: #ff5900
    }

    .btn-primary:hover {
        color: #ff5900;
        background: #fff;
        border-color: #ff5900
    }

    .btn-fff {
        background: #fff;
        border-color: #fff;
        color: #000;
    }

    .btn-fff:hover {
        color: #000;
        background: #fff;
        border-color: #fff
    }

    .card {
        background: rgba(0, 0, 0, .5);
    }

    .color-fff {
        color: white;
    }

    .logo-cefad {
        max-width: 200px;
    }

    .fail {
        color: red;
        text-align: center;
        background-color: white;
        border-radius: 15px;
        opacity: 0.8;
    }

    a:hover {
        color: #FF5900;
    }
</style>

<body class="body-bg">
    <div class="container m-auto">
        <div class="row">
            <div class="col-md-4 offset-md-4 col-sm-10 offset-sm-1">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center"> <img src="{{ url('img/anahuac.png') }}"
                                class="text-center logo-cefad m-3">
                            <p style="color:#dedede;font-family:Zilla Slab, serif;font-size: 1.2rem;font-weight: bold;">
                                Resultados del Propedéutico</p>
                            <a href="{{ route('connect') }}" class="btn btn-fff w-100 mt-2 mb-2"><img
                                    src="https://cdn.icon-icons.com/icons2/1156/PNG/512/1486565573-microsoft-office_81557.png"
                                    width="20px" class="ml-3 mr-3"><span class="ml-2">&nbsp;&nbsp;Iniciar sesión con
                                    Office 365</span></a>

                        </div>
                        <?php
$studentsURL = config('app.students_url');
if (isset($_GET["fallo"]) && $_GET["fallo"] == 'fail') {
    echo "<div class='fail'>Usuario no registrado, por favor contacta al área de sistemas.</div><br>";
} elseif (isset($_GET["fallo"]) && $_GET["fallo"] == 'true') {
    echo "<div class='fail'>Usuario no registrado</div><br>";
} elseif (isset($_GET["fallo"]) && $_GET["fallo"] == 'accesodenegado') {
    echo "<div class='fail'>Usuario no activo</div><br>";
} elseif (isset($_GET["pending"]) && $_GET["pending"] == 'invalid') {
    echo "<div class='fail'>Usuario pendiente, por favor contacta al área de sistemas.</div><br>";
} elseif (isset($_GET["domain"]) && $_GET["domain"] == 'invalid') {
    echo <<<HTML
                    <div class="fail" style="padding:10px">
                        Lo sentimos, estás intentando acceder a un sistema diferente al de tu perfil. <br><br> Para acceder correctamente puedes hacerlo dando clic en el siguiente botón:
                        <br><br>
                        <a href="{$studentsURL}" class="btn btn-primary">
                            <img src="https://soyleonadmin.anahuacqro.edu.mx/public/img/logos/anahuac-queretaro.png" width="20px" class="ml-3 mr-3">
                            <span class="ml-2">&nbsp;&nbsp;Iniciar sesión como Alumno/Docente</span>
                        </a>
                        <br><br>
                    </div>
                    HTML;
}
            ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"
        integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"
        integrity="sha384-	QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13"
        crossorigin="anonymous"></script>
</body>

</html>