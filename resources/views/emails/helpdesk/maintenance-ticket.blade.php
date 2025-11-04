<x-mail::message>
# Ticket Penyelenggaraan / Maintenance Ticket

Tiket penyelenggaraan **{{ ->ticket_number }}** telah dikeluarkan untuk aset berikut.

## Butiran Tiket / Ticket Details

**Subjek / Subject:** {{ ->subject }}  
**Keutamaan / Priority:** {{ ucfirst(->priority) }}  
**Permohonan Berkaitan / Linked Application:** {{ ->application_number }}

## Maklumat Aset / Asset Details

**Nama / Name:** {{ ->name }}  
**Tag Aset / Asset Tag:** {{ ->asset_tag }}  
**Keadaan Semasa / Current Condition:** {{ ->condition->label() }}

Sila lakukan tindakan penyelenggaraan dengan kadar segera. / Please proceed with maintenance immediately.

---

{{ config('app.name') }}
</x-mail::message>
