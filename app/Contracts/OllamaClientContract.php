<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Kontrak Klien Ollama untuk ICTServe v3.6.0
 *
 * Antara muka ini mentakrifkan kaedah yang diperlukan untuk berkomunikasi
 * dengan pelayan Ollama LLM. Selaras dengan D10 Source Code Documentation v3.6.0.
 *
 * @version 3.6.0
 *
 * @author Pasukan Pembangunan BPM MOTAC
 *
 * @compliance D10 Source Code Documentation v3.6.0
 *
 * @requirements 6.1, 7.1
 */
interface OllamaClientContract
{
    /**
     * Menjana respons teks menggunakan model LLM
     *
     * Kaedah ini menghantar prompt ke pelayan Ollama dan mengembalikan
     * respons yang dijana oleh model. Menyokong konfigurasi parameter
     * seperti temperature, top_p, dan max_tokens.
     *
     * @param  array<string, mixed>  $payload  Payload permintaan yang mengandungi:
     *                                         - model: string (nama model, contoh: 'llama3.1')
     *                                         - prompt: string (prompt pengguna)
     *                                         - system?: string (prompt sistem opsyen)
     *                                         - temperature?: float (kreativiti model, 0.0-1.0)
     *                                         - top_p?: float (nucleus sampling, 0.0-1.0)
     *                                         - max_tokens?: int (token maksimum untuk respons)
     * @return array<string, mixed> Respons yang mengandungi:
     *                              - response: string (teks yang dijana)
     *                              - model: string (model yang digunakan)
     *                              - created_at: string (masa penciptaan)
     *                              - done: bool (status selesai)
     *                              - total_duration?: int (masa pemprosesan dalam nanosaat)
     *                              - load_duration?: int (masa pemuatan model)
     *                              - prompt_eval_count?: int (bilangan token prompt)
     *                              - eval_count?: int (bilangan token respons)
     *
     * @throws \Illuminate\Http\Client\ConnectionException Jika sambungan gagal
     * @throws \Illuminate\Http\Client\RequestException Jika permintaan HTTP gagal
     * @throws \App\Exceptions\OllamaModelNotFoundException Jika model tidak dijumpai
     * @throws \App\Exceptions\OllamaTimeoutException Jika permintaan timeout
     */
    public function generate(array $payload): array;

    /**
     * Menjana vector embeddings untuk teks
     *
     * Kaedah ini mengubah teks input kepada representasi vektor yang boleh
     * digunakan untuk carian semantik dan perbandingan kesamaan.
     *
     * @param  string  $text  Teks untuk dijadikan embedding (maksimum 8192 aksara)
     * @param  string|null  $model  Model untuk embedding (lalai: model konfigurasi)
     * @return array<string, mixed> Respons yang mengandungi:
     *                              - embedding: array (vektor float dengan dimensi bergantung model)
     *                              - model: string (model yang digunakan)
     *                              - total_duration?: int (masa pemprosesan)
     *
     * @throws \InvalidArgumentException Jika teks kosong atau terlalu panjang
     * @throws \Illuminate\Http\Client\ConnectionException Jika sambungan gagal
     * @throws \App\Exceptions\OllamaModelNotFoundException Jika model tidak dijumpai
     */
    public function embeddings(string $text, ?string $model = null): array;

    /**
     * Melakukan perbualan chat dengan konteks sejarah
     *
     * Kaedah ini membolehkan perbualan berterusan dengan mengekalkan
     * konteks mesej sebelumnya untuk respons yang lebih kontekstual.
     *
     * @param  array<int, array{role: string, content: string}>  $messages  Array mesej dalam format:
     *                                                                      [
     *                                                                      ['role' => 'system', 'content' => 'prompt sistem'],
     *                                                                      ['role' => 'user', 'content' => 'mesej pengguna'],
     *                                                                      ['role' => 'assistant', 'content' => 'respons AI'],
     *                                                                      ...
     *                                                                      ]
     * @param  array<string, mixed>  $options  Pilihan tambahan:
     *                                         - model?: string (model untuk digunakan)
     *                                         - temperature?: float (kreativiti)
     *                                         - max_tokens?: int (token maksimum)
     *                                         - stream?: bool (streaming respons)
     * @return array<string, mixed> Respons chat yang mengandungi:
     *                              - message: array (mesej respons dengan role dan content)
     *                              - model: string (model yang digunakan)
     *                              - created_at: string (masa penciptaan)
     *                              - done: bool (status selesai)
     *                              - total_duration?: int (masa pemprosesan)
     *
     * @throws \InvalidArgumentException Jika format mesej tidak sah
     * @throws \Illuminate\Http\Client\ConnectionException Jika sambungan gagal
     */
    public function chat(array $messages, array $options = []): array;

    /**
     * Mendapatkan senarai model yang tersedia
     *
     * Kaedah ini mengembalikan senarai semua model LLM yang tersedia
     * pada pelayan Ollama untuk digunakan.
     *
     * @return array<string, mixed> Senarai model yang mengandungi:
     *                              - models: array (senarai model dengan maklumat)
     *                              [
     *                              [
     *                              'name' => 'llama3.1',
     *                              'modified_at' => '2024-01-01T00:00:00Z',
     *                              'size' => 4661224676,
     *                              'digest' => 'sha256:...',
     *                              'details' => [...]
     *                              ],
     *                              ...
     *                              ]
     *
     * @throws \Illuminate\Http\Client\ConnectionException Jika sambungan gagal
     */
    public function models(): array;

    /**
     * Memeriksa kesihatan sambungan pelayan Ollama
     *
     * Kaedah ini melakukan pemeriksaan kesihatan untuk memastikan
     * pelayan Ollama boleh diakses dan berfungsi dengan baik.
     *
     * @return bool True jika pelayan sihat dan boleh diakses, false sebaliknya
     */
    public function healthCheck(): bool;

    /**
     * Mendapatkan respons dari cache
     *
     * Kaedah ini mencari respons yang telah di-cache berdasarkan
     * kunci cache untuk meningkatkan prestasi.
     *
     * @param  string  $cacheKey  Kunci cache untuk dicari
     * @return array<string, mixed>|null Respons dari cache atau null jika tidak dijumpai
     */
    public function getCachedResponse(string $cacheKey): ?array;

    /**
     * Menyimpan respons ke dalam cache
     *
     * Kaedah ini menyimpan respons ke dalam cache dengan TTL yang
     * ditentukan untuk kegunaan masa depan.
     *
     * @param  string  $cacheKey  Kunci cache untuk penyimpanan
     * @param  array<string, mixed>  $response  Respons untuk disimpan
     * @param  int  $ttl  Masa hidup cache dalam saat
     */
    public function cacheResponse(string $cacheKey, array $response, int $ttl): void;

    /**
     * Membersihkan cache berdasarkan tag
     *
     * Kaedah ini membolehkan pembersihan cache secara selektif
     * berdasarkan tag untuk pengurusan cache yang lebih baik.
     *
     * @param  string|array<int, string>  $tags  Tag atau array tag untuk dibersihkan
     * @return bool True jika berjaya, false sebaliknya
     */
    public function clearCache(string|array $tags): bool;

    /**
     * Mendapatkan statistik prestasi
     *
     * Kaedah ini mengembalikan statistik prestasi untuk pemantauan
     * dan analisis penggunaan sistem AI.
     *
     * @return array<string, mixed> Statistik yang mengandungi:
     *                              - total_requests: int (jumlah permintaan)
     *                              - average_response_time: float (masa respons purata)
     *                              - cache_hit_rate: float (kadar hit cache)
     *                              - error_rate: float (kadar ralat)
     *                              - last_health_check: string (masa pemeriksaan kesihatan terakhir)
     */
    public function getPerformanceStats(): array;

    /**
     * Menetapkan konfigurasi runtime
     *
     * Kaedah ini membolehkan penetapan konfigurasi secara dinamik
     * semasa runtime untuk penyesuaian tingkah laku klien.
     *
     * @param  string  $key  Kunci konfigurasi
     * @param  mixed  $value  Nilai konfigurasi
     */
    public function setConfig(string $key, mixed $value): void;

    /**
     * Mendapatkan konfigurasi semasa
     *
     * Kaedah ini mengembalikan konfigurasi semasa klien untuk
     * tujuan debugging dan pemantauan.
     *
     * @param  string|null  $key  Kunci konfigurasi khusus (opsyen)
     * @return mixed Nilai konfigurasi atau array lengkap jika key null
     */
    public function getConfig(?string $key = null): mixed;
}
