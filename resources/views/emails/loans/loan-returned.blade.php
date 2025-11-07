<x-mail::message>
    # Aset Telah Dipulangkan

    Assalamualaikum dan Salam Sejahtera {{ $applicantName }},

    Terima kasih kerana memulangkan aset yang dipinjam. Pemulangan anda telah direkodkan dalam sistem.

    ## Butiran Pemulangan

    **No. Permohonan:** {{ $applicationNumber }}
    **Tarikh Pemulangan:** {{ $returnedAt }}
    **Diterima Oleh:** {{ $returnedBy }}

    ## Aset Dipulangkan

    {{ $assets }}

    ## Aksesori Dipulangkan

    {{ $accessoriesReturned }}

    @if ($missingAccessories)
        ## Aksesori Hilang

        ⚠️ {{ $missingAccessories }}

        *Sila hubungi Bahagian ICT untuk tindakan susulan.*
    @endif

    @if ($returnNotes)
        ## Catatan

        {{ $returnNotes }}
    @endif

    @if ($hasDamagedAssets)
        ---

        ## ⚠️ Notis Kerosakan Aset

        Kami telah mengesan kerosakan pada aset yang dipulangkan. Tiket penyelenggaraan telah dibuat secara automatik
        dan pasukan teknikal kami akan menghubungi anda untuk maklumat lanjut.

        Sila ambil perhatian bahawa kerosakan yang disebabkan oleh kecuaian mungkin memerlukan tindakan susulan.
    @endif

    ## Terima Kasih

    Terima kasih kerana menjaga aset dengan baik dan memulangkannya tepat pada masanya. Kami menghargai kerjasama anda.

    <x-mail::button :url="config('app.url')">
        Portal ICTServe
    </x-mail::button>

    Sekian, terima kasih.

    **Bahagian ICT**
    Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC)

    ---

    *E-mel ini dijana secara automatik. Sila jangan balas ke alamat e-mel ini.*
</x-mail::message>
