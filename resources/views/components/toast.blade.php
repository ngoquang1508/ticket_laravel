@if (session('success') || session('error') || (isset($errors) && $errors->any()))
    @php
        $toastData = [
            'type' => session('success') ? 'success' : 'error',
            'title' => session('success') ? 'Thành công' : 'Có lỗi xảy ra',
            'message' => session('success') ?: (session('error') ?: (isset($errors) ? $errors->first() : '')),
        ];
    @endphp

    <script>
        window.appToast = @json($toastData);
    </script>
@endif
