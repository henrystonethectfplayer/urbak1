<?php
header('Content-Type: application/xml');
header('Content-Disposition: attachment; filename="heartbeat_record.xml"');

$requestMethod = $_SERVER['REQUEST_METHOD'];
$contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';

if ($requestMethod === 'POST') {
    if (strpos($contentType, 'application/xml') !== false) {
        $xmlData = file_get_contents('php://input');
    } else if (isset($_POST['xml_data'])) {
        $xmlData = $_POST['xml_data'];
    } else {
        header('HTTP/1.0 400 Bad Request');
        echo 'XML verisi bulunamadı.';
        exit;
    }
    
    $startDate = '';
    $endDate = '';
    
    $dom = new DOMDocument();
    $dom->loadXML($xmlData, LIBXML_NOENT | LIBXML_DTDLOAD); 
    
    $xpath = new DOMXPath($dom);
    $startDateNodes = $xpath->query("//start_date");
    $endDateNodes = $xpath->query("//end_date");
    
    $patientNameNodes = $xpath->query("//patient/name");
    $patientIdNodes = $xpath->query("//patient/id");
    $modelNodes = $xpath->query("//device/model");
    $serialNodes = $xpath->query("//device/serial");
    $firmwareNodes = $xpath->query("//device/firmware");
    
    if ($startDateNodes->length > 0) {
        $startDate = $startDateNodes->item(0)->nodeValue;
    }
    
    if ($endDateNodes->length > 0) {
        $endDate = $endDateNodes->item(0)->nodeValue;
    }
    
    $patientName = "Hasta";
    $patientId = "12345";
    $model = "Urba K1";
    $serial = "RPi-CT200-9876";
    $firmware = "2.3.1";
    
    if ($patientNameNodes->length > 0) {
        $patientName = $patientNameNodes->item(0)->nodeValue;
    }
    
    if ($patientIdNodes->length > 0) {
        $patientId = $patientIdNodes->item(0)->nodeValue;
    }
    
    if ($modelNodes->length > 0) {
        $model = $modelNodes->item(0)->nodeValue;
    }
    
    if ($serialNodes->length > 0) {
        $serial = $serialNodes->item(0)->nodeValue;
    }
    
    if ($firmwareNodes->length > 0) {
        $firmware = $firmwareNodes->item(0)->nodeValue;
    }
    
    $records = '';
    
    if ($startDate && $endDate) {
        $records = generateRecords($startDate, $endDate);
    }
    
    $responseXml = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE heartbeat [
  <!ELEMENT heartbeat (patient, device, records)>
  <!ELEMENT patient (name, id)>
  <!ELEMENT name (#PCDATA)>
  <!ELEMENT id (#PCDATA)>
  <!ELEMENT device (model, serial, firmware)>
  <!ELEMENT model (#PCDATA)>
  <!ELEMENT serial (#PCDATA)>
  <!ELEMENT firmware (#PCDATA)>
  <!ELEMENT records (record*)>
  <!ELEMENT record (timestamp, duration, avg_bpm, min_bpm, max_bpm)>
  <!ELEMENT timestamp (#PCDATA)>
  <!ELEMENT duration (#PCDATA)>
  <!ELEMENT avg_bpm (#PCDATA)>
  <!ELEMENT min_bpm (#PCDATA)>
  <!ELEMENT max_bpm (#PCDATA)>
]>
<heartbeat>
  <patient>
    <name>' . htmlspecialchars($patientName) . '</name>
    <id>' . htmlspecialchars($patientId) . '</id>
  </patient>
  <device>
    <model>' . htmlspecialchars($model) . '</model>
    <serial>' . htmlspecialchars($serial) . '</serial>
    <firmware>' . htmlspecialchars($firmware) . '</firmware>
  </device>
  <records>
    ' . $records . '
  </records>
</heartbeat>';
    
    echo $responseXml;
    exit;
}

$requestedFile = isset($_GET['file']) ? $_GET['file'] : '';

function generateRecords($startDate, $endDate) {
    $startDateObj = DateTime::createFromFormat('d.m.Y', $startDate);
    $endDateObj = DateTime::createFromFormat('d.m.Y', $endDate);
    
    if (!$startDateObj || !$endDateObj) {
        return '<record>
      <timestamp>Geçersiz tarih aralığı</timestamp>
      <duration>0</duration>
      <avg_bpm>0</avg_bpm>
      <min_bpm>0</min_bpm>
      <max_bpm>0</max_bpm>
    </record>';
    }
    
    $records = '';
    
    $specialRecords = [
        '08.04.2025' => ['duration' => 45, 'avg_bpm' => 76, 'min_bpm' => 68, 'max_bpm' => 94],
        '05.04.2025' => ['duration' => 60, 'avg_bpm' => 82, 'min_bpm' => 71, 'max_bpm' => 98],
        '01.04.2025' => ['duration' => 30, 'avg_bpm' => 78, 'min_bpm' => 65, 'max_bpm' => 88]
    ];
    
    $currentDate = clone $startDateObj;
    
    while ($currentDate <= $endDateObj) {
        $dateKey = $currentDate->format('d.m.Y');
        $timestamp = $dateKey . ' ' . sprintf('%02d', rand(0, 23)) . ':' . sprintf('%02d', rand(0, 59)) . ':00';
        
        if (isset($specialRecords[$dateKey])) {
            $duration = $specialRecords[$dateKey]['duration'];
            $avgBpm = $specialRecords[$dateKey]['avg_bpm'];
            $minBpm = $specialRecords[$dateKey]['min_bpm'];
            $maxBpm = $specialRecords[$dateKey]['max_bpm'];
        } else {
            $duration = rand(30, 60);
            $avgBpm = rand(70, 85);
            $minBpm = $avgBpm - rand(5, 15);
            $maxBpm = $avgBpm + rand(5, 15);
        }
        
        $records .= '<record>
      <timestamp>' . $timestamp . '</timestamp>
      <duration>' . $duration . '</duration>
      <avg_bpm>' . $avgBpm . '</avg_bpm>
      <min_bpm>' . $minBpm . '</min_bpm>
      <max_bpm>' . $maxBpm . '</max_bpm>
    </record>';
        
        $currentDate->modify('+1 day');
    }
    
    return $records;
}

$xmlData = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE heartbeat [
  <!ELEMENT heartbeat (patient, device, records)>
  <!ELEMENT patient (name, id)>
  <!ELEMENT name (#PCDATA)>
  <!ELEMENT id (#PCDATA)>
  <!ELEMENT device (model, serial, firmware)>
  <!ELEMENT model (#PCDATA)>
  <!ELEMENT serial (#PCDATA)>
  <!ELEMENT firmware (#PCDATA)>
  <!ELEMENT records (record+)>
  <!ELEMENT record (timestamp, duration, avg_bpm, min_bpm, max_bpm)>
  <!ELEMENT timestamp (#PCDATA)>
  <!ELEMENT duration (#PCDATA)>
  <!ELEMENT avg_bpm (#PCDATA)>
  <!ELEMENT min_bpm (#PCDATA)>
  <!ELEMENT max_bpm (#PCDATA)>
]>
<heartbeat>
  <patient>
    <name>Hasta</name>
    <id>12345</id>
  </patient>
  <device>
    <model>Urba K1</model>
    <serial>RPi-CT200-9876</serial>
    <firmware>2.3.1</firmware>
  </device>
  <records>
    <record>
      <timestamp>08.04.2025 14:30:00</timestamp>
      <duration>45</duration>
      <avg_bpm>76</avg_bpm>
      <min_bpm>68</min_bpm>
      <max_bpm>94</max_bpm>
    </record>
    <record>
      <timestamp>05.04.2025 10:15:00</timestamp>
      <duration>60</duration>
      <avg_bpm>82</avg_bpm>
      <min_bpm>71</min_bpm>
      <max_bpm>98</max_bpm>
    </record>
    <record>
      <timestamp>01.04.2025 18:45:00</timestamp>
      <duration>30</duration>
      <avg_bpm>78</avg_bpm>
      <min_bpm>65</min_bpm>
      <max_bpm>88</max_bpm>
    </record>
  </records>
</heartbeat>';

switch ($requestedFile) {
    case 'record_08042025.xml':
        $xmlData = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE heartbeat [
  <!ELEMENT heartbeat (patient, device, records)>
  <!ELEMENT patient (name, id)>
  <!ELEMENT name (#PCDATA)>
  <!ELEMENT id (#PCDATA)>
  <!ELEMENT device (model, serial, firmware)>
  <!ELEMENT model (#PCDATA)>
  <!ELEMENT serial (#PCDATA)>
  <!ELEMENT firmware (#PCDATA)>
  <!ELEMENT records (record+)>
  <!ELEMENT record (timestamp, duration, avg_bpm, min_bpm, max_bpm)>
  <!ELEMENT timestamp (#PCDATA)>
  <!ELEMENT duration (#PCDATA)>
  <!ELEMENT avg_bpm (#PCDATA)>
  <!ELEMENT min_bpm (#PCDATA)>
  <!ELEMENT max_bpm (#PCDATA)>
]>
<heartbeat>
  <patient>
    <name>Hasta</name>
    <id>12345</id>
  </patient>
  <device>
    <model>Urba K1</model>
    <serial>RPi-CT200-9876</serial>
    <firmware>2.3.1</firmware>
  </device>
  <records>
    <record>
      <timestamp>08.04.2025 08:30:00</timestamp>
      <duration>45</duration>
      <avg_bpm>76</avg_bpm>
      <min_bpm>64</min_bpm>
      <max_bpm>92</max_bpm>
    </record>
    <record>
      <timestamp>09.04.2025 12:15:00</timestamp>
      <duration>40</duration>
      <avg_bpm>73</avg_bpm>
      <min_bpm>62</min_bpm>
      <max_bpm>88</max_bpm>
    </record>
    <record>
      <timestamp>10.04.2025 17:45:00</timestamp>
      <duration>50</duration>
      <avg_bpm>81</avg_bpm>
      <min_bpm>69</min_bpm>
      <max_bpm>95</max_bpm>
    </record>
  </records>
</heartbeat>';
        break;
    case 'record_05042025.xml':
        $xmlData = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE heartbeat [
  <!ELEMENT heartbeat (patient, device, records)>
  <!ELEMENT patient (name, id)>
  <!ELEMENT name (#PCDATA)>
  <!ELEMENT id (#PCDATA)>
  <!ELEMENT device (model, serial, firmware)>
  <!ELEMENT model (#PCDATA)>
  <!ELEMENT serial (#PCDATA)>
  <!ELEMENT firmware (#PCDATA)>
  <!ELEMENT records (record+)>
  <!ELEMENT record (timestamp, duration, avg_bpm, min_bpm, max_bpm)>
  <!ELEMENT timestamp (#PCDATA)>
  <!ELEMENT duration (#PCDATA)>
  <!ELEMENT avg_bpm (#PCDATA)>
  <!ELEMENT min_bpm (#PCDATA)>
  <!ELEMENT max_bpm (#PCDATA)>
]>
<heartbeat>
  <patient>
    <name>Hasta</name>
    <id>12345</id>
  </patient>
  <device>
    <model>Urba K1</model>
    <serial>RPi-CT200-9876</serial>
    <firmware>2.3.1</firmware>
  </device>
  <records>
    <record>
      <timestamp>05.04.2025 10:15:00</timestamp>
      <duration>60</duration>
      <avg_bpm>82</avg_bpm>
      <min_bpm>71</min_bpm>
      <max_bpm>98</max_bpm>
    </record>
    <record>
      <timestamp>06.04.2025 14:20:00</timestamp>
      <duration>45</duration>
      <avg_bpm>79</avg_bpm>
      <min_bpm>68</min_bpm>
      <max_bpm>91</max_bpm>
    </record>
    <record>
      <timestamp>07.04.2025 09:45:00</timestamp>
      <duration>40</duration>
      <avg_bpm>75</avg_bpm>
      <min_bpm>64</min_bpm>
      <max_bpm>89</max_bpm>
    </record>
  </records>
</heartbeat>';
        break;
    case 'record_01042025.xml':
        $xmlData = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE heartbeat [
  <!ELEMENT heartbeat (patient, device, records)>
  <!ELEMENT patient (name, id)>
  <!ELEMENT name (#PCDATA)>
  <!ELEMENT id (#PCDATA)>
  <!ELEMENT device (model, serial, firmware)>
  <!ELEMENT model (#PCDATA)>
  <!ELEMENT serial (#PCDATA)>
  <!ELEMENT firmware (#PCDATA)>
  <!ELEMENT records (record+)>
  <!ELEMENT record (timestamp, duration, avg_bpm, min_bpm, max_bpm)>
  <!ELEMENT timestamp (#PCDATA)>
  <!ELEMENT duration (#PCDATA)>
  <!ELEMENT avg_bpm (#PCDATA)>
  <!ELEMENT min_bpm (#PCDATA)>
  <!ELEMENT max_bpm (#PCDATA)>
]>
<heartbeat>
  <patient>
    <name>Hasta</name>
    <id>12345</id>
  </patient>
  <device>
    <model>Urba K1</model>
    <serial>RPi-CT200-9876</serial>
    <firmware>2.3.1</firmware>
  </device>
  <records>
    <record>
      <timestamp>01.04.2025 18:45:00</timestamp>
      <duration>30</duration>
      <avg_bpm>78</avg_bpm>
      <min_bpm>65</min_bpm>
      <max_bpm>88</max_bpm>
    </record>
    <record>
      <timestamp>02.04.2025 16:30:00</timestamp>
      <duration>35</duration>
      <avg_bpm>74</avg_bpm>
      <min_bpm>62</min_bpm>
      <max_bpm>86</max_bpm>
    </record>
    <record>
      <timestamp>03.04.2025 11:15:00</timestamp>
      <duration>40</duration>
      <avg_bpm>76</avg_bpm>
      <min_bpm>64</min_bpm>
      <max_bpm>90</max_bpm>
    </record>
  </records>
</heartbeat>';
        break;
    default:
        if (!isset($_POST['xml_data'])) {
            header('HTTP/1.0 404 Not Found');
            echo 'Dosya bulunamadı.';
            exit;
        }
}

echo $xmlData;
?> 