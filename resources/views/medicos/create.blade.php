<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuevo médico</h2></x-slot>
    <div class="py-8"><div class="max-w-3xl mx-auto sm:px-6 lg:px-8"><div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
        <form method="POST" action="{{ route('medicos.store') }}" data-validar>
            
            @include('medicos._form')
        </form>
    </div></div></div>
</x-app-layout>
