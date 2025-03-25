# Tugas Tambahan: Implementasi SOAP untuk Contact dan Address

## Deskripsi Tugas
Tugas ini merupakan kelanjutan dari proyek API RESTful dan Swagger yang telah kamu selesaikan sebelumnya. Sekarang, kamu diminta untuk:
1. Membuat dokumentasi API untuk **Contact** dan **Address** dalam format XML menggunakan **WSDL** (Web Services Description Language).
2. Mengimplementasikan program API untuk **Contact** dan **Address** menggunakan **SOAP** (Simple Object Access Protocol).

## Prasyarat
- Kamu telah memiliki proyek API sebelumnya yang menggunakan RESTful API dan Swagger.
- Infrastruktur seperti database dan model untuk **Contact** dan **Address** sudah ada dari proyek sebelumnya.
- Pengetahuan dasar tentang PHP dan cara mengintegrasikannya dengan database.

## Langkah-Langkah Pengerjaan

### 1. Memahami SOAP dan WSDL
- **SOAP**: Protokol berbasis XML untuk pertukaran data terstruktur dalam layanan web.
- **WSDL**: Dokumen XML yang mendeskripsikan layanan web, termasuk operasi, input, dan output.

### 2. Membuat Dokumen WSDL
- Definisikan operasi CRUD (Create, Read, Update, Delete) untuk **Contact** dan **Address**, misalnya:
  - `createContact`, `getContact`, `updateContact`, `deleteContact`
  - `createAddress`, `getAddress`, `updateAddress`, `deleteAddress`
- Tentukan tipe data untuk setiap operasi (contoh: `name`, `phone` untuk Contact; `street`, `city` untuk Address).
- Tulis file WSDL dalam format XML yang mencakup elemen seperti `types`, `message`, `portType`, `binding`, dan `service`.

### 3. Implementasi Server SOAP
- Gunakan PHP dengan ekstensi SOAP untuk membuat server.
- Implementasikan fungsi-fungsi yang sesuai dengan operasi di WSDL.
- Hubungkan server dengan database yang sudah ada untuk menyimpan dan mengelola data **Contact** dan **Address**.

### 4. Implementasi Client SOAP
- Buat client SOAP menggunakan PHP untuk menguji server.
- Kirim permintaan ke setiap operasi yang ada di server dan tampilkan hasilnya.

### 5. Integrasi dengan Proyek Sebelumnya
- Pastikan server SOAP menggunakan database dan model yang sama dengan proyek RESTful sebelumnya.
- Sesuaikan jika ada perbedaan format data atau struktur yang diperlukan oleh SOAP.

### 6. Pengujian
- Uji setiap operasi (CRUD) untuk memastikan server dan client SOAP berfungsi dengan baik.
- Periksa apakah data tersimpan dan diambil dengan benar dari database.

### 7. Dokumentasi
- Sertakan file WSDL sebagai dokumentasi utama.
- Tambahkan penjelasan singkat tentang cara menggunakan layanan SOAP, termasuk contoh permintaan dan respons.

## Sumber Daya Tambahan
- [Dokumentasi PHP SOAP](https://www.php.net/manual/en/book.soap.php)
- [Panduan WSDL](https://www.w3schools.com/xml/xml_wsdl.asp)
- [Contoh SOAP di PHP](https://www.tutorialspoint.com/php/php_soap.htm)

## Catatan Penting
- Pastikan server SOAP berjalan di lingkungan yang mendukung PHP (misalnya, XAMPP atau server lokal lainnya).
- Jika ada masalah integrasi dengan proyek sebelumnya, periksa koneksi database dan kompatibilitas model.