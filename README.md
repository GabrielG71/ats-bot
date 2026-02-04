# 🤖 ATS Bot — Simulador de Triagem de Currículos

Este projeto é um **ATS (Applicant Tracking System) simples e didático**, criado para demonstrar **como currículos são analisados por inteligências artificiais** antes de chegarem a um recrutador humano.

O objetivo não é reproduzir sistemas reais com 100% de fidelidade, mas **explicar na prática por que muitos currículos são rejeitados automaticamente**.

---

## 🧠 Como funciona

1. O usuário faz upload de um currículo em PDF (via interface web).
2. O Laravel salva o arquivo temporariamente.
3. Um script em **Python** é executado para:
   - Extrair o texto do PDF
   - Analisar critérios básicos
   - Gerar uma pontuação (score)
4. O resultado é retornado em JSON e exibido na interface.

---

## 📊 Critérios de pontuação (versão simplificada)

- Email encontrado
- Telefone encontrado
- Conteúdo mínimo
- Seção de experiência
- Seção de educação
- Seção de habilidades
- Palavras-chave técnicas

**Score máximo:** 100 pontos  
> ⚠️ Os critérios são propositalmente simples para fins didáticos.

---

## 🛠️ Stack utilizada

- **Python**
  - pdfplumber (extração de texto de PDF)
- **Laravel (PHP)**
  - Blade para o front-end
  - Controller para executar o script Python
- **HTML / CSS**
  - Interface simples e funcional

---

## 📂 Estrutura do projeto

```
projeto-ats/
├── python/
│ ├── analisar.py
│ └── requirements.txt
├── laravel-projeto/
│ ├── app/Http/Controllers/AtsController.php
│ ├── resources/views/
│ └── routes/web.php
```


---

## 🚀 Objetivo do projeto

- Demonstrar como ATS funcionam
- Ajudar desenvolvedores e candidatos a entenderem a lógica por trás da triagem automática
- Servir como projeto educacional e de portfólio

---

## 📺 Vídeo explicando o projeto

YouTube:  
https://www.youtube.com/watch?v=8Ka6q1WrjaE

---

## 👤 Autor

Gabriel Gonçalves  
GitHub: https://github.com/GabrielG71  
LinkedIn: https://www.linkedin.com/in/gabriel-goncalves
