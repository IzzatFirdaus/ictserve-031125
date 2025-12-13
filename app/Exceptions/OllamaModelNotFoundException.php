<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * Pengecualian Model Ollama Tidak Dijumpai
 *
 * Pengecualian ini dilemparkan apabila model LLM yang diminta
 * tidak tersedia pada pelayan Ollama.
 *
 * @version 3.6.0
 * @author Pasukan Pembangunan BPM MOTAC
 */
class OllamaModelNotFoundException extends Exception
{
    /**
     * Konstruktor
     *
     * @param string $model Nama model yang tidak dijumpai
     * @param int $code Kod ralat
     * @param \Throwable|null $previous Pengecualian sebelumnya
     */
    public function __construct(string $model, int $code = 0, ?\Throwable $previous = null)
    {
        $message = "Model Ollama '{$model}' tidak dijumpai pada pelayan. Sila pastikan model telah dimuat turun.";

        parent::__construct($message, $code, $previous);
    }
}
