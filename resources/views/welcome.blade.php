<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Projeto Analisador de Currículos ATS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Fraunces:wght@600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --purple: #7C3AED;
            --purple-dark: #6D28D9;
            --purple-light: #A78BFA;
            --bg: #FAFAFA;
            --text: #1F2937;
            --text-light: #6B7280;
            --success: #10B981;
            --warning: #F59E0B;
            --error: #EF4444;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem;
            background-image:
                radial-gradient(circle at 20% 30%, rgba(124, 58, 237, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(167, 139, 250, 0.08) 0%, transparent 50%);
        }

        header {
            text-align: center;
            margin-bottom: 3rem;
            animation: fadeInDown 0.8s ease-out;
        }

        h1 {
            font-family: 'Fraunces', serif;
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 600;
            color: var(--text);
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .highlight {
            color: var(--purple);
            position: relative;
            display: inline-block;
        }

        .highlight::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: var(--purple-light);
            opacity: 0.3;
            z-index: -1;
            transform: skew(-12deg);
        }

        .description {
            font-size: 1.125rem;
            color: var(--text-light);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
            animation: fadeInUp 0.8s ease-out 0.2s backwards;
        }

        .upload-container {
            background: white;
            border-radius: 24px;
            padding: 3rem;
            max-width: 500px;
            width: 100%;
            box-shadow:
                0 10px 40px rgba(124, 58, 237, 0.08),
                0 0 0 1px rgba(124, 58, 237, 0.05);
            animation: fadeInUp 0.8s ease-out 0.4s backwards;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 2rem;
        }

        .upload-container:hover {
            transform: translateY(-2px);
            box-shadow:
                0 20px 50px rgba(124, 58, 237, 0.12),
                0 0 0 1px rgba(124, 58, 237, 0.08);
        }

        .upload-area {
            text-align: center;
        }

        .upload-label {
            display: inline-block;
            background: var(--purple);
            color: white;
            border: none;
            border-radius: 16px;
            padding: 1.25rem 3rem;
            font-size: 1.125rem;
            font-weight: 500;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow:
                0 4px 14px rgba(124, 58, 237, 0.3),
                0 0 0 0 rgba(124, 58, 237, 0.5);
            position: relative;
            overflow: hidden;
        }

        .upload-label::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .upload-label:hover::before {
            width: 300px;
            height: 300px;
        }

        .upload-label:hover {
            background: var(--purple-dark);
            transform: translateY(-2px);
            box-shadow:
                0 8px 24px rgba(124, 58, 237, 0.4),
                0 0 0 4px rgba(124, 58, 237, 0.1);
        }

        input[type="file"] {
            display: none;
        }

        .upload-icon {
            display: inline-block;
            margin-right: 0.75rem;
            font-size: 1.25rem;
            vertical-align: middle;
        }

        .file-info {
            margin-top: 1.5rem;
            padding: 1rem;
            background: rgba(124, 58, 237, 0.05);
            border-radius: 12px;
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .btn-submit {
            margin-top: 1.5rem;
            width: 100%;
            background: var(--purple);
            color: white;
            border: none;
            border-radius: 16px;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background: var(--purple-dark);
            transform: translateY(-2px);
        }

        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Resultado */
        .resultado-container {
            background: white;
            border-radius: 24px;
            padding: 3rem;
            max-width: 700px;
            width: 100%;
            box-shadow:
                0 10px 40px rgba(124, 58, 237, 0.08),
                0 0 0 1px rgba(124, 58, 237, 0.05);
            animation: fadeInUp 0.6s ease-out;
        }

        .score-badge {
            text-align: center;
            margin-bottom: 2rem;
        }

        .score-numero {
            font-family: 'Fraunces', serif;
            font-size: 4rem;
            font-weight: 600;
            color: var(--purple);
            line-height: 1;
        }

        .score-total {
            font-size: 1.5rem;
            color: var(--text-light);
        }

        .detalhes-lista {
            list-style: none;
            padding: 0;
        }

        .detalhe-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            margin-bottom: 0.5rem;
            background: var(--bg);
            border-radius: 12px;
        }

        .detalhe-item.ok {
            border-left: 4px solid var(--success);
        }

        .detalhe-item.nok {
            border-left: 4px solid var(--error);
        }

        .detalhe-nome {
            font-weight: 500;
            color: var(--text);
        }

        .detalhe-pontos {
            font-weight: 600;
            color: var(--purple);
        }

        .tech-list {
            margin-top: 2rem;
            padding: 1.5rem;
            background: rgba(124, 58, 237, 0.05);
            border-radius: 12px;
        }

        .tech-list h3 {
            margin-bottom: 1rem;
            color: var(--text);
        }

        .tech-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .tech-tag {
            background: var(--purple);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .alert-error {
            background: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FCA5A5;
        }

        .btn-voltar {
            margin-top: 2rem;
            text-align: center;
        }

        .btn-voltar a {
            color: var(--purple);
            text-decoration: none;
            font-weight: 500;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 640px) {
            body {
                padding: 1.5rem;
            }

            .upload-container, .resultado-container {
                padding: 2rem 1.5rem;
            }

            .upload-label {
                padding: 1rem 2rem;
                font-size: 1rem;
            }

            .score-numero {
                font-size: 3rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Projeto Analisador de<br><span class="highlight">Currículos ATS</span></h1>
        <p class="description">
            Analise seu currículo com inteligência artificial e descubra como ele será avaliado pelos sistemas de rastreamento de candidatos.
        </p>
    </header>

    @if(session('erro'))
    <div class="alert alert-error">
        <strong>Erro:</strong> {{ session('erro') }}
    </div>
    @endif

    @if(!session('resultado'))
    <div class="upload-container">
        <form action="{{ route('analisar') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
            @csrf
            <div class="upload-area">
                <label for="curriculo" class="upload-label">
                    <span class="upload-icon">📄</span>
                    <span>Coloque seu currículo</span>
                </label>
                <input type="file" id="curriculo" name="curriculo" accept=".pdf" required>

                <div class="file-info" id="fileInfo" style="display: none;"></div>

                <button type="submit" class="btn-submit" id="btnSubmit" disabled>
                    Analisar Currículo
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="resultado-container">
        <div class="score-badge">
            <div class="score-numero">{{ session('resultado')['score'] }}</div>
            <div class="score-total">de {{ session('resultado')['max_score'] }} pontos</div>
        </div>

        <ul class="detalhes-lista">
            @foreach(session('resultado')['detalhes'] as $detalhe)
            <li class="detalhe-item {{ $detalhe['ok'] ? 'ok' : 'nok' }}">
                <span class="detalhe-nome">{{ $detalhe['criterio'] }}</span>
                <span class="detalhe-pontos">{{ $detalhe['pontos'] }}/{{ $detalhe['max'] }}</span>
            </li>
            @endforeach
        </ul>

        @if(count(session('resultado')['tech_encontradas']) > 0)
        <div class="tech-list">
            <h3>Palavras-chave técnicas encontradas ({{ count(session('resultado')['tech_encontradas']) }}):</h3>
            <div class="tech-tags">
                @foreach(session('resultado')['tech_encontradas'] as $tech)
                <span class="tech-tag">{{ $tech }}</span>
                @endforeach
            </div>
        </div>
        @endif

        <div class="btn-voltar">
            <a href="{{ route('home') }}">← Analisar outro currículo</a>
        </div>
    </div>
    @endif

    <script>
        const fileInput = document.getElementById('curriculo');
        const fileInfo = document.getElementById('fileInfo');
        const btnSubmit = document.getElementById('btnSubmit');

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                fileInfo.textContent = `Arquivo selecionado: ${file.name}`;
                fileInfo.style.display = 'block';
                btnSubmit.disabled = false;
            } else {
                fileInfo.style.display = 'none';
                btnSubmit.disabled = true;
            }
        });
    </script>
</body>
</html>
