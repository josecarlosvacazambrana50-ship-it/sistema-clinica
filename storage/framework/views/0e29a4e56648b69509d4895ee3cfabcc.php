<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['valor']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['valor']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $clase = match ($valor) {
        'activo', 'confirmada', 'atendida' => 'bg-green-100 text-green-800',
        'programada' => 'bg-blue-100 text-blue-800',
        'cancelada', 'inasistencia' => 'bg-red-100 text-red-800',
        default => 'bg-gray-200 text-gray-700',
    };
?>
<span class="px-2 py-0.5 rounded-full text-xs font-semibold <?php echo e($clase); ?>"><?php echo e(ucfirst($valor)); ?></span>
<?php /**PATH C:\Users\usuario\Downloads\sistema-clinica\clinica\resources\views/components/estado.blade.php ENDPATH**/ ?>