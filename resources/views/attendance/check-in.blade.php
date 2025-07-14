@extends('layouts.app')

@section('title', 'Check In/Out')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Attendance Check</h1>
            <p class="text-gray-600">Scan QR code or enter your employee ID</p>
        </div>

        <!-- Current Time Display -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6 text-center">
            <div class="text-4xl font-bold text-blue-600 mb-2" id="currentTime"></div>
            <div class="text-lg text-gray-600" id="currentDate"></div>
        </div>

        <!-- Check In/Out Status -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="text-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Your Status</h3>
            </div>
            
            <div id="statusDisplay" class="text-center">
                <div class="animate-pulse">
                    <div class="w-16 h-16 bg-gray-300 rounded-full mx-auto mb-3"></div>
                    <p class="text-gray-500">Loading...</p>
                </div>
            </div>
        </div>

        <!-- QR Code Scanner -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="text-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">QR Code Scanner</h3>
                <p class="text-sm text-gray-600">Point your camera at the QR code</p>
            </div>
            
            <div id="qr-reader" class="w-full h-64 bg-gray-100 rounded-lg flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-qrcode text-4xl text-gray-400 mb-2"></i>
                    <p class="text-gray-500">Camera access required</p>
                </div>
            </div>
        </div>

        <!-- Manual Entry -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="text-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Manual Entry</h3>
                <p class="text-sm text-gray-600">Enter your employee ID manually</p>
            </div>
            
            <form id="manualForm" class="space-y-4">
                @csrf
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">Employee ID</label>
                    <input type="text" id="employee_id" name="employee_id" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-center text-lg"
                           placeholder="Enter your employee ID">
                </div>
                
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                    <textarea id="notes" name="notes" rows="2"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Any additional notes..."></textarea>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="checkIn()" 
                            class="bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-medium transition-colors">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Check In
                    </button>
                    <button type="button" onclick="checkOut()" 
                            class="bg-red-600 hover:bg-red-700 text-white py-3 px-4 rounded-lg font-medium transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Check Out
                    </button>
                </div>
            </form>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
            <div id="recentActivity" class="space-y-3">
                <!-- Activity items will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg p-8 max-w-sm w-full mx-4 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-2xl text-green-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2" id="modalTitle">Success!</h3>
            <p class="text-gray-600 mb-4" id="modalMessage">Your attendance has been recorded.</p>
            <button onclick="closeModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                OK
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
let currentEmployeeId = null;
let currentStatus = null;

// Update current time
function updateTime() {
    const now = new Date();
    document.getElementById('currentTime').textContent = now.toLocaleTimeString();
    document.getElementById('currentDate').textContent = now.toLocaleDateString();
}

// Initialize QR scanner
function initQRScanner() {
    const html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader", { fps: 10, qrbox: 250 });
    
    html5QrcodeScanner.render((decodedText) => {
        // Handle QR code scan
        const employeeId = decodedText;
        document.getElementById('employee_id').value = employeeId;
        checkEmployeeStatus(employeeId);
    }, (error) => {
        // Handle scan error
        console.log(error);
    });
}

// Check employee status
async function checkEmployeeStatus(employeeId) {
    try {
        const response = await fetch(`/attendance/employee/${employeeId}/status`);
        const data = await response.json();
        
        currentEmployeeId = employeeId;
        currentStatus = data;
        
        updateStatusDisplay(data);
    } catch (error) {
        console.error('Error checking status:', error);
    }
}

// Update status display
function updateStatusDisplay(data) {
    const statusDisplay = document.getElementById('statusDisplay');
    
    if (data.attendance) {
        if (data.attendance.check_in && !data.attendance.check_out) {
            statusDisplay.innerHTML = `
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-clock text-2xl text-green-600"></i>
                    </div>
                    <p class="text-green-600 font-medium">Currently Working</p>
                    <p class="text-sm text-gray-500">Checked in at ${data.attendance.check_in}</p>
                </div>
            `;
        } else if (data.attendance.check_out) {
            statusDisplay.innerHTML = `
                <div class="text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-home text-2xl text-gray-600"></i>
                    </div>
                    <p class="text-gray-600 font-medium">Checked Out</p>
                    <p class="text-sm text-gray-500">Completed for today</p>
                </div>
            `;
        }
    } else {
        statusDisplay.innerHTML = `
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-user text-2xl text-blue-600"></i>
                </div>
                <p class="text-blue-600 font-medium">Ready to Check In</p>
                <p class="text-sm text-gray-500">No attendance record for today</p>
            </div>
        `;
    }
}

// Check in function
async function checkIn() {
    const employeeId = document.getElementById('employee_id').value;
    const notes = document.getElementById('notes').value;
    
    if (!employeeId) {
        alert('Please enter your employee ID');
        return;
    }
    
    try {
        const response = await fetch('/attendance/check-in', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                employee_id: employeeId,
                notes: notes,
                location: await getCurrentLocation()
            })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            showSuccessModal('Check In Successful!', `You checked in at ${data.check_in_time}`);
            checkEmployeeStatus(employeeId);
            loadRecentActivity();
        } else {
            alert(data.message || 'Check-in failed');
        }
    } catch (error) {
        console.error('Error during check-in:', error);
        alert('Check-in failed. Please try again.');
    }
}

// Check out function
async function checkOut() {
    const employeeId = document.getElementById('employee_id').value;
    const notes = document.getElementById('notes').value;
    
    if (!employeeId) {
        alert('Please enter your employee ID');
        return;
    }
    
    try {
        const response = await fetch('/attendance/check-out', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                employee_id: employeeId,
                notes: notes,
                location: await getCurrentLocation()
            })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            showSuccessModal('Check Out Successful!', `You worked ${data.total_hours} hours today`);
            checkEmployeeStatus(employeeId);
            loadRecentActivity();
        } else {
            alert(data.message || 'Check-out failed');
        }
    } catch (error) {
        console.error('Error during check-out:', error);
        alert('Check-out failed. Please try again.');
    }
}

// Get current location
async function getCurrentLocation() {
    return new Promise((resolve) => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    resolve(`${position.coords.latitude},${position.coords.longitude}`);
                },
                () => {
                    resolve('Location unavailable');
                }
            );
        } else {
            resolve('Location not supported');
        }
    });
}

// Show success modal
function showSuccessModal(title, message) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').textContent = message;
    document.getElementById('successModal').classList.remove('hidden');
}

// Close modal
function closeModal() {
    document.getElementById('successModal').classList.add('hidden');
}

// Load recent activity
async function loadRecentActivity() {
    try {
        const response = await fetch('/attendance/today-summary');
        const data = await response.json();
        
        const recentActivity = document.getElementById('recentActivity');
        recentActivity.innerHTML = '';
        
        data.recent_attendance.forEach(record => {
            const activityItem = document.createElement('div');
            activityItem.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg';
            activityItem.innerHTML = `
                <div>
                    <p class="font-medium text-gray-900">${record.employee.name}</p>
                    <p class="text-sm text-gray-500">${record.check_in}</p>
                </div>
                <span class="text-sm text-green-600 font-medium">Checked In</span>
            `;
            recentActivity.appendChild(activityItem);
        });
    } catch (error) {
        console.error('Error loading recent activity:', error);
    }
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    updateTime();
    setInterval(updateTime, 1000);
    
    // Initialize QR scanner if supported
    if (typeof Html5QrcodeScanner !== 'undefined') {
        initQRScanner();
    }
    
    // Load initial data
    loadRecentActivity();
    
    // Auto-check status when employee ID changes
    document.getElementById('employee_id').addEventListener('change', function() {
        if (this.value) {
            checkEmployeeStatus(this.value);
        }
    });
});
</script>
@endpush 