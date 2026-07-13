Pertama saya mulai membuat dasar project terlebih dahulu. Saya melakukan setup project Laravel, mengatur koneksi database MySQL, membuat struktur awal dashboard, dan menambahkan fitur pilihan bahasa Inggris dan Indonesia.

Selain itu, saya juga mulai menyiapkan struktur database awal yang nanti akan digunakan untuk data negara, pelabuhan, berita, risk score, dan watchlist. Untuk sementara, fitur yang dibuat masih berupa fondasi awal agar pengembangan di minggu berikutnya lebih mudah dilanjutkan.

Fokus minggu pertama ini adalah membuat project bisa berjalan, memiliki tampilan awal, serta memiliki struktur dasar yang sesuai dengan kebutuhan project.

saya mulai menghubungkan data yang ada di database dengan REST API. Data awal yang digunakan meliputi negara, pelabuhan, berita, dan informasi mata uang.

Fitur yang sudah dibuat:

- Menambahkan data awal negara melalui seeder
- Menambahkan data awal pelabuhan
- Menambahkan data berita sementara
- Membuat API daftar negara
- Membuat API detail negara
- Membuat API data pelabuhan
- Membuat API data berita
- Membuat API mata uang
- Menghubungkan dropdown negara pada dashboard dengan API
- Menampilkan PDB, inflasi, dan mata uang sesuai negara yang dipilih

Endpoint yang sudah tersedia:

- `GET /api/countries`
- `GET /api/countries/{id}`
- `GET /api/ports`
- `GET /api/news`
- `GET /api/currency?country_id={id}`

Untuk tahap ini, data masih menggunakan data awal dari seeder. Integrasi API eksternal dan perhitungan skor risiko akan dikembangkan.

Selanjutnya saya mulai membuat sistem perhitungan risiko rantai pasok. Perhitungan dilakukan berdasarkan lima komponen, yaitu kondisi cuaca, inflasi, perubahan mata uang, sentimen berita, dan kondisi pelabuhan.

Saya menggunakan metode weighted scoring dengan pembagian bobot yang saya tentukan berdasarkan pengaruh setiap indikator terhadap proses impor.

Bobot yang digunakan:

- Risiko cuaca: 27%
- Risiko inflasi: 21%
- Risiko mata uang: 18%
- Risiko berita: 22%
- Risiko pelabuhan: 12%

Kategori risiko:

- 0–24: Low Risk
- 25–49: Moderate Risk
- 50–74: High Risk
- 75–100: Critical Risk

Fitur yang sudah dibuat:

- Endpoint perhitungan risiko negara
- Perhitungan lima komponen risiko
- Penyimpanan hasil perhitungan ke database
- Penentuan label risiko
- Pemberian rekomendasi bisnis
- Menampilkan total skor pada dashboard
- Menampilkan rincian setiap komponen risiko
- Menyesuaikan label dan rekomendasi berdasarkan bahasa yang dipilih

Endpoint yang ditambahkan:

- `GET /api/risk?country_id={id}`

Hasil perhitungan disimpan ke tabel `risk_scores` agar nantinya dapat digunakan untuk membuat grafik perkembangan risiko.
