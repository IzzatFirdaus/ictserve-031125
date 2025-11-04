<?php

declare(strict_types=1);

namespace App\Traits;

use App\Services\EncryptionService;
use Illuminate\Support\Facades\App;

/**
 * Encrypts Sensitive Data Trait
 *
 * Automatically encrypts and decrypts sensitive model attributes
 * using AES-256 encryption for PDPA 2010 compliance.
 *
 * @see D03-FR-010.3 Data encryption requirements
 * @see D03-FR-006.2 PDPA compliance
 */
trait EncryptsSensitiveData
{
    /**
     * The attributes that should be encrypted.
     */
    protected array $encrypted = [];

    /**
     * Boot the trait
     */
    protected static function bootEncryptsSensitiveData(): void
    {
        static::saving(function ($model) {
            $model->encryptSensitiveAttributes();
        });

        static::retrieved(function ($model) {
            $model->decryptSensitiveAttributes();
        });
    }

    /**
     * Encrypt sensitive attributes before saving
     */
    protected function encryptSensitiveAttributes(): void
    {
        $encryptionService = App::make(EncryptionService::class);

        foreach ($this->getEncryptedAttributes() as $attribute) {
            if (isset($this->attributes[$attribute]) && !$this->isEncrypted($this->attributes[$attribute])) {
                $this->attributes[$attribute] = $encryptionService->encryptSensitiveData($this->attributes[$attribute]);
            }
        }
    }

    /**
     * Decrypt sensitive attributes after retrieval
     */
    protected function decryptSensitiveAttributes(): void
    {
        $encryptionService = App::make(EncryptionService::class);

        foreach ($this->getEncryptedAttributes() as $attribute) {
            if (isset($this->attributes[$attribute]) && $this->isEncrypted($this->attributes[$attribute])) {
                try {
                    $this->attributes[$attribute] = $encryptionService->decryptSensitiveData($this->attributes[$attribute]);
                } catch (\Exception $e) {
                    // Log decryption failure but don't break the application
                    logger()->error('Failed to decrypt attribute', [
                        'model' => get_class($this),
                        'attribute' => $attribute,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * Get the encrypted attributes
     */
    protected function getEncryptedAttributes(): array
    {
        return $this->encrypted ?? [];
    }

    /**
     * Check if a value is already encrypted
     */
    protected function isEncrypted(string $value): bool
    {
        // Laravel's encrypted values start with "eyJpdiI6" (base64 encoded JSON)
        return str_starts_with($value, 'eyJpdiI6');
    }

    /**
     * Get attribute value (override to handle encryption)
     */
    public function getAttributeValue($key)
    {
        $value = parent::getAttributeValue($key);

        // If this is an encrypted attribute and it's still encrypted, decrypt it
        if (in_array($key, $this->getEncryptedAttributes()) && is_string($value) && $this->isEncrypted($value)) {
            try {
                $encryptionService = App::make(EncryptionService::class);
                return $encryptionService->decryptSensitiveData($value);
            } catch (\Exception $e) {
                logger()->error('Failed to decrypt attribute on access', [
                    'model' => get_class($this),
                    'attribute' => $key,
                    'error' => $e->getMessage(),
                ]);
                return $value;
            }
        }

        return $value;
    }

    /**
     * Set attribute value (override to handle encryption)
     */
    public function setAttribute($key, $value)
    {
        // Don't encrypt null values or already encrypted values
        if (in_array($key, $this->getEncryptedAttributes()) && $value !== null && !$this->isEncrypted($value)) {
            try {
                $encryptionService = App::make(EncryptionService::class);
                $value = $encryptionService->encryptSensitiveData($value);
            } catch (\Exception $e) {
                logger()->error('Failed to encrypt attribute on set', [
                    'model' => get_class($this),
                    'attribute' => $key,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Convert the model instance to an array (sanitize encrypted fields)
     */
    public function toArray(): array
    {
        $array = parent::toArray();

        // Sanitize encrypted fields for array output
        foreach ($this->getEncryptedAttributes() as $attribute) {
            if (isset($array[$attribute])) {
                $array[$attribute] = '[ENCRYPTED]';
            }
        }

        return $array;
    }

    /**
     * Get the model's original attributes (decrypt if needed)
     */
    public function getOriginal($key = null, $default = null)
    {
        $original = parent::getOriginal($key, $default);

        if ($key && in_array($key, $this->getEncryptedAttributes()) && is_string($original) && $this->isEncrypted($original)) {
            try {
                $encryptionService = App::make(EncryptionService::class);
                return $encryptionService->decryptSensitiveData($original);
            } catch (\Exception $e) {
                logger()->error('Failed to decrypt original attribute', [
                    'model' => get_class($this),
                    'attribute' => $key,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $original;
    }
}
