# Global Supply Chain Risk Intelligence Platform

Global Supply Chain Risk Intelligence Platform adalah aplikasi berbasis Laravel untuk memantau risiko rantai pasok global. Sistem ini dirancang untuk membantu perusahaan dalam menganalisis risiko impor berdasarkan beberapa indikator seperti cuaca, inflasi, kurs mata uang, berita ekonomi/logistik, dan kondisi pelabuhan.

## Studi Kasus

Sebuah perusahaan ingin mengimpor barang dari berbagai negara. Namun, proses impor dapat terganggu oleh beberapa faktor seperti cuaca buruk, perubahan nilai tukar mata uang, konflik geopolitik, kemacetan pelabuhan, dan inflasi negara asal.

Project ini dibuat sebagai dashboard monitoring yang membantu user melihat kondisi risiko suatu negara sebelum mengambil keputusan bisnis.

## Tech Stack

- Laravel
- PHP
- MySQL
- Bootstrap 5
- JavaScript
- Chart.js
- Leaflet.js
- REST API

## Progress Week 1

Pada minggu pertama, pengembangan difokuskan pada fondasi awal project.

Fitur dan struktur yang sudah dibuat:

- Inisialisasi project Laravel
- Konfigurasi database MySQL
- Aktivasi API route Laravel
- Pembuatan struktur awal dashboard
- Pembuatan fitur bilingual English / Indonesia
- Pembuatan middleware language switcher
- Pembuatan migration database awal:
    - countries
    - ports
    - news_caches
    - risk_scores
    - watchlists
- Pembuatan model awal:
    - Country
    - Port
    - NewsCache
    - RiskScore
    - Watchlist
- Pembuatan seeder data awal:
    - CountrySeeder
    - PortSeeder
    - NewsCacheSeeder

## Custom Feature

Project ini menambahkan fitur bilingual dashboard. Secara default sistem menggunakan Bahasa Inggris karena konteks project adalah global supply chain. Namun user juga dapat mengganti bahasa ke Bahasa Indonesia melalui language switcher.

## Fitur yang Akan Dikembangkan

- Global Country Dashboard
- Risk Scoring Engine
- Global Weather Monitoring
- Currency Impact Dashboard
- News Intelligence
- Port Location Dashboard
- Data Visualization Dashboard
- Country Comparison Engine
- Favorite Monitoring List
- Admin Dashboard
