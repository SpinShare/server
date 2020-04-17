let DOMSearchDifficultiesSelect = document.querySelector(".multi-select-difficulties");

function ToggleDifficultiesBox() {
    if(DOMSearchDifficultiesSelect.querySelector(".box").classList.contains("active")) {
        DOMSearchDifficultiesSelect.querySelector(".box").classList.remove("active");
    } else {
        DOMSearchDifficultiesSelect.querySelector(".box").classList.add("active");
    }
}