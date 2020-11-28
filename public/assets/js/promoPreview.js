let DOMForm = document.querySelector(".section-moderator-add form");

let DOMFields = DOMForm.querySelectorAll("input, select");

DOMFields.forEach((DOMField) => {
   DOMField.addEventListener('input', () => { updatePreview(); });
});

let DOMInputBanner = DOMForm.querySelector("#form_imagePath");
let DOMInputTitle = DOMForm.querySelector("#form_title");
let DOMInputType = DOMForm.querySelector("#form_type");
let DOMInputTextColor = DOMForm.querySelector("#form_textColor");
let DOMInputPrimaryColor = DOMForm.querySelector("#form_color");

let DOMPreview = document.querySelector(".preview .staff-promo");
let DOMPreviewType = DOMPreview.querySelector(".promo-type");
let DOMPreviewTitle = DOMPreview.querySelector(".promo-title");
let DOMPreviewButton = DOMPreview.querySelector(".promo-button");

function updatePreview() {
    DOMPreviewType.style.color = DOMInputPrimaryColor.value;
    DOMPreviewTitle.style.color = DOMInputTextColor.value;
    DOMPreviewButton.style.backgroundColor = DOMInputPrimaryColor.value;

    DOMPreviewType.innerText = (DOMInputType.value !== "") ? DOMInputType.value : "PromoType";
    DOMPreviewTitle.innerHTML = (DOMInputTitle.value !== "") ? DOMInputTitle.value : "PromoTitle";

    if(DOMInputBanner.files.length > 0) {
        let fileReader = new FileReader();
        fileReader.onload = function() {
            DOMPreview.style.backgroundImage = "url(" + fileReader.result + ")";
        }
        fileReader.readAsDataURL(DOMInputBanner.files[0]);
    } else {
        DOMPreview.style.backgroundImage = "url()";
    }
}

updatePreview();