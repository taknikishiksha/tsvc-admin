@props(['earnings'])

<div class="card shadow h-100">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Earnings Overview</h6>
        <select class="form-select form-select-sm w-auto" id="earningsPeriod" onchange="updateEarningsChart()">
            <option value="7days">Last 7 Days</option>
            <option value="30days" selected>Last 30 Days</option>
            <option value="90days">Last 90 Days</option>
        </select>
    </div>
    <div class="card-body">
        <!-- Quick Stats -->
        <div class="row text-center mb-4">
            <div class="col-4">
                <div class="border-end">
                    <div class="text-muted small">This Month</div>
                    <div class="h5 fw-bold text-success">₹{{ number_format($earnings['current_month'] ?? 0) }}</div>
                </div>
            </div>
            <div class="col-4">
                <div class="border-end">
                    <div class="text-muted small">Last Month</div>
                    <div class="h5 fw-bold text-primary">₹{{ number_format($earnings['last_month'] ?? 0) }}</div>
                </div>
            </div>
            <div class="col-4">
                <div class="">
                    <div class="text-muted small">Pending</div>
                    <div class="h5 fw-bold text-warning">₹{{ number_format($earnings['pending'] ?? 0) }}</div>
                </div>
            </div>
        </div>

        <!-- Earnings Chart -->
        <div class="chart-area">
            <canvas id="earningsChart" width="100%" height="40"></canvas>
        </div>

        <!-- Recent Transactions -->
        <div class="mt-4">
            <h6 class="small fw-bold text-muted mb-3">RECENT TRANSACTIONS</h6>
            <div class="list-group list-group-flush">
                @forelse(($earnings['recent_transactions'] ?? []) as $transaction)
                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                    <div>
                        <div class="fw-bold small">{{ $transaction['client_name'] }}</div>
                        <div class="small text-muted">{{ $transaction['date'] }}</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-success">₹{{ number_format($transaction['amount']) }}</div>
                        <span class="badge bg-{{ $transaction['status'] === 'completed' ? 'success' : 'warning' }} small">
                            {{ ucfirst($transaction['status']) }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-3">
                    <i class="fas fa-receipt fa-2x text-muted mb-2"></i>
                    <p class="text-muted small">No recent transactions</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let earningsChart;

function initEarningsChart() {
    const ctx = document.getElementById('earningsChart').getContext('2d');
    
    earningsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($earnings['chart_labels'] ?? []),
            datasets: [{
                label: 'Earnings (₹)',
                data: @json($earnings['chart_data'] ?? []),
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                pointBackgroundColor: '#4e73df',
                pointBorderColor: '#fff',
                pointRadius: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value;
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Earnings: ₹' + context.parsed.y;
                        }
                    }
                }
            }
        }
    });
}

function updateEarningsChart() {
    const period = document.getElementById('earningsPeriod').value;
    
    // Fetch new data based on period
    fetch(`/teacher/earnings/chart-data?period=${period}`)
        .then(response => response.json())
        .then(data => {
            earningsChart.data.labels = data.labels;
            earningsChart.data.datasets[0].data = data.data;
            earningsChart.update();
        });
}

document.addEventListener('DOMContentLoaded', function() {
    initEarningsChart();
});
</script>
@endpush