# Alur Pendaftaran Ujian Model UTBK

Dokumen ini berisi logika bisnis yang sudah disesuaikan dengan struktur project saat ini, termasuk alur dari dashboard user, proses verifikasi admin, data ujian, data ruangan, sesi ujian, plotting peserta, dan mekanisme agar kartu ujian bisa diunduh dengan benar.

## 1. Dasar Sistem yang Sudah Ada

Sebelum menentukan flow lanjutan, logika harus mengikuti data yang sudah ada di project saat ini.

### Data dan proses yang sudah tersedia

1. Akun peserta menggunakan tabel `users`.
2. Dokumen peserta menggunakan tabel `user_documents`.
3. Peserta memiliki tahapan submit dokumen melalui `documents_submitted_at`.
4. Admin sudah memiliki proses verifikasi dokumen peserta.
5. Data ujian sudah mulai dibangun melalui:
   `exams`
   `exam_rooms`
   `exam_sessions`
6. User dashboard sudah memiliki timeline:
   `Registrasi`
   `Upload Dokumen`
   `Verifikasi Admin`
   `Uji Kompetensi`

### Implikasi ke flow

Karena alur dasar sudah ada, maka proses berikutnya tidak perlu membuat alur baru dari nol. Logika yang benar adalah melanjutkan flow yang sudah dibuat oleh branch `vivi` dan `nanas`, lalu menambahkan tahap penjadwalan, plotting peserta, dan kartu ujian.

## 2. Alur Besar Sistem

Alur utamanya adalah:

1. Peserta registrasi akun.
2. Peserta login.
3. Peserta upload dokumen.
4. Peserta submit dokumen.
5. Admin memeriksa dokumen peserta.
6. Admin memverifikasi peserta.
7. Admin membuat data ruangan.
8. Admin membuat data ujian.
9. Admin membuat atau menentukan sesi ujian.
10. Sistem mengecek data peserta yang siap dijadwalkan.
11. Sistem melakukan plotting peserta ke ujian, sesi, ruangan, dan kursi.
12. Sistem mengaktifkan kartu ujian.
13. Peserta login dan berhasil mengunduh kartu ujian.

## 3. Alur Peserta Sesuai Timeline User Dashboard

Flow peserta harus mengikuti timeline yang saat ini sudah tampil di dashboard user.

### 3.1 Registrasi

1. Peserta membuat akun.
2. Sistem menyimpan akun ke tabel `users`.
3. Status awal peserta:
   `role = user`
   `status = Active`

### 3.2 Upload Dokumen

1. Peserta login ke dashboard.
2. Peserta mengunggah seluruh dokumen yang dipersyaratkan.
3. Dokumen disimpan ke tabel `user_documents`.
4. Status dokumen pada tahap ini masih berupa dokumen yang sudah diupload, tetapi belum final diverifikasi.

### 3.3 Submit Dokumen

1. Setelah semua dokumen lengkap, peserta menekan tombol kirim atau submit.
2. Sistem mengisi `documents_submitted_at`.
3. Setelah submit, dokumen tidak boleh diubah lagi oleh peserta.

### 3.4 Menunggu Verifikasi Admin

1. Peserta masuk ke tahap `Verifikasi Admin`.
2. Admin memeriksa seluruh dokumen.
3. Jika seluruh dokumen valid, peserta dianggap lolos verifikasi.
4. Jika belum valid, peserta belum boleh masuk ke tahap ujian.

### 3.5 Masuk Tahap Uji Kompetensi

1. Setelah verifikasi selesai, peserta masuk ke tahap `Uji Kompetensi`.
2. Pada tahap ini peserta belum langsung bisa download kartu.
3. Peserta baru bisa melihat kartu jika sudah mendapatkan penjadwalan lengkap.

## 4. Alur Login dan Tugas Admin

### 4.1 Login Admin

1. Admin membuka halaman login.
2. Admin mengisi email dan password.
3. Sistem memvalidasi akun.
4. Jika `role = admin` dan `status = Active`, admin masuk ke dashboard admin.
5. Jika bukan admin, akses ke area admin ditolak.
6. Jika akun `Inactive`, login ditolak.

### 4.2 Tugas Admin Setelah Login

Setelah login, admin memiliki alur kerja berikut:

1. Memeriksa data peserta.
2. Memeriksa dokumen peserta yang sudah disubmit.
3. Memverifikasi peserta.
4. Mengelola ruangan ujian.
5. Mengelola data ujian.
6. Mengelola sesi ujian.
7. Melakukan plotting peserta.
8. Mengaktifkan kartu ujian peserta.

## 5. Logika Verifikasi Peserta

Peserta dianggap siap dijadwalkan hanya jika seluruh kondisi berikut terpenuhi:

1. Akun peserta aktif.
2. Semua dokumen wajib sudah terupload.
3. Peserta sudah menekan submit dokumen.
4. Semua dokumen wajib sudah diverifikasi admin.

### Status logika peserta

Agar alur mudah dibaca sistem, status peserta dapat dipahami menjadi:

1. `registered`
   Peserta baru memiliki akun.
2. `documents_uploaded`
   Peserta sudah upload sebagian atau seluruh dokumen.
3. `documents_submitted`
   Peserta sudah final submit dokumen.
4. `verified`
   Dokumen sudah lolos verifikasi admin.
5. `scheduled`
   Peserta sudah mendapat jadwal ujian lengkap.
6. `card_ready`
   Kartu ujian sudah siap diunduh.

## 6. Logika Data Ruangan

Karena data ruangan sudah ada pada tabel `exam_rooms`, maka flow harus menyesuaikan tabel itu.

### Data utama ruangan

Minimal data yang dipakai:

1. `id`
2. `name`
3. `capacity`

### Pengembangan logika ruangan

Walaupun saat ini tabel ruangan baru memiliki `name` dan `capacity`, secara logika sistem harus membaca:

1. `kapasitas_total`
2. `jumlah_peserta_terplot`
3. `sisa_kursi`

### Rumus ruangan

1. `jumlah_peserta_terplot = total assignment peserta pada ruangan itu`
2. `sisa_kursi = capacity - jumlah_peserta_terplot`

### Aturan ruangan

1. Ruangan tidak boleh menerima peserta jika `sisa_kursi = 0`.
2. Ruangan tidak boleh dihapus jika sudah dipakai penjadwalan ujian.
3. Satu ruangan bisa dipakai lebih dari satu ujian hanya jika tanggal dan sesi tidak bentrok.

## 7. Logika Data Ujian dan Sesi

Karena saat ini sudah ada tabel `exams` dan `exam_sessions`, maka flow harus mengikuti struktur tersebut.

### Data ujian

Tabel `exams` saat ini sudah memuat:

1. `name`
2. `start_date`
3. `end_date`
4. relasi ke ruangan

### Data sesi

Tabel `exam_sessions` saat ini memuat:

1. `exam_id`
2. `order`
3. `start_time`
4. `end_time`

### Implikasi logika

Artinya, penjadwalan peserta tidak cukup hanya membaca ujian dan ruangan. Sistem juga harus membaca sesi ujian, karena plotting peserta sebaiknya ditempatkan ke:

1. ujian
2. sesi
3. ruangan
4. nomor kursi

## 8. Tahap Admin Setelah Membuat Ruangan

Bagian ini penting untuk menyesuaikan flow dengan yang sudah ada.

Setelah admin membuat data ruangan, tahapan berikutnya yang lebih tepat bukan langsung menaruh peserta secara manual satu per satu, melainkan:

1. Admin membuat data ujian.
2. Admin menentukan sesi ujian.
3. Sistem mengecek database peserta yang sudah lolos verifikasi.
4. Sistem menampilkan hanya peserta yang siap dijadwalkan.
5. Admin menjalankan proses plotting peserta.

### Kenapa harus cek database dulu

Karena user sudah memiliki timeline dan status dokumen, maka sistem harus membaca database terlebih dahulu agar:

1. peserta yang belum submit dokumen tidak ikut dijadwalkan
2. peserta yang belum diverifikasi tidak ikut dijadwalkan
3. peserta yang sudah pernah dijadwalkan tidak terplot dua kali
4. kursi ruangan tidak terisi melebihi kapasitas

## 9. Logika Cek Database Sebelum Plotting

Sebelum peserta dipetakan ke ujian, sistem harus melakukan pengecekan berikut.

### 9.1 Filter peserta yang layak dijadwalkan

Peserta yang boleh masuk daftar plotting hanyalah peserta yang:

1. `role = user`
2. `status = Active`
3. `documents_submitted_at` tidak kosong
4. seluruh dokumen wajib sudah `verified`
5. belum memiliki assignment ujian aktif

### 9.2 Filter ujian yang bisa dipakai

Ujian yang bisa dipakai adalah ujian yang:

1. memiliki minimal satu ruangan
2. memiliki sesi ujian
3. tanggal ujian masih valid
4. belum penuh

### 9.3 Filter ruangan yang bisa dipakai

Ruangan yang bisa dipakai adalah ruangan yang:

1. termasuk ke ujian yang dipilih
2. memiliki sisa kapasitas
3. tidak bentrok dengan sesi lain pada waktu yang sama

## 10. Logika Pengeplotan Peserta

Ini adalah inti logic agar sistem dapat membaca dan menempatkan peserta secara benar.

### 10.1 Tujuan plotting

Plotting peserta berarti sistem menentukan secara otomatis:

1. peserta masuk ke ujian mana
2. peserta masuk ke sesi mana
3. peserta masuk ke ruangan mana
4. peserta mendapatkan nomor kursi berapa
5. peserta mendapatkan nomor ujian

### 10.2 Urutan kerja plotting

Urutan logic yang disarankan:

1. Ambil daftar peserta yang sudah `verified`.
2. Urutkan peserta berdasarkan waktu submit atau waktu verifikasi.
3. Ambil daftar ujian aktif.
4. Ambil sesi dari ujian tersebut.
5. Ambil ruangan dari ujian tersebut.
6. Hitung kapasitas efektif setiap ruangan.
7. Tempatkan peserta satu per satu ke slot yang masih kosong.
8. Simpan hasil plotting sebagai assignment peserta ujian.

### 10.3 Prioritas urutan plotting

Agar adil dan mudah dibaca, prioritas urutan peserta sebaiknya:

1. berdasarkan waktu `documents_submitted_at`
2. jika sama, berdasarkan waktu verifikasi
3. jika masih sama, berdasarkan `id` peserta paling kecil

### 10.4 Prioritas urutan slot

Slot penempatan dibaca berurutan:

1. tanggal ujian terdekat
2. sesi paling awal
3. ruangan berdasarkan nama atau urutan input
4. kursi dari nomor terkecil

### 10.5 Logika nomor kursi

Untuk setiap kombinasi `ujian + sesi + ruangan`:

1. sistem membaca jumlah peserta yang sudah ditempatkan
2. nomor kursi berikutnya adalah `jumlah_peserta_terplot + 1`
3. jika nomor kursi melebihi `capacity`, ruangan ditutup untuk plotting berikutnya

### 10.6 Kondisi gagal plotting

Plotting harus ditolak jika:

1. tidak ada ujian aktif
2. ujian belum memiliki sesi
3. ujian belum memiliki ruangan
4. total kapasitas ruangan lebih kecil dari total peserta yang akan dijadwalkan

Jika kondisi ini terjadi, sistem harus menampilkan pesan bahwa kapasitas belum cukup sehingga admin perlu menambah ruangan atau membagi ke sesi lain.

## 11. Struktur Logika Assignment Peserta

Karena saat ini project sudah memiliki `exams`, `exam_rooms`, dan `exam_sessions`, maka bagian yang masih kurang secara logic adalah data assignment peserta.

### Data assignment yang dibutuhkan

Sistem nantinya perlu menyimpan data seperti:

1. `user_id`
2. `exam_id`
3. `exam_session_id`
4. `exam_room_id`
5. `seat_number`
6. `exam_number`
7. `assignment_status`
8. `card_generated_at`
9. `card_downloaded_at`

### Fungsi assignment

Data assignment ini menjadi penghubung antara:

1. peserta
2. ujian
3. sesi
4. ruangan
5. kartu ujian

Tanpa data assignment, sistem belum bisa memastikan peserta duduk di mana dan belum bisa membentuk kartu ujian yang valid.

## 12. Logika Kartu Ujian

Kartu ujian tidak cukup hanya berdasarkan status verifikasi. Kartu ujian harus membaca data assignment peserta.

### Kartu ujian baru boleh aktif jika

1. peserta sudah `verified`
2. peserta sudah memiliki `exam_id`
3. peserta sudah memiliki `exam_session_id`
4. peserta sudah memiliki `exam_room_id`
5. peserta sudah memiliki `seat_number`
6. nomor ujian peserta sudah terbentuk

### Isi kartu ujian

Data minimal yang harus tampil:

1. nomor ujian
2. nama peserta
3. email atau nomor HP
4. nama ujian
5. tanggal ujian
6. sesi ujian
7. jam ujian
8. nama ruangan
9. nomor kursi
10. status verifikasi

## 13. Logika Agar Download Kartu Ujian Berhasil

Bagian ini penting karena tombol download pada user tidak boleh aktif terlalu cepat.

### Tombol download hanya aktif jika

1. peserta sudah lolos verifikasi admin
2. peserta sudah memiliki assignment ujian lengkap
3. data ujian, sesi, dan ruangan masih valid
4. kartu sudah berstatus `card_ready`

### Flow download kartu ujian

1. Peserta login ke dashboard.
2. Sistem membaca apakah peserta memiliki assignment aktif.
3. Sistem membaca apakah kartu sudah siap.
4. Jika siap, tombol `Unduh Kartu Uji Kompetensi` aktif.
5. Saat tombol diklik, sistem membentuk file kartu ujian dari data assignment.
6. File berhasil dikirim ke user untuk diunduh.
7. Waktu unduh dapat disimpan sebagai `card_downloaded_at`.

### Kondisi tombol harus nonaktif

Tombol download harus tetap nonaktif jika:

1. peserta belum diverifikasi
2. peserta belum dijadwalkan
3. data sesi belum lengkap
4. ruangan belum ditentukan
5. nomor kursi belum terbentuk

### Pesan yang ditampilkan ke user

Jika kartu belum siap, pesan yang tepat adalah:

`Kartu ujian belum tersedia. Silakan tunggu verifikasi dan penjadwalan dari admin.`

## 14. Flow Admin yang Disarankan Setelah Ruangan Dibuat

Agar lebih rapi dan sesuai data yang sudah ada, flow admin sebaiknya menjadi:

1. Admin membuat ruangan ujian.
2. Admin membuat ujian.
3. Admin membuat sesi ujian.
4. Sistem membaca peserta yang statusnya sudah siap dijadwalkan.
5. Admin menjalankan plotting peserta.
6. Sistem menyimpan assignment peserta.
7. Sistem mengaktifkan kartu ujian.
8. Peserta dapat mengunduh kartu ujian dari dashboard.

## 15. Ringkasan Logic Inti

Ringkasan akhir logic sistem ini adalah:

1. Peserta register.
2. Peserta upload dokumen.
3. Peserta submit dokumen.
4. Admin verifikasi dokumen.
5. Admin membuat ruangan.
6. Admin membuat ujian.
7. Admin membuat sesi.
8. Sistem mengecek database untuk mencari peserta yang benar-benar siap.
9. Sistem memplot peserta ke ujian, sesi, ruangan, dan kursi.
10. Sistem membentuk assignment peserta.
11. Kartu ujian diaktifkan.
12. Peserta berhasil mengunduh kartu ujian.

## 16. Catatan Penyesuaian dengan Project Saat Ini

Flow ini sudah diselaraskan dengan kondisi project yang ada sekarang:

1. alur peserta mengikuti dashboard user yang sudah memiliki timeline
2. verifikasi mengikuti mekanisme dokumen yang sudah ada
3. ruangan mengikuti tabel `exam_rooms`
4. ujian mengikuti tabel `exams`
5. sesi mengikuti tabel `exam_sessions`
6. bagian yang masih perlu dilanjutkan nanti adalah assignment peserta dan kartu ujian

Dengan begitu, pengembangan berikutnya tidak bertabrakan dengan struktur yang sudah dibuat di branch sebelumnya, tetapi justru melanjutkan fondasi yang sudah ada.
