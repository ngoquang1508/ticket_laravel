@if (session('success') || session('error') || session('warning') || session('info') || (isset($errors) && $errors->any()))
    @php
        $type = session('success') ? 'success' : 'error';
        $title = session('toast_title') ?? (session('success') ? 'Thành công' : (session('warning') ? 'Cảnh báo' : (session('info') ? 'Thông báo' : 'Có lỗi xảy ra')));
        $message = session('success') ?: (session('error') ?: (session('warning') ?: (session('info') ?: (isset($errors) ? $errors->first() : ''))));

        $toastData = [
            'type' => $type,
            'title' => $title,
            'message' => $message,
        ];
    @endphp

    <script>
        window.appToast = @json($toastData);
    </script>
@endif
