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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Citas médicas</h2>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('permiso', ['citas', 'crear'])): ?>
                <a href="<?php echo e(route('citas.create')); ?>" class="px-4 py-2 bg-[#2F5597] text-white rounded-md text-sm hover:bg-[#1F3864]">+ Nueva cita</a>
            <?php endif; ?>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 mb-4">
                    <input type="date" name="fecha" value="<?php echo e($filtros['fecha'] ?? ''); ?>" class="rounded-md border-gray-300 text-sm">
                    <?php if (! (Auth::user()->esMedico() || Auth::user()->esPaciente())): ?>
                        <select name="medico_id" class="rounded-md border-gray-300 text-sm">
                            <option value="">Todos los médicos</option>
                            <?php $__currentLoopData = $medicos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($m->id); ?>" <?php if(($filtros['medico_id'] ?? '') == $m->id): echo 'selected'; endif; ?>><?php echo e($m->nombre_completo); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    <?php endif; ?>
                    <select name="estado" class="rounded-md border-gray-300 text-sm">
                        <option value="">Todos los estados</option>
                        <?php $__currentLoopData = \App\Models\Cita::ESTADOS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($k); ?>" <?php if(($filtros['estado'] ?? '') === $k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <div class="flex gap-2">
                        <button class="px-4 py-2 bg-[#2F5597] text-white rounded-md text-sm">Filtrar</button>
                        <a href="<?php echo e(route('citas.index')); ?>" class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700">Limpiar</a>
                    </div>
                </form>
                <?php echo $__env->make('citas._tabla', ['citas' => $citas], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <div class="mt-4"><?php echo e($citas->links()); ?></div>
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
<?php /**PATH C:\Users\usuario\Downloads\sistema-clinica\clinica\resources\views/citas/index.blade.php ENDPATH**/ ?>