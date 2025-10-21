<?php
// Menu de navigation admin à inclure dans le dashboard et les pages admin
if (session_status() === PHP_SESSION_NONE) session_start();

$current = basename($_SERVER['PHP_SELF']);
?>
<nav class="admin-menu">
    <div class="menu-container">
        <a href="dashboard.php" class="menu-item <?php echo $current === 'dashboard.php' ? 'active' : ''; ?>">
            <span class="icon">📊</span>
            <span>Dashboard</span>
        </a>
        <a href="contact.php" class="menu-item <?php echo $current === 'contact.php' ? 'active' : ''; ?>">
            <span class="icon">💬</span>
            <span>FAQ/Contact</span>
        </a>
    </div>
</nav>
<style>
.admin-menu {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    margin-bottom: 25px;
    overflow: hidden;
}

.menu-container {
    display: flex;
    justify-content: center;
    gap: 0;
}

.menu-item {
    flex: 1;
    max-width: 200px;
    padding: 16px 24px;
    text-decoration: none;
    color: #64748b;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s ease;
    border-bottom: 3px solid transparent;
    position: relative;
}

.menu-item .icon {
    font-size: 1.2em;
}

.menu-item:hover {
    background: linear-gradient(to bottom, rgba(102, 126, 234, 0.05), transparent);
    color: #667eea;
}

.menu-item.active {
    color: #667eea;
    background: linear-gradient(to bottom, rgba(102, 126, 234, 0.08), transparent);
    border-bottom-color: #667eea;
    font-weight: 600;
}

@media (max-width: 600px) {
    .menu-container {
        flex-direction: column;
    }
    
    .menu-item {
        max-width: 100%;
        border-bottom: 1px solid #e2e8f0;
        border-left: 3px solid transparent;
    }
    
    .menu-item.active {
        border-bottom: 1px solid #e2e8f0;
        border-left-color: #667eea;
    }
}
</style>
