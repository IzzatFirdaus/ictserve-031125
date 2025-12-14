<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Faq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit Tests for Faq Model
 *
 * @requirements 1.1, 1.5, 4.1
 *
 * @compliance D10 v3.6.0 Source Code Documentation
 */
class FaqTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_correct_fillable_attributes(): void
    {
        $faq = new Faq;

        $expected = [
            'question',
            'answer',
            'tags',
            'match_score',
            'preferred_model',
            'complexity_score',
            'created_by',
        ];

        $this->assertEquals($expected, $faq->getFillable());
    }

    #[Test]
    public function it_casts_attributes_correctly(): void
    {
        $faq = new Faq;

        $casts = $faq->getCasts();

        $this->assertEquals('array', $casts['tags']);
        $this->assertEquals('float', $casts['match_score']);
        $this->assertEquals('float', $casts['complexity_score']);
    }

    #[Test]
    public function it_belongs_to_a_creator(): void
    {
        $user = User::factory()->create();
        $faq = Faq::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $faq->creator);
        $this->assertEquals($user->id, $faq->creator->id);
    }

    #[Test]
    public function it_can_have_null_creator_for_true_hybrid_architecture(): void
    {
        $faq = Faq::factory()->create(['created_by' => null]);

        $this->assertNull($faq->created_by);
        $this->assertNull($faq->creator->id ?? null);
    }

    #[Test]
    public function it_can_search_by_question_and_answer(): void
    {
        Faq::factory()->create([
            'question' => 'Bagaimana cara reset kata laluan?',
            'answer' => 'Klik pautan Lupa Kata Laluan',
        ]);

        Faq::factory()->create([
            'question' => 'Cara membuat tiket?',
            'answer' => 'Pergi ke halaman tiket baru',
        ]);

        $results = Faq::search('kata laluan')->get();

        $this->assertCount(1, $results);
        $this->assertStringContainsString('kata laluan', $results->first()->question);
    }

    #[Test]
    public function it_can_filter_by_minimum_score(): void
    {
        Faq::factory()->create(['match_score' => 0.8]);
        Faq::factory()->create(['match_score' => 0.2]);
        Faq::factory()->create(['match_score' => 0.5]);

        $results = Faq::withMinScore(0.3)->get();

        $this->assertCount(2, $results);
        foreach ($results as $faq) {
            $this->assertGreaterThanOrEqual(0.3, $faq->match_score);
        }
    }

    #[Test]
    public function it_uses_soft_deletes(): void
    {
        $faq = Faq::factory()->create();

        $faq->delete();

        $this->assertSoftDeleted($faq);
        $this->assertNotNull($faq->fresh()->deleted_at);
    }

    #[Test]
    public function it_implements_auditable_contract(): void
    {
        $faq = new Faq;

        $this->assertInstanceOf(\OwenIt\Auditing\Contracts\Auditable::class, $faq);
    }

    #[Test]
    public function it_stores_tags_as_array(): void
    {
        $tags = ['kata laluan', 'reset', 'log masuk'];
        $faq = Faq::factory()->create(['tags' => $tags]);

        $this->assertEquals($tags, $faq->fresh()->tags);
        $this->assertIsArray($faq->fresh()->tags);
    }

    #[Test]
    public function it_stores_match_score_as_float(): void
    {
        $score = 0.85;
        $faq = Faq::factory()->create(['match_score' => $score]);

        $this->assertEquals($score, $faq->fresh()->match_score);
        $this->assertIsFloat($faq->fresh()->match_score);
    }

    #[Test]
    public function it_can_handle_null_tags(): void
    {
        $faq = Faq::factory()->create(['tags' => null]);

        $this->assertNull($faq->fresh()->tags);
    }

    #[Test]
    public function it_can_handle_null_match_score(): void
    {
        $faq = Faq::factory()->create(['match_score' => null]);

        $this->assertNull($faq->fresh()->match_score);
    }

    #[Test]
    public function it_has_timestamps(): void
    {
        $faq = Faq::factory()->create();

        $this->assertNotNull($faq->created_at);
        $this->assertNotNull($faq->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $faq->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $faq->updated_at);
    }
}
