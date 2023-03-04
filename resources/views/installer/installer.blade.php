<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="{{ asset('installer/css/app.css') }}" rel="stylesheet"/>
        <title>Muzzie Installer</title>
        <link rel="shortcut icon" href="{{ asset('/storage/defaults/images/favicon.png') }}" type="image/x-icon">
        <script>
            window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
            ]); ?>
        </script>
    </head>
    <body>
        <div id="app">
            <Master></Master>
        </div>
    </body>
    <script src="{{ asset('installer/js/app.js') }}"></script>
</html>