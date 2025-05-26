<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Under Maintenance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .maintenance-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="maintenance-bg min-h-screen flex items-center justify-center">
    <div class="max-w-2xl mx-auto text-center px-6">
        <!-- Maintenance Icon -->
        <div class="mb-8 float-animation">
            <div class="inline-flex items-center justify-center w-32 h-32 bg-white rounded-full shadow-lg">
                <svg class="w-16 h-16 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Main Content -->
        <div class="text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                System Under Maintenance
            </h1>
            <h2 class="text-xl md:text-2xl font-light mb-8 opacity-90">
                Survey Management System
            </h2>

            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-6 mb-8">
                <p class="text-lg mb-4">
                    We're currently performing scheduled maintenance to improve your experience.
                </p>
                <p class="text-base opacity-80">
                    Our team is working hard to get everything back online as soon as possible.
                </p>
            </div>

            <!-- Status Information -->
            <div class="grid md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white bg-opacity-10 rounded-lg p-4">
                    <div class="text-2xl mb-2">🔧</div>
                    <h3 class="font-semibold">System Updates</h3>
                    <p class="text-sm opacity-80">Improving performance</p>
                </div>
                <div class="bg-white bg-opacity-10 rounded-lg p-4">
                    <div class="text-2xl mb-2">🛡️</div>
                    <h3 class="font-semibold">Security Enhancements</h3>
                    <p class="text-sm opacity-80">Strengthening protection</p>
                </div>
                <div class="bg-white bg-opacity-10 rounded-lg p-4">
                    <div class="text-2xl mb-2">⚡</div>
                    <h3 class="font-semibold">Performance Optimization</h3>
                    <p class="text-sm opacity-80">Faster load times</p>
                </div>
            </div>

            <!-- Estimated Time -->
            <div class="bg-yellow-500 bg-opacity-20 border border-yellow-400 rounded-lg p-4 mb-8">
                <p class="font-semibold">Estimated Completion Time</p>
                <p class="text-sm opacity-80">Usually takes 15-30 minutes</p>
            </div>

            <!-- Contact Information -->
            <div class="text-sm opacity-70">
                <p>Need immediate assistance? Contact our support team:</p>
                <p class="font-medium">Email: support@yourcompany.com</p>
                <p class="font-medium">Phone: +62 xxx-xxxx-xxxx</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-12 text-white text-sm opacity-60">
            <p>© {{ date('Y') }} Survey Management System. All rights reserved.</p>
        </div>
    </div>

    <!-- Auto-refresh script -->
    <script>
        // Auto refresh every 30 seconds to check if maintenance is complete
        setTimeout(function() {
            window.location.reload();
        }, 30000);

        // Show countdown
        let seconds = 30;
        const countdown = setInterval(function() {
            seconds--;
            if (seconds <= 0) {
                clearInterval(countdown);
                seconds = 30;
            }
        }, 1000);
    </script>
</body>
</html>
