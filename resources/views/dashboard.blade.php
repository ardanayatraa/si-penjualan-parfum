<x-app-layout>
    @push('head')
        <meta name="dashboard" content="Admin Dashboard">
        <script src="https://cdn.tailwindcss.com"></script>
    @endpush

    <div class="">
        @livewire('dashboard.admin')
    </div>
</x-app-layout>
