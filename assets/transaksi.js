function hitungsubtotal() {
    var harga = parseFloat(document.getElementById("harga").value) || 0;
    var qty = parseInt(document.getElementById("qty").value) || 0;
    var subtotal = harga * qty;
    var subtotalfield = document.getElementById("subtotal");
    if (subtotalfield) {
        subtotalfield.value = subtotal.toFixed(2);
    }
}

function pilihbarang() {
    var select = document.getElementById("id_product");
    if (!select) {
        return;
    }
    var selected = select.options[select.selectedIndex];
    var harga = selected.getAttribute("data-harga");
    var hargafield = document.getElementById("harga");
    if (hargafield && harga) {
        hargafield.value = harga;
        hitungsubtotal();
    }
}

function confirmtransaksi() {
    return confirm("are you sure transaction data is correct?");
}
