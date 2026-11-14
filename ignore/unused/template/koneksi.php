<?php
   $judul = 'Tes koneksi database';
   $halaman = 'koneksi';

   require __DIR__ . '/config/database.php';
   require __DIR__ .'/partials/header.php';

   $versiServer = mysqli_get_server_info($koneksi);
   $charset = mysqli_get_server_info($koneksi);

   $hasilTabel = mysqli_query($koneksi,'SHOW TABLE');
   $daftarTabel = [''];
   while ($baris = mysqli_fetch_row($hasilTabel)) {
   $daftarTabel[$baris['']] = $baris[0];
   }

   echo $versiServer;
   var_dump($daftarTabel);
?>