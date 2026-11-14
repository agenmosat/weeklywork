<?php
// $sudahKirim = $_SERVER['REQUEST_METHOD'] === 'POST';
$error = [];

$nama = trim($_POST['nama'] ?? '');
$produk = $_POST['produk'] ?? '';
$jumlahinput = trim((string) ($_POST['jumlah'] ?? ''));
$metbyr = $_POST['bayar'] ?? '';
$catatan = trim($_POST['catatan'] ?? '');
echo $nama;
?>


<form method="POST" action="form_pembelian.php">
    <p>masukkan nama</p>
    <input type="text" name="nama">
    <br>
    <p>pilih produk</p>
    <select name="produk" id="">
        <option value="minyak">minyak -- rp40.000</option>
        <option value="beras">beras -- rp60.000</option>
        <option value="susu">susu -- rp20.000</option>
    </select>
    <br>
    <p>jumlah</p>
    <input type="number" name="jumlah" min="1">
    <br>
    <p>metode bayar</p>
    <select name="bayar" id="">
        <option value="qris">qris</option>
        <option value="cash">cash</option>
    </select>
    <br>
    <p>note</p>
    <input type="text" name="catatan">
    <button type="submit" name="kirim">kirim</button>
</form>

<!-- OUTPUT -->
<?php
// $sudahKirim = $_SERVER['REQUEST_METHOD'] === 'POST';
$error = [];
$harga = [40000, 60000, 20000];

$nama = trim($_POST['nama'] ?? '');
$produk = $_POST['produk'] ?? '';
$jumlahinput = trim((string) ($_POST['jumlah'] ?? ''));
$metbyr = $_POST['bayar'] ?? '';
$catatan = trim($_POST['catatan'] ?? '');


echo $nama;
echo "<br>";
echo $produk;
echo "<br>";
echo $jumlahinput;
echo "<br>";
echo $metbyr;
echo "<br>";
echo $catatan;
echo "<br>";
if ($produk == 'minyak')
    {
        $subtotal = $harga[0] * $jumlahinput;
        echo $subtotal;
        echo "<br>";
    }
    elseif ($produk == 'beras')
    {
        $subtotal = $harga[1] * $jumlahinput;
        echo $subtotal;
        echo "<br>";
    }
    elseif ($produk == 'susu')
    {
        $subtotal = $harga[2] * $jumlahinput;
        echo $subtotal;
        echo "<br>";
    }
?>