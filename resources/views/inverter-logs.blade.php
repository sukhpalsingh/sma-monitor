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
            @if ($currentWeather !== null)
                <div id="weather-container" class="row">
                    <div class="col-sm-12">
                        <div class="weather">
                            <div class="current">
                                <div class="info">
                                    <div>&nbsp;</div>
                                    <div class="city"><small><small>{{ Carbon\Carbon::now()->format('D d-m-Y') }}</small></small></div>
                                    <div class="temp">{{ $currentWeather->temperature }}&deg; <small>C</small></div>
                                    <div class="wind"><small><small>WIND:</small></small> {{ $currentWeather->wind_speed }} km/h</div>
                                    <div>&nbsp;</div>
                                </div>
                                <div class="icon">
                                    <img style="height: 100px;" src="https://developer.accuweather.com/sites/default/files/{{ $currentWeather->weather_icon < 9 ? '0' : '' }}{{ $currentWeather->weather_icon }}-s.png" />
                                </div>
                            </div>
                            <div class="future">
                                @foreach ($weatherPredictionLogs as $weatherPredictionLog)
                                    <div class="day">
                                        <h5>{{ $weatherPredictionLog->recorded_at->format('h:i') }}</h5>
                                        <p>
                                            <img src="https://developer.accuweather.com/sites/default/files/{{ $weatherPredictionLog->weather_icon < 9 ? '0' : '' }}{{ $weatherPredictionLog->weather_icon }}-s.png" />
                                        </p>
                                        <p>
                                            <div class="temp">{{ $weatherPredictionLog->temperature }}&deg; <small>C</small></div>
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="chart-container" style="position: absolute; margin: 0px auto auto; top: 0px;">
                <canvas id="myChart"></canvas>
            </div>
        </div>

        <script src="js/lib.js" type="text/javascript"></script>
        <link rel="stylesheet" type="text/css" href="/css/lib.css">
        <link rel="stylesheet" type="text/css" href="/css/app.css">
        <script>
            var screenWidth = $(window).width();
            var screenHeight = $(window).height();

            var chartWidth = screenWidth / 1.5;
            var chartHeight = screenHeight / 3;

            $('.chart-container').css('width', chartWidth);
            $('.chart-container').css('height', chartHeight);
            $('.chart-container').css('marginTop', chartHeight * 1.7);
            $('.chart-container').css('marginLeft', (screenWidth - chartWidth) / 2);

            $('#weather-container').css('width', chartWidth);
            $('#weather-container').css('marginLeft', (screenWidth - chartWidth) / 2);

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