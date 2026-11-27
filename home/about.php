<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/items.php';

$members = [
    [
        'name' => 'Nell',
        'word' => '"pls kodingkan dulu le"',
        'role' => 'project manager & techinal writer',
        'desc' => 'mengkoordinasikan pengerjaan proyek dan integrasi sistem',
        'image' => 'images/team/nell.webp',
        'github' => 'https://github.com/NyrfHello'
    ],
    [
        'name' => 'Gery',
        'word' => '"mengapa kalian ke branch saya"',
        'role' => 'programmer & system analyst',
        'desc' => 'mengembangkan arsitektur backend dan basis data',
        'image' => 'images/team/gery.webp',
        'github' => 'https://github.com/Gery-Elvan-Prasetyo'
    ],
    [
        'name' => 'Alfredo',
        'word' => '"ini tolong dianukan dulu ini apanamanya"',
        'role' => 'database manager & devops',
        'desc' => 'mengembangkan fungsionalitas dan logika database',
        'image' => 'images/team/alfredo.webp',
        'github' => 'https://github.com/anotheroom1'
    ],
    [
        'name' => 'Fahri',
        'word' => '"mass we token antum taro mana"',
        'role' => 'qa tester & ui/ux designer',
        'desc' => 'menguji kualitas proyek dan memberitahu ketepatan',
        'image' => 'images/team/fahri.webp',
        'github' => 'https://github.com/frospan110-dev'
    ],
    [
        'name' => 'Dinda',
        'word' => '"ini gimana caranya dah"',
        'role' => 'programmer & ui/ux designer',
        'desc' => 'mengembangkan tata letak halaman dan dokumentasi',
        'image' => 'images/team/dinda.webp',
        'github' => 'https://github.com/dinda2009'
    ]
];

$pagetitle = "tentang kami - fivefonders.net";
require __DIR__ . '/partials/header.php';
?>

    <?php require __DIR__ . '/partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/partials/sidebar.php'; ?>

        <div class="content">
            <div class="heading">tentang kami</div>

            <div class="emptybox aboutbox">
                <img src="images/teamimage.webp" alt="tim fivefonders.net" class="aboutimage">
                <div class="sidetitle">tentang fivefonders.net</div>
                <div class="detaildesc">
                    fivefonders.net adalah aplikasi platform marketplace modern yang dirancang untuk menghubungkan penjual (UMKM maupun individu) dengan pembeli dalam satu ekosistem transaksi yang cepat, transparan, dan aman. Proyek ini dibangun secara kolaboratif oleh tim beranggotakan 5 orang dengan fokus pada pengalaman pengguna (user experience) yang intuitif, keamanan transaksi melalui sistem rekening bersama (escrow), serta efisiensi manajemen toko bagi penjual..
                </div>
            </div>

            <div class="heading" id="tim">tim pengembang</div>

            <div class="teamgrid">
                <?php foreach ($members as $m): ?>
                    <a href="<?php echo htmlspecialchars($m['github']); ?>" target="_blank" rel="noopener noreferrer" class="teamcard">
                        <div class="photoframe">
                            <img src="<?php echo htmlspecialchars($m['image']); ?>" alt="<?php echo htmlspecialchars($m['name']); ?>" class="memberphoto" onerror="this.src='images/no-image.webp'">
                        </div>
                        <div class="membername"><?php echo htmlspecialchars($m['name']); ?></div>
                        <div class="memberword"><?php echo htmlspecialchars($m['word']); ?></div>
                        <div class="memberrole"><?php echo htmlspecialchars($m['role']); ?></div>
                        <div class="memberdesc"><?php echo htmlspecialchars($m['desc']); ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

<?php require __DIR__ . '/partials/footer.php'; ?>
