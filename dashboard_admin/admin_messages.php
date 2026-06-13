<?php
require_once '../db/db.php';
$messages = $conn->query("SELECT * FROM contacts ORDER BY created_at DESC");
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<a href="admin_dashboard.php" class="btn btn-outline-primary">ត្រឡប់ក្រោយ</a>
<table border="1" width="100%" style="border-collapse: collapse; background: white;">
    <tr style="background: #333; color: white;">
        <th padding="10">ឈ្មោះ</th>
        <th>អ៊ីមែល</th>
        <th>ប្រធានបទ</th>
        <th>សារ</th>
        <th>កាលបរិច្ឆេទ</th>
    </tr>
    <?php while($msg = $messages->fetch_assoc()): ?>
    <tr>
        <td style="padding: 10px;"><?php echo $msg['name']; ?></td>
        <td><?php echo $msg['email']; ?></td>
        <td><?php echo $msg['subject']; ?></td>
        <td><?php echo $msg['message']; ?></td>
        <td><?php echo $msg['created_at']; ?></td>
    </tr>
    <?php endwhile; ?>
</table>