<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AtsController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function analisar(Request $request)
    {
        $request->validate([
            'curriculo' => 'required|mimes:pdf|max:5120'
        ]);

        $arquivo = $request->file('curriculo');
        $nomeArquivo = time() . '_' . $arquivo->getClientOriginalName();
        $caminhoArquivo = $arquivo->storeAs('curriculos', $nomeArquivo);

        $caminhoCompleto = Storage::path($caminhoArquivo);

        $scriptPython = base_path('python' . DIRECTORY_SEPARATOR . 'analisar.py');

        if (!file_exists($scriptPython)) {
            Storage::delete($caminhoArquivo);
            return back()->with('erro', 'Script Python não encontrado.');
        }

        $comando = "py " . escapeshellarg($scriptPython) . " " . escapeshellarg($caminhoCompleto) . " 2>&1";

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $comando = 'chcp 65001 > nul && ' . $comando;
        }

        $output = shell_exec($comando);

        if (!mb_check_encoding($output, 'UTF-8')) {
            $output = mb_convert_encoding($output, 'UTF-8', 'Windows-1252');
        }

        Storage::delete($caminhoArquivo);

        if (empty($output)) {
            return back()->with('erro', 'Python não retornou nenhum output.');
        }

        $resultado = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->with('erro', 'Erro ao processar resposta do Python.');
        }

        if (isset($resultado['erro'])) {
            return back()->with('erro', $resultado['erro']);
        }

        return back()->with('resultado', $resultado);
    }
}
