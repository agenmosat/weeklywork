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

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", inittheme);
} else {
    inittheme();
}
