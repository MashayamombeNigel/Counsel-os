{{--
    Landing-page-only font addition. head-fonts.blade.php (shared with
    the authenticated app) already loads Inter + Material Symbols -
    this adds Plus Jakarta Sans on top, scoped to just this page so the
    logged-in app doesn't load a font family it never uses.
--}}
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
