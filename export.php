<?php
// export.php
include 'db.php';

// Файлыг Excel форматтай гэдгийг хөтөчид мэдэгдэх толгой мэдээлэл
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=Students_RIASEC_Report_" . date('Y-m-d') . ".xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Pragma: public");

// Монгол үсэг задрахаас сэргийлж BOM нэмэх
echo "\xEF\xBB\xBF"; 

$students = $pdo->query("SELECT * FROM students ORDER BY id DESC")->fetchAll();
?>
<table border="1">
    <tr style="background-color: #1E3A8A; color: white; font-weight: bold;">
        <th>ID</th>
        <th>Овог Нэр</th>
        <th>Төгссөн Сургууль</th>
        <th>Утасны дугаар</th>
        <th>Мэйл хаяг</th>
        <th>R оноо</th>
        <th>I оноо</th>
        <th>A оноо</th>
        <th>S оноо</th>
        <th>E оноо</th>
        <th>C оноо</th>
        <th>RIASEC Код</th>
        <th>Бүртгүүлсэн огноо</th>
    </tr>
    <?php foreach($students as $s): ?>
    <tr>
        <td><?php echo $s['id']; ?></td>
        <td><?php echo htmlspecialchars($s['name']); ?></td>
        <td><?php echo htmlspecialchars($s['school']); ?></td>
        <td><?php echo htmlspecialchars($s['phone']); ?></td>
        <td><?php echo htmlspecialchars($s['email']); ?></td>
        <td><?php echo $s['r_score']; ?></td>
        <td><?php echo $s['i_score']; ?></td>
        <td><?php echo $s['a_score']; ?></td>
        <td><?php echo $s['s_score']; ?></td>
        <td><?php echo $s['e_score']; ?></td>
        <td><?php echo $s['c_score']; ?></td>
        <td style="font-weight: bold; color: #2563EB;"><?php echo $s['top_types']; ?></td>
        <td><?php echo $s['created_at']; ?></td>
    </tr>
    <?php endforeach; ?>
</table>