<div class="menu-section">
    <a href="index.php?days=30" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
        <i class="menu-icon fas fa-home"></i>
        <span class="nav-link">Home</span>
    </a>
    <a href="categories2.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'categories2.php' ? 'active' : ''; ?>">
        <i class="menu-icon fas fa-list"></i>
        <span class="nav-link">Categories</span>
    </a>
    <a href="transactions.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'transactions.php' ? 'active' : ''; ?>">
        <i class="menu-icon fas fa-file-invoice-dollar"></i>
        <span class="nav-link">Transactions</span>
    </a>
    <a id="addIncomeBtn" href="#" class="menu-item">
        <i class="menu-icon fas fa-plus"></i>
        <span class="nav-link">Add Income</span>
    </a>
    <a id="addExpenseBtn" href="#" class="menu-item">
        <i class="menu-icon fas fa-minus"></i>
        <span class="nav-link">Add Expense</span>
    </a>
</div>

