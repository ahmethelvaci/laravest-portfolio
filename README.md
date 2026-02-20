# LaraVest Portfolio

**LaraVest Portfolio**, kişisel yatırım fonlarınızı (TEFAS) ve fiziki altın varlıklarınızı tek bir merkezden takip etmeniz için geliştirilmiş, PHP/Laravel tabanlı bir varlık yönetim panelidir.

## ✨ Özellikler

* **Varlık Takibi:** Yatırım fonları ve fiziki altın (Gram, Çeyrek, Ata vb.) yönetimi.
* **FIFO Maliyet Hesabı:** Vergi ve kar/zarar analizleri için "İlk Giren İlk Çıkar" algoritması.
* **Otomatik Fiyat Güncelleme:** - **TEFAS:** Özel REST API üzerinden günlük fon fiyatları.
* **Gold:** CollectAPI üzerinden günlük altın kurları.


* **Responsive Dashboard:** Mobil uyumlu arayüz ile her yerden portföy kontrolü.
* **İstatistiksel Analiz:** Varlık dağılımı (Pasta grafik) ve toplam portföy trendi (Çizgi grafik).

## 🚀 Teknik Altyapı

* **Backend:** Laravel 11 / PHP 8.2+
* **Frontend:** Tailwind CSS / Blade Templates
* **Database:** MySQL (Relations: Assets, Transactions, DailyPrices)
* **Auth:** Laravel Breeze

## 🛠️ Kurulum

1. Projeyi klonlayın:
```bash
git clone https://github.com/ahmethelvaci/laravest-portfolio.git

```


2. Bağımlılıkları yükleyin:
```bash
composer install
npm install && npm run build

```


3. `.env` dosyasını yapılandırın:
```env
DB_DATABASE=laravest_db
TEFAS_API_KEY=your_key_here
GOLD_API_KEY=your_collectapi_token_here

```


4. Veritabanını oluşturun ve verileri taşıyın:
```bash
php artisan migrate

```



## 📊 Veritabanı Yapısı

Proje temel olarak 3 ana tablo üzerine kuruludur:

* `assets`: Varlık tanımları ve türleri.
* `transactions`: Alış/Satış hareketleri ve FIFO için kalan miktarlar.
* `daily_prices`: Geçmişe dönük performans analizi için günlük kapanış fiyatları.

## 🔒 Güvenlik

* Uygulama kişisel kullanım içindir; bu nedenle kayıt (Register) özelliği varsayılan olarak kapalıdır.
* Tüm API anahtarları `.env` dosyasında korunur.
* Dashboard erişimi `auth` middleware ile korunmaktadır.
