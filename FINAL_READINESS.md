# SupplyGuard — Final Readiness Checklist

Dokumen ini adalah checklist lokal sebelum demo atau pengumpulan. Tidak berisi
API key, password, atau kredensial pengguna.

## Status fitur

- [x] Laravel full-stack, autentikasi pengguna, dan otorisasi admin.
- [x] Enam sumber data utama: REST Countries, World Bank, Open-Meteo,
  Frankfurter, GNews, dan UN/LOCODE; OpenStreetMap digunakan sebagai basemap.
- [x] Master negara global, data pelabuhan, berita, skor risiko, dan kamus
  sentimen tersimpan di database.
- [x] Weighted Risk Model lima komponen dengan total bobot 100%.
- [x] Prediksi keterlambatan explainable: estimasi utama, rentang, confidence,
  baseline historis pelabuhan, dan faktor dominan.
- [x] Dashboard analitik, Chart.js, Leaflet global map, clustering, pencarian,
  marker negara/pelabuhan berbeda, serta popup detail.
- [x] Perbandingan negara dan watchlist per pengguna.
- [x] Halaman pengguna terpisah untuk negara, pelabuhan, sentimen, dan News,
  lengkap dengan pencarian, filter, pagination, dan detail.
- [x] Dashboard admin, sinkronisasi data, manajemen record, dan API log.
- [x] Terjemahan web pengguna melalui LibreTranslate lokal dengan cache dan
  fallback ketika provider tidak tersedia.
- [x] Scheduler sinkronisasi dilengkapi `withoutOverlapping`.
- [x] Test otomatis untuk fitur inti, keamanan akses, katalog, forecasting,
  peta, dan terjemahan.

## Pemeriksaan sebelum demo

```powershell
cd C:\Users\USER\supply-chain-risk
php artisan migrate:status
docker compose -f compose.translation.yml ps
php artisan supplyguard:translation-status
php vendor\phpunit\phpunit\phpunit --colors=never --do-not-cache-result
php artisan optimize:clear
php artisan serve
```

Container penerjemah harus berstatus `healthy`. Setelah server aktif, periksa:

- `http://127.0.0.1:8000`
- `http://127.0.0.1:8000/system-overview`
- `http://127.0.0.1:8000/data/countries`
- `http://127.0.0.1:8000/data/ports`
- `http://127.0.0.1:8000/data/sentiments`
- `http://127.0.0.1:8000/news`

## Urutan presentasi yang disarankan

1. Login dan tunjukkan global map yang langsung dimuat.
2. Cari satu negara, buka detail negara dan beberapa pelabuhan.
3. Jalankan analisis risiko dan jelaskan lima komponen serta bobotnya.
4. Jelaskan prediksi keterlambatan, rentang, confidence, dan faktor dominan.
5. Tunjukkan grafik, perbandingan negara, dan watchlist.
6. Buka katalog negara, pelabuhan, sentimen, dan News beserta detailnya.
7. Ubah bahasa web, lalu tunjukkan status provider terjemahan.
8. Login admin untuk menunjukkan sinkronisasi, manajemen data, dan API log.

## Batas interpretasi data

- Prediksi merupakan estimasi explainable, bukan klaim model machine learning.
- UN/LOCODE adalah referensi lokasi; record tanpa data operasional ditampilkan
  sebagai `Unavailable`.
- Data eksternal dapat berubah dan waktu pembaruannya terlihat melalui field
  sinkronisasi atau API log.
