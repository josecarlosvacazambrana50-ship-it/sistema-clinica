<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mi perfil de paciente</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto space-y-6 sm:px-6 lg:px-8">
            @if (!$paciente)
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    Tu cuenta todavía no está vinculada a un perfil de paciente. Contacta a recepción para completar el registro.
                </div>
            @else
                <section class="rounded-lg bg-white p-4 shadow-sm sm:p-6">
                    <h3 class="mb-4 font-semibold text-[#1F3864]">Datos personales y contacto</h3>
                    <dl class="mb-5 grid grid-cols-1 gap-3 text-sm sm:grid-cols-3">
                        <div><dt class="text-gray-500">CI (solo lectura)</dt><dd class="font-medium">{{ $paciente->ci }}</dd></div>
                        <div><dt class="text-gray-500">Código (solo lectura)</dt><dd class="font-medium">{{ $paciente->codigo }}</dd></div>
                        <div><dt class="text-gray-500">Estado (solo lectura)</dt><dd><x-estado :valor="$paciente->estado" /></dd></div>
                    </dl>

                    <form method="POST" action="{{ route('perfil.paciente.update') }}" data-validar class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <x-input-label for="nombres" value="Nombres *" />
                                <x-text-input id="nombres" name="nombres" required maxlength="80" :value="old('nombres', $paciente->nombres)" class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('nombres')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="apellidos" value="Apellidos *" />
                                <x-text-input id="apellidos" name="apellidos" required maxlength="80" :value="old('apellidos', $paciente->apellidos)" class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('apellidos')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="fecha_nacimiento" value="Fecha de nacimiento *" />
                                <input id="fecha_nacimiento" type="date" name="fecha_nacimiento" required max="{{ today()->subDay()->toDateString() }}" value="{{ old('fecha_nacimiento', $paciente->fecha_nacimiento->toDateString()) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                                <x-input-error :messages="$errors->get('fecha_nacimiento')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="sexo" value="Sexo" />
                                <select id="sexo" name="sexo" class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                                    <option value="">No especificado</option>
                                    @foreach (['M' => 'Masculino', 'F' => 'Femenino', 'O' => 'Otro'] as $valor => $nombre)
                                        <option value="{{ $valor }}" @selected(old('sexo', $paciente->sexo) === $valor)>{{ $nombre }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('sexo')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="telefono" value="Teléfono *" />
                                <x-text-input id="telefono" name="telefono" required maxlength="20" data-tipo="telefono" :value="old('telefono', $paciente->telefono)" class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('telefono')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="correo" value="Correo de contacto / acceso" />
                                <x-text-input id="correo" type="email" name="correo" maxlength="120" :value="old('correo', $paciente->correo)" class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('correo')" class="mt-1" />
                            </div>
                            <div class="sm:col-span-2">
                                <x-input-label for="direccion" value="Dirección" />
                                <x-text-input id="direccion" name="direccion" maxlength="200" :value="old('direccion', $paciente->direccion)" class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('direccion')" class="mt-1" />
                            </div>
                        </div>

                        <div class="border-t pt-5">
                            <h4 class="mb-3 font-medium text-gray-800">Información médica</h4>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <x-input-label for="tipo_sangre" value="Tipo de sangre" />
                                    <select id="tipo_sangre" name="tipo_sangre" class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                                        <option value="">No especificado</option>
                                        @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $tipo)
                                            <option value="{{ $tipo }}" @selected(old('tipo_sangre', $paciente->tipo_sangre) === $tipo)>{{ $tipo }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('tipo_sangre')" class="mt-1" />
                                </div>
                                @foreach (['alergias' => 'Alergias', 'enfermedades_cronicas' => 'Enfermedades crónicas', 'medicamentos' => 'Medicamentos', 'antecedentes' => 'Antecedentes'] as $campo => $etiqueta)
                                    <div class="sm:col-span-2">
                                        <x-input-label :for="$campo" :value="$etiqueta" />
                                        <textarea id="{{ $campo }}" name="{{ $campo }}" rows="2" class="mt-1 block w-full rounded-md border-gray-300 text-sm">{{ old($campo, $paciente->$campo) }}</textarea>
                                        <x-input-error :messages="$errors->get($campo)" class="mt-1" />
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="border-t pt-5">
                            <h4 class="mb-3 font-medium text-gray-800">Contacto de emergencia</h4>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                @foreach (['emergencia_nombre' => 'Nombre', 'emergencia_parentesco' => 'Parentesco', 'emergencia_telefono' => 'Teléfono'] as $campo => $etiqueta)
                                    <div>
                                        <x-input-label :for="$campo" :value="$etiqueta" />
                                        <x-text-input :id="$campo" :name="$campo" maxlength="100" :data-tipo="$campo === 'emergencia_telefono' ? 'telefono' : null" :value="old($campo, $paciente->$campo)" class="mt-1 block w-full" />
                                        <x-input-error :messages="$errors->get($campo)" class="mt-1" />
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button class="rounded-md bg-[#2F5597] px-4 py-2 text-sm text-white">Guardar perfil</button>
                    </form>
                </section>

                <section class="rounded-lg bg-white p-4 shadow-sm sm:p-6">
                    <h3 class="mb-2 font-semibold text-[#1F3864]">Cambiar contraseña</h3>
                    <form method="POST" action="{{ route('perfil.paciente.password') }}" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <x-input-label for="current_password" value="Contraseña actual" />
                            <x-text-input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="password" value="Nueva contraseña" />
                            <x-text-input id="password" name="password" type="password" required minlength="8" autocomplete="new-password" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" value="Confirmar contraseña" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="mt-1 block w-full" />
                        </div>
                        <button class="rounded-md bg-[#2F3864] px-4 py-2 text-sm text-white">Actualizar contraseña</button>
                    </form>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
