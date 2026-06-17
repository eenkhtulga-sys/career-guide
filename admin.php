<?php
// admin.php
include 'db.php';

// 1. АСУУЛТ НЭМЭХ
if (isset($_POST['add_question'])) {
    $pdo->prepare("INSERT INTO questions (text, type) VALUES (?, ?)")->execute([$_POST['text'], $_POST['type']]);
    header("Location: admin.php?tab=questions"); exit;
}

// 2. СУРГУУЛЬ НЭМЭХ (ЗАСРАВ: website_url-ийг page_slug болгосон)
if (isset($_POST['add_university'])) {
    $pdo->prepare("INSERT INTO universities (name, description, page_slug) VALUES (?, ?, ?)")->execute([
        $_POST['uni_name'], 
        $_POST['uni_desc'], 
        strtolower(trim($_POST['page_slug']))
    ]);
    header("Location: admin.php?tab=unis"); exit;
}

// 3. МЭРГЭЖИЛ НЭМЭХ
if (isset($_POST['add_major'])) {
    $pdo->prepare("INSERT INTO majors (university_id, name, r_req, i_req, a_req, s_req, e_req, c_req) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")->execute([
        (int)$_POST['university_id'], $_POST['name'], 
        (int)$_POST['r_req'], (int)$_POST['i_req'], (int)$_POST['a_req'], 
        (int)$_POST['s_req'], (int)$_POST['e_req'], (int)$_POST['c_req']
    ]);
    header("Location: admin.php?tab=majors"); exit;
}

// УСТГАХ ҮЙЛДЛҮҮД
if (isset($_GET['del_q'])) { $pdo->prepare("DELETE FROM questions WHERE id = ?")->execute([$_GET['del_q']]); header("Location: admin.php?tab=questions"); exit; }
if (isset($_GET['del_u'])) { $pdo->prepare("DELETE FROM universities WHERE id = ?")->execute([$_GET['del_u']]); header("Location: admin.php?tab=unis"); exit; }
if (isset($_GET['del_m'])) { $pdo->prepare("DELETE FROM majors WHERE id = ?")->execute([$_GET['del_m']]); header("Location: admin.php?tab=majors"); exit; }

$tab = $_GET['tab'] ?? 'results';
$questions = $pdo->query("SELECT * FROM questions ORDER BY id DESC")->fetchAll();
$unis = $pdo->query("SELECT * FROM universities ORDER BY id DESC")->fetchAll();
$students = $pdo->query("SELECT * FROM students ORDER BY id DESC")->fetchAll();

$majors = $pdo->query("
    SELECT m.*, u.name as uni_name 
    FROM majors m 
    LEFT JOIN universities u ON m.university_id = u.id 
    ORDER BY m.id DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="mn">
<head>
    <meta charset="UTF-8">
    <title>Админ Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-7xl mx-auto bg-white p-8 rounded-2xl shadow-md">
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h1 class="text-3xl font-bold text-gray-800">Админ Удирдлага</h1>
            <div class="flex gap-2 bg-gray-100 p-1 rounded-xl text-sm">
                <a href="?tab=results" class="px-4 py-2 rounded-lg font-bold <?php echo $tab=='results'?'bg-white shadow text-blue-600':'' ?>">Үр дүн</a>
                <a href="?tab=questions" class="px-4 py-2 rounded-lg font-bold <?php echo $tab=='questions'?'bg-white shadow text-blue-600':'' ?>">Асуултууд</a>
                <a href="?tab=unis" class="px-4 py-2 rounded-lg font-bold <?php echo $tab=='unis'?'bg-white shadow text-blue-600':'' ?>">Сургуулиуд нэмэх</a>
                <a href="?tab=majors" class="px-4 py-2 rounded-lg font-bold <?php echo $tab=='majors'?'bg-white shadow text-blue-600':'' ?>">Мэргэжил + Оноо удирдах</a>
            </div>
        </div>

        <?php if($tab == 'results'): ?>
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-700 text-lg">Бүртгүүлсэн сурагчид (Нийт: <?php echo count($students); ?>)</h3>
                <a href="export.php" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-2.5 rounded-xl shadow transition-colors flex items-center gap-2">
                    Excel-ээр татах 📥
                </a>
            </div>
            
            <table class="w-full text-left border-collapse border rounded-xl overflow-hidden text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-4">Овог Нэр</th>
                        <th class="p-4">Төгссөн Сургууль</th>
                        <th class="p-4">Утас</th>
                        <th class="p-4">Мэйл хаяг</th>
                        <th class="p-4 text-center">RIASEC Оноо</th>
                        <th class="p-4">Код</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($students as $s): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4 font-bold text-gray-800"><?php echo htmlspecialchars($s['name']) ?></td>
                        <td class="p-4 text-gray-600"><?php echo htmlspecialchars($s['school']) ?></td>
                        <td class="p-4"><?php echo htmlspecialchars($s['phone']) ?></td>
                        <td class="p-4 text-gray-500"><?php echo htmlspecialchars($s['email']) ?></td>
                        <td class="p-4 text-center font-semibold text-blue-600"><?php echo "R:{$s['r_score']} I:{$s['i_score']} A:{$s['a_score']} S:{$s['s_score']} E:{$s['e_score']} C:{$s['c_score']}" ?></td>
                        <td class="p-4"><span class="bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded-full text-xs"><?php echo $s['top_types'] ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if($tab == 'questions'): ?>
            <form action="admin.php" method="POST" class="bg-gray-50 p-6 rounded-xl mb-6 border flex gap-4">
                <input type="text" name="text" placeholder="Шинэ асуулт..." class="flex-1 p-3 rounded-lg border" required>
                <select name="type" class="p-3 rounded-lg border font-bold"><option value="R">R</option><option value="I">I</option><option value="A">A</option><option value="S">S</option><option value="E">E</option><option value="C">C</option></select>
                <button type="submit" name="add_question" class="bg-blue-600 text-white px-6 rounded-lg font-bold">Нэмэх</button>
            </form>
            <div class="space-y-2"><?php foreach($questions as $q): ?><div class="flex justify-between items-center p-4 bg-white border rounded-xl shadow-sm"><div><span class="bg-gray-200 px-2 py-1 rounded mr-3 font-bold"><?php echo $q['type'] ?></span><?php echo htmlspecialchars($q['text']) ?></div><a href="admin.php?del_q=<?php echo $q['id'] ?>" class="text-red-500 font-bold" onclick="return confirm('Устгах уу?')">Устгах</a></div><?php endforeach; ?></div>
        <?php endif; ?>

        <?php if($tab == 'unis'): ?>
            <form action="admin.php" method="POST" class="bg-gray-50 p-6 rounded-xl mb-6 border flex flex-col gap-4">
                <h3 class="font-bold text-lg">Шинэ сургууль бүртгэх</h3>
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="uni_name" placeholder="Сургуулийн нэр (Ж: ҮИТС, СЭЗС)" class="p-3 border rounded-lg" required>
                    <input type="text" name="page_slug" placeholder="Сургуулийн богино код /slug/ (Ж: nets, sezs, hzs)" class="p-3 border rounded-lg" required>
                </div>
                <textarea name="uni_desc" placeholder="Сургуулийн богино танилцуулга..." class="p-3 border rounded-lg h-24"></textarea>
                <button type="submit" name="add_university" class="bg-blue-600 text-white py-2.5 rounded-lg font-bold">Сургууль хадгалах</button>
            </form>
            <div class="grid grid-cols-2 gap-4">
                <?php foreach($unis as $u): ?>
                <div class="p-4 bg-white border rounded-xl shadow-sm flex justify-between items-start">
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="font-bold text-xl text-gray-800"><?php echo htmlspecialchars($u['name']) ?></h4>
                            <span class="bg-gray-100 text-gray-600 text-xs font-mono px-2 py-0.5 rounded">slug: <?php echo htmlspecialchars($u['page_slug'] ?? '') ?></span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1"><?php echo htmlspecialchars($u['description']) ?></p>
                    </div>
                    <a href="admin.php?del_u=<?php echo $u['id'] ?>" class="text-red-500 text-sm font-bold ml-2 whitespace-nowrap" onclick="return confirm('Устгавал дагаж мэргэжлүүд нь алдаа заана шүү!')">Устгах</a>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if($tab == 'majors'): ?>
            <form action="admin.php" method="POST" class="bg-gray-50 p-6 rounded-xl mb-6 border flex flex-col gap-4">
                <h3 class="font-bold text-lg text-gray-700">Мэргэжил нэмэх үед сургуулийг нь сонгоно</h3>
                <div class="flex gap-4">
                    <select name="university_id" class="p-3 rounded-lg border font-bold bg-white" required>
                        <option value="">-- Сургуулийг сонгоно уу --</option>
                        <?php foreach($unis as $u): ?><option value="<?php echo $u['id'] ?>"><?php echo htmlspecialchars($u['name']) ?></option><?php endforeach; ?>
                    </select>
                    <input type="text" name="name" placeholder="Мэргэжлийн нэр (Ж: Программ хангамж)..." class="flex-1 p-3 rounded-lg border" required>
                </div>
                <div class="grid grid-cols-6 gap-3 bg-white p-4 rounded-xl border">
                    <div><label class="block text-xs font-bold mb-1 text-center text-red-600">R оноо</label><input type="number" name="r_req" value="0" class="w-full p-2 border rounded-md text-center"></div>
                    <div><label class="block text-xs font-bold mb-1 text-center text-blue-600">I оноо</label><input type="number" name="i_req" value="0" class="w-full p-2 border rounded-md text-center"></div>
                    <div><label class="block text-xs font-bold mb-1 text-center text-orange-600">A оноо</label><input type="number" name="a_req" value="0" class="w-full p-2 border rounded-md text-center"></div>
                    <div><label class="block text-xs font-bold mb-1 text-center text-green-600">S оноо</label><input type="number" name="s_req" value="0" class="w-full p-2 border rounded-md text-center"></div>
                    <div><label class="block text-xs font-bold mb-1 text-center text-purple-600">E оноо</label><input type="number" name="e_req" value="0" class="w-full p-2 border rounded-md text-center"></div>
                    <div><label class="block text-xs font-bold mb-1 text-center text-gray-600">C оноо</label><input type="number" name="c_req" value="0" class="w-full p-2 border rounded-md text-center"></div>
                </div>
                <button type="submit" name="add_major" class="bg-blue-600 text-white py-3 rounded-lg font-bold">Мэргэжлийг хадгалах</button>
            </form>

            <div class="space-y-3">
                <?php foreach($majors as $m): ?>
                <div class="flex justify-between items-center p-4 bg-white border rounded-xl shadow-sm">
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <span class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($m['name']) ?></span>
                            <span class="bg-gray-100 px-2 py-0.5 text-xs rounded font-bold text-gray-600"><?php echo htmlspecialchars($m['uni_name']) ?></span>
                        </div>
                    </div>
                    <a href="admin.php?del_m=<?php echo $m['id'] ?>" class="text-red-500 font-bold text-sm" onclick="return confirm('Устгах уу?')">Устгах</a>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>