/**
 * Base modal helper
 * Usage:
 *   const modal = createModal('addSubjectModal');
 *   modal.open();
 *   modal.close();
 */
console.log("modal-base loaded");

window.createModal = function (modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) {
        console.warn(`Modal not found: ${modalId}`);
        return null;
    }

    function open() {
        modal.classList.remove('hidden');
    }

    function close() {
        modal.classList.add('hidden');
    }

    // click outside closes modal
    modal.addEventListener('click', (e) => {
        if (e.target === modal) close();
    });

    return { open, close, el: modal };
};
