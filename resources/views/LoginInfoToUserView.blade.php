<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login information</title>
</head>
<body>
    <h1 style="text-align: center">Tracking by Light Group</h1>
    <h2 style="text-align: center">Mr/Mme {{$name}}, </h2>
    <p style="text-align: center">
        Vos informations de connexion sont les suivants:
    </p>
    <p style="text-align: center; font-size: 2em;">
        Lien: {{$link}}
    </p>
    <p style="text-align: center; font-size: 2em;">
        Identifiant: {{$email}}
    </p>
    <p style="text-align: center; font-size: 2em;">
        Mot de passe: {{$password}}
    </p>
</body>
</html>
