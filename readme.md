## running local tutorial
1. idupin xampp (atau lampp kalo nuxnux)
    - sudo /opt/lampp/lampp/ startmysql (nuxnux)
    - /opt/lampp/bin/php -S localhost:8080 (nuxnux from xampp)

2. idupin ekstensi dan jalanin local
    - php -d extensions=mysqli -S localhost:8080
        - sudo apt update && sudo apt install -y php-mysql (nuxnux mint)
        - sudo pacman -S php (nuxnux arch)
        - sudo dnf install -y php-mysqlnd (nuxnux fedora)

3. ke browser ketik di address bar
    - localhost:8080

4. done gitu aja

5. extra : cara matiin php dan xampp (terminal)
    - ctrl + c (matiin php)
    - sudo /opt/lampp/lampp stop (matiin xampp (nuxnux)) 

# alur work kita
## aturan universal
- mau push main harus pull-request dan tunggu persetujuan devops (alfred)
- kalo buat branch baru apus kalo udah ga kepake 
- boleh dan bebas di assist tapi wajib paham kodenya 
- cek hasil kerja satu sama lain dan beritahu opini2 kalian

## aturan branch
- main buat deployment ke website
- kerja buat development kode
- backup untuk failsafe kalo main rusak
- 3 itu aja, sisanya bebas

## aturan contribute
- kalo pake ai dipikir  mas (suruh baca INSTRUCTION.md)
- commit ga boleh ga mikir
- jangan asal push apalagi force
- kalo mau push ke main setidaknya denger mommy asmr dulu (di branch lain udah aman (stabil))
- wajib pull sebelum ngoding
- jangan komentar di kodenya langsung
- pastikan cek branch saat ini
- intinya jangan ai slop dah
- pastikan gambarnya .webp (biar loading web nya ga kelamaan)

## tugas
- liat di todo.md
- inisiatif 