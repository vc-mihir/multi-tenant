
@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            showAlert('success', 'Success', "{{ session('success') }}");
        });
    </script>
@endif

@if (session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            showAlert('error', 'Error', "{{ session('error') }}");
        });
    </script>
@endif

@if (session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            showAlert('warning', 'Warning', "{{ session('warning') }}");
        });
    </script>
@endif
