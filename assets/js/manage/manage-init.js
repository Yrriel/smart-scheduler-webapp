// assets/js/manage/manage-init.js
console.log("manage-init loaded");

document.addEventListener("DOMContentLoaded", () => {
    // Core
    if (window.initTabs) initTabs();
    if (window.initModals) initModals();

    // Tabs
    if (window.initSubjectTab) initSubjectTab();
    if (window.initCourseTab) initCourseTab();
    if (window.initFacultyTab) initFacultyTab();
    if (window.initSectionTab) initSectionTab();
    if (window.initRoomTab) initRoomTab();
    if (window.initAddSubjectModal) initAddSubjectModal();

    // Modals
    if (window.initAssignCourseSubjectModal) initAssignCourseSubjectModal();
    

});
