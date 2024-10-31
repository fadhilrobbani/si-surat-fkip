import "./bootstrap";
import "flowbite";
import "./editor";
import setupEditors from "./editor";
import Alpine from "alpinejs";

window.Alpine = Alpine;
window.setupEditors = setupEditors;
Alpine.start();

window.removeOutsideDiv = function (id) {
    // Ambil elemen dengan id yang sesuai
    const wysiwygContent = document.getElementById(id).innerHTML;

    // Menggunakan DOMParser untuk menghapus elemen luar
    const parser = new DOMParser();
    const doc = parser.parseFromString(wysiwygContent, "text/html");

    // Ambil isi tanpa elemen div dengan filter childNodes
    const contentWithoutDiv = Array.from(doc.body.childNodes)
        .filter((node) => node.nodeType === Node.ELEMENT_NODE) // Pastikan hanya mengambil node elemen
        .map((node) => node.outerHTML) // Dapatkan HTML dari setiap node
        .join(""); // Gabungkan menjadi satu string

    return contentWithoutDiv;
};
