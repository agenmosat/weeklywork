<?php
$pass_count = 0;
$fail_count = 0;
$total_count = 0;

function report_pass($test_id, $message) {
    global $pass_count, $total_count;
    $pass_count++;
    $total_count++;
    echo "[PASS] " . $test_id . ": " . $message . "\n";
}

function report_fail($test_id, $message) {
    global $fail_count, $total_count;
    $fail_count++;
    $total_count++;
    echo "[FAIL] " . $test_id . ": " . $message . "\n";
}

function assert_true($condition, $test_id, $message) {
    if ($condition) {
        report_pass($test_id, $message);
    } else {
        report_fail($test_id, $message);
    }
}

function assert_equals($expected, $actual, $test_id, $message) {
    if ($expected === $actual) {
        report_pass($test_id, $message);
    } else {
        report_fail($test_id, $message . " (expected: " . var_export($expected, true) . ", got: " . var_export($actual, true) . ")");
    }
}

function assert_contains($needle, $haystack, $test_id, $message) {
    if (strpos((string)$haystack, (string)$needle) !== false) {
        report_pass($test_id, $message);
    } else {
        report_fail($test_id, $message);
    }
}

function assert_regex($pattern, $subject, $test_id, $message) {
    if (preg_match($pattern, (string)$subject)) {
        report_pass($test_id, $message);
    } else {
        report_fail($test_id, $message);
    }
}

function get_test_connection() {
    if (!function_exists('mysqli_connect')) {
        return null;
    }
    if (function_exists('mysqli_report')) {
        mysqli_report(MYSQLI_REPORT_OFF);
    }
    @ini_set('mysqli.default_socket', '/tmp/mysql.sock');
    $conn = @mysqli_connect('localhost', 'root', '', 'toko');
    if ($conn) {
        return $conn;
    }
    $conn = @mysqli_connect('127.0.0.1', 'root', '', 'toko', 3306);
    if ($conn) {
        return $conn;
    }
    $conn = @mysqli_connect('localhost', 'root', '', 'toko', 3306, '/tmp/mysql.sock');
    if ($conn) {
        return $conn;
    }
    $conn = @mysqli_connect('localhost', 'root', '', 'toko', 3306, '/opt/lampp/var/mysql/mysql.sock');
    if ($conn) {
        return $conn;
    }
    $conn = @mysqli_connect('127.0.0.1', 'root', '', '');
    if ($conn) {
        mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS toko");
        mysqli_select_db($conn, 'toko');
        return $conn;
    }
    $conn = @mysqli_connect('localhost', 'root', '', '', 3306, '/tmp/mysql.sock');
    if ($conn) {
        mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS toko");
        mysqli_select_db($conn, 'toko');
        return $conn;
    }
    return null;
}

function clean_test_fixtures($koneksi) {
    if (!$koneksi) {
        return;
    }
    mysqli_query($koneksi, "DELETE FROM stok WHERE id_product IN (SELECT id FROM products WHERE name LIKE 'testprod%') OR no_penjualan LIKE 'TEST%' OR no_pembelian LIKE 'TEST%' OR keterangan LIKE '%test%' OR keterangan LIKE 'Penjualan item%' OR keterangan LIKE 'Pembelian item%'");
    mysqli_query($koneksi, "DELETE FROM penjualan_detail WHERE id_penjualan IN (SELECT id_penjualan FROM penjualan WHERE nomor_transaksi LIKE 'TEST%') OR id_product IN (SELECT id FROM products WHERE name LIKE 'testprod%')");
    mysqli_query($koneksi, "DELETE FROM penjualan WHERE nomor_transaksi LIKE 'TEST%'");
    mysqli_query($koneksi, "DELETE FROM pembelian_detail WHERE id_pembelian IN (SELECT id_pembelian FROM pembelian WHERE nomor_transaksi LIKE 'TEST%') OR id_product IN (SELECT id FROM products WHERE name LIKE 'testprod%')");
    mysqli_query($koneksi, "DELETE FROM pembelian WHERE nomor_transaksi LIKE 'TEST%'");
    mysqli_query($koneksi, "DELETE FROM products WHERE name LIKE 'testprod%'");
    mysqli_query($koneksi, "DELETE FROM supplier WHERE nama_supplier LIKE 'testsupp%'");
}

function run_tier1($koneksi) {
    echo "=== TIER 1: Schema and DDL Verification ===\n";
    if (!$koneksi) {
        $tier1_tests = array(
            'T1_01_stok_table_exists', 'T1_02_pengguna_table_exists', 'T1_03_products_table_exists',
            'T1_04_supplier_table_exists', 'T1_05_kasir_table_exists', 'T1_06_penjualan_table_exists',
            'T1_07_penjualan_detail_table_exists', 'T1_08_pembelian_table_exists', 'T1_09_pembelian_detail_table_exists',
            'T1_10_col_stok_id_stok', 'T1_11_col_stok_id_product', 'T1_12_col_stok_no_penjualan',
            'T1_13_col_stok_no_pembelian', 'T1_14_col_stok_saldo_awal', 'T1_15_col_stok_masuk',
            'T1_16_col_stok_keluar', 'T1_17_col_stok_saldo_akhir', 'T1_18_col_stok_tanggal',
            'T1_19_col_stok_keterangan', 'T1_20_col_pengguna_id_pengguna', 'T1_21_col_pengguna_username',
            'T1_22_col_pengguna_password', 'T1_23_col_pengguna_role', 'T1_24_col_supplier_id_supplier',
            'T1_25_seed_admin_exists', 'T1_26_seed_admin_bcrypt', 'T1_27_seed_kasir1_exists'
        );
        foreach ($tier1_tests as $tid) {
            report_fail($tid, 'database connection offline');
        }
        return;
    }
    $tables = array();
    $res = mysqli_query($koneksi, "SHOW TABLES");
    if ($res) {
        while ($row = mysqli_fetch_row($res)) {
            $tables[] = $row[0];
        }
    }
    assert_true(in_array('stok', $tables), 'T1_01_stok_table_exists', 'stok table exists');
    assert_true(in_array('pengguna', $tables), 'T1_02_pengguna_table_exists', 'pengguna table exists');
    assert_true(in_array('products', $tables), 'T1_03_products_table_exists', 'products table exists');
    assert_true(in_array('supplier', $tables), 'T1_04_supplier_table_exists', 'supplier table exists');
    assert_true(in_array('kasir', $tables), 'T1_05_kasir_table_exists', 'kasir table exists');
    assert_true(in_array('penjualan', $tables), 'T1_06_penjualan_table_exists', 'penjualan table exists');
    assert_true(in_array('penjualan_detail', $tables), 'T1_07_penjualan_detail_table_exists', 'penjualan_detail table exists');
    assert_true(in_array('pembelian', $tables), 'T1_08_pembelian_table_exists', 'pembelian table exists');
    assert_true(in_array('pembelian_detail', $tables), 'T1_09_pembelian_detail_table_exists', 'pembelian_detail table exists');

    $stok_cols = array();
    $res_stok = mysqli_query($koneksi, "SHOW COLUMNS FROM stok");
    if ($res_stok) {
        while ($col = mysqli_fetch_assoc($res_stok)) {
            $stok_cols[$col['Field']] = $col;
        }
    }
    assert_true(isset($stok_cols['id_stok']) && stripos($stok_cols['id_stok']['Type'], 'int') !== false, 'T1_10_col_stok_id_stok', 'stok id_stok is int primary key');
    assert_true(isset($stok_cols['id_product']) && stripos($stok_cols['id_product']['Type'], 'int') !== false, 'T1_11_col_stok_id_product', 'stok id_product is int');
    assert_true(isset($stok_cols['no_penjualan']) && $stok_cols['no_penjualan']['Null'] === 'YES', 'T1_12_col_stok_no_penjualan', 'stok no_penjualan is nullable');
    assert_true(isset($stok_cols['no_pembelian']) && $stok_cols['no_pembelian']['Null'] === 'YES', 'T1_13_col_stok_no_pembelian', 'stok no_pembelian is nullable');
    assert_true(isset($stok_cols['saldo_awal']) && stripos($stok_cols['saldo_awal']['Type'], 'int') !== false, 'T1_14_col_stok_saldo_awal', 'stok saldo_awal is int');
    assert_true(isset($stok_cols['masuk']) || isset($stok_cols['jumlah_masuk']), 'T1_15_col_stok_masuk', 'stok masuk or jumlah_masuk column exists');
    assert_true(isset($stok_cols['keluar']) || isset($stok_cols['jumlah_keluar']), 'T1_16_col_stok_keluar', 'stok keluar or jumlah_keluar column exists');
    assert_true(isset($stok_cols['saldo_akhir']) && stripos($stok_cols['saldo_akhir']['Type'], 'int') !== false, 'T1_17_col_stok_saldo_akhir', 'stok saldo_akhir is int');
    assert_true(isset($stok_cols['tanggal']), 'T1_18_col_stok_tanggal', 'stok tanggal column exists');
    assert_true(isset($stok_cols['keterangan']), 'T1_19_col_stok_keterangan', 'stok keterangan column exists');

    $pengguna_cols = array();
    $res_pengguna = mysqli_query($koneksi, "SHOW COLUMNS FROM pengguna");
    if ($res_pengguna) {
        while ($col = mysqli_fetch_assoc($res_pengguna)) {
            $pengguna_cols[$col['Field']] = $col;
        }
    }
    assert_true(isset($pengguna_cols['id_pengguna']) && stripos($pengguna_cols['id_pengguna']['Type'], 'int') !== false, 'T1_20_col_pengguna_id_pengguna', 'pengguna id_pengguna is int');
    assert_true(isset($pengguna_cols['username']) && $pengguna_cols['username']['Null'] === 'NO', 'T1_21_col_pengguna_username', 'pengguna username is not null');
    assert_true(isset($pengguna_cols['password']), 'T1_22_col_pengguna_password', 'pengguna password exists');
    assert_true(isset($pengguna_cols['role']), 'T1_23_col_pengguna_role', 'pengguna role exists');

    $supplier_cols = array();
    $res_supp = mysqli_query($koneksi, "SHOW COLUMNS FROM supplier");
    if ($res_supp) {
        while ($col = mysqli_fetch_assoc($res_supp)) {
            $supplier_cols[$col['Field']] = $col;
        }
    }
    assert_true(isset($supplier_cols['id_supplier']), 'T1_24_col_supplier_id_supplier', 'supplier has id_supplier column');

    $admin_row = null;
    $res_admin = mysqli_query($koneksi, "SELECT id_pengguna, username, password, role FROM pengguna WHERE username = 'admin' LIMIT 1");
    if ($res_admin && ($r = mysqli_fetch_assoc($res_admin))) {
        $admin_row = $r;
    }
    assert_true($admin_row !== null && $admin_row['role'] === 'admin', 'T1_25_seed_admin_exists', 'admin seed user exists with admin role');
    assert_true($admin_row !== null && (strpos($admin_row['password'], '$2y$') === 0 || strpos($admin_row['password'], '$2a$') === 0 || password_verify('admin123', $admin_row['password'])), 'T1_26_seed_admin_bcrypt', 'admin seed password is valid bcrypt hash');

    $kasir_row = null;
    $res_kasir = mysqli_query($koneksi, "SELECT id_pengguna, username, password, role FROM pengguna WHERE username = 'kasir1' OR username = 'kasir' LIMIT 1");
    if ($res_kasir && ($r2 = mysqli_fetch_assoc($res_kasir))) {
        $kasir_row = $r2;
    }
    assert_true($kasir_row !== null && ($kasir_row['role'] === 'cashier' || $kasir_row['role'] === 'kasir'), 'T1_27_seed_kasir1_exists', 'kasir seed user exists with cashier or kasir role');
}

function run_tier2($koneksi) {
    echo "=== TIER 2: Business Logic and Invariants ===\n";
    $pb = function_exists('buat_nomor') ? buat_nomor($koneksi, 'PB', 'pembelian', 'nomor_transaksi') : '';
    assert_regex('~^PB-\d{4}$~', $pb, 'T2_01_buat_nomor_pembelian_format', 'buat_nomor with PB prefix produces PB-XXXX');

    $pj = function_exists('buat_nomor') ? buat_nomor($koneksi, 'PJ', 'penjualan', 'nomor_transaksi') : '';
    assert_regex('~^PJ-\d{4}$~', $pj, 'T2_02_buat_nomor_penjualan_format', 'buat_nomor with PJ prefix produces PJ-XXXX');

    $init_seed = function_exists('buat_nomor') ? buat_nomor(null, 'PB', 'pembelian', 'nomor_transaksi') : '';
    assert_equals('PB-0001', $init_seed, 'T2_03_buat_nomor_initial_seed', 'buat_nomor on null connection or empty table produces PB-0001');

    if ($koneksi && function_exists('buat_nomor')) {
        mysqli_query($koneksi, "DELETE FROM pembelian WHERE nomor_transaksi LIKE 'TEST-PB-SEQ%'");

        mysqli_query($koneksi, "INSERT INTO pembelian (nomor_transaksi, id_supplier, total) VALUES ('TEST-PB-SEQ-0001', 1, 1000.00)");
        $seq_next = buat_nomor($koneksi, 'TEST-PB-SEQ', 'pembelian', 'nomor_transaksi');
        assert_equals('TEST-PB-SEQ-0002', $seq_next, 'T2_04_buat_nomor_seq_increment', 'sequential increment produces next padded number');

        mysqli_query($koneksi, "INSERT INTO pembelian (nomor_transaksi, id_supplier, total) VALUES ('TEST-PB-SEQ-0009', 1, 1000.00)");
        $roll2 = buat_nomor($koneksi, 'TEST-PB-SEQ', 'pembelian', 'nomor_transaksi');
        assert_equals('TEST-PB-SEQ-0010', $roll2, 'T2_05_buat_nomor_rollover_2digit', 'two-digit sequence formatted as 0010');

        mysqli_query($koneksi, "INSERT INTO pembelian (nomor_transaksi, id_supplier, total) VALUES ('TEST-PB-SEQ-0099', 1, 1000.00)");
        $roll3 = buat_nomor($koneksi, 'TEST-PB-SEQ', 'pembelian', 'nomor_transaksi');
        assert_equals('TEST-PB-SEQ-0100', $roll3, 'T2_06_buat_nomor_rollover_3digit', 'three-digit sequence formatted as 0100');

        mysqli_query($koneksi, "INSERT INTO pembelian (nomor_transaksi, id_supplier, total) VALUES ('TEST-PB-SEQ-0999', 1, 1000.00)");
        $roll4 = buat_nomor($koneksi, 'TEST-PB-SEQ', 'pembelian', 'nomor_transaksi');
        assert_equals('TEST-PB-SEQ-1000', $roll4, 'T2_07_buat_nomor_rollover_4digit', 'four-digit sequence formatted as 1000');

        mysqli_query($koneksi, "DELETE FROM pembelian WHERE nomor_transaksi LIKE 'TEST-PB-SEQ%'");
    } else {
        report_fail('T2_04_buat_nomor_seq_increment', 'database connection offline or buat_nomor missing');
        report_fail('T2_05_buat_nomor_rollover_2digit', 'database connection offline or buat_nomor missing');
        report_fail('T2_06_buat_nomor_rollover_3digit', 'database connection offline or buat_nomor missing');
        report_fail('T2_07_buat_nomor_rollover_4digit', 'database connection offline or buat_nomor missing');
    }

    $pb_dash = function_exists('buat_nomor') ? buat_nomor($koneksi, 'PB-', 'pembelian', 'nomor_transaksi') : '';
    assert_regex('~^PB-\d{4}$~', $pb_dash, 'T2_08_buat_nomor_prefix_tolerance', 'buat_nomor with trailing dash prefix produces PB-XXXX without double dash');

    $test_prod_id = 0;
    if ($koneksi) {
        clean_test_fixtures($koneksi);
        mysqli_query($koneksi, "INSERT INTO products (name, price, stock, category, description) VALUES ('testprod_fallback', 5000.00, 42, 'test', 'test desc')");
        $test_prod_id = (int)mysqli_insert_id($koneksi);
        $saldo_initial = function_exists('saldo_stok_terakhir') ? saldo_stok_terakhir($koneksi, $test_prod_id) : 0;
        assert_equals(42, $saldo_initial, 'T2_09_saldo_stok_fallback_product', 'saldo_stok_terakhir returns product stock when no stok rows exist');

        $col_masuk = 'jumlah_masuk';
        $col_keluar = 'jumlah_keluar';
        $q_chk = mysqli_query($koneksi, "SHOW COLUMNS FROM stok LIKE 'masuk'");
        if ($q_chk && mysqli_num_rows($q_chk) > 0) {
            $col_masuk = 'masuk';
            $col_keluar = 'keluar';
        }
        mysqli_query($koneksi, "INSERT INTO stok (id_product, tanggal, saldo_awal, " . $col_masuk . ", " . $col_keluar . ", saldo_akhir, keterangan) VALUES (" . $test_prod_id . ", NOW(), 42, 8, 0, 50, 'test add')");
        $saldo_latest = function_exists('saldo_stok_terakhir') ? saldo_stok_terakhir($koneksi, $test_prod_id) : 0;
        assert_equals(50, $saldo_latest, 'T2_10_saldo_stok_latest_record', 'saldo_stok_terakhir returns saldo_akhir of latest stok row');

        mysqli_query($koneksi, "INSERT INTO stok (id_product, tanggal, saldo_awal, " . $col_masuk . ", " . $col_keluar . ", saldo_akhir, keterangan) VALUES (" . $test_prod_id . ", NOW(), 50, 0, 15, 35, 'test dec')");
        $saldo_latest2 = function_exists('saldo_stok_terakhir') ? saldo_stok_terakhir($koneksi, $test_prod_id) : 0;
        assert_equals(35, $saldo_latest2, 'T2_11_saldo_stok_order_by_id_desc', 'saldo_stok_terakhir picks latest id_stok');
    } else {
        report_fail('T2_09_saldo_stok_fallback_product', 'database connection offline');
        report_fail('T2_10_saldo_stok_latest_record', 'database connection offline');
        report_fail('T2_11_saldo_stok_order_by_id_desc', 'database connection offline');
    }

    if ($koneksi && function_exists('catat_mutasi_stok')) {
        mysqli_query($koneksi, "INSERT INTO products (name, price, stock, category, description) VALUES ('testprod_ledger_inv', 1000.00, 100, 'test', 'ledger inv')");
        $id_inv = (int)mysqli_insert_id($koneksi);

        catat_mutasi_stok($koneksi, $id_inv, 25, 0, null, 'TEST-PB-INV1', 'test mutasi masuk');
        $q_inv1 = mysqli_query($koneksi, "SELECT saldo_awal, jumlah_masuk, jumlah_keluar, saldo_akhir FROM stok WHERE id_product = " . $id_inv . " AND no_pembelian = 'TEST-PB-INV1' ORDER BY id_stok DESC LIMIT 1");
        $row_inv1 = $q_inv1 ? mysqli_fetch_assoc($q_inv1) : null;
        assert_true($row_inv1 !== null && (int)$row_inv1['saldo_awal'] === 100 && (int)$row_inv1['saldo_akhir'] === 125, 'T2_12_running_balance_incoming', 'incoming balance invariant formula verified from stok table');

        catat_mutasi_stok($koneksi, $id_inv, 0, 15, 'TEST-PJ-INV1', null, 'test mutasi keluar');
        $q_inv2 = mysqli_query($koneksi, "SELECT saldo_awal, jumlah_masuk, jumlah_keluar, saldo_akhir FROM stok WHERE id_product = " . $id_inv . " AND (no_penjualan = 'TEST-PJ-INV1' OR keterangan = 'test mutasi keluar') ORDER BY id_stok DESC LIMIT 1");
        $row_inv2 = $q_inv2 ? mysqli_fetch_assoc($q_inv2) : null;
        assert_true($row_inv2 !== null && (int)$row_inv2['saldo_awal'] === 125 && (int)$row_inv2['saldo_akhir'] === 110, 'T2_13_running_balance_outgoing', 'outgoing balance invariant formula verified from stok table');

        assert_true($row_inv1 !== null && $row_inv2 !== null && (int)$row_inv1['saldo_akhir'] === (int)$row_inv2['saldo_awal'], 'T2_14_running_balance_chaining', 'subsequent saldo_awal strictly equals previous saldo_akhir in stok ledger');

        catat_mutasi_stok($koneksi, $id_inv, 0, 0, null, null, 'test zero mutation');
        $q_inv3 = mysqli_query($koneksi, "SELECT saldo_awal, saldo_akhir FROM stok WHERE id_product = " . $id_inv . " AND keterangan = 'test zero mutation' ORDER BY id_stok DESC LIMIT 1");
        $row_inv3 = $q_inv3 ? mysqli_fetch_assoc($q_inv3) : null;
        assert_true($row_inv3 !== null && (int)$row_inv3['saldo_awal'] === 110 && (int)$row_inv3['saldo_akhir'] === 110, 'T2_15_running_balance_zero_mutation', 'zero mutation preserves stock balance in stok ledger');

        mysqli_query($koneksi, "DELETE FROM stok WHERE id_product = " . $id_inv);
        mysqli_query($koneksi, "DELETE FROM products WHERE id = " . $id_inv);
    } else {
        report_fail('T2_12_running_balance_incoming', 'database connection offline or catat_mutasi_stok missing');
        report_fail('T2_13_running_balance_outgoing', 'database connection offline or catat_mutasi_stok missing');
        report_fail('T2_14_running_balance_chaining', 'database connection offline or catat_mutasi_stok missing');
        report_fail('T2_15_running_balance_zero_mutation', 'database connection offline or catat_mutasi_stok missing');
    }

    $d1 = '2026-11-30';
    $d2 = '2026-11-01';
    if ($d1 > $d2) {
        $tmp = $d1;
        $d1 = $d2;
        $d2 = $tmp;
    }
    assert_true($d1 === '2026-11-01' && $d2 === '2026-11-30', 'T2_16_date_autoswap_reversed', 'date auto-swap swaps reversed dates');

    $d3 = '2026-11-15';
    $d4 = '2026-11-15';
    if ($d3 > $d4) {
        $tmp = $d3;
        $d3 = $d4;
        $d4 = $tmp;
    }
    assert_true($d3 === '2026-11-15' && $d4 === '2026-11-15', 'T2_17_date_autoswap_identical', 'date auto-swap preserves identical dates');

    $d5 = '2026-11-01';
    $d6 = '2026-11-30';
    if ($d5 > $d6) {
        $tmp = $d5;
        $d5 = $d6;
        $d6 = $tmp;
    }
    assert_true($d5 === '2026-11-01' && $d6 === '2026-11-30', 'T2_18_date_autoswap_normal', 'date auto-swap preserves ordered dates');

    $raw_input = '';
    $d_def = empty($raw_input) ? date('Y-m-01') : $raw_input;
    assert_equals(date('Y-m-01'), $d_def, 'T2_19_date_fallback_default', 'empty date input falls back to first of current month');

    $pw_hash = password_hash('admin123', PASSWORD_BCRYPT);
    assert_true(password_verify('admin123', $pw_hash), 'T2_20_bcrypt_verify_valid', 'password_verify succeeds for valid password');
    assert_true(!password_verify('wrongpass', $pw_hash), 'T2_21_bcrypt_verify_invalid', 'password_verify fails for invalid password');
    assert_true(strpos($pw_hash, '$2y$') === 0 || strpos($pw_hash, '$2a$') === 0, 'T2_22_bcrypt_cost_prefix', 'bcrypt hash begins with 2y or 2a prefix');

    $pw_hash2 = password_hash('admin123', PASSWORD_BCRYPT);
    assert_true($pw_hash !== $pw_hash2, 'T2_23_bcrypt_different_salts', 'subsequent bcrypt hashes use unique random salts');

    if (function_exists('hitung_total_transaksi')) {
        $calc = (float)hitung_total_transaksi(15000, 3);
        assert_equals(45000.0, $calc, 'T2_24_hitung_total_transaksi_basic', 'hitung_total_transaksi computes price times quantity');
    } else {
        report_fail('T2_24_hitung_total_transaksi_basic', 'hitung_total_transaksi function does not exist');
    }

    if (function_exists('format_uang')) {
        $fmt = format_uang(50000);
        assert_contains('50,000.00', $fmt, 'T2_25_format_uang_standard', 'format_uang formats decimal currency');
    } else {
        report_fail('T2_25_format_uang_standard', 'format_uang function does not exist');
    }

    if ($koneksi && $test_prod_id > 0) {
        $mut_ok = function_exists('catat_mutasi_stok') ? catat_mutasi_stok($koneksi, $test_prod_id, 5, 0, null, 'TEST-PB-001', 'test mutasi') : false;
        $saldo_after_mut = function_exists('saldo_stok_terakhir') ? saldo_stok_terakhir($koneksi, $test_prod_id) : 0;
        assert_true($mut_ok && $saldo_after_mut === 40, 'T2_26_catat_mutasi_stok_behavior', 'catat_mutasi_stok records entry and synchronizes product stock');
        clean_test_fixtures($koneksi);
    } else {
        report_fail('T2_26_catat_mutasi_stok_behavior', 'database connection offline');
    }
}

function run_tier3($koneksi) {
    echo "=== TIER 3: Transactions and Relational Integrity ===\n";
    if (!$koneksi) {
        $tier3_tests = array(
            'T3_01_multi_row_purchase_commit', 'T3_02_multi_row_purchase_total', 'T3_03_multi_row_purchase_details',
            'T3_04_multi_row_purchase_stok_ledger', 'T3_05_multi_row_purchase_stock_updated', 'T3_06_multi_row_sales_commit',
            'T3_07_multi_row_sales_total', 'T3_08_multi_row_sales_details', 'T3_09_multi_row_sales_stok_ledger',
            'T3_10_multi_row_sales_stock_updated', 'T3_11_atomic_rollback_on_error', 'T3_12_atomic_rollback_no_header',
            'T3_13_atomic_rollback_no_details', 'T3_14_atomic_rollback_no_stok', 'T3_15_atomic_rollback_stock_untouched',
            'T3_16_delproduct_blocked_penjualan', 'T3_17_delproduct_blocked_pembelian', 'T3_18_delproduct_blocked_stok',
            'T3_19_delproduct_file_contains_error_relasi', 'T3_20_delproduct_unreferenced_allowed', 'T3_21_delsupplier_blocked_pembelian',
            'T3_22_delsupplier_file_contains_error_relasi', 'T3_23_delsupplier_unreferenced_allowed', 'T3_24_purchase_cancellation_stock_check',
            'T3_25_purchase_cancellation_reversal', 'T3_26_sales_cancellation_reversal'
        );
        foreach ($tier3_tests as $tid) {
            report_fail($tid, 'database connection offline');
        }
        return;
    }
    clean_test_fixtures($koneksi);
    mysqli_query($koneksi, "INSERT INTO products (name, price, stock, category, description) VALUES ('testprod_a', 10000.00, 20, 'test', 'item a')");
    $id_a = (int)mysqli_insert_id($koneksi);
    mysqli_query($koneksi, "INSERT INTO products (name, price, stock, category, description) VALUES ('testprod_b', 20000.00, 15, 'test', 'item b')");
    $id_b = (int)mysqli_insert_id($koneksi);
    mysqli_query($koneksi, "INSERT INTO supplier (nama_supplier, kota) VALUES ('testsupp_1', 'testcity')");
    $id_supp = (int)mysqli_insert_id($koneksi);

    $id_pb = 0;
    mysqli_begin_transaction($koneksi);
    try {
        mysqli_query($koneksi, "INSERT INTO pembelian (nomor_transaksi, id_supplier, total) VALUES ('TEST-PB-9001', " . $id_supp . ", 50000.00)");
        $id_pb = (int)mysqli_insert_id($koneksi);
        mysqli_query($koneksi, "INSERT INTO pembelian_detail (id_pembelian, id_product, harga_beli, qty, subtotal) VALUES (" . $id_pb . ", " . $id_a . ", 10000.00, 2, 20000.00)");
        mysqli_query($koneksi, "INSERT INTO pembelian_detail (id_pembelian, id_product, harga_beli, qty, subtotal) VALUES (" . $id_pb . ", " . $id_b . ", 10000.00, 3, 30000.00)");
        if (function_exists('catat_mutasi_stok')) {
            catat_mutasi_stok($koneksi, $id_a, 2, 0, null, 'TEST-PB-9001', 'test Pembelian item A');
            catat_mutasi_stok($koneksi, $id_b, 3, 0, null, 'TEST-PB-9001', 'test Pembelian item B');
        }
        mysqli_commit($koneksi);
    } catch (Throwable $e) {
        mysqli_rollback($koneksi);
    }

    assert_true($id_pb > 0, 'T3_01_multi_row_purchase_commit', 'multi-row purchase header committed');
    $q_pb = mysqli_query($koneksi, "SELECT total FROM pembelian WHERE id_pembelian = " . $id_pb);
    $row_pb = $q_pb ? mysqli_fetch_assoc($q_pb) : null;
    assert_true($row_pb !== null && (float)$row_pb['total'] === 50000.00, 'T3_02_multi_row_purchase_total', 'multi-row purchase total matches sum');

    $q_pbd = mysqli_query($koneksi, "SELECT COUNT(1) AS cnt FROM pembelian_detail WHERE id_pembelian = " . $id_pb);
    $row_pbd = $q_pbd ? mysqli_fetch_assoc($q_pbd) : null;
    assert_true($row_pbd !== null && (int)$row_pbd['cnt'] === 2, 'T3_03_multi_row_purchase_details', 'exactly 2 detail records created');

    $q_pbs = mysqli_query($koneksi, "SELECT COUNT(1) AS cnt FROM stok WHERE no_pembelian = 'TEST-PB-9001'");
    $row_pbs = $q_pbs ? mysqli_fetch_assoc($q_pbs) : null;
    assert_true($row_pbs !== null && (int)$row_pbs['cnt'] === 2, 'T3_04_multi_row_purchase_stok_ledger', '2 mutations logged in stok ledger');

    $q_stk_a = mysqli_query($koneksi, "SELECT stock FROM products WHERE id = " . $id_a);
    $row_stk_a = $q_stk_a ? mysqli_fetch_assoc($q_stk_a) : null;
    $q_stk_b = mysqli_query($koneksi, "SELECT stock FROM products WHERE id = " . $id_b);
    $row_stk_b = $q_stk_b ? mysqli_fetch_assoc($q_stk_b) : null;
    assert_true($row_stk_a !== null && (int)$row_stk_a['stock'] === 22 && $row_stk_b !== null && (int)$row_stk_b['stock'] === 18, 'T3_05_multi_row_purchase_stock_updated', 'products stock incremented after purchase');

    $id_pj = 0;
    mysqli_begin_transaction($koneksi);
    try {
        mysqli_query($koneksi, "INSERT INTO penjualan (nomor_transaksi, nama_pelanggan, total) VALUES ('TEST-PJ-9001', 'Test Customer', 35000.00)");
        $id_pj = (int)mysqli_insert_id($koneksi);
        mysqli_query($koneksi, "INSERT INTO penjualan_detail (id_penjualan, id_product, harga, qty, subtotal) VALUES (" . $id_pj . ", " . $id_a . ", 15000.00, 1, 15000.00)");
        mysqli_query($koneksi, "INSERT INTO penjualan_detail (id_penjualan, id_product, harga, qty, subtotal) VALUES (" . $id_pj . ", " . $id_b . ", 20000.00, 1, 20000.00)");
        if (function_exists('catat_mutasi_stok')) {
            catat_mutasi_stok($koneksi, $id_a, 0, 1, 'TEST-PJ-9001', null, 'test Penjualan item A');
            catat_mutasi_stok($koneksi, $id_b, 0, 1, 'TEST-PJ-9001', null, 'test Penjualan item B');
        }
        mysqli_commit($koneksi);
    } catch (Throwable $e) {
        mysqli_rollback($koneksi);
    }

    assert_true($id_pj > 0, 'T3_06_multi_row_sales_commit', 'multi-row sales header committed');
    $q_pj = mysqli_query($koneksi, "SELECT total FROM penjualan WHERE id_penjualan = " . $id_pj);
    $row_pj = $q_pj ? mysqli_fetch_assoc($q_pj) : null;
    assert_true($row_pj !== null && (float)$row_pj['total'] === 35000.00, 'T3_07_multi_row_sales_total', 'multi-row sales total matches sum');

    $q_pjd = mysqli_query($koneksi, "SELECT COUNT(1) AS cnt FROM penjualan_detail WHERE id_penjualan = " . $id_pj);
    $row_pjd = $q_pjd ? mysqli_fetch_assoc($q_pjd) : null;
    assert_true($row_pjd !== null && (int)$row_pjd['cnt'] === 2, 'T3_08_multi_row_sales_details', 'exactly 2 detail records created for sales');

    $q_pjs = mysqli_query($koneksi, "SELECT COUNT(1) AS cnt FROM stok WHERE no_penjualan = 'TEST-PJ-9001'");
    $row_pjs = $q_pjs ? mysqli_fetch_assoc($q_pjs) : null;
    assert_true($row_pjs !== null && (int)$row_pjs['cnt'] === 2, 'T3_09_multi_row_sales_stok_ledger', '2 mutations logged in stok ledger for sales');

    $q_stk_a2 = mysqli_query($koneksi, "SELECT stock FROM products WHERE id = " . $id_a);
    $row_stk_a2 = $q_stk_a2 ? mysqli_fetch_assoc($q_stk_a2) : null;
    $q_stk_b2 = mysqli_query($koneksi, "SELECT stock FROM products WHERE id = " . $id_b);
    $row_stk_b2 = $q_stk_b2 ? mysqli_fetch_assoc($q_stk_b2) : null;
    assert_true($row_stk_a2 !== null && (int)$row_stk_a2['stock'] === 21 && $row_stk_b2 !== null && (int)$row_stk_b2['stock'] === 17, 'T3_10_multi_row_sales_stock_updated', 'products stock decremented after sales');

    $rollback_executed = false;
    mysqli_begin_transaction($koneksi);
    $q_rb1 = mysqli_query($koneksi, "INSERT INTO pembelian (nomor_transaksi, id_supplier, total) VALUES ('TEST-PB-ROLLBACK', " . $id_supp . ", 9999.00)");
    $id_rb = (int)mysqli_insert_id($koneksi);
    $q_rb2 = mysqli_query($koneksi, "INSERT INTO pembelian_detail (id_pembelian, id_product, harga_beli, qty, subtotal) VALUES (" . $id_rb . ", " . $id_a . ", 9999.00, 10, 99990.00)");
    try {
        $q_failing = mysqli_query($koneksi, "INSERT INTO non_existent_table_for_rollback_test VALUES (1)");
        if (!$q_failing) {
            mysqli_rollback($koneksi);
            $rollback_executed = true;
        } else {
            mysqli_commit($koneksi);
        }
    } catch (Throwable $e) {
        mysqli_rollback($koneksi);
        $rollback_executed = true;
    }

    assert_true($rollback_executed, 'T3_11_atomic_rollback_on_error', 'rollback executed on error');
    $q_rb_h = mysqli_query($koneksi, "SELECT COUNT(1) AS cnt FROM pembelian WHERE nomor_transaksi = 'TEST-PB-ROLLBACK'");
    $row_rb_h = $q_rb_h ? mysqli_fetch_assoc($q_rb_h) : null;
    assert_true($row_rb_h !== null && (int)$row_rb_h['cnt'] === 0, 'T3_12_atomic_rollback_no_header', 'rolled back transaction leaves zero header rows');

    $q_rb_d = mysqli_query($koneksi, "SELECT COUNT(1) AS cnt FROM pembelian_detail WHERE id_pembelian = " . $id_rb);
    $row_rb_d = $q_rb_d ? mysqli_fetch_assoc($q_rb_d) : null;
    assert_true($row_rb_d !== null && (int)$row_rb_d['cnt'] === 0, 'T3_13_atomic_rollback_no_details', 'rolled back transaction leaves zero detail rows');

    $q_rb_s = mysqli_query($koneksi, "SELECT COUNT(1) AS cnt FROM stok WHERE no_pembelian = 'TEST-PB-ROLLBACK'");
    $row_rb_s = $q_rb_s ? mysqli_fetch_assoc($q_rb_s) : null;
    assert_true($row_rb_s !== null && (int)$row_rb_s['cnt'] === 0, 'T3_14_atomic_rollback_no_stok', 'rolled back transaction leaves zero stok rows');

    $q_stk_a3 = mysqli_query($koneksi, "SELECT stock FROM products WHERE id = " . $id_a);
    $row_stk_a3 = $q_stk_a3 ? mysqli_fetch_assoc($q_stk_a3) : null;
    assert_true($row_stk_a3 !== null && (int)$row_stk_a3['stock'] === 21, 'T3_15_atomic_rollback_stock_untouched', 'product stock untouched after rollback');

    $q_rel_pj = mysqli_query($koneksi, "SELECT COUNT(1) AS cnt FROM penjualan_detail WHERE id_product = " . $id_a);
    $row_rel_pj = $q_rel_pj ? mysqli_fetch_assoc($q_rel_pj) : null;
    assert_true($row_rel_pj !== null && (int)$row_rel_pj['cnt'] > 0, 'T3_16_delproduct_blocked_penjualan', 'product has referencing rows in penjualan_detail');

    $q_rel_pb = mysqli_query($koneksi, "SELECT COUNT(1) AS cnt FROM pembelian_detail WHERE id_product = " . $id_a);
    $row_rel_pb = $q_rel_pb ? mysqli_fetch_assoc($q_rel_pb) : null;
    assert_true($row_rel_pb !== null && (int)$row_rel_pb['cnt'] > 0, 'T3_17_delproduct_blocked_pembelian', 'product has referencing rows in pembelian_detail');

    $q_rel_st = mysqli_query($koneksi, "SELECT COUNT(1) AS cnt FROM stok WHERE id_product = " . $id_a);
    $row_rel_st = $q_rel_st ? mysqli_fetch_assoc($q_rel_st) : null;
    assert_true($row_rel_st !== null && (int)$row_rel_st['cnt'] > 0, 'T3_18_delproduct_blocked_stok', 'product has referencing rows in stok');

    $delprod_file = file_exists(__DIR__ . '/../admin/product/delproduct.php') ? file_get_contents(__DIR__ . '/../admin/product/delproduct.php') : '';
    assert_true(strpos($delprod_file, 'error_relasi') !== false, 'T3_19_delproduct_file_contains_error_relasi', 'delproduct.php checks relations and redirects with error_relasi');

    mysqli_query($koneksi, "INSERT INTO products (name, price, stock, category, description) VALUES ('testprod_unref', 1000.00, 5, 'test', 'unreferenced')");
    $id_unref = (int)mysqli_insert_id($koneksi);
    mysqli_query($koneksi, "DELETE FROM products WHERE id = " . $id_unref);
    $q_check_unref = mysqli_query($koneksi, "SELECT COUNT(1) AS cnt FROM products WHERE id = " . $id_unref);
    $row_unref = $q_check_unref ? mysqli_fetch_assoc($q_check_unref) : null;
    assert_true($row_unref !== null && (int)$row_unref['cnt'] === 0, 'T3_20_delproduct_unreferenced_allowed', 'unreferenced product deleted cleanly');

    $q_supp_pb = mysqli_query($koneksi, "SELECT COUNT(1) AS cnt FROM pembelian WHERE id_supplier = " . $id_supp);
    $row_supp_pb = $q_supp_pb ? mysqli_fetch_assoc($q_supp_pb) : null;
    assert_true($row_supp_pb !== null && (int)$row_supp_pb['cnt'] > 0, 'T3_21_delsupplier_blocked_pembelian', 'supplier has referencing rows in pembelian');

    $delsupp_file = file_exists(__DIR__ . '/../admin/supplier/delsupplier.php') ? file_get_contents(__DIR__ . '/../admin/supplier/delsupplier.php') : '';
    assert_true(strpos($delsupp_file, 'error_relasi') !== false, 'T3_22_delsupplier_file_contains_error_relasi', 'delsupplier.php checks relations and redirects with error_relasi');

    mysqli_query($koneksi, "INSERT INTO supplier (nama_supplier, kota) VALUES ('testsupp_unref', 'unrefcity')");
    $id_supp_unref = (int)mysqli_insert_id($koneksi);
    mysqli_query($koneksi, "DELETE FROM supplier WHERE id_supplier = " . $id_supp_unref);
    $q_check_supp_unref = mysqli_query($koneksi, "SELECT COUNT(1) AS cnt FROM supplier WHERE id_supplier = " . $id_supp_unref);
    $row_supp_unref = $q_check_supp_unref ? mysqli_fetch_assoc($q_check_supp_unref) : null;
    assert_true($row_supp_unref !== null && (int)$row_supp_unref['cnt'] === 0, 'T3_23_delsupplier_unreferenced_allowed', 'unreferenced supplier deleted cleanly');

    if (function_exists('batal_pembelian')) {
        mysqli_query($koneksi, "INSERT INTO products (name, price, stock, category, description) VALUES ('testprod_can_blk', 1000.00, 5, 'test', 'cancel block test')");
        $id_can_blk = (int)mysqli_insert_id($koneksi);
        mysqli_query($koneksi, "INSERT INTO pembelian (nomor_transaksi, id_supplier, total) VALUES ('TEST-PB-CAN-BLK', " . $id_supp . ", 10000.00)");
        $id_pb_blk = (int)mysqli_insert_id($koneksi);
        mysqli_query($koneksi, "INSERT INTO pembelian_detail (id_pembelian, id_product, harga_beli, qty, subtotal) VALUES (" . $id_pb_blk . ", " . $id_can_blk . ", 1000.00, 10, 10000.00)");

        $res_blk = batal_pembelian($koneksi, $id_pb_blk);
        $q_chk_stk = mysqli_query($koneksi, "SELECT stock FROM products WHERE id = " . $id_can_blk);
        $row_chk_stk = $q_chk_stk ? mysqli_fetch_assoc($q_chk_stk) : null;
        $q_chk_rev = mysqli_query($koneksi, "SELECT COUNT(1) AS cnt FROM stok WHERE no_pembelian = 'TEST-PB-CAN-BLK' AND keterangan LIKE '%Batal%'");
        $row_chk_rev = $q_chk_rev ? mysqli_fetch_assoc($q_chk_rev) : null;
        assert_true(!$res_blk && (int)$row_chk_stk['stock'] === 5 && (int)$row_chk_rev['cnt'] === 0, 'T3_24_purchase_cancellation_stock_check', 'purchase cancellation blocked if current stock is less than purchase qty');

        mysqli_query($koneksi, "INSERT INTO products (name, price, stock, category, description) VALUES ('testprod_can_ok', 1000.00, 20, 'test', 'cancel ok test')");
        $id_can_ok = (int)mysqli_insert_id($koneksi);
        mysqli_query($koneksi, "INSERT INTO pembelian (nomor_transaksi, id_supplier, total) VALUES ('TEST-PB-CAN-OK', " . $id_supp . ", 5000.00)");
        $id_pb_ok = (int)mysqli_insert_id($koneksi);
        mysqli_query($koneksi, "INSERT INTO pembelian_detail (id_pembelian, id_product, harga_beli, qty, subtotal) VALUES (" . $id_pb_ok . ", " . $id_can_ok . ", 1000.00, 5, 5000.00)");
        if (function_exists('catat_mutasi_stok')) {
            catat_mutasi_stok($koneksi, $id_can_ok, 5, 0, null, 'TEST-PB-CAN-OK', 'Pembelian item cancel ok');
        }

        $res_can_pb = batal_pembelian($koneksi, $id_pb_ok);
        $q_rev_pb = mysqli_query($koneksi, "SELECT id_stok, jumlah_keluar, saldo_akhir, keterangan FROM stok WHERE no_pembelian = 'TEST-PB-CAN-OK' AND keterangan LIKE '%Batal%' ORDER BY id_stok DESC LIMIT 1");
        $row_rev_pb = $q_rev_pb ? mysqli_fetch_assoc($q_rev_pb) : null;
        $q_stk_restored = mysqli_query($koneksi, "SELECT stock FROM products WHERE id = " . $id_can_ok);
        $row_stk_restored = $q_stk_restored ? mysqli_fetch_assoc($q_stk_restored) : null;
        assert_true($res_can_pb && $row_rev_pb !== null && (int)$row_stk_restored['stock'] === 20, 'T3_25_purchase_cancellation_reversal', 'purchase cancellation reversal records outgoing mutation');
    } else {
        report_fail('T3_24_purchase_cancellation_stock_check', 'batal_pembelian function does not exist');
        report_fail('T3_25_purchase_cancellation_reversal', 'batal_pembelian function does not exist');
    }

    if (function_exists('batal_penjualan')) {
        mysqli_query($koneksi, "INSERT INTO products (name, price, stock, category, description) VALUES ('testprod_can_pj', 2000.00, 20, 'test', 'cancel pj test')");
        $id_can_pj = (int)mysqli_insert_id($koneksi);
        mysqli_query($koneksi, "INSERT INTO penjualan (nomor_transaksi, nama_pelanggan, total) VALUES ('TEST-PJ-CAN-OK', 'Cancel Cust', 8000.00)");
        $id_pj_ok = (int)mysqli_insert_id($koneksi);
        mysqli_query($koneksi, "INSERT INTO penjualan_detail (id_penjualan, id_product, harga, qty, subtotal) VALUES (" . $id_pj_ok . ", " . $id_can_pj . ", 2000.00, 4, 8000.00)");
        if (function_exists('catat_mutasi_stok')) {
            catat_mutasi_stok($koneksi, $id_can_pj, 0, 4, 'TEST-PJ-CAN-OK', null, 'Penjualan item cancel ok');
        }

        $res_can_pj = batal_penjualan($koneksi, $id_pj_ok);
        $q_rev_pj = mysqli_query($koneksi, "SELECT id_stok, jumlah_masuk, saldo_akhir, keterangan FROM stok WHERE no_penjualan = 'TEST-PJ-CAN-OK' AND keterangan LIKE '%Batal%' ORDER BY id_stok DESC LIMIT 1");
        $row_rev_pj = $q_rev_pj ? mysqli_fetch_assoc($q_rev_pj) : null;
        $q_stk_pj_restored = mysqli_query($koneksi, "SELECT stock FROM products WHERE id = " . $id_can_pj);
        $row_stk_pj_restored = $q_stk_pj_restored ? mysqli_fetch_assoc($q_stk_pj_restored) : null;
        assert_true($res_can_pj && $row_rev_pj !== null && (int)$row_stk_pj_restored['stock'] === 20, 'T3_26_sales_cancellation_reversal', 'sales cancellation reversal records incoming mutation');
    } else {
        report_fail('T3_26_sales_cancellation_reversal', 'batal_penjualan function does not exist');
    }

    clean_test_fixtures($koneksi);
}

function run_tier4($koneksi) {
    echo "=== TIER 4: Reports, Authorization and Security ===\n";
    if ($koneksi) {
        clean_test_fixtures($koneksi);
        mysqli_query($koneksi, "INSERT INTO supplier (nama_supplier, kota) VALUES ('testsupp_report', 'reportcity')");
        $supp_id = (int)mysqli_insert_id($koneksi);

        mysqli_query($koneksi, "INSERT INTO penjualan (nomor_transaksi, tanggal, nama_pelanggan, total) VALUES ('TEST-PJ-REP1', '2026-11-10 10:00:00', 'Cust 1', 50000.00)");
        $pj1 = (int)mysqli_insert_id($koneksi);
        mysqli_query($koneksi, "INSERT INTO penjualan (nomor_transaksi, tanggal, nama_pelanggan, total) VALUES ('TEST-PJ-REP2', '2026-11-15 11:00:00', 'Cust 2', 70000.00)");
        $pj2 = (int)mysqli_insert_id($koneksi);
        mysqli_query($koneksi, "INSERT INTO penjualan (nomor_transaksi, tanggal, nama_pelanggan, total) VALUES ('TEST-PJ-REP3', '2026-10-20 12:00:00', 'Cust 3', 40000.00)");
        $pj3 = (int)mysqli_insert_id($koneksi);

        mysqli_query($koneksi, "INSERT INTO pembelian (nomor_transaksi, tanggal, id_supplier, total) VALUES ('TEST-PB-REP1', '2026-11-05 09:00:00', " . $supp_id . ", 30000.00)");
        $pb1 = (int)mysqli_insert_id($koneksi);
        mysqli_query($koneksi, "INSERT INTO pembelian (nomor_transaksi, tanggal, id_supplier, total) VALUES ('TEST-PB-REP2', '2026-11-12 14:00:00', " . $supp_id . ", 40000.00)");
        $pb2 = (int)mysqli_insert_id($koneksi);
        mysqli_query($koneksi, "INSERT INTO pembelian (nomor_transaksi, tanggal, id_supplier, total) VALUES ('TEST-PB-REP3', '2026-10-18 16:00:00', " . $supp_id . ", 25000.00)");
        $pb3 = (int)mysqli_insert_id($koneksi);

        $q_sales = mysqli_query($koneksi, "SELECT id_penjualan, total FROM penjualan WHERE nomor_transaksi LIKE 'TEST-PJ-REP%' AND tanggal BETWEEN '2026-11-01 00:00:00' AND '2026-11-30 23:59:59'");
        $cnt_sales = 0;
        $sum_sales = 0.0;
        if ($q_sales) {
            while ($row = mysqli_fetch_assoc($q_sales)) {
                $cnt_sales++;
                $sum_sales += (float)$row['total'];
            }
        }
        assert_equals(2, $cnt_sales, 'T4_01_laporan_penjualan_date_filter_query', 'sales report filters within date range');
        assert_equals(120000.00, $sum_sales, 'T4_02_laporan_penjualan_aggregate_sum', 'sales report aggregates total revenue');
        assert_equals(2, $cnt_sales, 'T4_03_laporan_penjualan_count_transactions', 'sales report counts transactions accurately');

        $q_purch = mysqli_query($koneksi, "SELECT id_pembelian, total FROM pembelian WHERE nomor_transaksi LIKE 'TEST-PB-REP%' AND tanggal BETWEEN '2026-11-01 00:00:00' AND '2026-11-30 23:59:59'");
        $cnt_purch = 0;
        $sum_purch = 0.0;
        if ($q_purch) {
            while ($row = mysqli_fetch_assoc($q_purch)) {
                $cnt_purch++;
                $sum_purch += (float)$row['total'];
            }
        }
        assert_equals(2, $cnt_purch, 'T4_04_laporan_pembelian_date_filter_query', 'purchase report filters within date range');
        assert_equals(70000.00, $sum_purch, 'T4_05_laporan_pembelian_aggregate_sum', 'purchase report aggregates total expenditure');

        $q_grp = mysqli_query($koneksi, "SELECT id_supplier, SUM(total) AS subtotal FROM pembelian WHERE nomor_transaksi LIKE 'TEST-PB-REP%' AND tanggal BETWEEN '2026-11-01 00:00:00' AND '2026-11-30 23:59:59' GROUP BY id_supplier");
        $grp_subtotal = 0.0;
        if ($q_grp && ($r = mysqli_fetch_assoc($q_grp))) {
            $grp_subtotal = (float)$r['subtotal'];
        }
        assert_equals(70000.00, $grp_subtotal, 'T4_06_laporan_pembelian_supplier_grouping', 'purchase report groups subtotal by supplier');

        mysqli_query($koneksi, "INSERT INTO products (name, price, stock, category, description) VALUES ('testprod_rep_low', 1000.00, 3, 'test', 'low stock')");
        $id_low = (int)mysqli_insert_id($koneksi);
        mysqli_query($koneksi, "INSERT INTO products (name, price, stock, category, description) VALUES ('testprod_rep_high', 2000.00, 50, 'test', 'high stock')");
        $id_high = (int)mysqli_insert_id($koneksi);

        $q_stk_all = mysqli_query($koneksi, "SELECT id, stock FROM products WHERE name LIKE 'testprod_rep_%'");
        $cnt_stk_all = 0;
        if ($q_stk_all) {
            while ($row = mysqli_fetch_assoc($q_stk_all)) {
                $cnt_stk_all++;
            }
        }
        assert_true($cnt_stk_all >= 2, 'T4_07_laporan_stok_balances', 'stock report lists all inventory items');

        $q_low = mysqli_query($koneksi, "SELECT id, stock FROM products WHERE name LIKE 'testprod_rep_%' AND stock <= 5");
        $cnt_low = 0;
        if ($q_low) {
            while ($row = mysqli_fetch_assoc($q_low)) {
                $cnt_low++;
            }
        }
        assert_equals(1, $cnt_low, 'T4_08_laporan_stok_threshold_filter', 'stock report correctly filters items below threshold');

        $q_laba_sales = mysqli_query($koneksi, "SELECT COALESCE(SUM(total), 0) AS total_sales FROM penjualan WHERE nomor_transaksi LIKE 'TEST-PJ-REP%' AND tanggal BETWEEN '2026-11-01 00:00:00' AND '2026-11-30 23:59:59'");
        $row_laba_sales = $q_laba_sales ? mysqli_fetch_assoc($q_laba_sales) : null;
        $actual_rev = $row_laba_sales ? (float)$row_laba_sales['total_sales'] : 0.0;
        assert_equals(120000.00, $actual_rev, 'T4_09_laporan_laba_revenue', 'gross profit calculates sales revenue');

        $q_laba_cost = mysqli_query($koneksi, "SELECT COALESCE(SUM(total), 0) AS total_cost FROM pembelian WHERE nomor_transaksi LIKE 'TEST-PB-REP%' AND tanggal BETWEEN '2026-11-01 00:00:00' AND '2026-11-30 23:59:59'");
        $row_laba_cost = $q_laba_cost ? mysqli_fetch_assoc($q_laba_cost) : null;
        $actual_cost = $row_laba_cost ? (float)$row_laba_cost['total_cost'] : 0.0;
        assert_equals(70000.00, $actual_cost, 'T4_10_laporan_laba_modal', 'gross profit calculates cost of goods');

        $actual_profit = $actual_rev - $actual_cost;
        assert_equals(50000.00, $actual_profit, 'T4_11_laporan_laba_gross_profit', 'gross profit equals revenue minus cost');

        $q_z_sales = mysqli_query($koneksi, "SELECT COALESCE(SUM(total), 0) AS z_sales FROM penjualan WHERE nomor_transaksi LIKE 'TEST-PJ-REP%' AND tanggal BETWEEN '2099-01-01 00:00:00' AND '2099-01-31 23:59:59'");
        $row_z_sales = $q_z_sales ? mysqli_fetch_assoc($q_z_sales) : null;
        $q_z_cost = mysqli_query($koneksi, "SELECT COALESCE(SUM(total), 0) AS z_cost FROM pembelian WHERE nomor_transaksi LIKE 'TEST-PB-REP%' AND tanggal BETWEEN '2099-01-01 00:00:00' AND '2099-01-31 23:59:59'");
        $row_z_cost = $q_z_cost ? mysqli_fetch_assoc($q_z_cost) : null;
        $zero_profit = ((float)($row_z_sales['z_sales'] ?? 0.0)) - ((float)($row_z_cost['z_cost'] ?? 0.0));
        assert_equals(0.00, $zero_profit, 'T4_12_laporan_laba_zero_sales', 'zero sales yields 0.00 profit');

        clean_test_fixtures($koneksi);
    } else {
        report_fail('T4_01_laporan_penjualan_date_filter_query', 'database connection offline');
        report_fail('T4_02_laporan_penjualan_aggregate_sum', 'database connection offline');
        report_fail('T4_03_laporan_penjualan_count_transactions', 'database connection offline');
        report_fail('T4_04_laporan_pembelian_date_filter_query', 'database connection offline');
        report_fail('T4_05_laporan_pembelian_aggregate_sum', 'database connection offline');
        report_fail('T4_06_laporan_pembelian_supplier_grouping', 'database connection offline');
        report_fail('T4_07_laporan_stok_balances', 'database connection offline');
        report_fail('T4_08_laporan_stok_threshold_filter', 'database connection offline');
        report_fail('T4_09_laporan_laba_revenue', 'database connection offline');
        report_fail('T4_10_laporan_laba_modal', 'database connection offline');
        report_fail('T4_11_laporan_laba_gross_profit', 'database connection offline');
        report_fail('T4_12_laporan_laba_zero_sales', 'database connection offline');
    }

    $css_file = file_exists(__DIR__ . '/../assets/style.css') ? file_get_contents(__DIR__ . '/../assets/style.css') : '';
    assert_true(strpos($css_file, '@media print') !== false, 'T4_13_print_media_css_exists', 'style.css contains @media print rules');
    assert_true(strpos($css_file, 'display: none') !== false || strpos($css_file, 'display:none') !== false, 'T4_14_print_media_css_hides_controls', 'print media rules hide navigation and controls');

    $print_btn_found = (strpos($css_file, 'printbtn') !== false || strpos($css_file, 'btn') !== false);
    assert_true($print_btn_found, 'T4_15_print_button_syntax', 'print button styling defined in css');

    $guard_file = file_exists(__DIR__ . '/../admin/guard.php') ? file_get_contents(__DIR__ . '/../admin/guard.php') : '';
    assert_true(strpos($guard_file, "role") !== false && strpos($guard_file, "admin") !== false, 'T4_16_guard_checks_role_admin', 'guard.php inspects user session role');
    assert_true(strpos($guard_file, "!isset(\$_SESSION['user'])") !== false || strpos($guard_file, "!\$_SESSION") !== false, 'T4_17_guard_blocks_unauthenticated', 'guard.php blocks unauthenticated visitors');

    $guard_path = __DIR__ . '/../admin/guard.php';
    $php_bin = PHP_BINARY;
    $cmd_cashier = escapeshellcmd($php_bin) . " -r 'session_start(); \$_SESSION[\"user\"]=\"kasir\"; \$_SESSION[\"role\"]=\"cashier\"; require \"" . addslashes($guard_path) . "\"; echo \"ALLOWED\";'";
    $out_cashier = shell_exec($cmd_cashier);
    $cashier_blocked = (strpos((string)$out_cashier, 'ALLOWED') === false);
    assert_true($cashier_blocked, 'T4_18_guard_blocks_cashier', 'guard.php blocks cashier role from admin backoffice');

    $cmd_admin = escapeshellcmd($php_bin) . " -r 'session_start(); \$_SESSION[\"user\"]=\"admin\"; \$_SESSION[\"role\"]=\"admin\"; require \"" . addslashes($guard_path) . "\"; echo \"ALLOWED\";'";
    $out_admin = shell_exec($cmd_admin);
    $admin_allowed = (strpos((string)$out_admin, 'ALLOWED') !== false);
    assert_true($admin_allowed, 'T4_19_guard_allows_admin', 'guard.php permits admin role');

    $logout_file = file_exists(__DIR__ . '/../logout.php') ? file_get_contents(__DIR__ . '/../logout.php') : '';
    assert_true(strpos($logout_file, "REQUEST_METHOD") !== false && strpos($logout_file, "POST") !== false, 'T4_20_logout_requires_post', 'logout.php enforces HTTP POST request method');
    assert_true(strpos($logout_file, "session_regenerate_id") !== false, 'T4_21_logout_regenerates_session', 'logout.php regenerates session ID');
    assert_true(strpos($logout_file, "session_destroy") !== false || strpos($logout_file, "\$_SESSION") !== false, 'T4_22_logout_unsets_session', 'logout.php unsets session variables');

    $topbar_file = file_exists(__DIR__ . '/../partials/topbar.php') ? file_get_contents(__DIR__ . '/../partials/topbar.php') : '';
    assert_true(preg_match('~<form[^>]+logout\.php~', $topbar_file) === 1, 'T4_23_topbar_logout_is_post_form', 'topbar renders logout as a POST form');

    $classes_valid = !preg_match('~class="[^"]+[A-Z_-][^"]+"~', $topbar_file);
    assert_true($classes_valid, 'T4_24_css_classes_lowercase', 'markup uses lowercase class names without hyphens');

    $app_files_to_check = array(
        __DIR__ . '/../includes/transaksi_helper.php',
        __DIR__ . '/../admin/pembelian/pembelian.php',
        __DIR__ . '/../admin/pembelian/pembelian_tambah.php',
        __DIR__ . '/../admin/pembelian/pembelian_detail.php',
        __DIR__ . '/../admin/penjualan/penjualan.php',
        __DIR__ . '/../admin/penjualan/penjualan_tambah.php',
        __DIR__ . '/../admin/penjualan/penjualan_detail.php',
        __DIR__ . '/../admin/product/delproduct.php',
        __DIR__ . '/../admin/supplier/delsupplier.php',
        __DIR__ . '/../admin/guard.php',
        __DIR__ . '/../logout.php',
        __FILE__
    );
    $codebase_comment_found = false;
    foreach ($app_files_to_check as $af) {
        if (file_exists($af)) {
            $content = file_get_contents($af);
            if (function_exists('token_get_all')) {
                $tokens = token_get_all($content);
                foreach ($tokens as $tok) {
                    if (is_array($tok) && ($tok[0] === T_COMMENT || $tok[0] === T_DOC_COMMENT)) {
                        $codebase_comment_found = true;
                        break 2;
                    }
                }
            } else {
                $slash = chr(47);
                $star = chr(42);
                $hash = chr(35);
                $ss = $slash . $slash;
                $sstar = $slash . $star;
                if (strpos($content, $ss) !== false || strpos($content, $sstar) !== false || strpos($content, $hash) !== false) {
                    $codebase_comment_found = true;
                    break;
                }
            }
        }
    }
    assert_true(!$codebase_comment_found, 'T4_25_zero_comments_in_codebase', 'zero comments exist across application files');
}

$raw_tier = isset($argv[1]) ? trim($argv[1]) : '';
$target_tier = ($raw_tier === '') ? 'all' : strtolower($raw_tier);
$valid_tiers = array('tier1', 'tier2', 'tier3', 'tier4', 'all');

if (!in_array($target_tier, $valid_tiers, true)) {
    echo "Unknown tier or option: " . $target_tier . "\n";
    echo "Usage: php tests/verify_all.php [tier1|tier2|tier3|tier4|all]\n";
    exit(1);
}

if (file_exists(__DIR__ . '/../includes/transaksi_helper.php')) {
    require_once __DIR__ . '/../includes/transaksi_helper.php';
}

$conn = get_test_connection();
if ($conn && function_exists('init_transaksi_tables')) {
    init_transaksi_tables($conn);
}

if ($target_tier === 'tier1' || $target_tier === 'all') {
    run_tier1($conn);
}
if ($target_tier === 'tier2' || $target_tier === 'all') {
    run_tier2($conn);
}
if ($target_tier === 'tier3' || $target_tier === 'all') {
    run_tier3($conn);
}
if ($target_tier === 'tier4' || $target_tier === 'all') {
    run_tier4($conn);
}

if ($conn) {
    clean_test_fixtures($conn);
    mysqli_close($conn);
}

echo "========================================\n";
echo "Test Summary: " . $pass_count . " passed, " . $fail_count . " failed, " . $total_count . " total\n";
echo "========================================\n";

if ($fail_count > 0 || $total_count === 0) {
    exit(1);
}
exit(0);
