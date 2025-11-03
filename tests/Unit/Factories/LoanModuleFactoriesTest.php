<?php

declare(strict_types=1);

namespace Tests\Unit\Factories;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\LoanPriority;
use App\Enums\LoanStatus;
use App\Enums\TransactionType;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Division;
use App\Models\LoanApplication;
use App\Models\LoanItem;
use App\Models\LoanTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Loan Module Factories Test
 *
 * Tests comprehensive factory functionality for loan module models.
 *
 * @see D03-FR-005.1 Model factories for testing
 */
class LoanModuleFactoriesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_loan_application_with_default_state(): void
    {
        $application = LoanApplication::factory()->create();

        $this->assertInstanceOf(LoanApplication::class, $application);
        $this->assertNotNull($application->application_number);
        $this->assertNull($application->user_id); // Default is guest
        $this->assertNotNull($application->applicant_name);
        $this->assertNotNull($application->applicant_email);
        $this->assertEquals(LoanStatus::SUBMITTED, $application->status);
        $this->assertEquals(LoanPriority::NORMAL, $application->priority);
    }

    /** @test */
    public function it_creates_authenticated_loan_application(): void
    {
        $application = LoanApplication::factory()->authenticated()->create();

        $this->assertNotNull($application->user_id);
        $this->assertInstanceOf(\App\Models\User::class, $application->user);
    }

    /** @test */
    public function it_creates_loan_application_with_various_statuses(): void
    {
        $draft = LoanApplication::factory()->draft()->create();
        $underReview = LoanApplication::factory()->underReview()->create();
        $approved = LoanApplication::factory()->approved()->create();
        $rejected = LoanApplication::factory()->rejected()->create();

        $this->assertEquals(LoanStatus::DRAFT, $draft->status);
        $this->assertEquals(LoanStatus::UNDER_REVIEW, $underReview->status);
        $this->assertNotNull($underReview->approval_token);
        $this->assertEquals(LoanStatus::APPROVED, $approved->status);
        $this->assertNotNull($approved->approved_at);
        $this->assertEquals(LoanStatus::REJECTED, $rejected->status);
        $this->assertNotNull($rejected->rejected_reason);
    }

    /** @test */
    public function it_creates_asset_with_default_state(): void
    {
        $asset = Asset::factory()->create();

        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertNotNull($asset->asset_tag);
        $this->assertNotNull($asset->name);
        $this->assertEquals(AssetStatus::AVAILABLE, $asset->status);
        $this->assertEquals(AssetCondition::GOOD, $asset->condition);
        $this->assertIsArray($asset->specifications);
        $this->assertIsArray($asset->accessories);
    }

    /** @test */
    public function it_creates_asset_with_various_statuses(): void
    {
        $available = Asset::factory()->available()->create();
        $loaned = Asset::factory()->loaned()->create();
        $maintenance = Asset::factory()->maintenance()->create();
        $damaged = Asset::factory()->damaged()->create();

        $this->assertEquals(AssetStatus::AVAILABLE, $available->status);
        $this->assertEquals(AssetStatus::LOANED, $loaned->status);
        $this->assertEquals(AssetStatus::MAINTENANCE, $maintenance->status);
        $this->assertEquals(AssetStatus::DAMAGED, $damaged->status);
        $this->assertEquals(AssetCondition::DAMAGED, $damaged->condition);
    }

    /** @test */
    public function it_creates_asset_category_with_specification_template(): void
    {
        $category = AssetCategory::factory()->laptops()->create();

        $this->assertEquals('Laptops', $category->name);
        $this->assertEquals('LAP', $category->code);
        $this->assertIsArray($category->specification_template);
        $this->assertArrayHasKey('processor', $category->specification_template);
        $this->assertTrue($category->requires_approval);
    }

    /** @test */
    public function it_creates_loan_item_with_condition_tracking(): void
    {
        $loanItem = LoanItem::factory()->issued()->create();

        $this->assertInstanceOf(LoanItem::class, $loanItem);
        $this->assertNotNull($loanItem->condition_before);
        $this->assertIsArray($loanItem->accessories_issued);
    }

    /** @test */
    public function it_creates_loan_item_with_damage(): void
    {
        $loanItem = LoanItem::factory()->damaged()->create();

        $this->assertNotNull($loanItem->condition_before);
        $this->assertNotNull($loanItem->condition_after);
        $this->assertNotEquals($loanItem->condition_before, $loanItem->condition_after);
        $this->assertNotNull($loanItem->damage_report);
    }

    /** @test */
    public function it_creates_loan_transaction_with_various_types(): void
    {
        $issue = LoanTransaction::factory()->issue()->create();
        $return = LoanTransaction::factory()->return()->create();
        $extend = LoanTransaction::factory()->extend()->create();

        $this->assertEquals(TransactionType::ISSUE, $issue->transaction_type);
        $this->assertEquals(TransactionType::RETURN, $return->transaction_type);
        $this->assertEquals(TransactionType::EXTEND, $extend->transaction_type);
    }

    /** @test */
    public function it_creates_division_with_bilingual_names(): void
    {
        $division = Division::factory()->ict()->create();

        $this->assertEquals('ICT', $division->code);
        $this->assertNotNull($division->name_ms);
        $this->assertNotNull($division->name_en);
        $this->assertTrue($division->is_active);
    }

    /** @test */
    public function it_creates_complete_loan_workflow(): void
    {
        // Create application with items and transactions
        $application = LoanApplication::factory()->inUse()->create();
        $asset = Asset::factory()->loaned()->create();

        $loanItem = LoanItem::factory()->issued()->create([
            'loan_application_id' => $application->id,
            'asset_id' => $asset->id,
        ]);

        $transaction = LoanTransaction::factory()->issue()->create([
            'loan_application_id' => $application->id,
            'asset_id' => $asset->id,
        ]);

        $this->assertEquals(LoanStatus::IN_USE, $application->status);
        $this->assertEquals($application->id, $loanItem->loan_application_id);
        $this->assertEquals($asset->id, $loanItem->asset_id);
        $this->assertEquals($application->id, $transaction->loan_application_id);
        $this->assertEquals(TransactionType::ISSUE, $transaction->transaction_type);
    }
}
