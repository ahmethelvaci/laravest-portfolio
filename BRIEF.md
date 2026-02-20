# PROJE BRİEFİ: Kişisel Varlık ve Portföy Takip Sistemi

## 1. PROJE ÖZETİ

Bu uygulama; kişisel olarak sahip olunan yatırım fonları (TEFAS) ve fiziki altın varlıklarının maliyet, güncel değer ve kar/zarar durumlarını takip etmek için tasarlanmış bir **Web Uygulamasıdır.** Uygulama yalnızca tek bir kullanıcı (ben) tarafından kullanılacaktır.

## 2. TEKNOLOJİ YIĞINI (TECH STACK)

* **Framework:** PHP / Laravel (Sürüm 10 veya 11).
* **Veritabanı:** MySQL.
* **Frontend:** Blade Templates + Tailwind CSS (Mobil uyumlu / Responsive).
* **Güvenlik:** Laravel Breeze/Fortify ile Login sistemi. Kayıt (Register) özelliği ilk kurulumdan sonra kapatılacaktır.

## 3. VERİ KAYNAKLARI VE API YÖNETİMİ

Sistem, API limitlerini korumak adına günde sadece **bir kez** (örneğin gece 23:59’da) fiyat güncellemesi yapmalıdır.

### A. Yatırım Fonları (TEFAS)

* **GET URL:** `http://tefas.res.ahmethelvaci.com/api/daily-stats`
* **Auth:** Header üzerinden `X-API-KEY`.
* **POST URL:** `http://tefas.res.ahmethelvaci.com/api/funds` (Yeni fon eklemek için).
* **Eşleştirme:** `fund.code` alanı üzerinden yapılır, fiyat `price` alanından çekilir.

### B. Altın Fiyatları

* **GET URL:** `https://api.collectapi.com/economy/goldPrice`
* **Auth:** Header üzerinden `authorization: apikey {TOKEN}`.
* **Eşleştirme:** `result[].name` (Gram Altın, Ata Altın vb.) üzerinden yapılır, `selling` fiyatı baz alınır.

## 4. VERİTABANI MİMARİSİ

* **`assets`:** `id, type (GOLD/FUND), code, name`.
* **`transactions`:** `id, asset_id, direction (BUY/SELL), quantity, price, date, remaining_qty`.
* **`daily_prices`:** `id, asset_id, price, date`. (Geçmiş performans grafikleri için).

## 5. KRİTİK İŞ MANTIKLARI

* **FIFO (İlk Giren İlk Çıkar):** Satış işlemleri girildiğinde, sistem en eski tarihli ve `remaining_qty > 0` olan "Alış" kaydından miktar düşmelidir. Kar/zarar hesabı bu maliyet üzerinden hesaplanmalıdır.
* **Ağırlıklı Ortalama:** Portföy genelinde ortalama maliyet hesabı gösterilmelidir.
* **Otomatik Varlık Oluşturma:** İşlem girerken varlık mevcut değilse;
* **Altın ise:** Sadece API listesindeki türlerden seçilebilir.
* **Fon ise:** Kod ve Ad girilir; veritabanına eklenirken aynı zamanda TEFAS API'sine POST isteği atılır.



## 6. ARAYÜZ (UI) GEREKSİNİMLERİ

### **A. Dashboard (Ana Ekran)**

* **Özet Kartları:** Toplam Portföy Değeri, Günlük Kar/Zarar (TL/%), Toplam Kar/Zarar (TL/%).
* **Pasta Grafik:** Varlık türü dağılımı (Altın vs Fon).
* **Trend Grafiği:** `daily_prices` tablosundan beslenen 1 aylık/1 yıllık toplam değer değişimi.
* **Varlık Listesi:** Her varlığın miktarı, ortalama maliyeti, güncel fiyatı ve kar/zarar yüzdesi.

### **B. İşlem Giriş Formu**

* Tarih, İşlem Tipi (Alış/Satış), Varlık Seçimi (Arama/Yeni Ekleme), Miktar, Birim Fiyat.
* Toplam Tutar (Sistem tarafından otomatik hesaplanır).

## 7. GÜVENLİK VE YAYINLAMA

* Uygulama Shared Hosting üzerinde barınacaktır.
* API Key'ler `.env` dosyasında saklanacaktır.
* Giriş yapmamış kullanıcılar Dashboard'a erişemeyecektir (Middleware).
