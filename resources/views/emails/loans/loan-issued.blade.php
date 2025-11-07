<x-mail::message>
    # Aset Telah Dikeluarkan

    Assalamualaikum dan Salam Sejahtera {{ $applicantName }},

    Aset yang anda mohon telah dikeluarkan dan sedia untuk digunakan.

    ## Butiran Pengeluaran

    **No. Permohonan:** {{ $applicationNumber }}
    **Tarikh Pengeluaran:** {{ $issuedAt }}
    **Dikeluarkan Oleh:** {{ $issuedBy }}
    **Tarikh Pemulangan:** {{ $returnDate }}

    ## Aset Dikeluarkan

    {{ $assets }}

    ## Aksesori Disertakan

    {{ $accessories }}

    @if ($specialInstructions)
        ## Arahan Khas

        {{ $specialInstructions }}
    @endif

    ## Tanggungjawab Peminjam

    Sila ambil perhatian:

    1. **Jaga Aset** - Anda bertanggungjawab menjaga aset dalam keadaan baik
    2. **Pulangkan Tepat Masa** - Aset mesti dipulangkan pada atau sebelum {{ $returnDate }}
    3. **Laporkan Kerosakan** - Sebarang kerosakan mesti dilaporkan segera
    4. **Aksesori Lengkap** - Semua aksesori mesti dipulangkan bersama aset

    ## Peringatan Pemulangan

    Anda akan menerima peringatan:
    - 48 jam sebelum tarikh pemulangan
    - Pada hari pemulangan
    - Setiap hari jika lewat memulangkan

    <x-mail::button :url="config('app.url')">
        Portal ICTServe
    </x-mail::button>

    Terima kasih atas kerjasama anda.

    Sekian, terima kasih.

    **Bahagian ICT**
    Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC)

    ---

    *E-mel ini dijana secara automatik. Sila jangan balas ke alamat e-mel ini.*
</x-mail::message>
