@extends('student.layout')

@section('content')
    <div class="content-wrapper">

        <div class="content-header">
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
                    <br>

                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">

                {{-- Card Statistik --}}
                <div class="row mb-4">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $totalAssignments }}</h3>
                                <p>Total Assignment</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $totalSubmitted }}</h3>
                                <p>Submitted Assignment</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-upload"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $ongoingAssignments }}</h3>
                                <p>Active Assignment</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ $quizAttempted }}</h3>
                                <p>Answered Quiz</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card untuk carta markah --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Graph of Exam Marks</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="examChart" height="100"></canvas>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const examMarks = @json($examMarks);

            // If examMarks is not empty, proceed with rendering the chart
            if (examMarks.length > 0) {
                const labels = examMarks.map(e => e.exam); // Extract exam names for labels
                const data = examMarks.map(e => e.mark); // Extract marks for data

                // Setup Chart.js
                const ctx = document.getElementById('examChart').getContext('2d');
                const examChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels, // Use extracted exam names as labels
                        datasets: [{
                            label: 'Markah Peperiksaan',
                            data: data, // Use extracted marks as data
                            backgroundColor: 'rgba(54, 162, 235, 0.7)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100 // Assuming the maximum mark is 100
                            }
                        }
                    }
                });
            } else {
                // Handle the case when examMarks is empty (for no data)
                $('#examChart').parent().html('<p>No exam marks available.</p>');
            }
        });
    </script>
@endsection
