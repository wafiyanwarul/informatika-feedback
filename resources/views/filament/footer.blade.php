<div class="fi-footer mt-8 border-t border-gray-200 bg-white px-6 py-4">
    <!-- Copyright Section -->
    <div class="mb-4 text-center">
        <p class="text-sm text-gray-600">
            © {{ date('Y') }} Survey Management System.
            <span class="font-medium">Powered by Laravel 11</span>
        </p>
        <p class="text-xs text-gray-500 mt-1">
            Built with ❤️ for Educational Excellence | Version 1.0.0
        </p>
    </div>

    <!-- Maintenance Mode Section -->
    <div class="border-2 border-red-200 rounded-lg p-4 bg-red-50">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-red-800">⚠️ DANGER AREA</h3>
                    <p class="text-sm text-red-700">System maintenance controls - Use with extreme caution</p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <!-- Status Indicator -->
                <div class="flex items-center space-x-2" id="maintenance-status">
                    @if(app()->isDownForMaintenance())
                        <div class="flex items-center space-x-2">
                            <div class="h-3 w-3 bg-red-500 rounded-full animate-pulse"></div>
                            <span class="text-sm font-semibold text-red-700">MAINTENANCE MODE ACTIVATED</span>
                        </div>
                    @else
                        <div class="flex items-center space-x-2">
                            <div class="h-3 w-3 bg-green-500 rounded-full"></div>
                            <span class="text-sm font-semibold text-green-700">System Online</span>
                        </div>
                    @endif
                </div>

                <!-- Maintenance Button -->
                <button
                    type="button"
                    onclick="toggleMaintenanceMode()"
                    id="maintenance-btn"
                    class="@if(app()->isDownForMaintenance()) bg-green-600 hover:bg-green-700 @else bg-red-600 hover:bg-red-700 @endif text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                    @if(app()->isDownForMaintenance())
                        🔄 COMPLETE MAINTENANCE
                    @else
                        🔧 ENTER MAINTENANCE MODE
                    @endif
                </button>
            </div>
        </div>
    </div>
</div>

<script>
async function toggleMaintenanceMode() {
    const btn = document.getElementById('maintenance-btn');
    const statusEl = document.getElementById('maintenance-status');

    // Disable button and show loading
    btn.disabled = true;
    btn.innerHTML = '⏳ Processing...';

    try {
        const response = await fetch('/admin/maintenance/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();

        if (data.success) {
            // Update UI based on new status
            if (data.maintenance_mode) {
                // Maintenance mode activated
                statusEl.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <div class="h-3 w-3 bg-red-500 rounded-full animate-pulse"></div>
                        <span class="text-sm font-semibold text-red-700">MAINTENANCE MODE ACTIVATED</span>
                    </div>
                `;
                btn.innerHTML = '🔄 COMPLETE MAINTENANCE';
                btn.className = 'bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500';

                // Show success message
                showNotification('Maintenance mode activated. Service will be paused.', 'success');

            } else {
                // Maintenance mode completed
                statusEl.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <div class="h-3 w-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm font-semibold text-green-700">System Online</span>
                    </div>
                `;
                btn.innerHTML = '🔧 ENTER MAINTENANCE MODE';
                btn.className = 'bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500';

                // Show success message
                showNotification('Maintenance completed. Service is now online.', 'success');
            }
        } else {
            showNotification(data.message || 'Failed to toggle maintenance mode', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Network error occurred. Please try again.', 'error');
    } finally {
        btn.disabled = false;
    }
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-md shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;

    document.body.appendChild(notification);

    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
