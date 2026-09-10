# Courier API

REST API untuk mengelola master data **Courier/Kurir** menggunakan Laravel.

Project ini dibuat sebagai technical test dengan fokus pada:

- RESTful API
- CRUD Courier
- Validasi request menggunakan Form Request
- API Resource untuk response
- Search multi-word
- Filter berdasarkan level
- Sorting berdasarkan nama dan tanggal bergabung
- Pagination
- Feature Testing

---

## Tech Stack

- Laravel 13
- PHP 8.3+
- MySQL
- Composer
- PHPUnit / Laravel Testing
- Eloquent ORM

---

## Requirements

Pastikan environment sudah memiliki:

- PHP >= 8.3
- Composer
- MySQL >= 8.0
- Git

Cek versi:

```bash
php -v
composer -V
mysql --version
```

## Instalasi

1. Clone repository dan masuk ke folder project.

```bash
git clone <repository-url>
cd gradin-technical-test
```

2. Install dependency dan siapkan environment Laravel.

```bash
composer install
copy .env.example .env
php artisan key:generate
```

Untuk macOS/Linux, gunakan `cp .env.example .env` sebagai pengganti `copy`.

3. Atur koneksi database di `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=courier_api
DB_USERNAME=root
DB_PASSWORD=
```

Buat database `courier_api`, lalu jalankan migration:

```bash
php artisan migrate
```

4. Jalankan server development:

```bash
php artisan serve
```

API dapat diakses melalui `http://127.0.0.1:8000/api`.

## Konvensi Response

Gunakan header berikut untuk request JSON:

```http
Accept: application/json
Content-Type: application/json
```

Response single courier menggunakan format berikut:

```json
{
	"data": {
		"id": 1,
		"name": "Budi Agung",
		"phone_number": "081234567890",
		"email": "budi@example.com",
		"address": "Malang",
		"level": 2,
		"status": "active",
		"joined_at": "2024-01-10",
		"created_at": "2026-09-10T08:00:00.000000Z",
		"updated_at": "2026-09-10T08:00:00.000000Z"
	}
}
```

Response list memiliki `data`, `links`, dan `meta` dari Laravel pagination:

```json
{
	"data": [],
	"links": {
		"first": "http://127.0.0.1:8000/api/couriers?page=1",
		"last": "http://127.0.0.1:8000/api/couriers?page=2",
		"prev": null,
		"next": "http://127.0.0.1:8000/api/couriers?page=2"
	},
	"meta": {
		"current_page": 1,
		"from": 1,
		"last_page": 2,
		"per_page": 15,
		"to": 15,
		"total": 20
	}
}
```

## Endpoint

Base URL: `http://127.0.0.1:8000/api`

| Method | Endpoint | Keterangan | Status sukses |
| --- | --- | --- | --- |
| `GET` | `/couriers` | Mendapatkan daftar courier | `200 OK` |
| `POST` | `/couriers` | Membuat courier baru | `201 Created` |
| `GET` | `/couriers/{id}` | Mendapatkan detail courier | `200 OK` |
| `PUT` / `PATCH` | `/couriers/{id}` | Memperbarui courier | `200 OK` |
| `DELETE` | `/couriers/{id}` | Menghapus courier | `204 No Content` |

Semua endpoint tidak memerlukan autentikasi pada implementasi saat ini.

### Daftar Courier

```http
GET /api/couriers
```

Query parameter:

| Parameter | Tipe | Default | Keterangan |
| --- | --- | --- | --- |
| `search` | string | - | Mencari nama. Banyak kata dipisahkan spasi dan seluruh kata harus cocok. |
| `level` | string | - | Filter satu atau beberapa level, contoh `2,3`. |
| `sort` | string | `name` | `name` atau `joined_at`; awalan `-` berarti descending. |
| `per_page` | integer | `15` | Jumlah data per halaman. |
| `page` | integer | `1` | Nomor halaman. |

Contoh:

```bash
curl "http://127.0.0.1:8000/api/couriers?search=budi%20agung&level=2,3&sort=-joined_at&per_page=10"
```

Sort yang tidak didukung kembali ke `name` ascending. Level di luar rentang 1-5 diabaikan.

### Hasil Pengujian Search

Pengujian manual menggunakan endpoint sesuai domain Laravel Valet:

```bash
curl "http://gradin-technical-test.test/api/couriers?search=budi%20agung"
```

Hasil pengujian berhasil mengembalikan `1` courier yang namanya mengandung seluruh keyword pencarian:

```json
{
	"data": [
		{
			"id": 1,
			"name": "Agung Budi Santoso",
			"phone_number": "082346458120",
			"email": "bernhard.maud@example.net",
			"address": "94980 Howell Lake\nO'Reillyshire, SC 59382-2087",
			"level": 4,
			"status": "inactive",
			"joined_at": "2022-03-21",
			"created_at": "2026-09-10T03:00:29.000000Z",
			"updated_at": "2026-09-10T03:00:29.000000Z"
		}
	],
	"meta": {
		"current_page": 1,
		"last_page": 1,
		"per_page": 15,
		"to": 1,
		"total": 1
	}
}
```

Hasil tersebut membuktikan bahwa pencarian multi-word `budi agung` dapat menemukan nama `Agung Budi Santoso`.

### Buat Courier

```http
POST /api/couriers
```

Request body:

```json
{
	"name": "Budi Agung",
	"phone_number": "081234567890",
	"email": "budi@example.com",
	"address": "Malang",
	"level": 2,
	"status": "active",
	"joined_at": "2024-01-10"
}
```

```bash
curl -X POST "http://127.0.0.1:8000/api/couriers" -H "Accept: application/json" -H "Content-Type: application/json" -d '{"name":"Budi Agung","phone_number":"081234567890","email":"budi@example.com","address":"Malang","level":2,"status":"active","joined_at":"2024-01-10"}'
```

### Detail Courier

```http
GET /api/couriers/{id}
```

Contoh: `curl "http://127.0.0.1:8000/api/couriers/1"`

### Update Courier

```http
PUT /api/couriers/{id}
```

`PATCH` juga tersedia melalui route resource, tetapi field wajib tetap harus dikirim karena aturan validasi update saat ini. Contoh:

```bash
curl -X PUT "http://127.0.0.1:8000/api/couriers/1" -H "Accept: application/json" -H "Content-Type: application/json" -d '{"name":"Budi Agung Updated","phone_number":"081234567890","email":"budi@example.com","address":"Batu","level":3,"status":"active","joined_at":"2024-01-10"}'
```

### Hapus Courier

```http
DELETE /api/couriers/{id}
```

Contoh: `curl -i -X DELETE "http://127.0.0.1:8000/api/couriers/1"`

Response berhasil tidak memiliki body dan menggunakan status `204 No Content`.

## Field dan Validasi

| Field | Tipe | Wajib | Aturan |
| --- | --- | --- | --- |
| `id` | integer | response | ID unik courier. |
| `name` | string | Ya | Maksimal 255 karakter. |
| `phone_number` | string | Ya | 10-15 digit, boleh diawali `+`, dan harus unik. |
| `email` | string/null | Tidak | Format email valid dan harus unik jika diisi. |
| `address` | string/null | Tidak | Alamat courier. |
| `level` | integer | Ya | Nilai 1 sampai 5. |
| `status` | string/null | Tidak | Hanya `active` atau `inactive`; default database `active`. |
| `joined_at` | date | Ya | Tanggal valid; response diformat `Y-m-d`. |
| `created_at` | datetime | response | Timestamp ISO 8601. |
| `updated_at` | datetime | response | Timestamp ISO 8601. |

## Error Response

Courier yang tidak ditemukan menghasilkan `404 Not Found`.

Request yang gagal validasi menghasilkan `422 Unprocessable Entity`, misalnya:

```json
{
	"message": "The name field is required.",
	"errors": {
		"name": ["The name field is required."],
		"phone_number": ["The phone number field is required."]
	}
}
```

Phone number dan email yang duplikat juga menghasilkan `422`.

## Testing

Jalankan seluruh test:

```bash
php artisan test
```

Atau gunakan script Composer:

```bash
composer test
```

Feature test mencakup pagination, sorting, pencarian multi-word, filter beberapa level, CRUD, validasi field wajib, dan response `404`.

## Pemenuhan Brief Technical Test

| Brief | Implementasi |
| --- | --- |
| Project Laravel baru | Project menggunakan Laravel 13 dengan struktur Laravel standar. |
| Model dan migration courier | Model `Courier` dan migration tabel `couriers` tersedia, dengan field identitas, kontak, level, status, tanggal bergabung, dan timestamps. |
| Level courier 1-5 | Field `level` divalidasi antara 1 sampai 5 dan disimpan sebagai unsigned tiny integer. |
| CRUD courier | `apiResource` menyediakan endpoint `index`, `store`, `show`, `update`, dan `destroy`. |
| Pagination | `GET /api/couriers` menggunakan pagination, default 15 data per halaman. |
| Sorting default nama | Data diurutkan berdasarkan `name` ascending secara default. |
| Override sorting tanggal daftar | Gunakan `sort=joined_at` atau `sort=-joined_at`. |
| Search multi-word | Gunakan `search=budi%20agung`; setiap keyword dicocokkan pada nama courier. |
| Filter level | Gunakan `level=2,3` untuk mengambil courier level 2 atau 3. |
| Show semua data courier | `GET /api/couriers/{id}` mengembalikan seluruh field melalui `CourierResource`. |
| Validasi store dan update | Form Request memvalidasi field wajib, format nomor telepon, email, level, status, tanggal, dan keunikan kontak. |
| Test penyimpanan | Feature test memverifikasi courier tersimpan ke database setelah store dan update. |
| Test penghapusan | Feature test memverifikasi courier terhapus dari database setelah destroy. |
| Upload ke GitHub | Repository: https://github.com/Nilfgard13/technical-test-fachrizal |

## Penilaian

- **Pemahaman brief:** seluruh kebutuhan endpoint, pencarian, filter, sorting, pagination, dan validasi diimplementasikan.
- **Kerapian program:** controller menggunakan Form Request, API Resource, route resource, dan Eloquent model scopes.
- **Best practices:** validasi dipisahkan dari controller, response memiliki format konsisten, dan parameter sorting dibatasi pada field yang diizinkan.
- **Dokumentasi dan testing:** README menjelaskan setup serta kontrak API, sementara feature test mencakup alur CRUD dan fitur index utama.
