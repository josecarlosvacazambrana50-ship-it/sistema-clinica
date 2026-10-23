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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Bitácora</h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">

                <form method="GET" class="flex flex-wrap gap-2 items-end mb-4">
                    <select name="user_id" class="border-gray-300 rounded-md shadow-sm w-full sm:w-auto">
                        <option value="">Todos los usuarios</option>
                        <?php $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($u->id); ?>" <?php if(request('user_id') == $u->id): echo 'selected'; endif; ?>><?php echo e($u->email); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <select name="accion" class="border-gray-300 rounded-md shadow-sm w-full sm:w-auto">
                        <option value="">Todas las acciones</option>
                        <?php $__currentLoopData = $acciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($a); ?>" <?php if(request('accion') === $a): echo 'selected'; endif; ?>><?php echo e($a); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <select name="tabla" class="border-gray-300 rounded-md shadow-sm w-full sm:w-auto">
                        <option value="">Todas las tablas</option>
                        <?php $__currentLoopData = $tablas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($t); ?>" <?php if(request('tabla') === $t): echo 'selected'; endif; ?>><?php echo e($t); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <div class="w-full sm:w-auto">
                        <label class="block text-xs text-gray-500">Desde</label>
                        <input type="date" name="desde" value="<?php echo e(request('desde')); ?>"
                               class="border-gray-300 rounded-md shadow-sm w-full">
                    </div>
                    <div class="w-full sm:w-auto">
                        <label class="block text-xs text-gray-500">Hasta</label>
                        <input type="date" name="hasta" value="<?php echo e(request('hasta')); ?>"
                               class="border-gray-300 rounded-md shadow-sm w-full">
                    </div>
                    <?php if (isset($component)) { $__componentOriginald411d1792bd6cc877d687758b753742c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald411d1792bd6cc877d687758b753742c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>Filtrar <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $attributes = $__attributesOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__attributesOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $component = $__componentOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__componentOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
                </form>

                <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-gray-600 border-b">
                        <tr>
                            <th class="py-2">Fecha y hora</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th class="hidden md:table-cell">Tabla</th>
                            <th class="hidden md:table-cell">Registro</th>
                            <th>Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $registros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $anterior = json_decode($r->valor_anterior ?? '', true) ?? [];
                                $nuevo = json_decode($r->valor_nuevo ?? '', true) ?? [];
                                $claves = array_unique(array_merge(array_keys($anterior), array_keys($nuevo)));
                            ?>
                            <tr class="border-b align-top">
                                <td class="py-2 whitespace-nowrap"><?php echo e($r->created_at->format('d/m/Y H:i:s')); ?></td>
                                <td><?php echo e($r->user?->name ?? 'Sin sesión'); ?></td>
                                <td>
                                    <span class="px-2 py-1 text-xs rounded
                                        <?php echo e($r->accion === 'login_fallido' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-700'); ?>">
                                        <?php echo e($r->accion); ?>

                                    </span>
                                </td>
                                <td class="hidden md:table-cell"><?php echo e($r->tabla_afectada ?? '—'); ?></td>
                                <td class="hidden md:table-cell"><?php echo e($r->registro_id ?? '—'); ?></td>
                                <td>
                                    <?php if($claves): ?>
                                        <details>
                                            <summary class="cursor-pointer text-indigo-600">Ver cambios</summary>
                                            <ul class="mt-1 text-xs text-gray-700 space-y-1">
                                                <?php $__currentLoopData = $claves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li>
                                                        <strong><?php echo e($c); ?>:</strong>
                                                        <?php if(array_key_exists($c, $anterior)): ?>
                                                            <?php echo e($anterior[$c] ?? 'vacío'); ?> →
                                                        <?php endif; ?>
                                                        <?php echo e(array_key_exists($c, $nuevo) ? ($nuevo[$c] ?? 'vacío') : '—'); ?>

                                                    </li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                        </details>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="6" class="py-6 text-center text-gray-500">No hay registros.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                </div>

                <div class="mt-4"><?php echo e($registros->links()); ?></div>
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
<?php endif; ?><?php /**PATH C:\Users\usuario\Downloads\sistema-clinica\clinica\resources\views/bitacora/index.blade.php ENDPATH**/ ?>