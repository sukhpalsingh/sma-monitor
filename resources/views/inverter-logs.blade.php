<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>SMA Monitor</title>
    </head>

    <body>
        <div style="width:800px; height: 200px; margin-top: 10px auto;">
            <canvas id="myChart"></canvas>
        </div>

        <script src="js/lib.js" type="text/javascript"></script>
        <link rel="stylesheet" type="text/css" href="/css/app.css">
        <script>
            var ctx = document.getElementById('myChart');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    datasets: [{
                        label: 'Today',
                        data: {{ json_encode($logs, true) }},
                    }]
                }
            });
        </script>
    </body>

</html>