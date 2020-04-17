let DOMtooltipElements = document.querySelectorAll("*[data-tooltip]");
let DOMtooltip = document.querySelector(".tooltip");

DOMtooltipElements.forEach(function(item) {
    item.addEventListener('mouseover', function(event) {
        ShowTooltip(item);
    });
    item.addEventListener('mouseout', function(event) {
        HideTooltip(item);
    });
});

function ShowTooltip(_tooltipItem) {
    let tooltipPosition = _tooltipItem.dataset.tooltipposition;

    DOMtooltip.innerHTML = _tooltipItem.dataset.tooltip;
    DOMtooltip.classList.add("active");

    switch(tooltipPosition) {
        default:
        case "top":
            DOMtooltip.classList.add("tooltip-top");
            DOMtooltip.style.top = (_tooltipItem.clientY - (_tooltipItem.getBoundingClientRect().height - 15)) + "px";
            DOMtooltip.style.left = (_tooltipItem.clientX + (_tooltipItem.getBoundingClientRect().width / 2) - (DOMtooltip.getBoundingClientRect().width / 2)) + "px";
            break;
        case "right":
            DOMtooltip.classList.add("tooltip-right");
            DOMtooltip.style.top = (_tooltipItem.getBoundingClientRect().y + (_tooltipItem.getBoundingClientRect().height / 2) - (DOMtooltip.getBoundingClientRect().height / 2)) + "px";
            DOMtooltip.style.left = (_tooltipItem.getBoundingClientRect().x + _tooltipItem.getBoundingClientRect().width - 5) + "px";
            break;
    }
}
function HideTooltip(_tooltipItem) {
    DOMtooltip.classList.remove("active");
    DOMtooltip.classList.remove("tooltip-top");
    DOMtooltip.classList.remove("tooltip-right");
}