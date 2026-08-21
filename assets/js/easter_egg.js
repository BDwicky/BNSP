/**
 * Secret Easter Egg Handler
 * Shortcut: Ctrl + Shift + M
 * atau klik 5x pada logo / titik footer
 */
document.addEventListener('DOMContentLoaded', () => {
    let clickCount = 0;
    let clickTimer = null;

    // 1. Trigger via Keyboard Shortcut (Ctrl + Shift + M)
    document.addEventListener('keydown', (e) => {
        if (e.ctrlKey && e.shiftKey && (e.key === 'M' || e.key === 'm')) {
            e.preventDefault();
            activateModernMode();
        }
    });

    // 2. Trigger via Multi-Click pada elemen class .easter-trigger
    const triggers = document.querySelectorAll('.easter-trigger');
    triggers.forEach(el => {
        el.addEventListener('click', (e) => {
            clickCount++;
            clearTimeout(clickTimer);

            if (clickCount >= 3) {
                clickCount = 0;
                activateModernMode();
            } else {
                clickTimer = setTimeout(() => {
                    clickCount = 0;
                }, 1000);
            }
        });
    });

    function activateModernMode() {
        window.location.href = 'index.php?mode=pro';
    }
});
