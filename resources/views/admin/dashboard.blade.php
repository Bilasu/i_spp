@extends('admin.layout')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">

                <div class="row mb-2">
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

                {{-- <div class="row mb-3">
                    <div class="image w-100">
                        <img src="/images/dashboard_smkdys.jpg" alt="Dashboard Image" style="width:100%; height:auto;">
                    </div>
                </div> --}}

                <div class="row">
                    <!-- Total Students -->
                    <div class="col-lg-2 col-md-4 col-sm-6 col-12 mb-3">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $totalStudents }}</h3>
                                <p>Total Student </p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Teachers -->
                    <div class="col-lg-2 col-md-4 col-sm-6 col-12 mb-3">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $totalTeachers }}</h3>
                                <p>Total Teacher</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Classes -->
                    <div class="col-lg-2 col-md-4 col-sm-6 col-12 mb-3">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $totalClasses }}</h3>
                                <p>Total Class</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-school"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Ongoing Markah -->
                    {{-- <div class="col-lg-3 col-md-6 col-sm-12 col-12 mb-3">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ $ongoingPengisian }}</h3>
                                <p>Pengisian Markah Belum Lengkap</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                        </div>
                    </div> --}}

                    <!-- Markah Diisi -->
                    <div class="col-lg-3 col-md-6 col-sm-12 col-12 mb-3">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ $totalMarkahDiisi }}</h3>
                                <p>Total Mark Filled</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bar Chart Gred -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Total of Examination</h3>
                    </div>
                    <div class="card-body">
                        <div id="barChartGredContainer">
                            <canvas id="barChartGred" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Line Chart Purata -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Average Examination Marks By CLasroom</h3>
                    </div>
                    <div class="card-body">
                        <div id="lineGraphPurataContainer">
                            <canvas id="lineGraphPurata" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // Bar Chart Gred
            fetch("{{ route('admin.dashboard.chart.grades') }}")
                .then(response => response.json())
                .then(data => {
                    if (!data || data.length === 0) {
                        document.getElementById('barChartGredContainer').innerHTML =
                            '<p class="text-center text-muted">No data to be displayed</p>';
                        return;
                    }

                    const labels = ['A', 'B', 'C', 'D', 'E'];
                    const datasets = data.map((exam, index) => ({
                        label: exam.exam,
                        data: exam.grades,
                        backgroundColor: `hsl(${index * 60}, 70%, 60%)`
                    }));

                    new Chart(document.getElementById('barChartGred'), {
                        type: 'bar',
                        data: {
                            labels,
                            datasets
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: Math.max(...datasets.flatMap(d => d.data)) + 5
                                }
                            }
                        }
                    });
                });

            // Line Graph Purata
            fetch("{{ route('admin.dashboard.chart.average_class') }}")
                .then(response => response.json())
                .then(data => {
                    if (!data || !data.datasets || data.datasets.length === 0) {
                        document.getElementById('lineGraphPurataContainer').innerHTML =
                            '<p class="text-center text-muted">No data to be displayed</p>';
                        return;
                    }

                    const datasets = data.datasets.map((cls, index) => ({
                        label: cls.label,
                        data: cls.data,
                        borderColor: `hsl(${index * 60}, 70%, 50%)`,
                        fill: false,
                        tension: 0.1
                    }));

                    new Chart(document.getElementById('lineGraphPurata'), {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: datasets
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100
                                }
                            }
                        }
                    });
                });

        });
    </script>
@endsection
