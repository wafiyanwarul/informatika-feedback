<div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-red-800 dark:text-red-400">System Maintenance Control</h3>
                    <p class="text-sm text-red-700 dark:text-red-300">Admin-only area - Use with extreme caution</p>
                </div>
            </div>

            <!-- Admin Badge -->
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                    👤 Admin Access
                </span>
            </div>
        </div>

        <!-- Status Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Laravel Status -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-gray-100">Laravel Application</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Local maintenance mode</p>
                    </div>
                    <div class="flex items-center space-x-2" id="laravel-status">
                        @if($isInMaintenance)
                            <div class="h-3 w-3 bg-red-500 rounded-full animate-pulse"></div>
                            <span class="text-sm font-semibold text-red-700 dark:text-red-400">DOWN</span>
                        @else
                            <div class="h-3 w-3 bg-green-500 rounded-full"></div>
                            <span class="text-sm font-semibold text-green-700 dark:text-green-400">ONLINE</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Koyeb Status -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-gray-100">Koyeb Service</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Cloud hosting status</p>
                    </div>
                    <div class="flex items-center space-x-2" id="koyeb-status">
                        @switch($koyebStatus)
                            @case('running')
                                <div class="h-3 w-3 bg-green-500 rounded-full"></div>
                                <span class="text-sm font-semibold text-green-700 dark:text-green-400">RUNNING</span>
                                @break
                            @case('paused')
                                <div class="h-3 w-3 bg-yellow-500 rounded-full"></div>
                                <span class="text-sm font-semibold text-yellow-700 dark:text-yellow-400">PAUSED</span>
                                @break
                            @case('not_configured')
                                <div class="h-3 w-3 bg-gray-500 rounded-full"></div>
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-400">NOT CONFIGURED</span>
                                @break
                            @default
                                <div class="h-3 w-3 bg-gray-500 rounded-full"></div>
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-400">{{ strtoupper($koyebStatus) }}</span>
                        @endswitch
                    </div>
                </div>
            </div>
        </div>

        <!-- Control Buttons -->
        <div class="flex items-center justify-center space-x-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button
                type="button"
                onclick="window.toggleMaintenanceMode()"
                id="maintenance-btn"
                class="@if($isInMaintenance) bg-green-600 hover:bg-green-700 focus:ring-green-500 @else bg-red-600 hover:bg-red-700 focus:ring-red-500 @endif text-white px-6 py-3 rounded-lg text-sm font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 shadow-lg hover:shadow-xl"
            >
                @if($isInMaintenance)
                    🔄 Complete Maintenance & Resume Service
                @else
                    🔧 Enter Maintenance Mode & Pause Service
                @endif
            </button>

            <button
                type="button"
                onclick="window.refreshStatus()"
                id="refresh-btn"
                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
            >
                🔄 Refresh Status
            </button>
        </div>

        <!-- Warning Notice -->
        <div class="bg-yellow-50 dark:bg-yellow-900/50 border border-yellow-200 dark:border-yellow-700 rounded-lg p-3">
            <div class="flex items-start space-x-2">
                <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <div>
                    <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">⚠️ Important Notice</p>
                    <p class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">
                        Maintenance mode will make the site inaccessible to users. Only use during scheduled maintenance windows.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
window.toggleMaintenanceMode = async function() {
    const btn = document.getElementById('maintenance-btn');
    const originalText = btn.innerHTML;

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
            // Update Laravel status
            const laravelStatus = document.getElementById('laravel-status');

            if (data.maintenance_mode) {
                // Maintenance mode activated
                laravelStatus.innerHTML = `
                    <div class="h-3 w-3 bg-red-500 rounded-full animate-pulse"></div>
                    <span class="text-sm font-semibold text-red-700 dark:text-red-400">DOWN</span>
                `;
                btn.innerHTML = '🔄 Complete Maintenance & Resume Service';
                btn.className = 'bg-green-600 hover:bg-green-700 focus:ring-green-500 text-white px-6 py-3 rounded-lg text-sm font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 shadow-lg hover:shadow-xl';

                showNotification('✅ Maintenance mode activated successfully', 'success');
            } else {
                // Maintenance mode completed
                laravelStatus.innerHTML = `
                    <div class="h-3 w-3 bg-green-500 rounded-full"></div>
                    <span class="text-sm font-semibold text-green-700 dark:text-green-400">ONLINE</span>
                `;
                btn.innerHTML = '🔧 Enter Maintenance Mode & Pause Service';
                btn.className = 'bg-red-600 hover:bg-red-700 focus:ring-red-500 text-white px-6 py-3 rounded-lg text-sm font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 shadow-lg hover:shadow-xl';

                showNotification('✅ Maintenance completed successfully', 'success');
            }

            // Refresh status after 2 seconds
            setTimeout(refreshStatus, 2000);
        } else {
            showNotification('❌ ' + (data.message || 'Failed to toggle maintenance mode'), 'error');
            btn.innerHTML = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('❌ Network error occurred. Please try again.', 'error');
        btn.innerHTML = originalText;
    } finally {
        btn.disabled = false;
    }
}

window.refreshStatus = async function() {
    const refreshBtn = document.getElementById('refresh-btn');
    refreshBtn.disabled = true;
    refreshBtn.innerHTML = '⏳ Refreshing...';

    try {
        // Refresh the entire widget
        location.reload();
    } catch (error) {
        console.error('Refresh error:', error);
        showNotification('❌ Failed to refresh status', 'error');
    } finally {
        refreshBtn.disabled = false;
        refreshBtn.innerHTML = '🔄 Refresh Status';
    }
}

window.showNotification = function(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}
</script>
