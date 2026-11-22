<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/items.php';

$members = [
    [
        'name' => 'Nell',
        'role' => 'project manager & techinal writer',
        'desc' => 'mengkoordinasikan pengerjaan proyek dan integrasi sistem',
        'image' => '../images/team/nell.webp'
    ],
    [
        'name' => 'Gery',
        'role' => 'programmer & system analyst',
        'desc' => 'mengembangkan arsitektur backend dan basis data',
        'image' => '../images/team/gery.webp'
    ],
    [
        'name' => 'Alfredo',
        'role' => 'database manager & devops',
        'desc' => 'mengembangkan fungsionalitas dan logika database',
        'image' => '../images/team/alred.webp'
    ],
    [
        'name' => 'Fahri',
        'role' => 'qa tester & ui/ux designer',
        'desc' => 'menguji kualitas proyek dan memberitahu ketepatan',
        'image' => '../images/team/fahri.webp'
    ],
    [
        'name' => 'Dinda',
        'role' => 'programmer & ui/ux designer',
        'desc' => 'mengembangkan tata letak halaman dan dokumentasi',
        'image' => '../images/team/dinda.webp'
    ]
];

$pagetitle = "tentang kami - fivefonders.net";
require __DIR__ . '/../partials/header.php';
?>

    <?php require __DIR__ . '/../partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="content">
            <div class="heading">tentang kami</div>

            <div class="emptybox aboutbox">
                <img src="../images/teamimage.webp" alt="tim fivefonders.net" class="aboutimage">
                <div class="sidetitle">tentang fivefonders.net</div>
                <div class="detaildesc">
                    fivefonders.net adalah platform penyedia bahan bangunan dan penyewaan alat berat terpercaya untuk mendukung berbagai kebutuhan konstruksi dan proyek bangunan anda.
                </div>
            </div>

            <div class="heading">tim pengembang</div>

            <div class="teamgrid">
                <?php foreach ($members as $m): ?>
                    <div class="teamcard">
                        <div class="photoframe">
                            <img src="<?php echo htmlspecialchars($m['image']); ?>" alt="<?php echo htmlspecialchars($m['name']); ?>" class="memberphoto" onerror="this.src='../images/no-image.webp'">
                        </div>
                        <div class="membername"><?php echo htmlspecialchars($m['name']); ?></div>
                        <div class="memberrole"><?php echo htmlspecialchars($m['role']); ?></div>
                        <div class="memberdesc"><?php echo htmlspecialchars($m['desc']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
