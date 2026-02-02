import sys
import json
import re
import pdfplumber

SECTIONS = {
    "experiencia": {
        "keywords": ["experiência", "experiencia", "experience", "trabalho", "emprego", "cargo", "atividades profissionais"],
        "max_points": 20,
    },
    "educacao": {
        "keywords": ["educação", "educacao", "education", "formação", "formacao", "graduação", "graduacao", "curso", "universidade", "faculdade"],
        "max_points": 15,
    },
    "habilidades": {
        "keywords": ["habilidades", "skills", "competências", "competencias", "conhecimentos", "tecnologias"],
        "max_points": 15,
    },
}

TECH_KEYWORDS = [
    "python", "java", "javascript", "typescript", "c#", "c++", "php",
    "html", "css", "react", "angular", "vue", "node", "django", "flask",
    "laravel", "spring", "sql", "mysql", "postgresql", "mongodb", "docker",
    "aws", "azure", "git", "linux", "agile", "scrum", "api", "rest",
]

TECH_MAX_POINTS = 20
TECH_POINTS_PER_KEYWORD = 5 


def extrair_texto(caminho_pdf: str) -> str:
    """Extrai todo o texto do PDF."""
    texto = ""
    with pdfplumber.open(caminho_pdf) as pdf:
        for pagina in pdf.pages:
            texto += pagina.extract_text() or ""
    return texto


def verificar_email(texto: str) -> dict:
    """Verifica se há um email no texto."""
    encontrado = bool(re.search(r"[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+", texto))
    return {"nome": "Email", "pontos": 10 if encontrado else 0, "max": 10, "encontrado": encontrado}


def verificar_telefone(texto: str) -> dict:
    """Verifica se há um telefone no texto."""
    encontrado = bool(re.search(r"(\+?\d{1,3}[\s.-]?)?\(?\d{2,3}\)?[\s.-]?\d{4,5}[\s.-]?\d{4}", texto))
    return {"nome": "Telefone", "pontos": 10 if encontrado else 0, "max": 10, "encontrado": encontrado}


def verificar_tamanho(texto: str) -> dict:
    """Penaliza currículos muito curtos (menos de 100 palavras)."""
    palavras = len(texto.split())
    encontrado = palavras >= 100
    return {"nome": "Conteúdo mínimo", "pontos": 10 if encontrado else 0, "max": 10, "encontrado": encontrado, "palavras": palavras}


def verificar_secao(texto: str, secao: dict) -> dict:
    """Verifica se uma seção existe no texto."""
    texto_lower = texto.lower()
    encontrado = any(kw in texto_lower for kw in secao["keywords"])
    return {"pontos": secao["max_points"] if encontrado else 0, "max": secao["max_points"], "encontrado": encontrado}


def verificar_tech(texto: str) -> dict:
    """Conta quantas keywords técnicas foram encontradas."""
    texto_lower = texto.lower()
    encontradas = [kw for kw in TECH_KEYWORDS if kw in texto_lower]
    pontos = min(len(encontradas) * TECH_POINTS_PER_KEYWORD, TECH_MAX_POINTS)
    return {"nome": "Palavras-chave técnicas", "pontos": pontos, "max": TECH_MAX_POINTS, "encontradas": encontradas}


def analisar(caminho_pdf: str) -> dict:
    """Função principal: analisa o PDF e retorna o resultado completo."""
    texto = extrair_texto(caminho_pdf)

    if not texto.strip():
        return {"erro": "Não foi possível extrair texto do PDF."}

    email = verificar_email(texto)
    telefone = verificar_telefone(texto)
    tamanho = verificar_tamanho(texto)
    tech = verificar_tech(texto)

    experiencia = verificar_secao(texto, SECTIONS["experiencia"])
    educacao = verificar_secao(texto, SECTIONS["educacao"])
    habilidades = verificar_secao(texto, SECTIONS["habilidades"])

    total = (
        email["pontos"]
        + telefone["pontos"]
        + tamanho["pontos"]
        + experiencia["pontos"]
        + educacao["pontos"]
        + habilidades["pontos"]
        + tech["pontos"]
    )

    detalhes = [
        {"criterio": "Email",                  "pontos": email["pontos"],        "max": 10,  "ok": email["encontrado"]},
        {"criterio": "Telefone",               "pontos": telefone["pontos"],     "max": 10,  "ok": telefone["encontrado"]},
        {"criterio": "Conteúdo mínimo",        "pontos": tamanho["pontos"],      "max": 10,  "ok": tamanho["encontrado"]},
        {"criterio": "Experiência",            "pontos": experiencia["pontos"],  "max": 20,  "ok": experiencia["encontrado"]},
        {"criterio": "Educação",               "pontos": educacao["pontos"],     "max": 15,  "ok": educacao["encontrado"]},
        {"criterio": "Habilidades",            "pontos": habilidades["pontos"],  "max": 15,  "ok": habilidades["encontrado"]},
        {"criterio": "Palavras-chave técnicas","pontos": tech["pontos"],         "max": 20,  "ok": len(tech["encontradas"]) > 0},
    ]

    return {
        "score": total,
        "max_score": 100,
        "detalhes": detalhes,
        "tech_encontradas": tech["encontradas"],
        "palavras_total": tamanho["palavras"],
    }

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"erro": "Nenhum arquivo informado. Uso: python analisar.py <caminho_do_pdf>"}))
        sys.exit(1)

    caminho = sys.argv[1]

    try:
        resultado = analisar(caminho)
        print(json.dumps(resultado, ensure_ascii=False, indent=2))
    except FileNotFoundError:
        print(json.dumps({"erro": f"Arquivo não encontrado: {caminho}"}))
    except Exception as e:
        print(json.dumps({"erro": str(e)}))