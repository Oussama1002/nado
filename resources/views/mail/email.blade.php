<!DOCTYPE html>
<html>
<head>
    <title>nado.ma</title>
</head>
<body>
    <h1>Subject: {{ $data['subject'] }}</h1>
    <p>Nom: {{ $data['name'] }}</p>
    <p>Phone: {{ $data['phone'] }}</p>
    <p>Email: {{ $data['email'] }}</p>
    @if(!empty($data['ville']))
        <p>Ville: {{ $data['ville'] }}</p>
    @endif

    <p>{{ $data['message'] }}</p>

    <p>Thank you</p>
</body>
</html>
