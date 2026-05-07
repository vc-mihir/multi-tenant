@once
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        if (typeof window.showAlert !== 'function') {
            window.showAlert = (icon, title, text) => {
                Swal.fire({
                    icon: icon,
                    title: title,
                    text: text,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    background: '#ffffff',
                    color: '#064e3b',
                    iconColor: '#059669',
                    customClass: {
                        popup: 'rounded-3xl border border-emerald-50 shadow-2xl shadow-emerald-900/10'
                    }
                });
            };
        }
    </script>
@endonce

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
