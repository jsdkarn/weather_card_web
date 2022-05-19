<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Weather</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://momentjs.com/downloads/moment.js"></script>
        <script>
            $(document).ready(function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(showLocation);
                } else {
                    $('#location').html('Geolocation is not supported by this browser.');
                }
            });

            function showLocation(position) {
                var latitude = position.coords.latitude;
                var longitude = position.coords.longitude;
                window.location.href = `getWeatherData.php?latitude=${latitude}&longitude=${longitude}`;
            }
        </script>
</body>
<style>
    * {
        margin: 0;
        padding: 0;
    }

    .element {
        height: 50px;
        width: 50px;
        margin: 10px;
    }

    .elements {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;

    }

    .white {
        fill: #FFFFFF
    }

    .gray {
        fill: #E0E0E0
    }

    .yellow {
        fill: #FFEB3B
    }
</style>

</html>