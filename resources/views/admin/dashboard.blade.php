@include('admin.layouts.header')
@extends('admin.layouts.main')
@section('title')
    Панель администратора
@endsection
@section('content')
    {{--<script src="/js/libs/chartjs.min.js"></script>--}}
    <!--<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load("current", {packages:["corechart"]});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Task', 'Hours per Day'],
                ['Work',     2],
                ['Eat',      2],
                ['Commute',  2],
                ['Watch TV', 2],
                ['Sleep',    7]
            ]);

            var options = {
                pieHole: 0,
                legend: 'none'
            };

            var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
            chart.draw(data, options);
        }
    </script>-->

    <div class="content-title">
        <div class="row">
            <div class="col-sm-12">
                <h1>Панель администратора</h1>
            </div>
        </div>
    </div>

    <div class="panel-group">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>Статистика заказов</h4>
            </div>
            <div class="panel-body dashboard">
                <div class="row">
                    <div class="col-sm-7">
                        <canvas id="orders" style="width:100%;"></canvas>
                    </div>
                    <div class="col-sm-5">
                        <div class="row">
                            <div class="col-sm-6 small-box">
                                <div class="small-box_link bg-blue">
                                    <div class="badge">{!! $stat['all_orders'] !!}</div>
                                    <div class="link">
                                        <a href="/admin/orders"><span>Все заказы</span><i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 small-box">
                                <div class="small-box_link bg-blue">
                                    <div class="badge">{!! $stat['week_order'] !!}</div>
                                    <div class="link">
                                        <a href="/admin/orders?&weeks"><span>За 2 недели</span><i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 small-box">
                                <div class="small-box_link bg-tomato">
                                    <div class="badge">{!! $stat['new_orders'] !!}</div>
                                    <div class="link">
                                        <a href="/admin/orders?&status=1"><span>Новые заказы</span><i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 small-box">
                                <div class="small-box_link bg-lightblue">
                                    <div class="badge">{!! $stat['finished'] !!}</div>
                                    <div class="link">
                                        <a href="/admin/orders?&status=6"><span>Завершенные</span><i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>Статистика продаж</h4>
            </div>
            <div class="panel-body dashboard">
                <div class="row">
                    <div class="col-sm-5">
                        <div class="row">
                            <div class="col-sm-12 small-box">
                                <div class="small-box_link sales-box bg-blue">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <h4>Сумма продаж</h4>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="badge">{!! $stat['total_sales'] !!} грн</div>
                                        </div>
                                    </div>
                                    <div class="link">
                                        <span>За все время</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 small-box">
                                <div class="small-box_link sales-box bg-lightblue">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <h4>Сумма продаж</h4>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="badge">{!! $stat['weekly_sales'] !!} грн</div>
                                        </div>
                                    </div>
                                    <div class="link">
                                        <span>За 2 недели</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <canvas id="sales" style="width:100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function(){
            var labels = [];
            var order_data = [];
            var sale_data = [];

            @foreach($orders as $key => $order)
            labels.push("{!! $key !!}");
            order_data.push("{!! $order['quantity'] !!}");
            sale_data.push("{!! $order['sales'] !!}");
                    @endforeach

            var orders = document.getElementById("orders");
            var myLineChart1 = new Chart(orders, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: "Количество заказов",
                            fill: true,
                            lineTension: 0.3,
                            backgroundColor: "rgba(95,175,228,0.8)",
                            borderColor: "rgba(95,175,228,1)",
                            borderCapStyle: 'round',
                            borderDash: [],
                            borderDashOffset: 0.0,
                            borderJoinStyle: 'miter',
                            pointBorderColor: "rgba(95,175,228,0.6)",
                            pointBackgroundColor: "#fff",
                            pointBorderWidth: 1,
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: "rgba(255,255,255,1)",
                            pointHoverBorderColor: "rgba(95,175,228,1)",
                            pointHoverBorderWidth: 2,
                            pointRadius: 3,
                            pointHitRadius: 10,
                            data: order_data,
                            spanGaps: false
                        }
                    ]
                }
            });

            var sales = document.getElementById("sales");
            var myLineChart2 = new Chart(sales, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: "Сумма продаж",
                            fill: true,
                            lineTension: 0.3,
                            backgroundColor: "rgba(95,175,228,0.8)",
                            borderColor: "rgba(95,175,228,1)",
                            borderCapStyle: 'round',
                            borderDash: [],
                            borderDashOffset: 0.0,
                            borderJoinStyle: 'miter',
                            pointBorderColor: "rgba(95,175,228,0.6)",
                            pointBackgroundColor: "#fff",
                            pointBorderWidth: 1,
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: "rgba(255,255,255,1)",
                            pointHoverBorderColor: "rgba(95,175,228,1)",
                            pointHoverBorderWidth: 2,
                            pointRadius: 3,
                            pointHitRadius: 10,
                            data: sale_data,
                            spanGaps: false
                        }
                    ]
                }
            });
        };
    </script>

@endsection
