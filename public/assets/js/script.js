document.addEventListener('DOMContentLoaded', () => {
    // Add masks
    for (const el of document.querySelectorAll('input[data-mask]')) {
        const mask = el.dataset['mask'];

        if (!mask) {
            continue;
        }

        const maskOptions = {
            mask: mask
        };

        IMask(el, maskOptions);
    }
});
