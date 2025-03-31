let DOMUploadInput = document.querySelector("input[type=file");
let DOMUploadInputHolder = document.querySelector(".upload-input");
let DOMUploadInputSelect = document.querySelector(".upload-input-select");
let DOMUploadInputSelected = document.querySelector(".upload-input-selected");
let DOMUploadInputSelectedFilename = DOMUploadInputSelected.querySelector("div");

DOMUploadInput.addEventListener('change', function() {
    if(DOMUploadInput.value != "") {
        DOMUploadInputHolder.classList.add("selected");
        DOMUploadInputSelect.classList.remove("active");
        DOMUploadInputSelected.classList.add("active");
        DOMUploadInputSelectedFilename.innerText = DOMUploadInput.value.split("\\")[2];
    } else {
        DOMUploadInputHolder.classList.remove("selected");
        DOMUploadInputSelect.classList.add("active");
        DOMUploadInputSelected.classList.remove("active");
    }
});