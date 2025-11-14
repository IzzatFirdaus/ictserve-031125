# Pelan Implementasi AI Chatbot Menggunakan Ollama untuk ICTServe (Future AI Chatbot Implementation Plan Using Ollama)

**Sistem Helpdesk & ICT Asset Loan MOTAC BPM - Inisiatif Masa Depan**
**Versi:** 1.0.0 (SemVer)
**Tarikh Kemaskini:** 17 Oktober 2025
**Status:** Aktif - Pelan Implementasi Masa Depan
**Klasifikasi:** Terhad - Dalaman MOTAC
**Penulis:** Pasukan Pembangunan BPM MOTAC
**Standard Rujukan:** ISO/IEC/IEEE 29148 (Software Requirements), ISO 8000 (Data Quality)

---

## Maklumat Dokumen (Document Information)

| Atribut                | Nilai                                           |
| ---------------------- | ----------------------------------------------- |
| **Versi**              | 1.0.0                                           |
| **Tarikh Kemaskini**   | 17 Oktober 2025                                 |
| **Status**             | Aktif - Pelan Implementasi Masa Depan           |
| **Klasifikasi**        | Terhad - Dalaman MOTAC                          |
| **Pematuhi**           | ISO/IEC/IEEE 29148, ISO 8000                    |
| **Bahasa**             | Bahasa Melayu (utama), English (teknikal)      |

---

## Sejarah Perubahan (Changelog)

| Versi  | Tarikh          | Perubahan                                      | Penulis       |
|--------|-----------------|------------------------------------------------|---------------|
| 1.0.0  | 17 Oktober 2025 | Versi awal pelan implementasi AI Chatbot       | Pasukan BPM   |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem
- **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]** - Spesifikasi Keperluan Perisian
- **[D09_DATABASE_DOCUMENTATION.md]** - Dokumentasi Pangkalan Data
- **[D10_SOURCE_CODE_DOCUMENTATION.md]** - Dokumentasi Kod Sumber
- **[D12_UI_UX_DESIGN_GUIDE.md]** - Panduan Rekabentuk UI/UX
- **[GLOSSARY.md]** - Glosari Istilah Sistem


---

## Ringkasan Eksekutif

Pelan ini menggariskan strategi implementasi kecerdasan buatan (AI) untuk sistem ICTServe menggunakan **Ollama** (Large Language Model lokal) dan teknik **Retrieval-Augmented Generation (RAG)**. Matlamat adalah untuk meningkatkan pengalaman pengguna melalui chatbot pintar yang menguruskan helpdesk dan pinjaman peralatan ICT.

Matlamat di sini adalah untuk menyediakan sokongan peringkat pertama yang lebih pantas (24/7)
dan mengoptimumkan beban kerja pasukan teknikal.

### 1. Chatbot Soalan Lazim (FAQ) Pintar 🤖

Chatbot ini akan bertindak sebagai medium hubungan pertama bagi staf yang menghadapi isu IT,
menyelesaikan masalah lazim secara serta-merta sebelum sesuatu tiket aduan diwujudkan.

- **Masalah:** Pasukan Meja Bantuan berulang kali menjawab soalan yang sama
  (contohnya, "Bagaimana cara sambungan ke pencetak?", "Kata laluan Wi-Fi," "PC saya lambat").

- **Penyelesaian AI:**
  - Membangunkan chatbot AI menggunakan **Ollama** dan teknik **RAG**.
  - **Pangkalan Pengetahuan:** Sistem ini akan dilatih menggunakan dokumen dalaman seperti:
    - Prosedur Operasi Standard (SOP) untuk penyelesaian masalah IT.
    - Soalan Lazim (FAQ).
    - Sejarah tiket aduan dan penyelesaiannya (data tanpa nama).
    - **Cara Ia Berfungsi:**
            1. Pengguna menaip soalan dalam bahasa biasa, contohnya,
               _"Saya tak boleh cetak dokumen ke pencetak di tingkat 5."_

            2. Sistem akan mencari maklumat yang paling relevan daripada pangkalan pengetahuannya
               (SOP untuk pencetak tersebut).

            3. LLM kemudiannya akan menjana panduan langkah demi langkah yang jelas
               berdasarkan maklumat yang telah disahkan itu sahaja.

- **Faedah:**
  - **Sokongan Segera:** Memberikan jawapan serta-merta kepada pengguna pada bila-bila masa.
  - **Mengurangkan Tiket:** Memberi ruang kepada pasukan Meja Bantuan untuk fokus pada isu yang lebih kompleks.
  - **Konsistensi:** Memastikan semua staf menerima penyelesaian yang seragam dan tepat.


### 2. Penghalaan & Ringkasan Tiket Pintar 📧

Bagi isu yang tidak dapat diselesaikan oleh chatbot, AI boleh membantu menguruskan tiket yang masuk dengan lebih cekap.

- **Masalah:** Tiket perlu dibaca, dikategori, diberi keutamaan,
  dan diagihkan kepada juruteknik yang betul secara manual,

  menyebabkan kelewatan.

- **Penyelesaian AI:**
  - Apabila pengguna menghantar tiket, LLM (yang beroperasi di Ollama) akan memproses teks tersebut secara automatik.
  - AI akan:
    - **Meringkaskan** isu kepada satu ayat yang padat.
    - **Mengklasifikasikan** tiket (contohnya, Perkakasan, Rangkaian, Perisian, Akses Pengguna).
    - **Mencadangkan Tahap Keutamaan** (Rendah, Sederhana, Tinggi) berdasarkan kata kunci.
    - **Mengagihkan** tiket kepada pasukan teknikal atau staf yang bersesuaian.
- **Faedah:**
  - **Masa Respons Lebih Cepat:** Juruteknik yang betul menerima tiket dengan segera.
  - **Kecekapan Meningkat:** Mengautomasikan tugas pentadbiran pengurusan tiket.
  - **Analisis Lebih Baik:** Data yang dikategorikan boleh dianalisis untuk mengenal pasti masalah yang kerap berulang.


---

## AI untuk Pinjaman Peralatan 💻

AI akan membantu pengguna mencari dan meminjam peralatan, di samping memudahkan proses pentadbiran.

### 1. Chatbot Pembantu & Cadangan Peralatan 🧑‍🏫

Chatbot ini membantu staf mencari peralatan yang sesuai dan memahami cara menggunakannya tanpa perlu bertanya kepada pentadbir.

- **Masalah:** Staf mungkin tidak tahu peranti mana (contohnya, projektor, komputer riba)
  yang sesuai untuk keperluan mereka atau cara mengendalikannya.

- **Penyelesaian AI:**
  - Mewujudkan chatbot berasaskan RAG untuk portal pinjaman peralatan.
  - **Pangkalan Pengetahuan:** AI akan dilatih menggunakan:
    - Pangkalan data semua peralatan yang ada berserta spesifikasinya
          (contohnya, keterangan projektor, RAM komputer riba).

    - Manual pengguna digital untuk setiap peralatan (PDF/Word).
    - Polisi pinjaman peralatan rasmi (SOP Pinjaman Peralatan).
  - **Cara Ia Berfungsi:**
    - Pengguna boleh bertanya:
      -     "Saya perlukan projektor untuk mesyuarat di bilik persidangan utama, mana yang paling sesuai?"
    - AI akan mendapatkan semula spesifikasi semua projektor yang ada dan mencadangkan yang terbaik
          berdasarkan kemungkinan saiz bilik.

    - Pengguna juga boleh meminta arahan, seperti
      -     "Bagaimana cara sambungkan laptop ke projektor ini?",
                  dan AI akan memberikan langkah-langkah daripada manual pengguna.

- **Faedah:**
  - **Memperkasa Pengguna:** Membantu pengguna membuat keputusan termaklum dan menyelesaikan isu teknikal kecil sendiri.
  - **Mengurangkan Beban Kerja Pentadbir:** Kurang pertanyaan terus yang perlu dijawab oleh pentadbir peralatan.
  - **Penggunaan Peralatan yang Betul:** Memastikan pengguna mendapat alat yang tepat untuk tugasan mereka.


---

## Cadangan Pelan Hala Tuju Pelaksanaan 🚀

1. **Fasa 1: _Proof of Concept_ (PoC) (1-2 Bulan)**
    - Menyediakan pelayan (server) dengan perkakasan yang disyorkan
      (contohnya, RAM 32GB+, GPU NVIDIA yang bersesuaian).

    - Memasang **Ollama** dan pangkalan data vektor (seperti ChromaDB).
    - Fokus pada **Chatbot FAQ Meja Bantuan**. Mulakan dengan set kecil 10-15 dokumen sokongan IT.
      Uji ketepatannya secara dalaman.

2. **Fasa 2: Perintis dan Integrasi (3-4 Bulan)**
    - Membangunkan **API _backend_** yang selamat menggunakan Python atau JS
      untuk menghubungkan Ollama ke portal dalaman anda.

    - Melancarkan Chatbot Meja Bantuan kepada kumpulan pengguna perintis
      (contohnya, khusus untuk BPM).

    - Mengumpul maklum balas untuk menambah baik _prompt_ dan meluaskan pangkalan pengetahuan.


3. **Fasa 3: Pelancaran Penuh & Pengembangan (Berterusan)**
    - Melancarkan Chatbot Meja Bantuan yang telah ditambah baik kepada semua staf MOTAC.
    - Memulakan pembangunan ciri AI yang lain: Penghalaan Tiket Pintar dan Pembantu Pinjaman Peralatan.
    - Mengemas kini pangkalan pengetahuan secara berterusan dengan dokumen dan SOP baharu.
