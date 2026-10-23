<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestión de pacientes</h2>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('permiso', ['pacientes', 'crear'])): ?>
                <a href="<?php echo e(route('pacientes.create')); ?>" class="px-4 py-2 bg-[#2F5597] text-white rounded-md text-sm hover:bg-[#1F3864]">+ Registrar paciente</a>
            <?php endif; ?>        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <form method="GET" class="flex gap-2 mb-4">
                    <input name="q" value="<?php echo e($buscar); ?>" placeholder="Buscar por CI, nombre o apellido..." class="w-full sm:w-96 rounded-md border-gray-300 text-sm">
                    <button class="px-4 py-2 bg-[#2F5597] text-white rounded-md text-sm">Buscar</button>
                </form>
                <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-gray-600 border-b"><tr><th class="py-2">Código</th><th>CI</th><th>Paciente</th><th class="hidden md:table-cell">Teléfono</th><th>Estado</th><th></th></tr></thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $pacientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="border-b">
                            <td class="py-2 font-medium"><?php echo e($p->codigo); ?></td>
                            <td><?php echo e($p->ci); ?></td>
                            <td><?php echo e($p->nombre_completo); ?></td>
                            <td class="hidden md:table-cell"><?php echo e($p->telefono); ?></td>
                            <td><?php if (isset($component)) { $__componentOriginald54ce5ecfb77cf9651bfc81950534c53 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald54ce5ecfb77cf9651bfc81950534c53 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.estado','data' => ['valor' => $p->estado]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('estado'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['valor' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($p->estado)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald54ce5ecfb77cf9651bfc81950534c53)): ?>
<?php $attributes = $__attributesOriginald54ce5ecfb77cf9651bfc81950534c53; ?>
<?php unset($__attributesOriginald54ce5ecfb77cf9651bfc81950534c53); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald54ce5ecfb77cf9651bfc81950534c53)): ?>
<?php $component = $__componentOriginald54ce5ecfb77cf9651bfc81950534c53; ?>
<?php unset($__componentOriginald54ce5ecfb77cf9651bfc81950534c53); ?>
<?php endif; ?></td>
                            <td class="text-right whitespace-nowrap">
                                <a href="<?php echo e(route('pacientes.show', $p)); ?>" class="text-indigo-600 hover:underline">Ver / historial</a>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('permiso', ['pacientes', 'modificar'])): ?>
                                    <a href="<?php echo e(route('pacientes.edit', $p)); ?>" class="ms-3 text-indigo-600 hover:underline">Editar</a>
                                    <form method="POST" action="<?php echo e(route('pacientes.estado', $p)); ?>" class="inline" onsubmit="return confirm('¿Cambiar el estado del paciente?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                        <button class="ms-3 text-gray-600 hover:underline"><?php echo e($p->estado === 'activo' ? 'Desactivar' : 'Activar'); ?></button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="py-6 text-center text-gray-500">No se encontraron pacientes.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                </div>
                <div class="mt-4"><?php echo e($pacientes->links()); ?></div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\Users\usuario\Downloads\sistema-clinica\clinica\resources\views/pacientes/index.blade.php ENDPATH**/ ?>