<?php if (isset($_GET['success'])): ?>
<script>
  alert('✅ Account updated successfully!');
</script>
<?php endif; ?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard | SmartSched</title>
<link rel="stylesheet" href="../../styleadmin.css">
<link rel="shortcut icon" href="../../src/logo.png">
</head>

<style>
    /* ===== ACCOUNT SETTINGS INPUTS ===== */

.settings-card input {
  width: 100%;
  padding: 12px 1px;
  font-size: 14px;
  border-radius: 10px;
  border: 1.5px solid #cbd5e1;
  background: #f8fafc;
  transition: all 0.2s ease;
  outline: none;
}

/* Hover */
.settings-card input:hover {
  border-color: #94a3b8;
}

/* Focus */
.settings-card input:focus {
  border-color: #304ffe;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(48, 79, 254, 0.15);
}

/* Placeholder */
.settings-card input::placeholder {
  color: #94a3b8;
  font-size: 13px;
}

/* Labels */
.settings-card label {
  font-size: 13px;
  font-weight: 600;
  color: #334155;
  margin-bottom: 6px;
}

/* Spacing */
.settings-card .form-group {
  margin-bottom: 16px;
}

</style>

<body>

<!-- =======================
     NAV 
======================= -->
<nav>
    <div class="nav-listdown">
        <div class="logo-container">
            <span class="img-container"><img src="../../src/logo.png"></span>
            <p>SmartSched</p>
        </div>

        <a href="../page_dashboard/dashboard.php">
            <div class="nav-list nav-dashboard active"><span class="img-container-logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard-icon lucide-layout-dashboard"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
            </span>
                <p>Dashboard</p>
            </div>
        </a>

        <a href="../page_schedule/schedule-ui.php">
            <div class="nav-list nav-schedule"><span class="img-container-logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-heart-icon lucide-calendar-heart"><path d="M12.127 22H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v5.125"/><path d="M14.62 18.8A2.25 2.25 0 1 1 18 15.836a2.25 2.25 0 1 1 3.38 2.966l-2.626 2.856a.998.998 0 0 1-1.507 0z"/><path d="M16 2v4"/><path d="M3 10h18"/><path d="M8 2v4"/></svg>
            </span>
                <p>Schedule</p></div>
        </a>

        <a href="../page_manage/manage.php?tab=course">
            <div class="nav-list nav-schedule"><span class="img-container-logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-folder-kanban-icon lucide-folder-kanban"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/><path d="M8 10v4"/><path d="M12 10v2"/><path d="M16 10v6"/></svg>
            </span>
                <p>Manage</p></div>
        </a>
    </div>

    <div class="nav-listdown-below">
        <a href="../page_account/account.php"><div class="nav-list"><span class="img-container-logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-cog-icon lucide-user-cog"><path d="M10 15H6a4 4 0 0 0-4 4v2"/><path d="m14.305 16.53.923-.382"/><path d="m15.228 13.852-.923-.383"/><path d="m16.852 12.228-.383-.923"/><path d="m16.852 17.772-.383.924"/><path d="m19.148 12.228.383-.923"/><path d="m19.53 18.696-.382-.924"/><path d="m20.772 13.852.924-.383"/><path d="m20.772 16.148.924.383"/><circle cx="18" cy="15" r="3"/><circle cx="9" cy="7" r="4"/></svg>
        </span>
            <p>User Settings</p></div></a>
        <a href="../../backend/login/backend_logout.php"><div class="nav-list"><span class="img-container-logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out-icon lucide-log-out"><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/></svg>
        </span><p>Log Out</p></div></a>
    </div>
</nav>

<section class="section-class">
<div class="container">

    <!-- Header -->
    <div class="content-header">
        <div class="header-text">
            <h1>Account Settings</h1>
        </div>
    </div>

    <div class="settings-card">
  <h2>Change Account Credentials</h2>

  <form method="POST" action="../../backend/page_account/update_credentials.php">
    
    <div class="form-group">
      <label>New Username</label>
      <input type="text" name="username" placeholder="Enter new username" required>
    </div>

    <div class="form-group">
      <label>Current Password</label>
      <input type="password" name="current_password" placeholder="Current password" required>
    </div>

    <div class="form-group">
      <label>New Password</label>
      <input type="password" name="new_password" placeholder="New password" required>
    </div>

    <div class="form-group">
      <label>Confirm New Password</label>
      <input type="password" name="confirm_password" placeholder="Confirm new password" required>
    </div>

    <button type="submit" class="btn-primary">Save Changes</button>
  </form>
</div>




</div>
</section>

</body>
</html>
