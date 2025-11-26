<div 
    x-data="{ message: '' }" 
    x-on:notify.window="message = $event.detail.message; setTimeout(() => message = '', 5000)"
    class="sr-only" 
    role="status" 
    aria-live="polite" 
    aria-atomic="true"
>
    <span x-text="message"></span>
</div>
