(function () {
    function apply() {
        if (window.innerWidth < 850) return;
        var infoCol = document.querySelector('.product-info-sticky');
        if (!infoCol) return;

        infoCol.style.position = 'sticky';
        infoCol.style.top = '90px';
        infoCol.style.alignSelf = 'flex-start';

        var node = infoCol.parentElement;
        while (node && node.tagName !== 'BODY') {
            var cs = window.getComputedStyle(node);
            if (cs.overflow === 'hidden' || cs.overflowX === 'hidden' || cs.overflowY === 'hidden' ||
                cs.overflow === 'auto' || cs.overflow === 'scroll') {
                node.style.overflow = 'visible';
            }
            node = node.parentElement;
        }
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', apply);
    else apply();
})();
