Berikut adalah file context yang berisi rangkuman deskripsi tugas mata kuliah Pemrograman API secara detail serta langkah-langkah pengerjaan yang harus dilakukan untuk menyelesaikan tugas ini dari awal sampai akhir, termasuk output yang diharapkan.

---

## Deskripsi Tugas Mata Kuliah Pemrograman API

Tugas ini bertujuan untuk mengembangkan sebuah **API berbasis RESTful** yang mencakup fungsi registrasi pengguna, autentikasi, pengelolaan kontak, pengelolaan alamat, serta dokumentasi API menggunakan **Swagger**. Berikut adalah rincian tugas yang harus diselesaikan:

1. **Registrasi Pengguna**  
   - Membuat endpoint untuk mendaftarkan pengguna baru dengan data:  
     - `username` (unik)  
     - `password` (terenkripsi)  
     - `name` (nama lengkap pengguna)  
   - Mengembalikan respons sukses atau pesan kesalahan jika registrasi gagal.

2. **Login Pengguna**  
   - Memverifikasi kredensial (`username` dan `password`).  
   - Menghasilkan **token autentikasi** yang akan digunakan untuk mengakses rute yang dilindungi.  

3. **Mendapatkan Informasi Pengguna**  
   - Menampilkan data pengguna yang sedang login berdasarkan token autentikasi.

4. **Memperbarui Informasi Pengguna**  
   - Mengizinkan pengguna untuk mengubah `name` atau `password`.

5. **Logout Pengguna**  
   - Menghapus atau membatalkan token autentikasi agar tidak dapat digunakan lagi.

6. **Pengelolaan Kontak**  
   - Membuat fungsi **CRUD** (Create, Read, Update, Delete) untuk data kontak yang terkait dengan pengguna yang login.  
   - Data kontak meliputi:  
     - `first_name`  
     - `last_name`  
     - `email`  
     - `phone`  

7. **Pengelolaan Alamat**  
   - Membuat fungsi **CRUD** untuk data alamat yang terkait dengan kontak tertentu.  
   - Data alamat meliputi:  
     - `street`  
     - `city`  
     - `province`  
     - `country`  
     - `postal_code`  

8. **Dokumentasi API**  
   - Menyediakan dokumentasi API yang lengkap menggunakan **Swagger** dalam format **JSON** atau **YAML**.  
   - Dokumentasi harus mencakup semua endpoint, parameter, serta contoh respons.

---

## Langkah-Langkah Pengerjaan

Berikut adalah langkah-langkah terperinci untuk menyelesaikan tugas ini dari awal hingga akhir:

### 1. Persiapan Lingkungan Pengembangan
- **Instalasi Perangkat Lunak**  
  - Unduh dan instal **VS Code** sebagai editor kode.  
  - Instal **XAMPP** untuk menjalankan server lokal (Apache dan MySQL).  
  - Pastikan **PHP** (versi 7.4 atau lebih baru) dan **Composer** sudah terinstal.  
- **Jalankan XAMPP**  
  - Buka **XAMPP Control Panel**, lalu aktifkan modul **Apache** dan **MySQL**.  
- **Buat Database**  
  - Akses **phpMyAdmin** melalui `http://localhost/phpmyadmin`.  
  - Buat database baru, misalnya bernama `contact_api`.  
- **Buat Project Laravel**  
  - Buka terminal di folder kerja Anda, lalu jalankan perintah:  
    ```bash
    composer create-project laravel/laravel contact-api
    ```  
  - Masuk ke direktori proyek:  
    ```bash
    cd contact-api
    ```  
- **Konfigurasi Database**  
  - Buka file `.env` di root proyek, lalu sesuaikan konfigurasi database:  
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=contact_api
    DB_USERNAME=root
    DB_PASSWORD=
    ```

### 2. Membuat Model dan Migrasi
- **Model User**  
  - Buat model `User` beserta migrasi:  
    ```bash
    php artisan make:model User -m
    ```  
  - Edit file migrasi di `database/migrations` untuk menambahkan kolom:  
    ```php
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('username')->unique();
        $table->string('password');
        $table->string('name');
        $table->string('token')->nullable();
        $table->timestamps();
    });
    ```  
- **Model Contact**  
  - Buat model `Contact` beserta migrasi:  
    ```bash
    php artisan make:model Contact -m
    ```  
  - Edit file migrasi:  
    ```php
    Schema::create('contacts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('first_name');
        $table->string('last_name')->nullable();
        $table->string('email')->nullable();
        $table->string('phone')->nullable();
        $table->timestamps();
    });
    ```  
- **Model Address**  
  - Buat model `Address` beserta migrasi:  
    ```bash
    php artisan make:model Address -m
    ```  
  - Edit file migrasi:  
    ```php
    Schema::create('addresses', function (Blueprint $table) {
        $table->id();
        $table->foreignId('contact_id')->constrained()->onDelete('cascade');
        $table->string('street')->nullable();
        $table->string('city')->nullable();
        $table->string('province')->nullable();
        $table->string('country');
        $table->string('postal_code')->nullable();
        $table->timestamps();
    });
    ```  
- **Jalankan Migrasi**  
  - Eksekusi perintah berikut untuk membuat tabel di database:  
    ```bash
    php artisan migrate
    ```

### 3. Membuat Request untuk Validasi
- Buat kelas **Request** untuk memvalidasi input:  
  - **UserRegisterRequest**:  
    ```bash
    php artisan make:request UserRegisterRequest
    ```  
    Edit aturan validasi di `app/Http/Requests/UserRegisterRequest.php`:  
    ```php
    public function rules() {
        return [
            'username' => 'required|unique:users',
            'password' => 'required|min:6',
            'name' => 'required',
        ];
    }
    ```  
  - **UserLoginRequest**, **UserUpdateRequest**, **ContactRequest**, dan **AddressRequest** dibuat dengan cara serupa, disesuaikan dengan kebutuhan validasi masing-masing.

### 4. Membuat Resource untuk Format Respons
- Buat kelas **Resource** untuk mengatur format respons JSON:  
  - **UserResource**:  
    ```bash
    php artisan make:resource UserResource
    ```  
  - **ContactResource** dan **AddressResource** dibuat dengan cara serupa.

### 5. Membuat Middleware untuk Autentikasi
- Buat middleware `ApiAuthMiddleware`:  
  ```bash
  php artisan make:middleware ApiAuthMiddleware
  ```  
- Edit logika untuk memvalidasi token di `app/Http/Middleware/ApiAuthMiddleware.php`.

### 6. Membuat Controller
- **UserController**:  
  ```bash
  php artisan make:controller API/UserController
  ```  
  - Implementasikan fungsi untuk registrasi, login, get, update, dan logout.  
- **ContactController** dan **AddressController** dibuat dengan cara serupa untuk menangani CRUD.

### 7. Membuat Route API
- Edit file `routes/api.php`:  
  ```php
  Route::post('/register', [UserController::class, 'register']);
  Route::post('/login', [UserController::class, 'login']);
  Route::middleware('apiauth')->group(function () {
      Route::get('/user', [UserController::class, 'get']);
      Route::put('/user', [UserController::class, 'update']);
      Route::post('/logout', [UserController::class, 'logout']);
      Route::resource('contacts', ContactController::class);
      Route::resource('contacts.addresses', AddressController::class);
  });
  ```

### 8. Menguji API
- Gunakan **Postman** untuk menguji semua endpoint:  
  - POST `/register`, POST `/login`, GET `/user`, dll.

### 9. Dokumentasi API dengan Swagger
- Instal package `l5-swagger`:  
  ```bash
  composer require "darkaonline/l5-swagger"
  ```  
- Konfigurasi dan buat dokumentasi di `config/l5-swagger.php`.  
- Generate dokumentasi:  
  ```bash
  php artisan l5-swagger:generate
  ```

---

## Output yang Diharapkan
- **API yang Berfungsi**:  
  - Endpoint untuk registrasi, login, pengelolaan kontak, dan alamat dapat diakses dan bekerja sesuai spesifikasi.  
- **Dokumentasi API**:  
  - File Swagger (JSON/YAML) yang mendokumentasikan semua endpoint dengan jelas.

---

Dengan mengikuti langkah-langkah ini, tugas mata kuliah Pemrograman API dapat diselesaikan secara lengkap dan menghasilkan output yang sesuai dengan harapan. Pastikan setiap tahap diuji dengan teliti untuk memastikan fungsionalitas dan keakuratan.