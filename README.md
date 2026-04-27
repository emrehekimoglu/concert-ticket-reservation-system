# Concert Ticket Management System

Bu proje, konser organizasyonları için geliştirilmiş bir konser biletleme ve yönetim sistemidir. Sistem hem ilişkisel veritabanı tasarımını hem de PHP tabanlı bir admin panelini içermektedir.

## Proje Kapsamı

Sistem aşağıdaki bileşenlerden oluşur:

- Oracle SQL ile geliştirilmiş ilişkisel veritabanı
- Konser, mekan, sanatçı, müşteri, rezervasyon, bilet ve ödeme yönetimi
- PHP tabanlı admin paneli
- Authentication (giriş/çıkış) sistemi
- Generic CRUD işlemleri
- Dinamik form yapısı
- Foreign key ilişkileri için otomatik seçim alanları
- CSS ve JavaScript ile temel kullanıcı arayüzü

## Kullanılan Teknolojiler

- Oracle SQL
- PHP
- HTML
- CSS
- JavaScript

## Klasör Yapısı

- `database/` → SQL scriptleri
- `admin_interface/` → PHP tabanlı yönetim paneli
- `docs/` → ER diagram 

## Veritabanı Özellikleri

Sistem aşağıdaki temel varlıkları içermektedir:

- Venue
- Artist
- Event
- TicketCategory
- Customer
- Booking
- Ticket
- Payment

## Admin Panel Özellikleri

- Login / logout sistemi
- Tablolar için listeleme ekranı
- Kayıt ekleme, düzenleme ve silme
- Schema tabanlı dinamik form üretimi
- Foreign key alanları için otomatik dropdown desteği

## Kurulum

### Veritabanı
1. `database/create_tables.sql` dosyasını çalıştırın
2. `database/insert_data.sql` dosyasını çalıştırın
3. İsteğe bağlı olarak `database/queries.sql` ile örnek sorguları test edin

### Admin Interface
1. `admin_interface/config/config.php` dosyasını kendi Oracle ayarlarınıza göre düzenleyin
2. PHP OCI8 eklentisinin kurulu olduğundan emin olun
3. `admin_interface/` klasörü içinde local server başlatın
4. Tarayıcıdan projeyi açın

## Dokümantasyon

- ER diagram: `docs/er-diagram.png`

## Amaç

Bu proje, veritabanı modelleme, backend geliştirme ve yönetim paneli tasarımı konularındaki teknik yetkinlikleri göstermek amacıyla geliştirilmiştir.
