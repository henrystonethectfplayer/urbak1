# Kalp Pili Cihazı Web Paneli (CTF Görevi)

Bu CTF görevi, zafiyetli bir kalp pili cihazının web yönetim panelini içerir. Görev, gerçek bir tıbbi cihazı hedeflememektedir - yalnızca eğitim amaçlı olarak hazırlanmış bir simülasyondur.

## Senaryo

Urbatek firmasının yeni geliştirdiği Urba K1 modeli kalp pili cihazlarından birine erişim sağladınız. Bu cihaz, aslında bir Raspberry Pi cihazı üzerine inşa edilmiş ve uzaktan yönetim için bir web arayüzüne sahiptir.

Doktorlar, periyodik olarak bu web arayüzüne bağlanarak hastaların kalp ritim verilerini kontrol edebilirler. Ancak, sisteme sızarak cihaz üzerinde daha fazla yetki elde edebilir misiniz?

## Görev

Web panel üzerinden bir zafiyet bulun ve bu zafiyeti kullanarak sisteme SSH ile erişim sağlayın.

## İpuçları

- Web panelinde bazı kayıtları indirebilirsiniz. Bu indirme işleminde bir zafiyet olabilir mi?
- XML işleme ile ilgili yaygın güvenlik zaafiyetlerini düşünün.
- Cihaz, bir Raspberry Pi üzerinde çalışıyor ve SSH erişimi için gerekli anahtar `/home/pi/.ssh/` dizininde olabilir.

## Kurulum

Sistemi çalıştırmak için bir web sunucusu (Apache, Nginx vb.) ve PHP desteği gereklidir. Basit bir şekilde:

```bash
# PHP'nin dahili geliştirme sunucusu ile çalıştırmak için
cd kalp-pili-web
php -S localhost:8000
```

## Hedef

Cihazın SSH özel anahtarını çıkarın ve bu anahtarı kullanarak SSH üzerinden sisteme bağlanın.

## Notlar

Bu CTF görevi, aşağıdaki güvenlik kavramlarını test eder:
- XXE (XML External Entity) Processing
- LFI (Local File Inclusion)
- SSH Authentication with Private Keys

İyi şanslar! 