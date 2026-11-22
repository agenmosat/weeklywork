<?php
$footerbase = '';
if (file_exists('assets/main.js')) {
    $footerbase = '';
} elseif (file_exists('../assets/main.js')) {
    $footerbase = '../';
} elseif (file_exists('../../assets/main.js')) {
    $footerbase = '../../';
}
?>
<script src="<?= $footerbase ?>assets/main.js"></script>
</body>
</html>
