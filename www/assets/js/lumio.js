/**
 * Core JS for Lumio
 *
 * @author TB
 * @date 6.5.2025
 *
 */



document.addEventListener('DOMContentLoaded', () => {



});

/**
 * Copy given text to clipboard
 *
 * @author TB
 * @date 6.5.2025
 *
 * @param text
 */
function copy2clipboard(text) {

    const input = document.createElement('input');
    input.style.position = 'fixed';
    input.style.left = '-10000px';
    input.style.top = '-10000px';
    input.value = text;

    document.body.appendChild(input);
    input.select();
    input.setSelectionRange(0, 99999); // For mobile devices

    try {
        document.execCommand('copy');
    } catch (err) {
        console.error('Copy failed', err);
    }

    document.body.removeChild(input);
}







