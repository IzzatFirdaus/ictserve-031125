<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * Pengecualian Timeout Ollama
 *
 * Pengecualian ini dilemparkan apabila permintaan ke pelayan Ollama
 * melebihi had masa yang ditetapkan.
 *
 * @version 3.6.0
 * @author Pasukan Pembangunan BPM MOTAC
 */
class OllamaTimeoutException extends Exception
{
    /**
     * Konstruktor
     *
     * @param int $timeout Masa timeout dalam saat
     * @param int $code Kod ralat
     * @param \Throwable|null $previous Pengecualian sebelumnya
     */
    public function __construct(int $timeout, int $code = 0, ?\Throwable $previous = null)
    {
        $message = "Permintaan ke pelayan Ollama timeout selepas {$timeout} saat. Sila periksa sambungan rangkaian dan status pelayan.";

        parent::__construct($message, $code, $previous);
    }
}
