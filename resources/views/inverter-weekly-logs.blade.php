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
                                    <div class="wind">
                                        <small><small>Humidity:</small></small> {{ $currentWeather->humidity }}
                                        @if ($currentWeather->last_hour_rain > 0)
                                            <small class="ml-2"><small>Rain:</small></small> {{ $currentWeather->last_hour_rain }} mm
                                        @endif
                                    </div>
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
                                            <div class="temp">
                                                {{ $weatherPredictionLog->temperature }}&deg; <small>C</small>
                                            </div>
                                            @if ($weatherPredictionLog->last_hour_rain > 0)
                                                <small class="ml-2">{{ $weatherPredictionLog->last_hour_rain }} mm</small>
                                            @endif
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

            var chartWidth = screenWidth / 1.2;
            var chartHeight = screenHeight / 3;

            $('.chart-container').css('width', chartWidth);
            $('.chart-container').css('height', chartHeight);
            $('.chart-container').css('marginTop', chartHeight * 1.9);
            $('.chart-container').css('marginLeft', (screenWidth - chartWidth) / 2);

            $('#weather-container').css('width', chartWidth);
            $('#weather-container').css('marginLeft', (screenWidth - chartWidth) / 2);

            var ctx = document.getElementById('myChart');
            var myChart = new Chart(ctx, {
                type: 'bar',
                labelString: 'kw',
                data: {
                    labels: [{!! "'" . implode("', '", $labels) . "'" !!}],
                    datasets: [{
                        label: '{{ $title }}',
                        data: {{ json_encode($logs, true) }},
                        backgroundColor: [{!! "'" . implode("', '", $backgroundColors) . "'" !!}]
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
                                labelString: 'Week'
                            }
                        }],
                        yAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Kws'
                            },
                            ticks: {
                                min: 0,
                                max: 40,
                                stepSize: 5
                            }
                            
                        }]
                    },
                    events: false,
                    tooltips: {
                        enabled: false
                    },
                    hover: {
                        animationDuration: 0
                    },
                    animation: {
                        duration: 1,
                        onComplete: function () {
                            var chartInstance = this.chart,
                                ctx = chartInstance.ctx;
                            ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'bottom';

                            this.data.datasets.forEach(function (dataset, i) {
                                var meta = chartInstance.controller.getDatasetMeta(i);
                                meta.data.forEach(function (bar, index) {
                                    var data = dataset.data[index];                            
                                    ctx.fillText(data, bar._model.x, bar._model.y - 5);
                                });
                            });
                        }
                    }
                }
            });
        </script>
    </body>

</html>