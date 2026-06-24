
@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            showAlert('success', 'Success', @js(session('success')));
        });
    </script>
@endif

@if (session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            showAlert('error', 'Error', @js(session('error')));
        });
    </script>
@endif

@if (session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            showAlert('warning', 'Warning', @js(session('warning')));
        });
    </script>
@endif
