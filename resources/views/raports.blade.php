@extends('layouts.app')
@section('content')
@section('head')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css" />

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.css" />

        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
        <style>
        .button {
          padding: 2px 8px;
          font-size: 16px;
          text-align: center;
          cursor: pointer;
          color: #fff;
          background-color: #049c64;
          border: none;
          border-radius: 15px;
          box-shadow: 0 5px #999;
        }
        
        .button:hover {background-color: #3e8e41}
        
        .button:active {
          background-color: #3e8e41;
          box-shadow: 0 5px #666;
          transform: translateY(4px);
        }
        </style>

@endsection

<div class="container">

    <div class="col-6" style="margin-bottom: 2%">
        <h1><i class="fa-sharp fa-solid fa-flag"></i> Raports</h1>
    </div>

    <div style="margin-bottom: 2%">
                <button class="button">
                    <a class="navbar-brand" href="{{ url('/raports') }}">
                    {{ config('name', 'Bar chart') }}
                    </a>
                </button>

                <button class="button">
                    <a class="navbar-brand" href="{{ url('/raports/line') }}">
                        {{ config('name', 'Line chart') }}
                    </a>
                </button>
    </div>

    <template>
        <div>
            {{-- <canvas id="myChart" style="width:100%;max-width:600px"></canvas> --}}
            <canvas id="myChart" width="100%" height="50"></canvas>
        </div>
    </template>

    <div>
        <script type="application/javascript">
            // var barColors = ["red", "green","blue","orange","brown"];

            var chartData = @json($chartData);

            if(chartData.type === "line"){
                chartData.options = {
                    plugins: {
                        tooltip: {
                            callbacks: {
                                beforeTitle: function(context) {
                                    return `Average call time: ${context[0].raw.t}`;
                                }
                            }
                        }
                },
            };
            }else if(chartData.type === "bar"){
                chartData.options = {
                plugins: {
                    legend: {display: false},
                        tooltip: {
                            callbacks: {
                                beforeTitle: function(context) {
                                    const userByName = chartData.data.time[context[0].dataIndex];
                                    return `Average call time: ${userByName}`;
                                }
                            }
                        }
                },
            };
            }
                console.log(chartData);
                    new Chart("myChart", chartData);

        </script>
    </div>
</div>

@endsection