import "./bootstrap";
import "flowbite";
import "./editor";
import setupEditors from "./editor";
import Alpine from "alpinejs";

window.Alpine = Alpine;
window.setupEditors = setupEditors;
Alpine.start();
