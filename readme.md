# Submission Food Api

## 1. Problem

Ada sebuah perusahaan retail produk makanan yang meminta untuk dibuatkan sebuah API dengan requirement sebagai berikut:
a. API yang dimaksud adalah API untuk menyimpan / mengubah data produk dalam database dengan kondisi jika masa expired kurang dari 10 hari dari masa simpan / ubah maka produk akan mendapatkan diskon 10% jika sudah expired maka data akan tetap tersimpan dengan status expired dan memberikan respon status produk expired. ✅
b. API untuk read data produk dengan parameter code/qrcode produk, jika data ditemukan akan menampilkan detail produk jika tidak akan merespon produk tidak ditemukan ✅
c. API untuk read list data produk dengan paging. ✅
d. API untuk delete produk dengan catatan produk yang didelete suatu saat akan bisa direstore kembali jika suatu saat diperlukan. ✅

## 2. Solution

### Database

| No  | Field       | Description                                          |
| --- | ----------- | ---------------------------------------------------- |
| 1   | id          | id for table food                                    |
| 2   | qr_code     | qr code for table food                               |
| 3   | name        | name for table food                                  |
| 4   | price       | price for table food                                 |
| 5   | expired     | expired time for table food                          |
| 6   | is_delete   | is_delete to check if this field has been deleted    |
| 7   | is_discount | is_discount to check if this field has been discount |

### Documentation

##### Note

Sebelum Menjalankan API Untuk Pertama Kali, Edit Terlebih File di Direktori /config/Database.php sesuai dengan Environment Database

```php
private $host = "localhost";
private $db_name = "tes";
private $username = "root";
private $password = "";
public $conn;
```

Kemudian Jalankan Terlebih Endpoint Berikut :

```php
GET /migrate.php
```

#### A. Get All Data

##### Endpoint

```php
GET /read.php
```

##### Response

```JSON
{
    "data": [
        {
            "id": "11",
            "qr_code": "121221",
            "name": "semangka",
            "status": "EXPIRED"
        },
        {
            "id": "16",
            "qr_code": "1212211",
            "name": "jeruk",
            "price": "2700",
            "expired": "2023-08-15"
        },
        {
            "id": "17",
            "qr_code": "11212211",
            "name": "apel",
            "price": "30000",
            "expired": "2023-08-17"
        },
    ]
}
```

#### B. Get Data By Qr Code

##### Endpoint

```php
GET /read_one.php?qr_code=1212211
```

##### Response

```JSON
{
    "data": {
        "id": "16",
        "name": "jeruk",
        "qr_code": "1212211",
        "price": "2700",
        "expired": "2023-08-15"
    }
}
```

#### C. Get All Data Using Paging

##### Endpoint

```php
GET /read.php?halaman=2
```

##### Response

```JSON
{
    "page": 2,
    "data": [
        {
            "id": "20",
            "qr_code": "8512775",
            "name": "anggur",
            "price": "30000",
            "expired": "2023-08-20"
        },
        {
            "id": "21",
            "qr_code": "1212188",
            "name": "manggis",
            "price": "30000",
            "expired": "2023-08-20"
        },
    ]
}
```

#### D. Create New Data

##### Endpoint

```php
POST /create.php
```

##### Request

```JSON
{
    "qr_code" : "09997877", // Required
    "name" : "sirsak", // Required
    "price" : 23000, // Required
    "expired" : "2023-08-16" // Required
}
```

##### Response

```JSON
{
    "pesan": "Food Berhasil Ditambahkan"
}
```
