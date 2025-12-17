<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model DocumentChunk untuk sistem AI Ollama
 * 
 * Per Requirements 2.1, 2.2: Document chunking untuk vector embeddings
 * Selaras dengan D09 Database Documentation v3.6.0
 *
 * @property int $id
 * @property int $document_id
 * @property string $chunk_text
 * @property array $embedding
 * @property string|null $source
 * @property int $chunk_index
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Document $document
 * @property-read string $preview
 * @property-read int $text_length
 * @method static \Database\Factories\DocumentChunkFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentChunk newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentChunk newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentChunk query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentChunk whereChunkIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentChunk whereChunkText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentChunk whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentChunk whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentChunk whereEmbedding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentChunk whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentChunk whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentChunk whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentChunk withEmbedding()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentChunk withIndex(int $index)
 * @mixin \Eloquent
 */
class DocumentChunk extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'document_id',
        'chunk_text',
        'embedding',
        'source',
        'chunk_index',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'embedding' => 'array',
        ];
    }

    /**
     * Hubungan dengan Document
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Scope untuk chunk dengan indeks tertentu
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithIndex($query, int $index)
    {
        return $query->where('chunk_index', $index);
    }

    /**
     * Scope untuk chunk dengan embedding
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithEmbedding($query)
    {
        return $query->whereNotNull('embedding');
    }

    /**
     * Kira persamaan cosine dengan embedding lain
     */
    public function cosineSimilarity(array $otherEmbedding): float
    {
        if (empty($this->embedding) || empty($otherEmbedding)) {
            return 0.0;
        }

        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        $count = min(count($this->embedding), count($otherEmbedding));

        for ($i = 0; $i < $count; $i++) {
            $dotProduct += $this->embedding[$i] * $otherEmbedding[$i];
            $normA += $this->embedding[$i] * $this->embedding[$i];
            $normB += $otherEmbedding[$i] * $otherEmbedding[$i];
        }

        $normA = sqrt($normA);
        $normB = sqrt($normB);

        if ($normA == 0.0 || $normB == 0.0) {
            return 0.0;
        }

        return $dotProduct / ($normA * $normB);
    }

    /**
     * Dapatkan preview teks chunk (100 karakter pertama)
     */
    public function getPreviewAttribute(): string
    {
        return strlen($this->chunk_text) > 100
            ? substr($this->chunk_text, 0, 100).'...'
            : $this->chunk_text;
    }

    /**
     * Dapatkan panjang teks chunk
     */
    public function getTextLengthAttribute(): int
    {
        return strlen($this->chunk_text);
    }

    /**
     * Semak sama ada chunk mempunyai embedding
     */
    public function hasEmbedding(): bool
    {
        return ! empty($this->embedding);
    }
}
