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

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () { inittheme(); markactivelinks(); });
} else {
    inittheme();
    markactivelinks();
}
