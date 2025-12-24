<aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen bg-white border-r border-gray-200 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
    <div class="p-4 border-b">
        <h2 class="text-xl font-bold text-blue-600">ChamaHub</h2>
    </div>
    <nav class="p-4 space-y-2">
        <a href="dashboard.php" class="block px-4 py-2 rounded hover:bg-blue-100">🏠 Dashboard</a>
        <a href="#" class="block px-4 py-2 rounded hover:bg-blue-100">👥 My Chamas</a>
        <a href="#" class="block px-4 py-2 rounded hover:bg-blue-100">💰 Transactions</a>
        <a href="#" class="block px-4 py-2 rounded hover:bg-blue-100">🔔 Notifications</a>
        <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-red-100">🚪 Logout</a>
    </nav>
</aside>

<!-- Sidebar Toggle for mobile -->
<button onclick="toggleSidebar()" class="fixed z-50 top-4 left-4 md:hidden p-2 rounded-full bg-blue-600 text-white shadow-lg">
    ☰
</button>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('-translate-x-full');
}
</script>
