Hello Elementor Child (TPB)
===========================

This is a minimal, safe child theme to establish a baseline for the TPB modal/quickview work.

Contents
- style.css                — child theme header + color token
- functions.php            — enqueues parent & child styles; optionally loads assets/css/js if present
- assets/css/tpb-quickview.css — baseline CSS (VERY light)
- assets/js/tpb-modal.js       — JS stub that defines window.TPB_QV.open() to avoid runtime errors

Install
1) Upload zip in Appearance → Themes → Add New → Upload Theme.
2) Activate “Hello Elementor Child (TPB)”.

This build intentionally avoids template overrides or hooks that could break other pages.
You can now commit this theme to a repo and iterate in small, reviewable changes.