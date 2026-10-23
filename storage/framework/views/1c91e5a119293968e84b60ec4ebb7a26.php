<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e(config('app.name', 'Clínica Vida Salud')); ?></title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
        <style>[x-cloak]{display:none !important;}</style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <!-- En escritorio el contenido se corre a la derecha del menú lateral -->
            <div class="lg:pl-64">
                <?php if(isset($header)): ?>
                    <header class="bg-white dark:bg-gray-800 shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            <?php echo e($header); ?>

                        </div>
                    </header>
                <?php endif; ?>

                <main>
                    <?php if(session('success')): ?>
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6"><div class="p-3 bg-green-100 text-green-800 rounded"><?php echo e(session('success')); ?></div></div>
                    <?php endif; ?>
                    <?php if(session('error')): ?>
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6"><div class="p-3 bg-red-100 text-red-800 rounded"><?php echo e(session('error')); ?></div></div>
                    <?php endif; ?>
                    <?php echo e($slot); ?>

                </main>
            </div>
        </div>
    </body>
</html><?php /**PATH C:\Users\usuario\Downloads\sistema-clinica\clinica\resources\views/layouts/app.blade.php ENDPATH**/ ?>