<?php

declare(strict_types=1);

/**
 * Terjemahan Bahasa Melayu untuk Modul Ollama AI
 *
 * Selaras dengan D15 v3.6.0: Bahasa Melayu sahaja
 * Semua teks antara muka AI dalam Bahasa Melayu
 *
 * @trace Requirements 1.4, 5.4, 5.5
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
        'high_score_filter' => 'Skor Tinggi (≥0.7)',

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
];
