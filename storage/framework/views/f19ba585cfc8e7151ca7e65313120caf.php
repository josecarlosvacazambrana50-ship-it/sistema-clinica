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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Roles y permisos</h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <?php if(session('success')): ?>
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded"><?php echo e(session('error')); ?></div>
            <?php endif; ?>

            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <p class="text-sm text-gray-600 mb-4">
                    Cada usuario recibe los permisos de su rol. Los cambios se aplican en la siguiente
                    pantalla que abra la persona, sin necesidad de volver a iniciar sesión.
                </p>

                <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-gray-600 border-b">
                        <tr>
                            <th class="py-2">Rol</th>
                            <th class="hidden md:table-cell">Descripción</th>
                            <th class="text-right hidden sm:table-cell">Usuarios</th>
                            <th class="text-right hidden sm:table-cell">Permisos</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="border-b">
                                <td class="py-2 font-medium"><?php echo e($rol->nombre); ?></td>
                                <td class="hidden md:table-cell"><?php echo e($rol->descripcion); ?></td>
                                <td class="text-right hidden sm:table-cell"><?php echo e($rol->usuarios_count); ?></td>
                                <td class="text-right hidden sm:table-cell"><?php echo e($rol->permisos_count); ?></td>
                                <td class="text-right whitespace-nowrap">
                                    <?php if($rol->nombre === 'Administrador'): ?>
                                        <span class="text-xs text-gray-500">Acceso total (bloqueado)</span>
                                    <?php elseif($rol->id === Auth::user()->rol_id): ?>
                                        <span class="text-xs text-gray-500">Tu rol (bloqueado)</span>
                                    <?php else: ?>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('permiso', ['roles', 'modificar'])): ?>
                                            <a href="<?php echo e(route('roles.permisos', $rol)); ?>"
                                               class="text-indigo-600 hover:underline">Permisos</a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                </div>
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
<?php endif; ?><?php /**PATH C:\Users\usuario\Downloads\sistema-clinica\clinica\resources\views/roles/index.blade.php ENDPATH**/ ?>