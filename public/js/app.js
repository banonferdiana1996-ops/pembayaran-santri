$(function () {
    // ===== CSRF global =====
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // ===== Toast helper =====
    window.showToast = function (type, message) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
        Toast.fire({ icon: type, title: message });
    };

    // ===== SweetAlert confirm delete =====
    window.confirmDelete = function (formId, title) {
        Swal.fire({
            title: title || 'Yakin ingin menghapus?',
            text: 'Data yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
        return false;
    };

    // ===== Form loading state + error handling =====
    $(document).on('submit', 'form[data-ajax]', function (e) {
        e.preventDefault();
        const $form = $(this);
        const $btn = $form.find('[type="submit"]');
        const original = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').remove();

        $.ajax({
            url: $form.attr('action'),
            method: $form.attr('method') || 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function (res) {
                $btn.prop('disabled', false).html(original);
                if (res.success) {
                    showToast('success', res.message || 'Berhasil disimpan!');
                    if (res.redirect) {
                        setTimeout(() => window.location.href = res.redirect, 600);
                    } else if (res.reload) {
                        setTimeout(() => window.location.reload(), 600);
                    } else if (typeof window.afterAjaxSave === 'function') {
                        afterAjaxSave(res);
                    }
                } else {
                    showToast('error', res.message || 'Terjadi kesalahan!');
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html(original);
                let errors = '';
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function (field, msgs) {
                        const $el = $form.find('[name="' + field + '"]');
                        $el.addClass('is-invalid');
                        $el.after('<div class="invalid-feedback">' + msgs[0] + '</div>');
                        errors += msgs[0] + '<br>';
                    });
                } else {
                    errors = xhr.responseJSON?.message || 'Terjadi kesalahan server!';
                }
                showToast('error', errors.replace(/<br>/g, ' '));
            }
        });
    });

    // ===== DataTables default (server-side ready) =====
    window.initDataTable = function (selector, options) {
        const defaults = {
            processing: true,
            responsive: true,
            autoWidth: false,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json'
            },
            dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rt<"d-flex justify-content-between align-items-center mt-3"lip>',
            buttons: [
                { extend: 'excelHtml5', text: '<i class="fas fa-file-excel me-1"></i> Excel', className: 'btn btn-success btn-sm rounded-3' },
                { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf me-1"></i> PDF', className: 'btn btn-danger btn-sm rounded-3' },
                { extend: 'print', text: '<i class="fas fa-print me-1"></i> Print', className: 'btn btn-secondary btn-sm rounded-3' }
            ],
            pageLength: 10,
            lengthMenu: [[10, 20, 30, 40, 50, 100, -1], [10, 20, 30, 40, 50, 100, 'All']]
        };
        return $(selector).DataTable($.extend(true, {}, defaults, options));
    };

    // ===== Select2 default =====
    window.initSelect2 = function (selector) {
        $(selector).select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $(selector).closest('.modal').length ? $(selector).closest('.modal') : $('body')
        });
    };

    // ===== Clock widget (dashboard) =====
    window.startClock = function (selector) {
        function update() {
            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const time = now.toLocaleTimeString('id-ID', { hour12: false });
            const date = days[now.getDay()] + ', ' + now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
            $(selector).html(
                '<div class="fw-bold fs-3">' + time + '</div>' +
                '<div class="text-muted small">' + date + '</div>'
            );
        }
        update();
        setInterval(update, 1000);
    };

    // ===== Money formatter =====
    window.formatRupiah = function (num) {
        return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
    };

    // ===== PWA Service Worker =====
    if ('serviceWorker' in navigator && window.location.protocol === 'https:') {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        });
    }
});
