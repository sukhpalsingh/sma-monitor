<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>SMA Monitor</title>
    </head>

    <body class="{{ $theme }}">
        <div class="container-fluid">
            <div class="row">
                <a href="/?from={{ $previousDate }}" class="btn btn-default"><</a>

                @if ($nextDate !== null)
                    <a href="/?from={{ $nextDate }}" class="btn btn-default">></a>
                @endif
            </div>
            <div class="chart-container" style="position: relative; margin: 30% auto;">
                <canvas id="myChart"></canvas>
            </div>
        </div>

        <script src="js/lib.js" type="text/javascript"></script>
        <link rel="stylesheet" type="text/css" href="/css/app.css">
        <script>
            var screenWidth = $(window).width();
            var screenHeight = $(window).height();

            var chartWidth = screenWidth / 1.5;
            var chartHeight = screenHeight / 3;

            $('.chart-container').css('width', chartWidth);
            $('.chart-container').css('height', chartHeight);
            $('.chart-container').css('marginTop', chartHeight * 1.7);

            var ctx = document.getElementById('myChart');
            var myChart = new Chart(ctx, {
                type: 'line',
                labelString: 'kw',
                data: {
                    labels: [{!! "'" . implode("', '", $labels) . "'" !!}],
                    datasets: [{
                        label: '{{ $title }}',
                        data: {{ json_encode($logs, true) }},
                        backgroundColor: [
                            "#709000",
                        ]
                    }],
                   
                },
                
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        xAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Month'
                            }
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