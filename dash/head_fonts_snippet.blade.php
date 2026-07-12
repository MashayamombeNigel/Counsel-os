{{--
    Add this inside the <head> of the file that defines <x-app-layout>
    (see app-layout.blade.php below) - not a standalone file, a snippet
    to merge in. Vite already compiles Tailwind/app.css, but these are
    external font links the reference design depends on and aren't
    part of the npm build pipeline.
--}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        display: inline-block;
        line-height: 1;
        vertical-align: middle;
    }
</style>
