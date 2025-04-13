<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalp Pili Yönetim Paneli</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Urbatek Kalp Pili Yönetim Paneli</h1>
            <p>Model: Urba K1</p>
        </header>

        <div class="dashboard">
            <div class="status-panel">
                <h2>Cihaz Durumu</h2>
                <div class="status-info">
                    <p>Bağlantı Durumu: <span class="status-disconnected">Bağlı Değil</span> (Debug)</p>
                    <p>Pil Durumu: <span class="battery">85%</span></p>
                    <p>Son Kontrol: <span class="last-check">08.04.2025 14:30</span></p>
                </div>
            </div>

            <div class="heartbeat-panel">
                <h2>Kalp Ritmi Monitörü</h2>
                <div class="heartbeat-visual">
                    <div class="heartbeat-animation">
                        <div class="heartbeat-line"></div>
                    </div>
                </div>
                <div class="current-bpm">
                    <span id="bpm-value">-- BPM</span>
                    <p class="note">* Gerçek zamanlı veri aktif değil. Geçmiş kayıtları inceleyiniz.</p>
                </div>
            </div>

            <div class="records-panel">
                <h2>Geçmiş Kayıtlar</h2>
                <table class="records-table">
                    <thead>
                        <tr>
                            <th>Tarih</th>
                            <th>Süre</th>
                            <th>Ort. BPM</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>08.04.2025</td>
                            <td>45 dakika</td>
                            <td>76</td>
                        </tr>
                        <tr>
                            <td>05.04.2025</td>
                            <td>60 dakika</td>
                            <td>82</td>
                        </tr>
                        <tr>
                            <td>01.04.2025</td>
                            <td>30 dakika</td>
                            <td>78</td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="date-selector-panel">
                    <h3>Kayıt İndir</h3>
                    <form id="record-download-form" action="records.php" method="POST">
                        <div class="date-inputs">
                            <div class="date-input-group">
                                <label for="start-date">Başlangıç Tarihi:</label>
                                <input type="date" id="start-date" name="start_date" value="2025-04-01" required>
                            </div>
                            <div class="date-input-group">
                                <label for="end-date">Bitiş Tarihi:</label>
                                <input type="date" id="end-date" name="end_date" value="2025-04-08" required>
                            </div>
                        </div>
                        <button type="button" id="generate-xml-btn" class="download-btn">XML Oluştur ve İndir</button>
                        
                        <input type="hidden" id="xml-data" name="xml_data" value="">
                    </form>
                </div>
            </div>
        </div>

        <footer>
            <p>Urbatek Sağlık Teknolojileri &copy; 2025</p>
        </footer>
    </div>
    
    <script src="heartbeat.js"></script>
</body>
</html> 