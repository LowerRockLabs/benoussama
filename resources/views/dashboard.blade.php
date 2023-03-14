<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            here goes the flag
            <span class='fi fi-al'></span>
            <div class="p-10 bg-white shadow-xl sm:rounded-lg">
                <livewire:link-table />
            </div>
        </div>
        <div class="w-80">
        </div>
    </div>
</x-app-layout>
