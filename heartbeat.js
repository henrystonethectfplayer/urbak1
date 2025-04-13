document.addEventListener('DOMContentLoaded', function() {
    const heartbeatLine = document.querySelector('.heartbeat-line');
    
    function simulateHeartbeat() {
        document.getElementById('bpm-value').textContent = `-- BPM`;
        
        const ekgWidth = heartbeatLine.parentElement.offsetWidth;

        function drawEKG() {
            if (!heartbeatLine) return;
            
            let ekgPath = "";
            for (let i = 0; i < ekgWidth; i += 2) {
                let height = Math.sin(i/20) * 10;
                
                if (i % 100 === 0) {
                    height += 30;
                } else if (i % 100 === 4) {
                    height -= 15;
                }
                
                ekgPath += `${i},${50 + height} `;
            }
            
            const svgEKG = document.createElementNS("http://www.w3.org/2000/svg", "path");
            svgEKG.setAttribute("d", `M ${ekgPath}`);
            svgEKG.setAttribute("stroke", "#2ecc71");
            svgEKG.setAttribute("fill", "none");
            svgEKG.setAttribute("stroke-width", "2");
            
            if (heartbeatLine.querySelector('svg')) {
                heartbeatLine.querySelector('svg').remove();
            }
            
            const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
            svg.setAttribute("width", "100%");
            svg.setAttribute("height", "100%");
            svg.setAttribute("viewBox", `0 0 ${ekgWidth} 100`);
            svg.appendChild(svgEKG);
            
            heartbeatLine.appendChild(svg);
        }
        
        drawEKG();
        
        window.addEventListener('resize', drawEKG);
    }
    
    simulateHeartbeat();
    
    setInterval(simulateHeartbeat, 5000);
    
    function formatDate(dateString) {
        const parts = dateString.split('-');
        return `${parts[2]}.${parts[1]}.${parts[0]}`;
    }
    
    const generateXmlBtn = document.getElementById('generate-xml-btn');
    const downloadForm = document.getElementById('record-download-form');
    
    if (generateXmlBtn && downloadForm) {
        generateXmlBtn.addEventListener('click', function() {
            const startDate = document.getElementById('start-date').value;
            const endDate = document.getElementById('end-date').value;
            
            if (!startDate || !endDate) {
                alert('Lütfen tarih aralığını belirtin.');
                return;
            }
            
            const xmlData = `<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE heartbeat [
  <!ELEMENT heartbeat (request, patient, device)>
  <!ELEMENT request (start_date, end_date)>
  <!ELEMENT start_date (#PCDATA)>
  <!ELEMENT end_date (#PCDATA)>
  <!ELEMENT patient (name, id)>
  <!ELEMENT name (#PCDATA)>
  <!ELEMENT id (#PCDATA)>
  <!ELEMENT device (model, serial, firmware)>
  <!ELEMENT model (#PCDATA)>
  <!ELEMENT serial (#PCDATA)>
  <!ELEMENT firmware (#PCDATA)>
]>
<heartbeat>
  <request>
    <start_date>${formatDate(startDate)}</start_date>
    <end_date>${formatDate(endDate)}</end_date>
  </request>
  <patient>
    <name>Hasta</name>
    <id>12345</id>
  </patient>
  <device>
    <model>Urba K1</model>
    <serial>RPi-CT200-9876</serial>
    <firmware>2.3.1</firmware>
  </device>
</heartbeat>`;
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'records.php', true);
            
            xhr.setRequestHeader('Content-Type', 'application/xml');
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const blob = new Blob([xhr.responseText], {type: 'application/xml'});
                    const url = URL.createObjectURL(blob);
                    
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'heartbeat_record.xml';
                    document.body.appendChild(a);
                    a.click();
                    
                    setTimeout(function() {
                        document.body.removeChild(a);
                        window.URL.revokeObjectURL(url);
                    }, 0);
                } else {
                    alert('Kayıt indirme sırasında bir hata oluştu.');
                }
            };
            
            xhr.send(xmlData);
        });
    }
}); 