// assets/js/manage/modals/add-subject.js

window.initAddSubjectModal = function () {
    const modal = document.getElementById('addSubjectModal');
    const cancelBtn = document.getElementById('btncancel');
    const addBtn = document.getElementById('add-btn-subject');

    if (!modal || !addBtn || !cancelBtn) return;

    addBtn.addEventListener('click', () => {
        modal.classList.remove('hidden');
    });

    cancelBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.add('hidden');
    });
};
