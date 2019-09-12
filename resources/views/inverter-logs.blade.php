<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>SMA Monitor</title>
    </head>

    <body>
        <div class="chart-container" style="position: relative; height:20vh; width: 50vw; margin: 20px auto;">
            <canvas id="myChart"></canvas>
        </div>

        <script src="js/lib.js" type="text/javascript"></script>
        <link rel="stylesheet" type="text/css" href="/css/app.css">
        <script>
            var ctx = document.getElementById('myChart');
            var myChart = new Chart(ctx, {
                type: 'line',
                labelString: 'kw',
                data: {
                    labels: [{!! "'" . implode("', '", $labels) . "'" !!}],
                    datasets: [{
                        label: '{{ $title }}',
                        data: {{ json_encode($logs, true) }},
                    }],
                   
                },
                options: {
                    responsive: true,
                    scales: {
                        xAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Month'
                            },
                        }],
                        yAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Value'
                            },
                            ticks: {
                                min: 0,
                                max: 7,
                                stepSize: 1
                            }
                            
                        }]
                    }
                }
            });
        </script>
    </body>

</html>