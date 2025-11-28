<?php

declare(strict_types=1);

return [
    // Page titles and descriptions
    'title' => 'Perwakilan Kelulusan',
    'description' => 'Urus perwakilan sementara kuasa kelulusan anda kepada pelulus lain.',

    // Actions
    'create_delegation' => 'Cipta Perwakilan',
    'create' => 'Cipta Perwakilan',
    'create_first' => 'Cipta Perwakilan Pertama Anda',
    'deactivate' => 'Nyahaktif',
    'confirm_deactivate' => 'Sahkan Nyahaktif',
    'confirm_deactivate_message' => 'Adakah anda pasti mahu menyahaktifkan perwakilan ini? Pelulus yang diwakilkan tidak lagi boleh meluluskan bagi pihak anda.',

    // Labels
    'delegated_to' => 'Diwakilkan Kepada',
    'delegated_to_me' => 'Diwakilkan Kepada Saya',
    'delegated_approver' => 'Pelulus Diwakilkan',
    'select_approver' => 'Pilih pelulus...',
    'my_delegations' => 'Perwakilan Saya',
    'period' => 'Tempoh',
    'start_date' => 'Tarikh Mula',
    'end_date' => 'Tarikh Tamat',
    'reason' => 'Sebab',
    'reason_placeholder' => 'cth., Cuti tahunan dari 1hb hingga 15hb Januari',
    'reason_help' => 'Berikan sebab ringkas untuk perwakilan ini (10-500 aksara).',
    'status' => 'Status',
    'actions' => 'Tindakan',
    'from' => 'Daripada',
    'to' => 'hingga',

    // Status labels
    'active' => 'Aktif',
    'upcoming' => 'Akan Datang',
    'expired' => 'Tamat Tempoh',
    'inactive' => 'Tidak Aktif',

    // Filters
    'filter_by_status' => 'Tapis mengikut status',
    'filter_all' => 'Semua',
    'filter_active' => 'Aktif',
    'filter_upcoming' => 'Akan Datang',
    'filter_expired' => 'Tamat Tempoh',
    'filter_inactive' => 'Tidak Aktif',

    // Empty states
    'no_delegations' => 'Tiada Perwakilan',
    'no_delegations_description' => 'Anda belum mencipta sebarang perwakilan. Cipta satu apabila anda memerlukan seseorang untuk meluluskan bagi pihak anda.',

    // Success messages
    'created_successfully' => 'Perwakilan berjaya dicipta.',
    'deactivated_successfully' => 'Perwakilan berjaya dinyahaktifkan.',

    // Approval interface
    'manage_delegations' => 'Urus Perwakilan',
    'delegated_to_me_info' => 'Anda sedang menerima kelulusan diwakilkan daripada :count pelulus.',
    'on_behalf_of' => 'Bagi pihak',

    // Error messages
    'creation_failed' => 'Gagal mencipta perwakilan. Sila cuba lagi.',
    'deactivation_failed' => 'Gagal menyahaktifkan perwakilan. Sila cuba lagi.',

    // Validation errors
    'error' => [
        'start_before_end' => 'Tarikh mula mesti sebelum tarikh tamat.',
        'start_not_past' => 'Tarikh mula tidak boleh pada masa lalu.',
        'original_not_found' => 'Pelulus asal tidak dijumpai.',
        'delegated_not_found' => 'Pelulus diwakilkan tidak dijumpai.',
        'original_not_approver' => 'Pengguna asal mesti mempunyai peranan pelulus.',
        'delegated_not_approver' => 'Pengguna diwakilkan mesti mempunyai peranan pelulus.',
        'same_user' => 'Tidak boleh mewakilkan kepada diri sendiri.',
        'overlap' => 'Tempoh perwakilan bertindih dengan perwakilan aktif sedia ada.',
    ],

    // Validation messages
    'validation' => [
        'approver_required' => 'Sila pilih pelulus diwakilkan.',
        'approver_not_found' => 'Pelulus yang dipilih tidak wujud.',
        'start_required' => 'Tarikh mula diperlukan.',
        'start_not_past' => 'Tarikh mula tidak boleh pada masa lalu.',
        'end_required' => 'Tarikh tamat diperlukan.',
        'end_after_start' => 'Tarikh tamat mesti selepas tarikh mula.',
        'reason_required' => 'Sebab perwakilan diperlukan.',
        'reason_min' => 'Sebab mesti sekurang-kurangnya 10 aksara.',
        'reason_max' => 'Sebab tidak boleh melebihi 500 aksara.',
    ],
];
