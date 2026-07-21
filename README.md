# SupplyGuard — Global Supply Chain Risk Intelligence

SupplyGuard adalah aplikasi Laravel untuk menggabungkan data global, mengukur
risiko rantai pasok per negara, memperkirakan keterlambatan pengiriman, dan
menyajikan hasilnya melalui dashboard interaktif.

## Fitur utama

- Analisis 250 negara dan wilayah.
- Lima komponen risiko: cuaca, inflasi, mata uang, sentimen berita, dan pelabuhan.
- Weighted Risk Model yang transparan beserta status kelengkapan data.
- Prediksi rentang keterlambatan, confidence, dan faktor dominan.
- Perbandingan beberapa negara.
- Grafik komponen risiko dan tren kurs menggunakan Chart.js.
- Peta pelabuhan interaktif menggunakan Leaflet dan OpenStreetMap.
- Watchlist pribadi, autentikasi pengguna, serta hak akses admin.
- Admin panel untuk sinkronisasi data, pengguna, berita, pelabuhan, kamus
  sentimen, skor risiko, dan log API.
- Web pengguna multibahasa melalui LibreTranslate lokal.

## Sumber data

| Sumber | Kegunaan | Kredensial |
|---|---|---|
| REST Countries | Master negara, bendera, bahasa, mata uang | Tergantung endpoint/provider |
| World Bank API | GDP, inflasi, dan populasi | Tidak perlu API key |
| Open-Meteo | Cuaca global | Tidak perlu API key |
| Frankfurter | Kurs mata uang | Tidak perlu API key |
| GNews | Berita ekonomi, perdagangan, dan logistik | `GNEWS_API_KEY` |
| UN/LOCODE dataset | Referensi pelabuhan global | Tidak perlu API key |
| OpenStreetMap | Basemap pada visualisasi Leaflet | Tidak perlu API key |

UN/LOCODE diperlakukan sebagai dataset referensi, sedangkan kondisi dan
keterlambatan pelabuhan yang tersedia digunakan sebagai indikator analisis.

## Algoritma risiko

Setiap komponen dinilai pada skala 0–100, lalu dihitung dengan bobot:

| Komponen | Bobot |
|---|---:|
| Cuaca | 27% |
| Inflasi | 21% |
| Mata uang | 18% |
| Sentimen berita | 22% |
| Pelabuhan | 12% |

Komponen yang belum memiliki data tidak otomatis dianggap aman. Sistem
menormalisasi skor berdasarkan bobot yang tersedia dan menampilkan persentase
kelengkapan data. Kategori hasil adalah Low, Moderate, High, dan Critical.

Prediksi keterlambatan menggunakan rata-rata keterlambatan pelabuhan sebagai
baseline, kemudian menambahkan tekanan berdasarkan total risk score. Hasilnya
berupa estimasi utama, rentang minimum–maksimum, confidence, jumlah sampel
pelabuhan, dan tiga faktor risiko dominan. Metode ini bersifat explainable
estimation, bukan klaim machine learning.

## Persyaratan

- PHP 8.2 atau lebih baru
- Composer
- MySQL/MariaDB atau SQLite
- Node.js dan npm
- Docker Desktop + WSL2 jika fitur terjemahan lokal digunakan

## Instalasi

```powershell
git clone <repository-url>
cd supply-chain-risk
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Atur koneksi database dan, bila digunakan, `GNEWS_API_KEY` serta
`REST_COUNTRIES_API_KEY` pada `.env`. Setelah itu:

```powershell
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Aplikasi tersedia di `http://127.0.0.1:8000`.

## Sinkronisasi data

```powershell
php artisan supplyguard:sync-countries
php artisan supplyguard:sync-economy
php artisan supplyguard:sync-currency
php artisan supplyguard:sync-weather
php artisan supplyguard:sync-global-news
php artisan supplyguard:sync-global-ports
php artisan supplyguard:recalculate-risks
```

Untuk menjalankan pembaruan otomatis selama development:

```powershell
php artisan schedule:work
```

Jadwal produksi harus menjalankan `php artisan schedule:run` setiap menit.
Perintah menggunakan pencegahan overlap agar sinkronisasi panjang tidak berjalan
bersamaan.

## Terjemahan lokal

```powershell
docker compose -f compose.translation.yml up -d
php artisan config:clear
php artisan supplyguard:translation-status
```

Model lokal yang disiapkan adalah English, Indonesia, Japanese, Arabic, dan
Simplified Chinese. Proses boot pertama dapat memerlukan beberapa menit karena
model harus diunduh. Cek kondisi container dengan:

```powershell
docker compose -f compose.translation.yml ps
curl.exe http://127.0.0.1:5000/languages
```

## Endpoint aplikasi

| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/api/countries` | Daftar negara |
| GET | `/api/countries/{id}` | Detail negara |
| GET | `/api/economy?country_id={id}` | Data ekonomi |
| GET | `/api/currency?country_id={id}` | Kurs dan tren mata uang |
| GET | `/api/news?country_id={id}` | Berita negara |
| GET | `/api/ports?country_id={id}` | Pelabuhan negara |
| GET | `/api/risk?country_id={id}` | Skor risiko dan prediksi keterlambatan |

## Halaman data pengguna

| Halaman | Fungsi |
|---|---|
| `/data/countries` | Daftar, pencarian, filter wilayah, dan detail negara |
| `/data/ports` | Daftar, pencarian, filter negara, dan detail pelabuhan |
| `/data/sentiments` | Kamus sentimen, bobot, status, dan detail kata |
| `/news` | Feed berita, filter negara/sentimen, dan detail analisis |

Semua halaman tersebut membaca record database aplikasi dan menyediakan tombol
Detail. Data referensi yang belum memiliki kondisi operasional ditandai sebagai
`Unavailable` agar tidak disalahartikan sebagai kondisi aman.

Semua permintaan API dicatat oleh middleware log untuk membantu audit sumber
data dan diagnosis kegagalan.

## Pengujian

```powershell
php artisan test
```

Test suite mencakup risk API dan prediksi keterlambatan, algoritma prediksi,
watchlist pengguna, pembatasan admin, katalog dan detail data, validasi input,
global map, News, serta integrasi dan fallback penerjemahan.

Sebelum presentasi, hasil yang diharapkan adalah seluruh test berstatus lulus:

```powershell
php vendor\phpunit\phpunit\phpunit --colors=never --do-not-cache-result
```

## Struktur penting

- `app/Services/RiskScoringService.php` — weighted risk model.
- `app/Services/DelayPredictionService.php` — estimasi keterlambatan explainable.
- `app/Services/SentimentAnalysisService.php` — analisis sentimen lexicon-based.
- `routes/console.php` — jadwal sinkronisasi data.
- `resources/views/dashboard.blade.php` — dashboard analitik pengguna.
- `resources/views/admin` — dashboard operasional administrator.

## Checklist demo

1. Pastikan database, Laravel, dan LibreTranslate berstatus aktif.
2. Pilih negara dengan kelengkapan data tinggi pada Dashboard.
3. Jelaskan lima komponen, bobot, total skor, dan rekomendasi.
4. Tunjukkan prediksi keterlambatan beserta confidence dan faktor dominan.
5. Tunjukkan grafik, peta pelabuhan, perbandingan negara, dan watchlist.
6. Masuk sebagai admin untuk menunjukkan sinkronisasi dan log API.
7. Demonstrasikan perubahan bahasa pada web pengguna.

## Catatan keamanan

Jangan commit `.env`, API key, password database, atau kredensial akun demo.
Gunakan `.env.example` hanya sebagai daftar nama konfigurasi.
Untuk deployment non-lokal, gunakan `APP_ENV=production`, `APP_DEBUG=false`,
HTTPS, password database yang kuat, dan jalankan `php artisan optimize` setelah
seluruh nilai `.env` final.
