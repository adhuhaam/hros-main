<video id="qr-video"></video>
<input type="text" id="emp_no" placeholder="Scanned QR will appear here">

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let scanner = new Html5QrcodeScanner("qr-video", { fps: 10, qrbox: 250 });
    scanner.render((decodedText) => {
        document.getElementById("emp_no").value = decodedText;
        scanner.clear();
    });
</script>
