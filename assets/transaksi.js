function tambahbaris() {
    var template = document.getElementById("templatebaris");
    var tbody = document.getElementById("badanitem") || document.getElementById("itembody") || document.getElementById("itemsbody");
    if (!tbody) {
        return;
    }
    if (template && template.content) {
        var clone = template.content.cloneNode(true);
        tbody.appendChild(clone);
    } else {
        var firstrow = tbody.querySelector("tr");
        if (firstrow) {
            var newrow = firstrow.cloneNode(true);
            var inputs = newrow.querySelectorAll("input, select");
            for (var i = 0; i < inputs.length; i++) {
                if (inputs[i].tagName.toLowerCase() === "select") {
                    inputs[i].selectedIndex = 0;
                } else if (inputs[i].classList.contains("qtyinput") || inputs[i].name === "qty[]" || inputs[i].name === "jumlah[]") {
                    inputs[i].value = "1";
                } else {
                    inputs[i].value = "0.00";
                }
            }
            tbody.appendChild(newrow);
        }
    }
    hitunggrandtotal();
}

function hapusbaris(btn) {
    var row = btn.closest("tr");
    if (!row) {
        return;
    }
    var tbody = row.closest("tbody") || document.getElementById("badanitem") || document.getElementById("itembody") || document.getElementById("itemsbody");
    if (!tbody) {
        return;
    }
    var rows = tbody.querySelectorAll("tr");
    if (rows.length > 1) {
        row.parentNode.removeChild(row);
    } else {
        var select = row.querySelector("select");
        if (select) {
            select.selectedIndex = 0;
        }
        var price = row.querySelector(".priceinput, input[name='harga[]'], input[name='harga_beli[]']");
        if (price) {
            price.value = "0.00";
        }
        var qty = row.querySelector(".qtyinput, input[name='qty[]'], input[name='jumlah[]']");
        if (qty) {
            qty.value = "1";
        }
        var subtotal = row.querySelector(".subtotalinput, input[name='subtotal[]']");
        if (subtotal) {
            subtotal.value = "0.00";
        }
    }
    hitunggrandtotal();
}

function pilihitem(select) {
    var row = select.closest("tr");
    if (!row) {
        return;
    }
    var opt = select.options[select.selectedIndex];
    var harga = opt ? (opt.getAttribute("data-harga") || 0) : 0;
    var priceinput = row.querySelector(".priceinput, input[name='harga[]']");
    if (priceinput) {
        priceinput.value = parseFloat(harga).toFixed(2);
    }
    hitungbaris(select);
}

function hitungbaris(el) {
    var row = el.closest("tr");
    if (!row) {
        return;
    }
    var priceel = row.querySelector(".priceinput, input[name='harga[]'], input[name='harga_beli[]']");
    var qtyel = row.querySelector(".qtyinput, input[name='qty[]'], input[name='jumlah[]']");
    var subtotalel = row.querySelector(".subtotalinput, input[name='subtotal[]']");
    var price = priceel ? (parseFloat(priceel.value) || 0) : 0;
    var qty = qtyel ? (parseInt(qtyel.value) || 0) : 0;
    var subtotal = price * qty;
    if (subtotalel) {
        if (subtotalel.tagName && subtotalel.tagName.toLowerCase() === "input") {
            subtotalel.value = subtotal.toFixed(2);
        } else {
            subtotalel.textContent = subtotal.toFixed(2);
        }
    }
    hitunggrandtotal();
}

function hitunggrandtotal() {
    var tbody = document.getElementById("badanitem") || document.getElementById("itembody") || document.getElementById("itemsbody");
    var grand = 0;
    if (tbody) {
        var subtotals = tbody.querySelectorAll(".subtotalinput, input[name='subtotal[]']");
        for (var i = 0; i < subtotals.length; i++) {
            var val = subtotals[i].value !== undefined ? subtotals[i].value : subtotals[i].textContent;
            grand += parseFloat(val) || 0;
        }
    }
    var totalel = document.getElementById("grandtotal");
    if (totalel) {
        if (totalel.tagName && totalel.tagName.toLowerCase() === "input") {
            totalel.value = grand.toFixed(2);
        } else {
            totalel.textContent = grand.toFixed(2);
        }
    }
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", hitunggrandtotal);
} else {
    hitunggrandtotal();
}
