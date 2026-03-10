</div>
<!-- End Main Content -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('pageLoadingOverlay');

    function showLoading() {
        if (overlay) overlay.classList.add('show');
    }

    function shouldHandleNavigation(el) {
        if (!el) return false;
        if (el.hasAttribute('data-no-transition')) return false;
        if (el.closest('[data-no-transition]')) return false;
        return true;
    }

    function navigateWithLoading(navigateFn) {
        showLoading();
        window.setTimeout(function () {
            navigateFn();
        }, 500);
    }

    document.addEventListener('click', function (e) {
        const a = e.target.closest('a');
        if (!a) return;
        if (!shouldHandleNavigation(a)) return;
        const href = a.getAttribute('href');
        if (!href) return;
        if (href.startsWith('#')) return;
        if (a.target && a.target.toLowerCase() === '_blank') return;
        if (a.hasAttribute('download')) return;
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

        e.preventDefault();
        navigateWithLoading(function () {
            window.location.href = href;
        });
    });

    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!form || form.tagName !== 'FORM') return;
        if (!shouldHandleNavigation(form)) return;
        if ((form.getAttribute('method') || '').toLowerCase() === 'dialog') return;

        e.preventDefault();
        navigateWithLoading(function () {
            form.submit();
        });
    }, true);
});
</script>
</body>
</html>