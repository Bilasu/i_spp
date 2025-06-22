@extends('teacher.layout')
<style>
    .card {
        border: 1px solid #ccc;
        /* Optional: To define a border for the chart container */
        padding: 20px;
        /* Optional: Adds padding inside the card */
    }

    .card-header {
        font-size: 18px;
        font-weight: bold;
    }

    .card-body {
        text-align: center;
    }
</style>
@section('content')
    <div class="content-wrapper">
        {{-- Alert Message --}}
        <div class="content-header">
            @if (Session::has('success'))
                <div class="alert alert-success">
                    {{ Session::get('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <div class="col-sm-6">
                            @php
                                $user =
                                    Auth::guard('admin')->user() ??
                                    (Auth::guard('teacher')->user() ?? Auth::guard('student')->user());
                            @endphp

                            <div class="d-flex align-items-center justify-content-between flex-wrap mb-4">
                                <div>
                                    <h1 class="h3 mb-1 text-gray-800">Welcome, {{ $user->name ?? 'User' }}! 👋</h1>
                                    <p class="text-muted mb-0">Wishing you a productive and successful day. Keep giving your
                                        best! 💪</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Summary Cards --}}
                <div class="row">
                    @php
                        $cards = [
                            [
                                'label' => 'Total Students',
                                'value' => $totalStudents,
                                'icon' => 'fas fa-user-graduate',
                                'color' => 'info',
                            ],
                            [
                                'label' => 'Total Classes',
                                'value' => $totalClasses,
                                'icon' => 'fas fa-school',
                                'color' => 'success',
                            ],

                            [
                                'label' => 'Marks Submitted',
                                'value' => $totalMarksSubmitted,
                                'icon' => 'fas fa-check-circle',
                                'color' => 'primary',
                            ],
                        ];
                    @endphp

                    @foreach ($cards as $card)
                        <div class="col-lg-3 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="small-box bg-{{ $card['color'] }}">
                                <div class="inner">
                                    <h3>{{ $card['value'] }}</h3>
                                    <p>{{ $card['label'] }}</p>
                                </div>
                                <div class="icon">
                                    <i class="{{ $card['icon'] }}"></i>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Grade Distribution Pie Charts --}}
                <div class="row mb-4">
                    @foreach ($gradeChartData as $index => $classGrade)
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="text-center">Grade Distribution for Class: {{ $classGrade['class'] }}</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="gradePieChart{{ $index }}"
                                        style="height: 300px; width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                        @if (($index + 1) % 3 == 0)
                </div>
                <div class="row mb-4"> <!-- New row after every 3rd pie chart -->
                    @endif
                    @endforeach
                </div>


            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const gradeChartData = @json($gradeChartData);

            @foreach ($gradeChartData as $index => $classGrade)
                const ctxPie{{ $index }} = document.getElementById('gradePieChart{{ $index }}')
                    .getContext('2d');
                new Chart(ctxPie{{ $index }}, {
                    type: 'pie',
                    data: {
                        labels: ['A', 'B', 'C', 'D', 'E', 'F'], // Grade Labels
                        datasets: [{
                            data: [
                                {{ $classGrade['grades']['A'] }},
                                {{ $classGrade['grades']['B'] }},
                                {{ $classGrade['grades']['C'] }},
                                {{ $classGrade['grades']['D'] }},
                                {{ $classGrade['grades']['E'] }},
                                {{ $classGrade['grades']['F'] }}
                            ],
                            backgroundColor: [
                                '#4caf50', '#2196f3', '#ff9800', '#e91e63', '#9c27b0', '#f44336'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            @endforeach
        });
    </script>
@endsection
