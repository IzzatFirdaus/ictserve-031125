<?php

declare(strict_types=1);

/**
 * Impersonation Translations - Bahasa Melayu
 *
 * Translation keys for user impersonation features and security messages.
 *
 * @trace /D03-FR-002.5 (Impersonation Security)
 * @trace /D04 §5.0.3 (Impersonation Middleware)
 *
 * @version 1.0.0
 *
 * @created 2025-11-26
 */

return [
    // Security Messages
    'action_blocked' => 'Tindakan Disekat',
    'action_blocked_message' => 'Tindakan ini tidak dibenarkan semasa menyamar sebagai pengguna lain. Sila hentikan penyamaran untuk melakukan tindakan keselamatan.',

    // Banner Messages
    'impersonation_active' => 'Penyamaran Aktif',
    'impersonating_user' => 'Anda sedang menyamar sebagai :name',
    'logged_in_as_admin' => 'Log masuk sebagai pentadbir: :admin',
    'stop_impersonation' => 'Hentikan Penyamaran',
    'return_to_admin' => 'Kembali ke Admin',

    // Audit Messages
    'impersonation_started' => 'Penyamaran pengguna dimulakan',
    'impersonation_ended' => 'Penyamaran pengguna ditamatkan',
    'action_blocked_audit' => 'Tindakan keselamatan disekat semasa penyamaran',

    // Error Messages
    'cannot_impersonate_self' => 'Anda tidak boleh menyamar sebagai diri sendiri.',
    'cannot_impersonate_admin' => 'Anda tidak boleh menyamar sebagai Pentadbir Super lain.',
    'unauthorized_action' => 'Anda tidak dibenarkan untuk melakukan tindakan ini.',

    // Confirmation Messages
    'confirm_stop' => 'Adakah anda pasti mahu menghentikan penyamaran pengguna ini?',
    'impersonation_stopped' => 'Penyamaran telah dihentikan. Anda kini log masuk sebagai diri sendiri.',
];
