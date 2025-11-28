<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eSpolek — Платформа для збору коштів</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Source+Sans+3:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-bg: #FDFBF7;
            --color-text: #2D3436;
            --color-text-muted: #636E72;
            --color-primary: #00897B;
            --color-primary-dark: #00695C;
            --color-accent: #FF7043;
            --color-card: #FFFFFF;
            --gradient-hero: linear-gradient(135deg, #E8F5E9 0%, #E0F2F1 50%, #FFF8E1 100%);
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.06);
            --shadow-md: 0 8px 32px rgba(0,0,0,0.08);
            --shadow-lg: 0 16px 48px rgba(0,0,0,0.12);
            --font-display: 'Playfair Display', Georgia, serif;
            --font-body: 'Source Sans 3', -apple-system, sans-serif;
            --radius: 16px;
            --radius-sm: 8px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: var(--font-body);
            background: var(--color-bg);
            color: var(--color-text);
            line-height: 1.7;
            font-size: 18px;
            overflow-x: hidden;
        }

        /* Навігація */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            padding: 1rem 2rem;
            background: rgba(253, 251, 247, 0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideDown 0.6s ease-out;
        }

        @keyframes slideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .logo {
            font-family: var(--font-display);
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--color-primary);
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .logo span {
            color: var(--color-accent);
        }

        .nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--color-primary);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
        }

        .nav-btn:hover {
            background: var(--color-primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .nav-btn svg {
            width: 18px;
            height: 18px;
        }

        /* Hero секція */
        .hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 8rem 2rem 6rem;
            background: var(--gradient-hero);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 80%;
            height: 150%;
            background: radial-gradient(ellipse, rgba(0, 137, 123, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 60%;
            height: 100%;
            background: radial-gradient(ellipse, rgba(255, 112, 67, 0.06) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: white;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--color-text-muted);
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
            animation: fadeUp 0.8s ease-out 0.2s both;
        }

        .hero-badge::before {
            content: '';
            width: 8px;
            height: 8px;
            background: var(--color-primary);
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.2); }
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero h1 {
            font-family: var(--font-display);
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 700;
            line-height: 1.15;
            max-width: 900px;
            margin-bottom: 1.5rem;
            letter-spacing: -1px;
            animation: fadeUp 0.8s ease-out 0.4s both;
        }

        .hero h1 .highlight {
            color: var(--color-primary);
            position: relative;
        }

        .hero h1 .highlight::after {
            content: '';
            position: absolute;
            bottom: 0.1em;
            left: 0;
            right: 0;
            height: 0.15em;
            background: var(--color-accent);
            opacity: 0.4;
            border-radius: 2px;
        }

        .hero-description {
            font-size: 1.25rem;
            color: var(--color-text-muted);
            max-width: 650px;
            margin-bottom: 2.5rem;
            animation: fadeUp 0.8s ease-out 0.6s both;
        }

        .hero-cta {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
            animation: fadeUp 0.8s ease-out 0.8s both;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            background: var(--color-primary);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(0, 137, 123, 0.3);
        }

        .btn-primary:hover {
            background: var(--color-primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 137, 123, 0.4);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            background: white;
            color: var(--color-text);
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
        }

        .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        /* Секції */
        section {
            padding: 6rem 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-label {
            display: inline-block;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--color-primary);
            margin-bottom: 1rem;
        }

        .section-title {
            font-family: var(--font-display);
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        .section-subtitle {
            font-size: 1.125rem;
            color: var(--color-text-muted);
            max-width: 600px;
            margin: 0 auto;
        }

        /* Для кого */
        .audience {
            background: white;
        }

        .audience-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .audience-card {
            padding: 2rem;
            background: var(--color-bg);
            border-radius: var(--radius);
            transition: all 0.3s ease;
        }

        .audience-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .audience-icon {
            width: 56px;
            height: 56px;
            background: var(--gradient-hero);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 1.75rem;
        }

        .audience-card h3 {
            font-family: var(--font-display);
            font-size: 1.375rem;
            margin-bottom: 0.75rem;
        }

        .audience-card p {
            color: var(--color-text-muted);
            font-size: 1rem;
        }

        /* Можливості */
        .features {
            background: var(--color-bg);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 3rem;
        }

        @media (max-width: 900px) {
            .features-grid {
                grid-template-columns: 1fr;
            }
        }

        .feature-card {
            background: white;
            border-radius: var(--radius);
            padding: 2.5rem;
            box-shadow: var(--shadow-sm);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--color-primary), var(--color-accent));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s ease;
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
        }

        .feature-number {
            font-family: var(--font-display);
            font-size: 3rem;
            font-weight: 700;
            color: var(--color-primary);
            opacity: 0.15;
            position: absolute;
            top: 1rem;
            right: 1.5rem;
        }

        .feature-card h3 {
            font-family: var(--font-display);
            font-size: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .feature-card h3 svg {
            width: 28px;
            height: 28px;
            color: var(--color-primary);
        }

        .feature-card p {
            color: var(--color-text-muted);
            margin-bottom: 1.5rem;
        }

        .feature-list {
            list-style: none;
        }

        .feature-list li {
            padding: 0.5rem 0;
            padding-left: 1.75rem;
            position: relative;
            color: var(--color-text-muted);
            font-size: 0.95rem;
        }

        .feature-list li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--color-primary);
            font-weight: 700;
        }

        /* Як це працює */
        .how-it-works {
            background: linear-gradient(180deg, white 0%, var(--color-bg) 100%);
        }

        .steps {
            display: flex;
            flex-direction: column;
            gap: 3rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .step {
            display: flex;
            gap: 2rem;
            align-items: flex-start;
        }

        @media (max-width: 600px) {
            .step {
                flex-direction: column;
                gap: 1rem;
            }
        }

        .step-number {
            flex-shrink: 0;
            width: 64px;
            height: 64px;
            background: var(--color-primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-display);
            font-size: 1.5rem;
            font-weight: 700;
            box-shadow: 0 4px 20px rgba(0, 137, 123, 0.3);
        }

        .step-content h3 {
            font-family: var(--font-display);
            font-size: 1.375rem;
            margin-bottom: 0.5rem;
        }

        .step-content p {
            color: var(--color-text-muted);
        }

        /* CTA фінальна */
        .cta-section {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 80%;
            height: 200%;
            background: radial-gradient(ellipse, rgba(255,255,255,0.1) 0%, transparent 60%);
            pointer-events: none;
        }

        .cta-section .section-title {
            color: white;
        }

        .cta-section .section-subtitle {
            color: rgba(255,255,255,0.85);
            margin-bottom: 2.5rem;
        }

        .btn-white {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1.25rem 2.5rem;
            background: white;
            color: var(--color-primary);
            text-decoration: none;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.125rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }

        .btn-white:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
        }

        /* Футер */
        footer {
            background: var(--color-text);
            color: rgba(255,255,255,0.7);
            padding: 3rem 2rem;
            text-align: center;
        }

        footer .logo {
            color: white;
            margin-bottom: 1rem;
            display: inline-block;
        }

        footer p {
            font-size: 0.95rem;
        }

        /* Анімації при скролі */
        .fade-in {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s ease-out;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Декоративні елементи */
        .decoration {
            position: absolute;
            pointer-events: none;
            opacity: 0.5;
        }
    </style>
</head>
<body>
<nav>
    <a href="#" class="logo">e<span>Spolek</span></a>
    <a href="/admin" class="nav-btn">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
            <polyline points="10 17 15 12 10 7"/>
            <line x1="15" y1="12" x2="3" y2="12"/>
        </svg>
        Увійти
    </a>
</nav>

<section class="hero">
    <span class="hero-badge">Для неприбуткових організацій</span>
    <h1>Простий спосіб <span class="highlight">збирати кошти</span> та керувати благодійністю</h1>
    <p class="hero-description">
        Платформа для невеликих організацій, яка допоможе вести облік, керувати кампаніями та будувати довірливі відносини з донорами.
    </p>
    <div class="hero-cta">
        <a href="/admin" class="btn-primary">
            Почати роботу
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"/>
                <polyline points="12 5 19 12 12 19"/>
            </svg>
        </a>
        <a href="#features" class="btn-secondary">
            Дізнатися більше
        </a>
    </div>
</section>

<section class="audience">
    <div class="container">
        <div class="section-header fade-in">
            <span class="section-label">Для кого</span>
            <h2 class="section-title">Ідеально підходить для</h2>
            <p class="section-subtitle">Платформа створена для невеликих команд, які хочуть професійно організувати збір коштів</p>
        </div>
        <div class="audience-grid">
            <div class="audience-card fade-in">
                <div class="audience-icon">🏃</div>
                <h3>Спортивні клуби</h3>
                <p>Збір на змагання, екіпірування, тренувальні табори та розвиток команди.</p>
            </div>
            <div class="audience-card fade-in">
                <div class="audience-icon">🐝</div>
                <h3>Громадські об'єднання</h3>
                <p>Бджолярі, садівники, культурні спільноти — будь-хто, хто об'єднується навколо спільної справи.</p>
            </div>
            <div class="audience-card fade-in">
                <div class="audience-icon">👶</div>
                <h3>Благодійні фонди</h3>
                <p>Допомога дітям, літнім людям, тваринам — прозорий облік кожної гривні.</p>
            </div>
            <div class="audience-card fade-in">
                <div class="audience-icon">🎯</div>
                <h3>Волонтерські ініціативи</h3>
                <p>Швидкий запуск кампаній на конкретні цілі з повним контролем над процесом.</p>
            </div>
        </div>
    </div>
</section>

<section class="features" id="features">
    <div class="container">
        <div class="section-header fade-in">
            <span class="section-label">Можливості</span>
            <h2 class="section-title">Усе для ефективного збору коштів</h2>
            <p class="section-subtitle">Чотири потужні інструменти в одному простому інтерфейсі</p>
        </div>
        <div class="features-grid">
            <div class="feature-card fade-in">
                <span class="feature-number">01</span>
                <h3>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                        <line x1="8" y1="21" x2="16" y2="21"/>
                        <line x1="12" y1="17" x2="12" y2="21"/>
                    </svg>
                    Кампанії
                </h3>
                <p>Створюйте одноразові або постійні збори з чіткими цілями та етапами.</p>
                <ul class="feature-list">
                    <li>Одноразові кампанії з цільовою сумою</li>
                    <li>Постійні збори з етапами (щомісяця)</li>
                    <li>QR-коди для швидких переказів</li>
                    <li>Публічна сторінка кампанії</li>
                </ul>
            </div>
            <div class="feature-card fade-in">
                <span class="feature-number">02</span>
                <h3>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    Донори
                </h3>
                <p>База даних усіх, хто підтримує вашу організацію — фізичні та юридичні особи.</p>
                <ul class="feature-list">
                    <li>Автоматична ідентифікація донорів</li>
                    <li>Історія всіх внесків</li>
                    <li>Генерація податкових підтверджень</li>
                    <li>Пошук та фільтрація</li>
                </ul>
            </div>
            <div class="feature-card fade-in">
                <span class="feature-number">03</span>
                <h3>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23"/>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                    Дарунки
                </h3>
                <p>Кожен внесок відстежується та прив'язується до конкретної кампанії.</p>
                <ul class="feature-list">
                    <li>Автоматичний імпорт з банку</li>
                    <li>Прив'язка до кампаній та етапів</li>
                    <li>Детальна статистика</li>
                    <li>Експорт даних</li>
                </ul>
            </div>
            <div class="feature-card fade-in">
                <span class="feature-number">04</span>
                <h3>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                    Грошовий щоденник
                </h3>
                <p>Простий бухгалтерський облік для невеликих організацій без зайвих складнощів.</p>
                <ul class="feature-list">
                    <li>Усі операції в одному місці</li>
                    <li>Категоризація витрат і доходів</li>
                    <li>Завантаження документів</li>
                    <li>Кілька банківських рахунків</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="how-it-works">
    <div class="container">
        <div class="section-header fade-in">
            <span class="section-label">Як це працює</span>
            <h2 class="section-title">Почніть за 5 хвилин</h2>
            <p class="section-subtitle">Простий шлях від реєстрації до першого збору</p>
        </div>
        <div class="steps">
            <div class="step fade-in">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h3>Створіть обліковий запис</h3>
                    <p>Зареєструйте організацію та отримайте власне середовище: yourorg.espolek.cz</p>
                </div>
            </div>
            <div class="step fade-in">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h3>Підключіть банк</h3>
                    <p>Інтегруйте банківський рахунок через API — операції синхронізуються автоматично.</p>
                </div>
            </div>
            <div class="step fade-in">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h3>Створіть кампанію</h3>
                    <p>Вкажіть мету, суму, додайте опис та фото. Отримайте унікальний QR-код для збору.</p>
                </div>
            </div>
            <div class="step fade-in">
                <div class="step-number">4</div>
                <div class="step-content">
                    <h3>Збирайте кошти</h3>
                    <p>Донори переказують гроші — ви бачите все в реальному часі: хто, скільки, на що.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <div class="section-header fade-in">
            <h2 class="section-title">Готові спростити збір коштів?</h2>
            <p class="section-subtitle">Приєднуйтесь до організацій, які вже використовують eSpolek для прозорої та ефективної благодійності.</p>
        </div>
        <a href="/admin" class="btn-white fade-in">
            Увійти до системи
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"/>
                <polyline points="12 5 19 12 12 19"/>
            </svg>
        </a>
    </div>
</section>

<footer>
    <a href="#" class="logo">e<span>Spolek</span></a>
    <p>© 2025 eSpolek. Платформа для неприбуткових організацій.</p>
</footer>

<script>
    // Анімації при скролі
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));

    // Плавний скрол для кнопки "Дізнатися більше"
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
</script>
</body>
</html>
