@props(['classes'])

<div class="table-responsive">
    <table class="table table-bordered" id="upcomingClassesTable" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th>Date & Time</th>
                <th>Client</th>
                <th>Type</th>
                <th>Location</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($classes as $class)
            <tr>
                <td>
                    <div class="small">{{ $class->session_date->format('M j, Y') }}</div>
                    <div class="small text-muted">{{ $class->session_date->format('h:i A') }}</div>
                </td>
                <td>
                    <div class="fw-bold">{{ $class->client->name }}</div>
                    <div class="small text-muted">{{ $class->client->phone }}</div>
                </td>
                <td>
                    <span class="badge bg-{{ $class->session_type === 'online' ? 'info' : 'primary' }}">
                        {{ ucfirst($class->session_type) }}
                    </span>
                </td>
                <td>
                    @if($class->session_type === 'online')
                    <span class="text-muted">Online</span>
                    @else
                    <span class="small">{{ Str::limit($class->location, 30) }}</span>
                    @endif
                </td>
                <td>
                    <span class="badge bg-{{ match($class->status) {
                        'confirmed' => 'success',
                        'pending' => 'warning',
                        'completed' => 'secondary',
                        'cancelled' => 'danger',
                        default => 'secondary'
                    } }}">
                        {{ ucfirst($class->status) }}
                    </span>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" 
                                onclick="viewClassDetails({{ $class->id }})"
                                title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        @if($class->status === 'confirmed')
                        <button class="btn btn-outline-warning" 
                                onclick="rescheduleClass({{ $class->id }})"
                                title="Reschedule">
                            <i class="fas fa-calendar-alt"></i>
                        </button>
                        <button class="btn btn-outline-success" 
                                onclick="markAttendance({{ $class->id }})"
                                title="Mark Attendance">
                            <i class="fas fa-check"></i>
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-4">
                    <i class="fas fa-calendar-times fa-2x text-muted mb-3"></i>
                    <p class="text-muted">No upcoming classes scheduled</p>
                    <a href="{{ route('teacher.availability') }}" class="btn btn-primary btn-sm">
                        Set Availability
                    </a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('scripts')
<script>
function viewClassDetails(classId) {
    // Implement view class details
    console.log('View class:', classId);
}

function rescheduleClass(classId) {
    // Implement reschedule functionality
    console.log('Reschedule class:', classId);
}

function markAttendance(classId) {
    // Implement attendance marking
    console.log('Mark attendance for class:', classId);
}
</script>
@endpush