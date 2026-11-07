<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Programar cita médica</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('citas.store') }}" id="form-cita" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @csrf

                {{-- BLOQUE DE ERRORES VISIBLES --}}
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
                    <h3 class="font-semibold text-[#1F3864] text-lg border-b pb-2">Datos de la cita</h3>

                    {{-- POLÍTICAS Y PRERREQUISITOS DE LA CLÍNICA (Regla de Negocio) --}}
                    <div class="p-3 bg-amber-50 border-l-4 border-amber-400 rounded-md">
                        <div class="text-xs text-amber-800">
                            <p class="font-bold">⚠️ Políticas y Prerrequisitos:</p>
                            <ul class="list-disc pl-4 mt-1 space-y-0.5">
                                <li>Especialidades sugieren evaluación previa en Medicina General.</li>
                                <li>Cancelaciones permitidas hasta 24 horas antes de la consulta.</li>
                            </ul>
                        </div>
                    </div>

                    @php
                        $user = Auth::user();
                        $esPaciente = ($user->rol_id == 3);
                    @endphp

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

                <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6 space-y-4 flex flex-col justify-between">
                    <div>
                        <h3 class="font-semibold text-[#1F3864] text-lg border-b pb-2 mb-3">Horarios disponibles</h3>
                        <input type="hidden" name="hora_inicio" id="hora_inicio" value="{{ old('hora_inicio') }}">
                        <input type="hidden" name="hora_fin" id="hora_fin" value="{{ old('hora_fin') }}">
                        <input type="hidden" name="estado" value="programada">

                        <p id="msg-seleccione" class="text-sm text-gray-500">Seleccione médico y fecha para ver las horas libres.</p>
                        <p id="msg-cargando" class="hidden text-sm text-gray-500">Consultando disponibilidad...</p>
                        <p id="msg-vacio" class="hidden text-sm text-amber-700">El médico no atiende en esa fecha.</p>

                        <div id="bloques" class="flex flex-wrap gap-2"></div>
                        <p id="msg-leyenda" class="hidden mt-3 text-xs text-gray-500">Los horarios tachados ya están ocupados o fuera de tiempo.</p>
                        <x-input-error :messages="$errors->get('hora_inicio')" class="mt-2" />

                        {{-- PASARELA DE PAGO --}}
                        <div class="mt-6 pt-4 border-t border-gray-100">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-semibold text-sm text-[#1F3864]">Método de Pago</h3>
                                <span class="px-2 py-0.5 bg-blue-100 text-[#1F3864] text-[10px] font-bold rounded tracking-wider">SIMULACIÓN</span>
                            </div>
                            <p class="text-xs text-gray-500 mb-3">Seleccione cómo desea cancelar su consulta:</p>
                            
                            <div class="grid grid-cols-2 gap-3 mb-2">
                                <!-- Opción QR -->
                                <label class="p-3 border-2 rounded-xl cursor-pointer transition-all flex items-center justify-center gap-2 hover:border-[#1F3864] hover:bg-slate-50 border-[#1F3864] bg-[#1F3864]/5 shadow-xs" id="label-qr">
                                    <input type="radio" name="metodo_pago" value="QR" class="text-[#1F3864] focus:ring-[#1F3864] h-4 w-4" checked onchange="actualizarSeleccionPago()">
                                    <div class="text-left">
                                        <span class="block font-bold text-xs text-[#1F3864]">Pago QR</span>
                                        <span class="block text-[10px] text-gray-500">Banca Móvil</span>
                                    </div>
                                </label>

                                <!-- Opción Tarjeta -->
                                <label class="p-3 border-2 rounded-xl cursor-pointer transition-all flex items-center justify-center gap-2 hover:border-[#1F3864] hover:bg-slate-50 border-gray-200 bg-white" id="label-tarjeta">
                                    <input type="radio" name="metodo_pago" value="Tarjeta" class="text-[#1F3864] focus:ring-[#1F3864] h-4 w-4" onchange="actualizarSeleccionPago()">
                                    <div class="text-left">
                                        <span class="block font-bold text-xs text-gray-700" id="text-tarjeta-title">Tarjeta</span>
                                        <span class="block text-[10px] text-gray-500">Débito / Crédito</span>
                                    </div>
                                </label>
                            </div>
                            <input type="hidden" name="estado_pago" value="Pagado">
                        </div>
                    </div>

                    <div class="pt-4 flex gap-2">
                        <button type="submit" id="btn-guardar" class="w-full py-2.5 bg-green-600 text-white font-semibold rounded-md text-sm hover:bg-green-700 transition shadow-sm">Confirmar y Guardar Cita</button>
                        <a href="{{ route('citas.index') }}" class="px-4 py-2.5 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50 text-center">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function actualizarSeleccionPago() {
            const qrChecked = document.querySelector('input[name="metodo_pago"][value="QR"]').checked;
            const labelQr = document.getElementById('label-qr');
            const labelTarjeta = document.getElementById('label-tarjeta');
            const titleTarjeta = document.getElementById('text-tarjeta-title');

            if (qrChecked) {
                labelQr.className = "p-3 border-2 rounded-xl cursor-pointer transition-all flex items-center justify-center gap-2 border-[#1F3864] bg-[#1F3864]/5 shadow-xs";
                labelTarjeta.className = "p-3 border-2 rounded-xl cursor-pointer transition-all flex items-center justify-center gap-2 border-gray-200 bg-white";
                titleTarjeta.classList.remove('text-[#1F3864]');
                titleTarjeta.classList.add('text-gray-700');
            } else {
                labelTarjeta.className = "p-3 border-2 rounded-xl cursor-pointer transition-all flex items-center justify-center gap-2 border-[#1F3864] bg-[#1F3864]/5 shadow-xs";
                labelQr.className = "p-3 border-2 rounded-xl cursor-pointer transition-all flex items-center justify-center gap-2 border-gray-200 bg-white";
                titleTarjeta.classList.remove('text-gray-700');
                titleTarjeta.classList.add('text-[#1F3864]');
            }
        }

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