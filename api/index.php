<?php
// index.php
include 'db.php';

$questions = $pdo->query("SELECT * FROM questions ORDER BY id ASC")->fetchAll();
$majors = $pdo->query("
    SELECT m.*, u.name as uni_name, u.description as uni_desc, u.page_slug 
    FROM majors m 
    LEFT JOIN universities u ON m.university_id = u.id
")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save') {
    $scores = json_decode($_POST['scores'], true);
    
    $temp_scores = $scores;
    arsort($temp_scores);
    $top_types = implode('', array_slice(array_keys($temp_scores), 0, 2));

    // ШИНЭЧЛЭГДСЭН ИНСЕРТ ЛОГИК
    $stmt = $pdo->prepare("INSERT INTO students (name, school, phone, email, r_score, i_score, a_score, s_score, e_score, c_score, top_types) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['name'], $_POST['school'], $_POST['phone'], $_POST['email'],
        $scores['R'], $scores['I'], $scores['A'], $scores['S'], $scores['E'], $scores['C'],
        $top_types
    ]);
    echo json_encode(['success' => true]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="mn">
<head>
    <meta charset="UTF-8">
    <title>RIASEC Мэргэжил Сонголт</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 select-none">

    <div id="step-0" class="flex flex-col items-center justify-center min-h-screen p-6">
        <h1 class="text-5xl font-black mb-8 text-blue-900 text-center tracking-tight">МЭРГЭЖИЛ СОНГОЛТЫН ТЕСТ</h1>
        <div class="w-full max-w-2xl bg-white p-10 rounded-3xl shadow-2xl border">
            <div class="space-y-4">
                <input type="text" id="student_name" placeholder="Овог Нэр" class="w-full text-2xl p-5 rounded-2xl border-2 bg-gray-50 focus:outline-none focus:border-blue-500">
                <input type="text" id="student_school" placeholder="Төгссөн / Сурч буй сургууль" class="w-full text-2xl p-5 rounded-2xl border-2 bg-gray-50 focus:outline-none focus:border-blue-500">
                <input type="number" id="student_phone" placeholder="Утасны дугаар" class="w-full text-2xl p-5 rounded-2xl border-2 bg-gray-50 focus:outline-none focus:border-blue-500">
                <input type="email" id="student_email" placeholder="Мэйл хаяг" class="w-full text-2xl p-5 rounded-2xl border-2 bg-gray-50 focus:outline-none focus:border-blue-500 mb-6">
            </div>
            <button onclick="startTest()" class="w-full bg-blue-600 text-white text-3xl font-bold py-5 rounded-2xl active:scale-95 transition-transform shadow-lg mt-4">Тест эхлэх</button>
        </div>
    </div>

    <div id="step-1" class="hidden flex flex-col items-center justify-between min-h-screen p-8 bg-blue-50">
        <div class="w-full bg-gray-200 h-6 rounded-full overflow-hidden shadow-inner">
            <div id="progress-bar" class="bg-blue-600 h-full transition-all duration-300" style="width: 0%"></div>
        </div>
        <div class="bg-white my-auto p-12 rounded-3xl shadow-2xl w-full max-w-4xl border flex flex-col items-center">
            <h2 id="question-text" class="text-4xl font-bold text-center text-gray-800 leading-snug mb-12">Асуулт...</h2>
            <div class="w-full flex justify-between items-center px-4 mb-6">
                <span class="text-red-500 font-bold text-xl w-32 text-left">Огт санал нийлэхгүй</span>
                <div class="flex gap-6 md:gap-10 justify-center">
                    <?php for($i=1; $i<=5; $i++): ?>
                        <label class="flex flex-col items-center cursor-pointer group">
                            <input type="radio" name="likert" value="<?php echo $i; ?>" class="w-12 h-12 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 cursor-pointer transform scale-125">
                            <span class="text-xl font-bold mt-2 text-gray-600 group-hover:text-blue-600"><?php echo $i; ?></span>
                        </label>
                    <?php endfor; ?>
                </div>
                <span class="text-green-600 font-bold text-xl w-32 text-right">Бүрэн санал нийлнэ</span>
            </div>
        </div>
        <button onclick="nextQuestion()" class="w-full max-w-4xl bg-blue-600 text-white text-4xl font-bold py-6 rounded-2xl shadow-lg active:scale-95 transition-transform">Дараагийн асуулт ➡️</button>
    </div>

    <div id="step-2" class="hidden flex flex-col items-center min-h-screen bg-gradient-to-b from-blue-50 to-slate-50 p-8">
        <h1 class="text-5xl font-black text-blue-900 mb-2 mt-4">ТАНЫ ТЕСТИЙН ҮР ДҮН</h1>
        <p class="text-xl text-gray-600 mb-8">✨ Систем таны хариултуудад шинжилгээ хийж дараах үр дүнг гаргалаа.</p>
        <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
            <div class="bg-white p-8 rounded-3xl shadow-xl border border-blue-100 flex flex-col items-center text-center">
                <div class="bg-blue-600 text-white rounded-2xl p-4 w-24 h-24 flex items-center justify-center text-4xl font-black shadow-lg mb-4" id="top-code-badge">--</div>
                <h3 class="text-2xl font-black text-gray-800" id="top-type-title">Тооцоолж байна...</h3>
                <p class="text-gray-500 mt-4 text-sm leading-relaxed text-left border-t pt-4" id="top-type-desc">Уншиж байна...</p>
                <div class="w-full mt-6 space-y-2 border-t pt-4 text-left" id="scores-mini-bars"></div>
            </div>
            <div class="md:col-span-2 bg-white p-8 rounded-3xl shadow-xl border border-gray-100">
                <h2 class="text-2xl font-black text-gray-800 mb-6 border-b pb-4">🎯 Танд хамгийн сайн тохирох хөтөлбөрүүд:</h2>
                <div id="matched-majors-list" class="space-y-4 max-h-[500px] overflow-y-auto pr-2"></div>
            </div>
        </div>
        <button onclick="window.location.reload()" class="bg-slate-950 text-white text-3xl font-bold py-5 px-16 rounded-2xl shadow-xl active:scale-95 transition-transform mt-10">Дараагийн сурагч 🔄</button>
    </div>

    <script>
        const questions = <?php echo json_encode($questions); ?>;
        const allMajors = <?php echo json_encode($majors); ?>;
        const typeDefinitions = {
            'R': { title: 'Бодит үйл үйлдлийн (Realistic)', desc: 'Та гараар хийх, техник технологитой ажиллах, бодит зүйлсийг угсарч бүтээх дуртай.' },
            'I': { title: 'Шинжлэн судлагч (Investigative)', desc: 'Та асуудлын учир шалтгааныг олох, судалгаа шинжилгээ хийх, математик логик сонирхолтой.' },
            'A': { title: 'Уран бүтээлч, Гоо зүйн (Artistic)', desc: 'Та шинийг сэдэх, урлаг дизайн сонирхох, чөлөөт сэтгэлгээгээр өөрийгөө илэрхийлэх дуртай.' },
            'S': { title: 'Нийгэмсэг (Social)', desc: 'Та бусдад туслах, зааж сургах, зөвлөгөө өгөх, нийгмийн сайн сайхны төлөө ажиллах дуртай.' },
            'E': { title: 'Идэвхтэй, Манлайлагч (Enterprising)', desc: 'Та бусдыг удирдан чиглүүлэх, бизнес төсөл эхлүүлэх, итгүүлэн үнэмшүүлэх сонирхолтой.' },
            'C': { title: 'Уламжлалт, Цэгцтэй (Conventional)', desc: 'Та өгөгдөл мэдээлэл боловсруулах, тоо бүртгэл хөтлөх, дүрэм журмыг нарийн мөрдөх ажилд сайн.' }
        };

        let currentQ = 0;
        let userData = { name: '', school: '', phone: '', email: '' };
        let scores = { R: 0, I: 0, A: 0, S: 0, E: 0, C: 0 };

        function startTest() {
            userData.name = document.getElementById('student_name').value;
            userData.school = document.getElementById('student_school').value;
            userData.phone = document.getElementById('student_phone').value;
            userData.email = document.getElementById('student_email').value;
            
            if(!userData.name || !userData.school || !userData.phone || !userData.email) return alert('Бүх талбарыг бүрэн оруулна уу!');
            document.getElementById('step-0').classList.add('hidden');
            document.getElementById('step-1').classList.remove('hidden');
            showQuestion();
        }

        function showQuestion() {
            document.getElementById('question-text').innerText = questions[currentQ].text;
            let progress = ((currentQ + 1) / questions.length) * 100;
            document.getElementById('progress-bar').style.width = progress + '%';
            let radios = document.getElementsByName('likert');
            radios.forEach(r => r.checked = false);
        }

        function nextQuestion() {
            let radios = document.getElementsByName('likert');
            let selectedValue = 0;
            for (let r of radios) { if (r.checked) { selectedValue = parseInt(r.value); break; } }
            if(selectedValue === 0) return alert('Утга сонгоно уу!');

            scores[questions[currentQ].type] += selectedValue;

            if(currentQ < questions.length - 1) {
                currentQ++; showQuestion();
            } else {
                saveResults();
            }
        }

        function saveResults() {
            let formData = new FormData();
            formData.append('action', 'save');
            formData.append('name', userData.name);
            formData.append('school', userData.school);
            formData.append('phone', userData.phone);
            formData.append('email', userData.email);
            formData.append('scores', JSON.stringify(scores));

            fetch('index.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => { if(data.success) { showFinalScreen(); } });
        }

        function showFinalScreen() {
            document.getElementById('step-1').classList.add('hidden');
            document.getElementById('step-2').classList.remove('hidden');

            let sortedTypes = Object.keys(scores).sort((a, b) => scores[b] - scores[a]);
            let top1 = sortedTypes[0]; let top2 = sortedTypes[1];
            let combinedCode = top1 + top2;

            document.getElementById('top-code-badge').innerText = combinedCode;
            document.getElementById('top-type-title').innerText = `${typeDefinitions[top1].title}`;
            document.getElementById('top-type-desc').innerHTML = `${typeDefinitions[top1].desc}<br><br>Дагалдах: <b>${typeDefinitions[top2].title}</b>`;

            let maxScorePossible = Math.max(...Object.values(scores), 1);
            document.getElementById('scores-mini-bars').innerHTML = Object.keys(scores).map(key => {
                let barWidth = (scores[key] / maxScorePossible) * 100;
                return `
                    <div class="text-xs font-bold text-gray-600 flex items-center justify-between"><span>${key}</span><span>${scores[key]} он</span></div>
                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden mb-2"><div class="bg-blue-500 h-full" style="width: ${barWidth}%"></div></div>
                `;
            }).join('');

            let calculatedMajors = allMajors.map(major => {
                let totalReq = (parseInt(major.r_req)||0) + (parseInt(major.i_req)||0) + (parseInt(major.a_req)||0) + (parseInt(major.s_req)||0) + (parseInt(major.e_req)||0) + (parseInt(major.c_req)||0);
                if (totalReq === 0) return { percent: 0 };
                let totalMatch = Math.min(scores.R, parseInt(major.r_req)||0) + Math.min(scores.I, parseInt(major.i_req)||0) + Math.min(scores.A, parseInt(major.a_req)||0) + Math.min(scores.S, parseInt(major.s_req)||0) + Math.min(scores.E, parseInt(major.e_req)||0) + Math.min(scores.C, parseInt(major.c_req)||0);
                return { name: major.name, uni_name: major.uni_name, uni_desc: major.uni_desc, page_slug: major.page_slug, percent: Math.round((totalMatch / totalReq) * 100) };
            }).filter(m => m.percent > 0).sort((a, b) => b.percent - a.percent);

            document.getElementById('matched-majors-list').innerHTML = calculatedMajors.map(m => `
                <div class="bg-gray-50 p-5 rounded-2xl border-2 border-gray-100 flex justify-between items-center hover:border-blue-400 transition-all">
                    <div>
                        <div class="flex items-center gap-2"><span class="text-xl font-black text-gray-800">🎓 ${m.name}</span><span class="bg-blue-100 text-blue-800 font-extrabold px-3 py-0.5 rounded-lg text-xs">${m.uni_name}</span></div>
                        <p class="text-gray-500 mt-2 text-sm line-clamp-1">${m.uni_desc}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-black text-blue-600 block mb-1">${m.percent}%</span>
                        <a href="university.php?page=${m.page_slug}" class="bg-blue-600 text-white font-bold px-4 py-2 rounded-xl text-xs hover:bg-blue-700 block text-center">Сургууль үзэх 🏢</a>
                    </div>
                </div>
            `).join('') || '<div class="text-gray-400 text-center py-8">Тохирох мэргэжил олдсонгүй.</div>';
        }
    </script>
</body>
</html>
