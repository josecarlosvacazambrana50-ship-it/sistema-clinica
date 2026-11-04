<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Programar cita médica</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('citas.store') }}" id="form-cita" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @csrf

                {{-- BLOQUE DE ERRORES VISIBLES: Muestra cualquier fallo de validación del servidor en rojo --}}
                @if ($errors->any())
                    <div class="col-span-full bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded shadow-sm">
                        <div class="flex">
                            <div class="text-sm text-red-700">
                                <p class="font-bold">Por favor corrige los siguientes errores:</p>
                                <ul class="list-disc pl-5 mt-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6 space-y-4">
                    <h3 class="font-semibold text-[#1F3864]">Datos de la cita</h3>

                    @php
                        $user = Auth::user();
                        $esPaciente = ($user->rol_id == 3);
                    @endphp

                    {{-- Si NO es paciente, mostramos selector; si es paciente, inyectamos su ID oculto --}}
                    @if(!$esPaciente)
                        <div>
                            <x-input-label for="paciente_id" value="Paciente *" />
                            <select id="paciente_id" name="paciente_id" required class="block mt-1 w-full rounded-md border-gray-300 text-sm">
                                <option value="">Seleccione...</option>
                                @if(isset($pacientes))
                                    @foreach ($pacientes as $p)
                                        <option value="{{ $p->id }}" @selected(old('paciente_id', request('paciente_id')) == $p->id)>{{ $p->codigo }} · {{ $p->nombre_completo ?? $p->nombres }} (CI {{ $p->ci }})</option>
                                    @endforeach
                                @endif
                            </select>
                            <x-input-error :messages="$errors->get('paciente_id')" class="mt-1" />
                        </div>
                    @else
                        <input type="hidden" name="paciente_id" value="{{ $user->paciente_id }}">
                    @endif

                    <div>
                        <x-input-label for="especialidad_id" value="Especialidad *" />
                        <select id="especialidad_id" name="especialidad_id" required class="block mt-1 w-full rounded-md border-gray-300 text-sm">
                            <option value="">Seleccione...</option>
                            @if(isset($especialidades))
                                @foreach ($especialidades as $e)
                                    <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                                @endforeach
                            @endif
                        </select>
                        <x-input-error :messages="$errors->get('especialidad_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="medico_id" value="Médico *" />
                        <select id="medico_id" name="medico_id" required class="block mt-1 w-full rounded-md border-gray-300 text-sm">
                            <option value="">Seleccione una especialidad primero</option>
                        </select>
                        <x-input-error :messages="$errors->get('medico_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="fecha" value="Fecha *" />
                        <input id="fecha" type="date" name="fecha" required min="{{ date('Y-m-d') }}" class="block mt-1 w-full rounded-md border-gray-300 text-sm">
                        <x-input-error :messages="$errors->get('fecha')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="motivo" value="Motivo de la consulta (opcional)" />
                        <x-text-input id="motivo" name="motivo" type="text" maxlength="200" class="block mt-1 w-full" :value="old('motivo')" />
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                    <h3 class="font-semibold text-[#1F3864] mb-3">Horarios disponibles</h3>
                    <input type="hidden" name="hora_inicio" id="hora_inicio" value="{{ old('hora_inicio') }}">
                    <input type="hidden" name="hora_fin" id="hora_fin" value="{{ old('hora_fin') }}">
                    <input type="hidden" name="estado" value="programada">

                    <p id="msg-seleccione" class="text-sm text-gray-500">Seleccione médico y fecha para ver las horas libres.</p>
                    <p id="msg-cargando" class="hidden text-sm text-gray-500">Consultando disponibilidad...</p>
                    <p id="msg-vacio" class="hidden text-sm text-amber-700">El médico no atiende en esa fecha.</p>

                    <div id="bloques" class="flex flex-wrap gap-2"></div>
                    <p id="msg-leyenda" class="hidden mt-3 text-xs text-gray-500">Los horarios tachados ya están ocupados o fuera de tiempo.</p>
                    <x-input-error :messages="$errors->get('hora_inicio')" class="mt-2" />

                    <div class="mt-6 flex gap-2">
                        <button type="submit" id="btn-guardar" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700 transition">Guardar cita</button>
                        <a href="{{ route('citas.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const especialidadSelect = document.getElementById('especialidad_id');
            const medicoSelect = document.getElementById('medico_id');
            const fechaInput = document.getElementById('fecha');
            const bloquesDiv = document.getElementById('bloques');
            const horaInicioInput = document.getElementById('hora_inicio');
            const horaFinInput = document.getElementById('hora_fin');
            const msgSeleccione = document.getElementById('msg-seleccione');
            const msgCargando = document.getElementById('msg-cargando');
            const msgVacio = document.getElementById('msg-vacio');
            const formCita = document.getElementById('form-cita');

            function cargarDisponibilidad() {
                const medicoId = medicoSelect.value;
                const fecha = fechaInput.value;

                bloquesDiv.innerHTML = '';
                horaInicioInput.value = '';
                if (horaFinInput) horaFinInput.value = '';

                if (!medicoId || !fecha) {
                    msgSeleccione.classList.remove('hidden');
                    return;
                }

                msgSeleccione.classList.add('hidden');
                msgCargando.classList.remove('hidden');
                msgVacio.classList.add('hidden');

                fetch(`{{ url('citas/disponibilidad') }}/${medicoId}?fecha=${fecha}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Error en el servidor');
                        return response.json();
                    })
                    .then(data => {
                        msgCargando.classList.add('hidden');
                        if (!data || data.length === 0) {
                            msgVacio.classList.remove('hidden');
                            return;
                        }

                        data.forEach(bloque => {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.textContent = bloque.inicio; 
                            
                            btn.className = `px-3 py-1.5 text-xs rounded-md border font-medium transition ${bloque.libre ? 'bg-white text-gray-700 border-gray-300 hover:bg-indigo-50' : 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed line-through'}`;
                            
                            if (bloque.libre) {
                                btn.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    document.querySelectorAll('#bloques button').forEach(b => {
                                        b.classList.remove('bg-green-600', 'text-white', 'border-green-600');
                                        b.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
                                    });
                                    btn.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
                                    btn.classList.add('bg-green-600', 'text-white', 'border-green-600');
                                    
                                    horaInicioInput.value = bloque.inicio;
                                    if (horaFinInput) horaFinInput.value = bloque.fin;
                                });
                            }
                            bloquesDiv.appendChild(btn);
                        });
                    })
                    .catch(error => {
                        msgCargando.classList.add('hidden');
                        console.error('Error al cargar disponibilidad:', error);
                    });
            }

            especialidadSelect.addEventListener('change', function() {
                const espId = this.value;
                medicoSelect.innerHTML = '<option value="">Cargando médicos...</option>';
                bloquesDiv.innerHTML = '';
                horaInicioInput.value = '';
                if (horaFinInput) horaFinInput.value = '';
                
                if (!espId) {
                    medicoSelect.innerHTML = '<option value="">Seleccione una especialidad primero</option>';
                    msgSeleccione.classList.remove('hidden');
                    return;
                }

                fetch(`{{ url('citas/medicos') }}/${espId}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Error en el servidor');
                        return response.json();
                    })
                    .then(data => {
                        medicoSelect.innerHTML = '<option value="">Seleccione un médico</option>';
                        data.forEach(medico => {
                            medicoSelect.innerHTML += `<option value="${medico.id}">${medico.nombre}</option>`;
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        medicoSelect.innerHTML = '<option value="">Error al cargar médicos</option>';
                    });
            });

            medicoSelect.addEventListener('change', cargarDisponibilidad);
            fechaInput.addEventListener('change', cargarDisponibilidad);

            formCita.addEventListener('submit', function(e) {
                if (!horaInicioInput.value) {
                    e.preventDefault();
                    alert('Por favor, haz clic sobre un horario disponible antes de guardar.');
                    return false;
                }
            });
        });
    </script>
</x-app-layout>