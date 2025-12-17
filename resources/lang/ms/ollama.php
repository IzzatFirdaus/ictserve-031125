<?php

declare(strict_types=1);

/**
 * Terjemahan Bahasa Melayu untuk Modul Ollama AI
 *
 * Selaras dengan D15 v3.6.0: Bahasa Melayu sahaja
 * Semua teks antara muka AI dalam Bahasa Melayu
 *
 * Trace: Requirements 1.4, 5.4, 5.5
 */
return [
    // Navigation
    'navigation_label' => 'Ollama AI',

    // FAQ Resource
    'faq' => [
        'navigation_label' => 'Pengurusan FAQ',
        'model_label' => 'FAQ',
        'plural_label' => 'FAQ',
        'list_title' => 'Senarai FAQ',
        'create_title' => 'Cipta FAQ Baharu',
        'edit_title' => 'Edit FAQ',
        'view_title' => 'Lihat FAQ',
        'welcome_message' => 'Selamat datang ke FAQ Bot ICTServe! Bagaimana saya boleh membantu anda hari ini?',

        // Form fields
        'section_details' => 'Butiran FAQ',
        'section_details_description' => 'Masukkan soalan dan jawapan untuk FAQ',
        'section_metadata' => 'Metadata',
        'question' => 'Soalan',
        'question_helper' => 'Masukkan soalan yang kerap ditanya (maksimum 500 aksara)',
        'answer' => 'Jawapan',
        'answer_helper' => 'Berikan jawapan yang lengkap dan jelas',
        'tags' => 'Tag',
        'tags_placeholder' => 'Tambah tag...',
        'tags_helper' => 'Tambah tag untuk memudahkan carian dan pengelasan',
        'match_score' => 'Skor Padanan',
        'match_score_helper' => 'Skor persamaan (0.0 - 1.0). Skor lebih tinggi = padanan lebih baik',
        'created_by' => 'Dicipta Oleh',
        'created_at' => 'Tarikh Dicipta',
        'updated_at' => 'Tarikh Dikemaskini',

        // Table & Filters
        'search_placeholder' => 'Cari soalan atau jawapan...',
        'high_score_filter' => 'Skor Tinggi (>=0.7)',

        // Actions
        'create' => 'Cipta FAQ',
        'import_csv' => 'Import CSV',
        'export_csv' => 'Eksport CSV',
        'export_started' => 'Eksport dimulakan',
    ],

    // Document Resource
    'document' => [
        'navigation_label' => 'Pengurusan Dokumen',
        'model_label' => 'Dokumen',
        'plural_label' => 'Dokumen',
        'list_title' => 'Senarai Dokumen',
        'create_title' => 'Muat Naik Dokumen',
        'edit_title' => 'Edit Dokumen',
        'view_title' => 'Lihat Dokumen',

        // Form fields
        'section_upload' => 'Muat Naik Fail',
        'section_upload_description' => 'Muat naik dokumen untuk diproses oleh AI',
        'section_details' => 'Butiran Dokumen',
        'section_metadata' => 'Metadata Dokumen',
        'section_processing' => 'Status Pemprosesan',
        'filename' => 'Nama Fail',
        'filename_helper' => 'Nama asal fail yang dimuat naik',
        'file' => 'Fail',
        'file_helper' => 'Format yang disokong: PDF, DOCX, TXT (maksimum 10MB)',
        'status' => 'Status',
        'uploaded_by' => 'Dimuat Naik Oleh',
        'file_size' => 'Saiz Fail',
        'file_type' => 'Jenis Fail',
        'chunks_count' => 'Bilangan Chunk',
        'created_at' => 'Tarikh Muat Naik',
        'updated_at' => 'Tarikh Dikemaskini',
        'metadata' => 'Metadata',
        'metadata_key' => 'Kunci',
        'metadata_value' => 'Nilai',
        'metadata_add' => 'Tambah Metadata',

        // Status labels
        'status_pending' => 'Menunggu',
        'status_processing' => 'Sedang Diproses',
        'status_completed' => 'Selesai',
        'status_failed' => 'Gagal',

        // Actions
        'upload' => 'Muat Naik',
        'reprocess' => 'Proses Semula',
        'reprocess_confirm' => 'Adakah anda pasti mahu memproses semula dokumen ini?',
        'reprocess_confirm_heading' => 'Sahkan Proses Semula',
        'reprocess_confirm_description' => 'Dokumen akan dikembalikan ke status menunggu dan diproses semula.',
        'reprocess_started' => 'Pemprosesan semula dimulakan',
        'reprocess_bulk' => 'Proses Semula Dipilih',
        'reprocess_bulk_started' => 'Pemprosesan semula untuk rekod terpilih dimulakan',
        'view_chunks' => 'Lihat Chunk',

        // Filters
        'filter_status' => 'Status',
        'filter_uploaded_by' => 'Dimuat Naik Oleh',
        'filter_failed_only' => 'Gagal Sahaja',
        'failed_only_filter' => 'Gagal Sahaja',
        'search_placeholder' => 'Cari dokumen...',
    ],

    // Auto-Reply Template Resource
    'template' => [
        'navigation_label' => 'Template Auto-Reply',
        'model_label' => 'Template',
        'plural_label' => 'Template',
        'list_title' => 'Senarai Template Auto-Reply',
        'create_title' => 'Cipta Template Baharu',
        'edit_title' => 'Edit Template',
        'view_title' => 'Lihat Template',

        // Form fields
        'section_details' => 'Butiran Template',
        'section_content' => 'Kandungan Template',
        'section_variables' => 'Pembolehubah',
        'name' => 'Nama Template',
        'name_helper' => 'Nama unik untuk mengenal pasti template',
        'template_content' => 'Kandungan',
        'template_content_helper' => 'Gunakan {{nama_pembolehubah}} untuk pembolehubah dinamik',
        'variables' => 'Pembolehubah',
        'variables_helper' => 'Senarai pembolehubah yang tersedia dalam template',
        'status' => 'Status',
        'created_by' => 'Dicipta Oleh',
        'created_at' => 'Tarikh Dicipta',
        'updated_at' => 'Tarikh Dikemaskini',

        // Status labels
        'status_draft' => 'Draf',
        'status_active' => 'Aktif',
        'status_archived' => 'Diarkibkan',

        // Actions
        'create' => 'Cipta Template',
        'activate' => 'Aktifkan',
        'archive' => 'Arkibkan',
        'preview' => 'Pratonton',
        'test' => 'Uji Template',
        'duplicate' => 'Duplikat',
    ],

    // Auto-Reply Draft Resource
    'draft' => [
        'navigation_label' => 'Draf Auto-Reply',
        'model_label' => 'Draf',
        'plural_label' => 'Draf',
        'list_title' => 'Senarai Draf Auto-Reply',
        'view_title' => 'Lihat Draf',

        // Form fields
        'section_details' => 'Butiran Draf',
        'section_content' => 'Kandungan Draf',
        'section_approval' => 'Maklumat Kelulusan',
        'draft_content' => 'Kandungan',
        'template' => 'Template',
        'status' => 'Status',
        'generated_by' => 'Dijana Oleh',
        'approved_by' => 'Diluluskan Oleh',
        'approved_at' => 'Tarikh Kelulusan',
        'rejection_reason' => 'Sebab Penolakan',
        'related_item' => 'Item Berkaitan',
        'created_at' => 'Tarikh Dicipta',

        // Status labels
        'status_draft' => 'Draf',
        'status_pending_review' => 'Menunggu Semakan',
        'status_approved' => 'Diluluskan',
        'status_rejected' => 'Ditolak',
        'status_sent' => 'Dihantar',

        // Actions
        'approve' => 'Luluskan',
        'reject' => 'Tolak',
        'approve_confirm' => 'Adakah anda pasti mahu meluluskan draf ini?',
        'reject_confirm' => 'Sila berikan sebab penolakan',
        'rejection_reason_label' => 'Sebab Penolakan',
        'approved_success' => 'Draf telah diluluskan',
        'rejected_success' => 'Draf telah ditolak',

        // Filters
        'filter_status' => 'Status',
        'filter_pending_only' => 'Menunggu Semakan Sahaja',
    ],

    // Message Log Resource
    'message_log' => [
        'navigation_label' => 'Log Mesej AI',
        'model_label' => 'Log Mesej',
        'plural_label' => 'Log Mesej',
        'list_title' => 'Senarai Log Mesej AI',
        'view_title' => 'Lihat Log Mesej',

        // Form fields
        'section_request' => 'Maklumat Permintaan',
        'section_response' => 'Maklumat Respons',
        'section_audit' => 'Jejak Audit',
        'request_id' => 'ID Permintaan',
        'operation_type' => 'Jenis Operasi',
        'user' => 'Pengguna',
        'sanitized_input' => 'Input (Disanitasi)',
        'response_summary' => 'Ringkasan Respons',
        'metadata' => 'Metadata',
        'hash' => 'Hash',
        'previous_hash' => 'Hash Sebelumnya',
        'processed_at' => 'Masa Diproses',
        'created_at' => 'Tarikh Dicipta',

        // Operation types
        'operation_faq_query' => 'Pertanyaan FAQ',
        'operation_document_analysis' => 'Analisis Dokumen',
        'operation_auto_reply_generation' => 'Penjanaan Auto-Reply',

        // Filters
        'filter_operation_type' => 'Jenis Operasi',
        'filter_user' => 'Pengguna',
        'filter_date_range' => 'Julat Tarikh',
        'filter_from_date' => 'Dari Tarikh',
        'filter_until_date' => 'Hingga Tarikh',
    ],

    // Performance Dashboard
    'performance' => [
        'navigation_label' => 'Prestasi AI',
        'page_title' => 'Papan Pemuka Prestasi Ollama AI',
        'page_description' => 'Pantau prestasi dan kesihatan sistem AI',

        // Widgets
        'response_time' => 'Masa Respons',
        'response_time_p50' => 'P50',
        'response_time_p95' => 'P95',
        'response_time_p99' => 'P99',
        'system_health' => 'Kesihatan Sistem',
        'uptime' => 'Masa Aktif',
        'server_status' => 'Status Pelayan',
        'cache_performance' => 'Prestasi Cache',
        'cache_hit_rate' => 'Kadar Hit Cache',
        'cache_size' => 'Saiz Cache',
        'database_performance' => 'Prestasi Pangkalan Data',
        'query_time' => 'Masa Query',
        'slow_queries' => 'Query Perlahan',
        'resource_utilization' => 'Penggunaan Sumber',
        'cpu_usage' => 'Penggunaan CPU',
        'memory_usage' => 'Penggunaan Memori',
        'ai_operations' => 'Operasi AI',
        'operations_by_type' => 'Operasi Mengikut Jenis',
        'total_operations' => 'Jumlah Operasi',

        // Status
        'status_healthy' => 'Sihat',
        'status_degraded' => 'Merosot',
        'status_unhealthy' => 'Tidak Sihat',
        'status_unknown' => 'Tidak Diketahui',

        // Actions
        'refresh' => 'Muat Semula',
        'export_report' => 'Eksport Laporan',
        'date_range' => 'Julat Tarikh',
    ],

    // Common
    'common' => [
        'guest' => 'Tetamu',
        'unknown' => 'Tidak Diketahui',
        'none' => 'Tiada',
        'yes' => 'Ya',
        'no' => 'Tidak',
        'loading' => 'Memuatkan...',
        'processing' => 'Memproses...',
        'success' => 'Berjaya',
        'error' => 'Ralat',
        'warning' => 'Amaran',
        'info' => 'Maklumat',
    ],

    // Bedrock Integration
    'bedrock' => [
        'title' => 'Sembang Bedrock AI',
        'description' => 'Tanya soalan anda menggunakan AI yang berkuasa dari AWS Bedrock',
        'chat_button' => 'Mula Sembang AI',
        'powered_by' => 'Dikuasakan oleh AWS Bedrock',
        'switch_to_bedrock' => 'Tukar ke Bedrock AI',
        'switch_to_ollama' => 'Tukar ke Ollama AI',
        'model_selection' => 'Pilih Model AI',
        'nova_micro' => 'Nova Micro (Sangat Pantas)',
        'nova_lite' => 'Nova Lite (Pantas & Cekap)',
        'nova_pro' => 'Nova Pro (Berkuasa)',
        'titan_lite' => 'Titan Text Lite (Ringan)',
        'titan_express' => 'Titan Text Express (Ekspres)',
        'claude_opus' => 'Claude Opus (Paling Berkuasa)',
        'claude_sonnet' => 'Claude Sonnet (Seimbang)',
        'claude_haiku' => 'Claude Haiku (Paling Pantas)',
    ],

    // Widget (Floating Chat Bot)
    'widget' => [
        'aria_label' => 'Widget FAQ Bot AI',
        'toggle_button' => 'Buka atau tutup FAQ Bot',
        'title' => 'FAQ Bot ICTServe',
        'welcome_user' => 'Selamat datang, :name',
        'welcome_guest' => 'Selamat datang, Tetamu',
        'minimize' => 'Minimumkan widget',
        'close' => 'Tutup widget',
        'conversation_log' => 'Log perbualan',
        'user_message' => 'Mesej anda',
        'bot_message' => 'Mesej bot',
        'no_messages' => 'Tiada mesej lagi. Mulakan perbualan!',
        'typing' => 'Sedang menaip...',
        'query_label' => 'Pertanyaan anda',
        'query_placeholder' => 'Taip soalan anda...',
        'send_button' => 'Hantar mesej',
        'query_help' => 'Tekan Enter atau klik butang hantar untuk menghantar.',
        'clear_conversation' => 'Kosongkan perbualan',
        'open_full_bot' => 'Buka FAQ Bot penuh',
        'click_to_restore' => 'Klik untuk memulihkan widget',
        'welcome_message' => 'Selamat datang ke FAQ Bot ICTServe! Bagaimana saya boleh membantu anda hari ini?',
    ],

    // Accessibility (WCAG 2.2 AA - D12-D14 v3.6.0)
    'accessibility' => [
        // ARIA Labels
        'chat_region' => 'Kawasan perbualan AI',
        'loading_response' => 'Sedang menjana respons AI',
        'response_ready' => 'Respons AI sedia',
        'loading_indicator' => 'Penunjuk pemuatan',
        'loading_text' => 'Sila tunggu...',

        // Skip Links
        'skip_to_main' => 'Langkau ke kandungan utama',
        'skip_to_chat' => 'Langkau ke perbualan AI',
        'skip_to_results' => 'Langkau ke hasil carian',

        // Keyboard Instructions
        'key_enter' => 'Tekan Enter untuk menghantar mesej',
        'key_escape' => 'Tekan Escape untuk menutup',
        'key_tab' => 'Tekan Tab untuk navigasi',
        'key_arrow_up' => 'Tekan anak panah atas untuk mesej sebelumnya',
        'key_arrow_down' => 'Tekan anak panah bawah untuk mesej seterusnya',

        // Screen Reader Announcements
        'sr_loading' => 'Sedang memproses permintaan anda. Sila tunggu.',
        'sr_response_received' => 'Respons diterima: :preview',
        'sr_error_occurred' => 'Ralat berlaku semasa memproses permintaan anda.',
        'sr_no_results' => 'Tiada hasil ditemui untuk pertanyaan anda.',
        'sr_status_unknown' => 'Status tidak diketahui.',

        // Icon Labels
        'icon_success' => 'Ikon kejayaan',
        'icon_error' => 'Ikon ralat',
        'icon_warning' => 'Ikon amaran',
        'icon_info' => 'Ikon maklumat',

        // Form Labels
        'input_query' => 'Masukkan pertanyaan anda',
        'input_query_placeholder' => 'Taip soalan anda di sini...',
        'button_submit' => 'Hantar pertanyaan',
        'button_clear' => 'Kosongkan perbualan',
        'button_copy' => 'Salin respons',
        'button_feedback' => 'Beri maklum balas',

        // Status Messages
        'status_connecting' => 'Menyambung ke pelayan AI...',
        'status_connected' => 'Bersambung ke pelayan AI',
        'status_disconnected' => 'Terputus dari pelayan AI',
        'status_reconnecting' => 'Menyambung semula...',

        // Error Messages
        'error_connection' => 'Tidak dapat menyambung ke pelayan AI. Sila cuba lagi.',
        'error_timeout' => 'Permintaan tamat masa. Sila cuba lagi.',
        'error_rate_limit' => 'Terlalu banyak permintaan. Sila tunggu sebentar.',
        'error_server' => 'Ralat pelayan. Sila hubungi pentadbir.',
        'error_empty_query' => 'Sila masukkan pertanyaan anda.',
        'error_query_too_long' => 'Pertanyaan terlalu panjang. Maksimum 500 aksara.',

        // Widget-specific accessibility
        'widget_opened' => 'Widget FAQ Bot dibuka',
        'widget_closed' => 'Widget FAQ Bot ditutup',
        'widget_minimized' => 'Widget FAQ Bot diminimumkan',
        'widget_restored' => 'Widget FAQ Bot dipulihkan',
        'conversation_cleared' => 'Perbualan telah dikosongkan',
        'processing_query' => 'Sedang memproses pertanyaan anda...',
        'response_received' => 'Respons diterima: :preview',
        'error_occurred' => 'Ralat berlaku semasa memproses permintaan.',
        'history_opened' => 'Panel sejarah dibuka',
        'history_closed' => 'Panel sejarah ditutup',

        // Help Text
        'help_keyboard' => 'Gunakan papan kekunci untuk navigasi',
        'help_screen_reader' => 'Antara muka ini serasi dengan pembaca skrin',
        'help_touch_target' => 'Semua butang mempunyai saiz minimum 44x44 piksel',
    ],

    // Errors
    'errors' => [
        'general_error' => 'Ralat berlaku. Sila cuba lagi.',
        'server_error' => 'Ralat pelayan. Sila hubungi pentadbir sistem.',
        'connection_error' => 'Tidak dapat menyambung ke pelayan AI.',
        'timeout_error' => 'Permintaan tamat masa. Sila cuba lagi.',
    ],

    // Validation
    'validation' => [
        'query_required' => 'Sila masukkan pertanyaan anda.',
        'query_too_long' => 'Pertanyaan terlalu panjang. Maksimum 500 aksara.',
    ],
];
