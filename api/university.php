<?php
// university.php
include 'db.php';

// URL-аас page параметр буюу сургуулийн slug-ийг унших (Байхгүй бол шууд nets рүү үсрүүлнэ)
$slug = $_GET['page'] ?? 'nets';

// 1. Сургуулийн үндсэн мэдээллийг татах
$stmt = $pdo->prepare("SELECT * FROM universities WHERE page_slug = ?");
$stmt->execute([$slug]);
$uni = $stmt->fetch();

// Хэрэв буруу slug оруулсан бол алдаа заахаас сэргийлж эхний сургуулийг харуулна
if (!$uni) {
    echo "<div style='text-align:center; padding:50px; font-family:sans-serif;'><h2>Уучлаарай, сургуулийн мэдээлэл олдсонгүй!</h2><a href='index.php'>Нүүр хуудас руу буцах</a></div>";
    exit;
}

// 2. Тухайн сургуульд хамаарах мэргэжлүүдийг татах
$stmt = $pdo->prepare("SELECT * FROM majors WHERE university_id = ? ORDER BY id ASC");
$stmt->execute([$uni['id']]);
$majors = $stmt->fetchAll();

// Сургууль бүрт тохирох нүүр зураг болон өнгөний кодыг тохируулах (Динамикаар гоё харагдуулах үүднээс)
$uni_meta = [
    'nets' => [
        'bg_color' => 'from-blue-600 to-indigo-900',
        'accent' => 'text-indigo-600',
        'btn' => 'bg-indigo-600 hover:bg-indigo-750',
        'image' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=1200&q=80', // Технологи
        'features' => ['Орчин үеийн өндөр хүчин чадалтай лаборатори', '', '100% практикт суурилсан сургалт']
    ],
    'hzs' => [
        'bg_color' => 'from-amber-800 to-amber-950',
        'accent' => 'text-amber-800',
        'btn' => 'bg-amber-800 hover:bg-amber-900',
        'image' => 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?auto=format&fit=crop&w=1200&q=80', // Хууль
        'features' => ['Иж бүрэн шүүх хурлын дадлагын танхим', 'Олон улсын эрх зүйн мэргэшсэн багш нар', 'Хуулийн фирмүүдэд дадлага хийх боломж']
    ],
    'aus' => [
        'bg_color' => 'from-teal-600 to-cyan-900',
        'accent' => 'text-teal-600',
        'btn' => 'bg-teal-600 hover:bg-teal-700',
        'image' => 'https://images.unsplash.com/photo-1538108149393-fbbd8189893d?auto=format&fit=crop&w=1200&q=80', // Анагаах
        'features' => ['Орчин үеийн симуляцийн төв', 'Төгсөөд шууд харьяа эмнэлгүүдэд ажиллах эрх', 'Дэлхийн стандартад нийцсэн лаборатори']
    ],
    'hus' => [
        'bg_color' => 'from-rose-600 to-purple-900',
        'accent' => 'text-rose-600',
        'btn' => 'bg-rose-600 hover:bg-rose-700',
        'image' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1200&q=80', // Хүмүүнлэг / Багш
        'features' => ['Бүтээлч сэтгэлгээг дэмжих арт студи', 'Театр, хэвлэл мэдээллийн өөрийн студи', 'Сэтгэл зүйн зөвлөгөө өгөх төв']
    ],
    'sezs' => [
        'bg_color' => 'from-slate-700 to-blue-950',
        'accent' => 'text-slate-700',
        'btn' => 'bg-slate-700 hover:bg-slate-800',
        'image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=1200&q=80', // Санхүү / Бизнес
        'features' => ['Bloomberg терминал бүхий санхүүгийн лаборатори', 'Бизнес инкубатор төв', 'Олон улсын ACCA, CFA зэрэг эрх авах хөтөлбөр']
    ],
    'abhss' => [
        
    ]
];

// Хэрэв дээрх санд байхгүй шинэ slug админ үүсгэсэн бол default загвар өгөх
$meta = $uni_meta[$slug] ?? [
    'bg_color' => 'from-blue-600 to-slate-900',
    'accent' => 'text-blue-600',
    'btn' => 'bg-blue-600 hover:bg-blue-700',
    'image' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1200&q=80',
    'features' => ['Чанартай боловсрол', 'Нөхөрсөг хамт олон', 'Уян хатан тэтгэлэгт хөтөлбөрүүд']
];
?>
<!DOCTYPE html>
<html lang="mn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($uni['name']); ?> - Сургуулийн Танилцуулга</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <div class="relative bg-gradient-to-r <?php echo $meta['bg_color']; ?> text-white overflow-hidden shadow-xl">
        <div class="absolute inset-0 opacity-20 bg-cover bg-center" style="background-image: url('<?php echo $meta['image']; ?>');"></div>
        <div class="max-w-6xl mx-auto px-6 py-20 relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="max-w-2xl">
                <a href="index.php" class="inline-flex items-center gap-2 bg-white/20 text-white font-bold text-sm px-4 py-2 rounded-full backdrop-blur-sm hover:bg-white/30 transition-colors mb-6">
                    ⬅️ Буцах
                </a>
                <h1 class="text-4xl md:text-6xl font-black tracking-tight mb-4"><?php echo htmlspecialchars($uni['name']); ?></h1>
                <p class="text-xl text-blue-100 font-medium leading-relaxed"><?php echo htmlspecialchars($uni['description']); ?></p>
            </div>
            <div class="w-full md:w-96 shrink-0 bg-white/10 backdrop-blur-md p-6 rounded-3xl border border-white/20 shadow-2xl">
                <h3 class="font-bold text-xl mb-3">Холбоо барих</h3>
                <p class="text-sm text-blue-100 mb-2">📍 Хаяг: Улаанбаатар хот, Баянзүрх дүүрэг</p>
                <p class="text-sm text-blue-100 mb-2">📞 Утас: 7711-9696</p>
                <p class="text-sm text-blue-100">🌐 Вэбсайт: www.ikhzasag<?php echo $slug; ?>.edu.mn</p>
            </div>
        </div>
    </div>

    <main class="max-w-6xl mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-3 gap-12">
        
        <div class="md:col-span-2 space-y-8">
            <div class="rounded-3xl overflow-hidden shadow-lg border h-80 bg-cover bg-center" style="background-image: url('<?php echo $meta['image']; ?>');"></div>
            
            <div class="bg-white p-8 rounded-3xl shadow-sm border">
                <h2 class="text-2xl font-black mb-6 flex items-center gap-2 text-gray-800">
                    ⭐ Сургуулийн давуу талууд
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <?php foreach($meta['features'] as $feat): ?>
                    <div class="p-4 bg-gray-50 rounded-2xl border flex items-start gap-3">
                        <span class="text-xl">✅</span>
                        <p class="font-semibold text-gray-700 text-sm"><?php echo $feat; ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-sm border h-fit">
            <h2 class="text-2xl font-black mb-6 text-gray-800 flex items-center gap-2">
                🎓 Сургалтын хөтөлбөрүүд
            </h2>
            <p class="text-xs text-gray-400 mb-4 uppercase font-bold tracking-wider">Нийт <?php echo count($majors); ?> идэвхтэй хөтөлбөр</p>
            
            <div class="space-y-3">
                <?php if(count($majors) > 0): ?>
                    <?php foreach($majors as $m): ?>
                    <div class="p-4 bg-gray-50 hover:bg-blue-50 border rounded-2xl transition-all group cursor-pointer">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-gray-800 group-hover:text-blue-600 transition-colors">
                                🔹 <?php echo htmlspecialchars($m['name']); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-400 text-sm italic py-4">Энэ сургууль дээр одоогоор мэргэжил бүртгэгдээгүй байна.</p>
                <?php endif; ?>
            </div>

            <div class="mt-8 border-t pt-6">
                <a href="index.php" class="w-full inline-block text-center text-white font-bold py-4 px-6 rounded-2xl shadow transition-colors <?php echo $meta['btn']; ?>">
                    Дахин тест өгөх 🔄
                </a>
            </div>
        </div>

    </main>

    <footer class="bg-white border-t mt-20 py-8 text-center text-sm text-gray-400 font-medium">
        &copy; <?php echo date('Y'); ?> RIASEC Мэргэжил Сонголтын Систем.
    </footer>

</body>
</html>
