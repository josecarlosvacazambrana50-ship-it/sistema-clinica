<div class="overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead class="text-gray-600 border-b">
            <tr>
                <th class="py-2">Código</th><th>Fecha</th><th>Hora</th><th>Paciente</th>
                <th class="hidden md:table-cell">Médico</th><th class="hidden md:table-cell">Especialidad</th><th>Estado</th><th></th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $citas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b">
                    <td class="py-2 font-medium"><?php echo e($c->codigo); ?></td>
                    <td><?php echo e($c->fecha->format('d/m/Y')); ?></td>
                    <td><?php echo e($c->hora_inicio_corta); ?> – <?php echo e($c->hora_fin_corta); ?></td>
                    <td><?php echo e($c->paciente->nombre_completo); ?></td>
                    <td class="hidden md:table-cell">Dr(a). <?php echo e($c->medico->nombre_completo); ?></td>
                    <td class="hidden md:table-cell"><?php echo e($c->especialidad->nombre); ?></td>
                    <td><?php if (isset($component)) { $__componentOriginald54ce5ecfb77cf9651bfc81950534c53 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald54ce5ecfb77cf9651bfc81950534c53 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.estado','data' => ['valor' => $c->estado]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('estado'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['valor' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($c->estado)]); ?>
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
                    <td class="text-right"><a href="<?php echo e(route('citas.show', $c)); ?>" class="text-indigo-600 hover:underline">Ver</a></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="8" class="py-6 text-center text-gray-500">No hay citas para mostrar.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php /**PATH C:\Users\usuario\Downloads\sistema-clinica\clinica\resources\views/citas/_tabla.blade.php ENDPATH**/ ?>