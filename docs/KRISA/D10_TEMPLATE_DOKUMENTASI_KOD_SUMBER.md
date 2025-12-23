# D10 DOKUMENTASI KOD SUMBER

**(NAMA SISTEM)**

*(Sertakan nama modul di bawah nama sistem sekiranya dokumen disediakan secara berasingan bagi setiap modul di bawah sistem yang sama)*

| | |
| :--- | :--- |
| **NAMA AGENSI** | : |
| **NAMA AGENSI INDUK** | : |
| **TARIKH DOKUMEN** | : |
| **VERSI DOKUMEN** | : |

---

## i. Keterangan Dokumen

*Seksyen ini adalah ruangan untuk menyatakan secara ringkas keterangan berkenaan dokumen yang disediakan. Contoh keterangan dokumen adalah seperti di bawah :*

> "Dokumen ini menyatakan piawaian dan panduan kod pengaturcaraan yang digunakan bagi membangunkan sistem. Piawaian ini akan dirujuk semasa fasa pembangunan. Ia merupakan garis panduan terperinci berkenaan kod pengaturcaraan yang perlu dipatuhi oleh semua pengaturcara semasa membangunkan sistem."

## ii. Semakan dan Pengesahan Dokumen

*Seksyen ini adalah ruangan bagi pegawai-pegawai yang bertanggungjawab untuk melakukan semakan dan pengesahan kepada maklumat-maklumat yang terkandung di dalam dokumen ini. Sila sertakan maklumat seperti nama, jawatan, tandatangan dan tarikh semakan atau kelulusan. Contoh ruangan semakan dan pengesahan adalah seperti berikut :*

**SEMAKAN DOKUMEN**

| Disemak Oleh | Jawatan | Tandatangan | Tarikh Semakan |
| :--- | :--- | :--- | :--- |
| | | | |
| | | | |

**PENGESAHAN DOKUMEN**

| Disahkan Oleh | Jawatan | Tandatangan | Tarikh Semakan |
| :--- | :--- | :--- | :--- |
| | | | |
| | | | |

## iii. Kawalan Dokumen

*Seksyen ini adalah ruangan untuk mencatatkan maklumat-maklumat penyediaan dokumen termasuk maklumat pindaan yang telah dilakukan ke atas dokumen ini. Sila masukkan nombor versi, tarikh, ringkasan pindaan dan nama penyedia di dalam jadual seperti di bawah :*

**KAWALAN DOKUMEN**

| No. Versi | Tarikh | Ringkasan Pindaan | Penyedia |
| :--- | :--- | :--- | :--- |
| | | | |
| | | | |

*Penentuan nombor versi adalah bergantung kepada saiz pindaan kepada dokumen yang telah dilakukan. Sekiranya saiz pindaan yang dilakukan adalah kecil ataupun sederhana, perubahan nombor versi hanya melibat perubahan angka selepas titik perpuluhan sahaja, contohnya, perubahan dari nombor versi 1.2 kepada 1.3. Sekiranya pindaan yang dilakukan adalah besar dan melibatkan perubahan kepada kandungan dokumen, gunakan angka nombor yang seterusnya, contohnya, dari nombor 1.2 kepada 2.0.*

## iv. Kandungan

*Seksyen ini merupakan ruangan untuk memasukkan maklumat kandungan dokumen berserta nombor muka surat yang terlibat.*

## v. Senarai Gambarajah

*Seksyen ini merupakan ruangan untuk memasukkan senarai nombor rujukan bagi gambarajah-gambarajah yang terkandung di dalam dokumen berserta nombor muka surat yang terlibat.*

## vi. Senarai Jadual

*Seksyen ini merupakan ruangan untuk memasukkan senarai nombor rujukan bagi jadual-jadual yang terkandung di dalam dokumen berserta nombor muka surat yang terlibat.*

## vii. Definisi dan Akronim

### a. Akronim

*Sub seksyen ini adalah ruangan untuk menerangkan akronim-akronim yang digunakan di dalam dokumen. Contoh pengisian di ruangan akronim adalah seperti jadual di bawah :*

| Akronim | Keterangan |
| :--- | :--- |
| | |
| | |

### b. Definisi

*Sub seksyen ini adalah ruangan untuk menerangkan definisi bagi terma atau istilah yang digunakan di dalam dokumen. Contoh pengisian di ruangan definisi adalah seperti jadual di bawah :*

| Terma/Istilah | Definisi |
| :--- | :--- |
| | |
| | |

## viii. Sumber Rujukan

*Seksyen ini adalah ruangan untuk menyenaraikan semua sumber-sumber rujukan yang digunakan di dalam penyediaan dokumen ini, contohnya seperti surat pekeliling perkhidmatan, manual prosedur kerja, garis-garis panduan, dokumen-dokumen piawaian ISO/IEC/IEEE dan bahan rujukan lain yang berkaitan.*

---

## 1. TUJUAN DOKUMEN

*Perenggan ini menerangkan tujuan dokumen ini dihasilkan. Senaraikan kumpulan sasar dokumen ini. Nyatakan andaian, batasan dan kekangan dalam menyediakan dokumentasi kod sumber ini.*

## 2. SKOP DOKUMEN

*Perenggan ini menyatakan skop bagi penyediaan kod sumber. Berikut adalah contoh maklumat yang boleh dinyatakan:*

i. Nama sistem yang terlibat  
ii. Nama modul yang terlibat  
iii. Nama pasukan pembangunan sistem yang terlibat

## 3. PIAWAIAN KOD SUMBER

*Nyatakan piawaian kod sumber (cara penulisan kod) yang digunakan semasa membangunkan sistem. Pasukan pembangun sistem boleh menyenaraikan kod yang bersesuaian mengikut keperluan. Berikut antara contoh kod sumber yang perlu dipatuhi.*

### i. File Name

*<Nyatakan nama fail yang terlibat>*

Contoh:  
.java, .php

### ii. Class Headers and Declaration

*<Isytiharkan Class Header yang mengandungi class name, inheritance, attributes, methods, functionality, visibility, requirement number dan penyataan yang mengisytiharkan Class tersebut>*

Contoh:

```java
/**********************************************************
 * Class name:
 * Inheritance:
 * Attributes:
 * Methods:
 * Functionality:
 * Visibility:
 * From requirement number
**********************************************************/

public class ClassName
```

### iii. Method Headers and Declaration

*<Isytiharkan Class Header yang mengandungi method name, inheritance, attributes, precondition, postcondition, functionality, visibility, @param, @return dan requirement numbers>*

Contoh:

```java
/**********************************************************
 * Method name:
 * Inheritance:
 * Attributes:
 * Precondition:
 * Postcondition:
 * Functionality:
 * Visibility:
 * @param:
 * @return:
 * From requirement number
**********************************************************/
```

### iv. Indentation

*<Nyatakan kaedah indentation yang digunakan>*

Contoh:  
Four spaces should be used as the unit of indentation

### v. Inline Comments

*<Nyatakan kaedah inline comment yang digunakan>*

Contoh:  
Inline comment should make up 20% of the total lines of code in a program, excluding the header documentation blocks.

### vi. Variable Names

*<Nyatakan kaedah penulisan variable names yang digunakan>*

Contoh:  
Variable shall have mnemonic or meaningful names that convey to a casual observer, the intent of its use. Variables shall be initialized prior to its first use.  
String tempWord;

### vii. Use of Braces

*<Nyatakan gaya penggunaan Braces>*

Contoh:

Style 1:

```java
for (int j = 0 ; j < max_iterations ; ++j)
{
    /* Some work is done here. */
}
```

Style 2:

```java
for ( int j = 0 ; j < max_iterations ; ++j )
{
    /* Some work is done here. */
}
```

### viii. Line Length

*<Nyatakan line length yang digunakan>*

Contoh:  
Keep the lengths of source code lines at or below 80 characters.

### ix. Spacing

*<Nyatakan kaedah spacing yang digunakan>*

Contoh:

i. A keyword followed by a parenthesis should be separated by a space.  
ii. A blank space should appear after each comma in an argument list.

```java
cost = price + ( price * sales_tax );
fprintf (stdout, "The total cost is %5.2f\n", cost) ;
```

### x. Wrapping Lines

*<Nyatakan kaedah wrapping lines yang digunakan>*

Contoh:

i. Break after a comma

```java
fprintf ( stdout , "\nThere are %d reasons to use standards\n" num_reasons ) ;
```

ii. Break after an operator

```java
long int total_apples = num_my_apples + num_his_apples num_her_apples ;
```

### xi. Program Statement

*<Nyatakan kaedah penulisan program statement yang digunakan>*

Contoh:  
Program statement should be limited to one per line. Also nested statements should be avoided when possible.

```java
number_of_names = names.length ;
b = new JButton [ number_of_names ] ;
```

## 4. LAMPIRAN

*Seksyen ini merupakan ruangan untuk menyertakan dokumen-dokumen sokongan yang perlu dirujuk seperti format borang fizikal, format laporan dan pelbagai lagi dokumen-dokumen lain yang berkaitan.*
