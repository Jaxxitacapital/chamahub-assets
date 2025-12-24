<?php
session_start();
require_once 'db_chamahub.php';

$msg = "";

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
    $file = $_FILES['document'];
    $name = trim($_POST['name']);
    $category = $_POST['category'] ?? 'General';
    $uploaded_by = $_SESSION['user_data']['name'] ?? 'Unknown';

    if ($file['error'] === 0) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('doc_') . '.' . $ext;
        $destination = 'uploads/documents/' . $filename;

        if (!is_dir('uploads/documents')) {
            mkdir('uploads/documents', 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $stmt = $conn->prepare("INSERT INTO documents (name, file_path, uploaded_by, category) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $destination, $uploaded_by, $category);
            $stmt->execute();
            $msg = "✅ File uploaded successfully.";
        } else {
            $msg = "❌ Failed to move file.";
        }
    } else {
        $msg = "❌ File error: " . $file['error'];
    }
}

// Filter inputs
$filter_uploader = $_GET['uploader'] ?? '';
$filter_date = $_GET['date'] ?? '';
$filter_category = $_GET['category'] ?? '';

$query = "SELECT * FROM documents WHERE 1";
$params = [];
$types = "";

if ($filter_uploader) {
    $query .= " AND uploaded_by = ?";
    $params[] = $filter_uploader;
    $types .= "s";
}
if ($filter_date) {
    $query .= " AND DATE(uploaded_at) = ?";
    $params[] = $filter_date;
    $types .= "s";
}
if ($filter_category) {
    $query .= " AND category = ?";
    $params[] = $filter_category;
    $types .= "s";
}
$query .= " ORDER BY uploaded_at DESC";
$stmt = $conn->prepare($query);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>📂 Document Management</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 30px;
            background-color: #f4f6f8;
        }
        h2, h3 { color: #333; }
        form {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        input[type="text"], input[type="file"], select, input[type="date"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover { background: #0056b3; }
        .doc-card {
            background: #fff;
            padding: 12px 15px;
            margin-bottom: 10px;
            border-left: 5px solid #007bff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .doc-card a {
            margin-right: 10px;
            text-decoration: none;
            color: #007bff;
        }
        .msg {
            background: #eaf7ea;
            padding: 10px;
            color: #1b5e20;
            border-left: 4px solid #4caf50;
            margin-bottom: 20px;
        }
        #searchInput {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
        }
    </style>
    <script>
        function searchDocs() {
            var input = document.getElementById("searchInput");
            var filter = input.value.toLowerCase();
            var cards = document.getElementsByClassName("doc-card");
            for (var i = 0; i < cards.length; i++) {
                var name = cards[i].textContent || cards[i].innerText;
                cards[i].style.display = name.toLowerCase().includes(filter) ? "block" : "none";
            }
        }
    </script>
</head>
<body>

<h2>📂 Upload Document</h2>

<?php if ($msg): ?>
    <div class="msg"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Document Name:</label>
    <input type="text" name="name" required>
    <label>Choose File:</label>
    <input type="file" name="document" required>
    <label>Category:</label>
    <select name="category">
        <option value="General">General</option>
        <option value="Finance">Finance</option>
        <option value="Meetings">Meetings</option>
        <option value="Legal">Legal</option>
    </select>
    <button type="submit">⬆ Upload</button>
</form>

<h3>🗂 Filter Documents</h3>
<form method="GET">
    <input type="text" name="uploader" placeholder="Uploader" value="<?= htmlspecialchars($filter_uploader) ?>">
    <input type="date" name="date" value="<?= htmlspecialchars($filter_date) ?>">
    <select name="category">
        <option value="">All Categories</option>
        <option value="General" <?= $filter_category === 'General' ? 'selected' : '' ?>>General</option>
        <option value="Finance" <?= $filter_category === 'Finance' ? 'selected' : '' ?>>Finance</option>
        <option value="Meetings" <?= $filter_category === 'Meetings' ? 'selected' : '' ?>>Meetings</option>
        <option value="Legal" <?= $filter_category === 'Legal' ? 'selected' : '' ?>>Legal</option>
    </select>
    <button type="submit">🔍 Filter</button>
</form>

<input type="text" id="searchInput" onkeyup="searchDocs()" placeholder="🔍 Search by name or uploader...">

<h3>📑 Uploaded Documents</h3>

<?php
if ($result->num_rows > 0):
    while ($doc = $result->fetch_assoc()):
?>
    <div class="doc-card">
        <strong><?= htmlspecialchars($doc['name']) ?></strong><br>
        Category: <?= htmlspecialchars($doc['category']) ?><br>
        Uploaded by: <?= htmlspecialchars($doc['uploaded_by']) ?><br>
        Uploaded on: <?= date("M d, Y H:i", strtotime($doc['uploaded_at'])) ?><br>
        <a href="<?= $doc['file_path'] ?>" target="_blank">📄 View</a>
        <a href="<?= $doc['file_path'] ?>" download>⬇ Download</a>
        <a href="delete_doc.php?id=<?= $doc['id'] ?>" onclick="return confirm('Delete this document?')">🗑 Delete</a>
    </div>
<?php
    endwhile;
else:
    echo "<p>No documents found.</p>";
endif;
?>

<hr>
<form method="POST" action="export_docs.php">
    <button type="submit">📊 Export Report</button>
</form>

</body>
</html>
