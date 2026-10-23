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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Panel de control</h2>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <?php if (! (Auth::user()->esPaciente())): ?>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <?php $__currentLoopData = [['citas_hoy', 'Citas de hoy'], ['pacientes', 'Pacientes activos'], ['medicos', 'Médicos activos'], ['inasist', 'Inasistencias del mes']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$k, $t]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-white shadow-sm sm:rounded-lg p-5">
                            <div class="text-3xl font-bold text-[#1F3864]"><?php echo e($resumen[$k]); ?></div>
                            <div class="text-sm text-gray-500"><?php echo e($t); ?></div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-[#1F3864]">Próximas citas</h3>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('permiso', ['citas', 'crear'])): ?>
                        <a href="<?php echo e(route('citas.create')); ?>" class="px-4 py-2 bg-[#2F5597] text-white rounded-md text-sm hover:bg-[#1F3864]">+ Nueva cita</a>
                    <?php endif; ?>
                </div>
                <?php echo $__env->make('citas._tabla', ['citas' => $proximas], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
<?php /**PATH C:\Users\usuario\Downloads\sistema-clinica\clinica\resources\views/dashboard.blade.php ENDPATH**/ ?>