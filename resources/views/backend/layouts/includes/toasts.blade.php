<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Use the global ToastMagic instance provided by the package
        if (typeof ToastMagic !== 'undefined') {
            window.toastMagic = new ToastMagic();
            
            @if(session('success'))
                window.toastMagic.success("{!! addslashes(session('success')) !!}");
            @endif
            @if(session('error'))
                window.toastMagic.error("{!! addslashes(session('error')) !!}");
            @endif
            @if(session('status'))
                window.toastMagic.info("{!! addslashes(session('status')) !!}");
            @endif
            @if(session('warning'))
                window.toastMagic.warning("{!! addslashes(session('warning')) !!}");
            @endif

            @if(session('message'))
                @php
                    $type = session('alert-type', 'info');
                    $method = in_array($type, ['error', 'success', 'warning', 'info']) ? $type : 'info';
                @endphp
                window.toastMagic.{{ $method }}("{!! addslashes(session('message')) !!}");
            @endif

            // Validation errors
            @if(isset($errors) && method_exists($errors, 'any') && $errors->any())
                @foreach($errors->all() as $error)
                    window.toastMagic.error("{!! addslashes($error) !!}");
                @endforeach
            @endif
        } else {
            console.error('ToastMagic is not defined. Please check if scripts are loaded correctly.');
        }
    });
</script>
