window.addEventListener("load", function () {
    onHeadInput();
});

function onHeadInput() {
    let txAdminElm = document.getElementById("uf_txadmin_id");
    let steamElm = document.getElementById("uf_steam_name");
    let labelElm = document.getElementById("uf_slider_label");

    if (txAdminElm.value.length > 0) {
        steamElm.setAttribute("disabled", "");
        txAdminElm.removeAttribute("disabled");
        steamElm.style.display = "none";
        txAdminElm.style.removeProperty("display");

        txAdminElm.style.width = "100%";
        steamElm.style.removeProperty("width");
    } else if (steamElm.value.length > 0) {
        txAdminElm.setAttribute("disabled", "");
        steamElm.removeAttribute("disabled");
        txAdminElm.style.display = "none";
        steamElm.style.removeProperty("display");

        steamElm.style.width = "100%";
        txAdminElm.style.removeProperty("width");
    } else {
        txAdminElm.style.removeProperty("display");
        steamElm.style.removeProperty("display");
        txAdminElm.removeAttribute("disabled");
        steamElm.removeAttribute("disabled");

        steamElm.style.removeProperty("width");
        txAdminElm.style.removeProperty("width");
    }
}