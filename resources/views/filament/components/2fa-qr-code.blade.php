<div class="space-y-4">
    <div class="flex justify-center">
        <img src="{{ $qrCodeUrl }}" alt="QR Code" class="w-64 h-64">
    </div>
    
    <div class="text-center">
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Or enter this code manually:</p>
        <code class="px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded text-sm font-mono">{{ $secretKey }}</code>
    </div>
</div>
