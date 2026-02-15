<?php

return [
    // Modal Headers
    'create_title' => 'Umpan Balik',
    'create_subtitle' => 'Bantu kami meningkatkan sistem ini',
    'edit_title' => 'Ubah Umpan Balik',
    'edit_subtitle' => 'Perbarui umpan balik Anda',
    'respond_title' => 'Respon Umpan Balik',
    'respond_subtitle' => 'Berikan tanggapan untuk feedback pengguna',

    // Index Page
    'page_subtitle_admin' => 'Kelola feedback dari semua pengguna',
    'page_subtitle_user' => 'Lihat dan kelola feedback Anda',
    'send_new_feedback' => 'Kirim Feedback Baru',
    'send_first_feedback' => 'Kirim Feedback Pertama',
    'send_feedback' => 'Kirim Feedback',

    // Stats
    'stat_in_progress' => 'In Progress',
    'stat_resolved' => 'Resolved',
    'stat_bugs' => 'Bugs',
    'stat_features' => 'Features',
    'stat_suggestions' => 'Saran',

    // Tabs
    'tab_my_feedbacks' => 'Feedback Saya',
    'tab_all_feedbacks' => 'Semua Feedback',

    // Table Headers
    'header_type' => 'Jenis',
    'header_title' => 'Judul',
    'header_sender' => 'Pengirim',
    'header_priority' => 'Prioritas',
    'header_status' => 'Status',
    'header_created' => 'Dibuat',
    'header_actions' => 'Aksi',

    // Filter
    'search_all' => 'Cari judul, deskripsi, atau nama pengirim...',
    'search_own' => 'Cari judul atau deskripsi...',
    'filter_type' => 'Jenis',
    'filter_all_types' => 'Semua Jenis',
    'filter_priority' => 'Prioritas',

    // Status Options
    'status_open' => 'Open',
    'status_in_progress' => 'In Progress',
    'status_resolved' => 'Resolved',
    'status_closed' => 'Closed',

    // Priority Options
    'priority_low' => 'Low',
    'priority_medium' => 'Medium',
    'priority_high' => 'High',
    'priority_critical' => 'Critical',

    // Form Labels
    'feedback_type' => 'Jenis Feedback',
    'priority' => 'Prioritas',
    'title_placeholder' => 'Ringkasan singkat feedback Anda...',
    'description_placeholder' => 'Jelaskan secara detail... Untuk bug report, sertakan langkah-langkah untuk mereproduksi masalah.',
    'page_url' => 'URL Halaman',
    'page_url_hint' => 'URL halaman terkait feedback ini (otomatis terisi)',
    'max_characters' => 'Maksimal :count karakter',
    'response' => 'Respon',
    'response_placeholder' => 'Tulis tanggapan Anda untuk feedback ini...',
    'response_notification_hint' => 'Pengguna akan menerima notifikasi tentang respon ini',

    // Type Options
    'type_bug' => 'Bug Report',
    'type_feature' => 'Feature Request',
    'type_feedback' => 'Kritik/Saran',
    'select_type' => 'Pilih jenis...',

    // Priority Options (Select)
    'select_priority' => 'Pilih prioritas...',

    // Attachment
    'screenshot_attachment' => 'Screenshot / Lampiran',
    'attachment_optional' => 'Opsional - Upload screenshot atau dokumen pendukung',
    'paste_hint' => 'Tekan Ctrl+V untuk paste gambar',
    'file_label' => 'File',
    'file_tip' => 'JPG, PNG, atau PDF (Maks 5MB)',
    'processing_clipboard' => 'Memproses gambar dari clipboard...',
    'file_too_large' => 'File terlalu besar',
    'max_image_size' => 'Ukuran gambar maksimal 5MB',
    'upload_failed' => 'Upload gagal',
    'upload_error' => 'Terjadi kesalahan saat mengupload gambar',
    'replace_attachment' => 'Ganti Lampiran',
    'attachment_label' => 'Lampiran',
    'click_to_open' => 'Klik untuk membuka',

    // Help Tips
    'tips_title' => 'Tips untuk feedback yang baik:',
    'tip_bug' => 'Sertakan langkah untuk mereproduksi, perilaku yang diharapkan vs yang terjadi',
    'tip_feature' => 'Jelaskan use case dan manfaat fitur yang diinginkan',
    'tip_feedback' => 'Berikan konteks dan saran konkret untuk perbaikan',

    // Respond Form
    'from' => 'Dari',
    'source_page' => 'Halaman Asal',
    'respond_info' => 'Respon Anda akan langsung terkirim ke pengguna sebagai notifikasi dan ditampilkan di detail feedback.',
    'send_response' => 'Kirim Respon',
    'select_status' => 'Pilih status...',

    // Show
    'admin_response' => 'Respon Admin',
    'respond' => 'Respon',

    // Empty State
    'no_feedback_yet' => 'Belum ada feedback',
    'no_feedback_admin' => 'Belum ada feedback yang dikirim oleh pengguna',
    'no_feedback_user' => 'Anda belum pernah mengirim feedback',

    // Action Tooltips
    'view_detail' => 'Lihat Detail',

    // Delete
    'delete_title' => 'Hapus Feedback?',
    'delete_confirm_message' => 'Feedback ":title" akan dihapus permanen. Aksi ini tidak dapat dibatalkan.',
    'delete_confirm' => 'Ya, Hapus',
    'deleted_success' => 'Feedback berhasil dihapus',
    'no_delete_permission' => 'Anda tidak memiliki akses untuk menghapus feedback ini',
    'no_view_permission' => 'Anda tidak memiliki akses ke feedback ini',

    // Status Change
    'status_changed' => 'Status diubah menjadi :status',

    // Validation Messages
    'validation' => [
        'title_required' => 'Judul harus diisi',
        'title_max' => 'Judul maksimal 255 karakter',
        'description_required' => 'Deskripsi harus diisi',
        'description_max' => 'Deskripsi maksimal 5000 karakter',
        'attachment_max' => 'Ukuran file maksimal 5MB',
        'attachment_mimes' => 'File harus berformat JPG, PNG, atau PDF',
        'response_required' => 'Respon harus diisi',
        'response_max' => 'Respon maksimal 5000 karakter',
    ],

    // Notification
    'notification_new_title' => 'Feedback Baru Diterima',
    'notification_new_message' => ':type dari :user: :title',
    'notification_responded_title' => 'Feedback Direspon',
    'notification_responded_message' => 'Feedback Anda ":title" telah direspon oleh :responder',

    // Messages
    'created_success' => 'Feedback berhasil dikirim! Terima kasih atas masukan Anda.',
    'updated_success' => 'Feedback berhasil diperbarui',
    'response_sent' => 'Respon berhasil dikirim',
    'not_found' => 'Feedback tidak ditemukan',
    'no_edit_permission' => 'Anda tidak memiliki akses untuk mengedit feedback ini',
    'cannot_edit_processed' => 'Feedback yang sudah diproses tidak dapat diedit',
    'cannot_edit' => 'Feedback tidak dapat diedit',
    'no_respond_permission' => 'Anda tidak memiliki akses untuk merespon feedback',
    'cannot_respond' => 'Feedback ini tidak dapat direspon',
];
