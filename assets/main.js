function switchtheme(theme) {
    if (document.body.classList.contains("marketpage")) {
        document.body.className = "marketpage " + theme;
    }
    localStorage.setItem("theme", theme);
}

function inittheme() {
    var savedtheme = localStorage.getItem("theme");
    if (savedtheme && document.body.classList.contains("marketpage")) {
        document.body.className = "marketpage " + savedtheme;
        var sel = document.getElementById("themepick");
        if (sel) {
            sel.value = savedtheme;
        }
    }
}

function toggleshow() {
    var field = document.getElementById("token");
    if (field) {
        if (field.type === "password") {
            field.type = "text";
        } else {
            field.type = "password";
        }
    }
}

function confirmclear() {
    return confirm("are you sure you want to clear the cart?");
}

function confirmremove() {
    return confirm("are you sure you want to remove this item?");
}

function confirmdelete() {
    return confirm("are you sure you want to delete this?");
}

function confirmoff() {
    return confirm("are you sure you want to deactivate this cashier?");
}

function markactivelinks() {
    var here = (location.pathname.split("/").pop() || "index.php").toLowerCase();
    document.querySelectorAll(".navlink, .sidelink").forEach(function (a) {
        var href = (a.getAttribute("href") || "").split("?")[0].split("/").pop().toLowerCase();
        if (href && href === here) {
            a.classList.add("is-active");
        }
    });
}

function initHeroBanner() {
    var slider = document.getElementById("herobanner") || document.getElementById("heroBanner");
    if (!slider) return;

    var slides = slider.querySelectorAll(".bannerslide, .herobanner-slide");
    var dots = slider.querySelectorAll(".bannerdot, .banner-dot");
    var prevBtn = document.getElementById("bannerprev") || document.getElementById("bannerPrev");
    var nextBtn = document.getElementById("bannernext") || document.getElementById("bannerNext");
    if (!slides || slides.length === 0) return;

    var currentIndex = 0;
    var timer = null;

    function showSlide(index) {
        if (index < 0) {
            index = slides.length - 1;
        } else if (index >= slides.length) {
            index = 0;
        }
        currentIndex = index;

        slides.forEach(function (slide, i) {
            if (i === currentIndex) {
                slide.classList.add("active");
                slide.classList.add("is-active");
            } else {
                slide.classList.remove("active");
                slide.classList.remove("is-active");
            }
        });

        dots.forEach(function (dot, i) {
            if (i === currentIndex) {
                dot.classList.add("active");
                dot.classList.add("is-active");
            } else {
                dot.classList.remove("active");
                dot.classList.remove("is-active");
            }
        });
    }

    function nextSlide() {
        showSlide(currentIndex + 1);
    }

    function prevSlide() {
        showSlide(currentIndex - 1);
    }

    function startAutoPlay() {
        stopAutoPlay();
        timer = setInterval(nextSlide, 5000);
    }

    function stopAutoPlay() {
        if (timer) {
            clearInterval(timer);
            timer = null;
        }
    }

    if (prevBtn) {
        prevBtn.addEventListener("click", function (e) {
            e.preventDefault();
            prevSlide();
            startAutoPlay();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener("click", function (e) {
            e.preventDefault();
            nextSlide();
            startAutoPlay();
        });
    }

    dots.forEach(function (dot) {
        dot.addEventListener("click", function (e) {
            e.preventDefault();
            var targetIdx = parseInt(dot.getAttribute("data-slide"), 10);
            if (!isNaN(targetIdx)) {
                showSlide(targetIdx);
                startAutoPlay();
            }
        });
    });

    slider.addEventListener("mouseenter", stopAutoPlay);
    slider.addEventListener("mouseleave", startAutoPlay);

    showSlide(0);
    startAutoPlay();
}

function showmoreitems() {
    var hiddenitems = document.querySelectorAll(".grid .item.loadmorehidden");
    var batch = 35;
    for (var i = 0; i < batch && i < hiddenitems.length; i++) {
        hiddenitems[i].classList.remove("loadmorehidden");
    }
    var remaining = document.querySelectorAll(".grid .item.loadmorehidden");
    if (remaining.length === 0) {
        var btn = document.getElementById("loadmorebtn");
        var info = document.getElementById("loadmoreinfo");
        if (btn) {
            btn.style.display = "none";
        }
        if (info) {
            info.style.display = "block";
        }
    }
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
        inittheme();
        markactivelinks();
        initHeroBanner();
    });
} else {
    inittheme();
    markactivelinks();
    initHeroBanner();
}
