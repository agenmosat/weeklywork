# oke kawan2 karna ada yang belom tau cara run lokal sini le ditutorin
1. idupin xampp (atau lampp kalo nuxnux)
    sudo /opt/lampp/lampp/ startmysql (nuxnux)
    cd C:\xampp && .\xampp_start.exe (winblot)

2. idupin ekstensi dan jalanin local
    php -d extensions=mysqli -S localhost:8080
        sudo apt update && sudo apt install -y php-mysql (nuxnux mint)
        sudo pacman -S php (nuxnux arch)
        sudo dnf install -y php-mysqlnd (nuxnux fedora)

3. ke browser ketik di address bar
    localhost:8080

4. done gitu aja

5. extra : cara matiin
    ctrl + c (di terminal)



<>><><><><><><><><><><><>



# jadi gini le
## aturan branch
- main buat deployment
- branch-ini buat basing basingan aja
- branch-itu buat testing code 
- branch-mana buat testing terakhir

## aturan contrib
- wajib commit sekali sehari
- kalo pake ai dipikir  mas (suruh baca INSTRUCTION.md)
- commit ga boleh ga mikir
- jangan asal push ke main apalagi force
- kalo mau push ke main setidaknya denger asmr dulu (di brannch lain udah aman (stabil))
- wajib pull sebelum ngoding
- jangan komentar di kodenya langsung
- pastikan cek branch saat ini
- intinya jangan ai slop dah
- pastikan gambarnya .webp (biar loading web nya ga kelamaan)

## setup lokal
- copy `config/database.example.php` jadi `config/database.php`, isi kredensial database lokal
- copy `token.example.php` jadi `token.php`, isi token login lokal
- `config/database.php` dan `token.php` ga ikut ke git, di server dibuat dari GitHub Secrets

## tugas
- Lengkapi modul kasir dan supplier
- Fokus di index dan tambah dahulu

- Silahkan diatur tasknya oleh ktuanya
- Wajib semua paham ya