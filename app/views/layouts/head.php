<?php
/** @var string $title */
$title = $title ?? 'iBITS Portal | We Code as One';
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="<?= e(Csrf::token()) ?>">
<title><?= e($title) ?></title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">

<script src="https://cdn.tailwindcss.com"></script>
<script src="<?= asset('assets/js/tailwind-config.js') ?>"></script>

<script>
    // Expose the CSRF token to fetch() calls
    window.CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';
</script>