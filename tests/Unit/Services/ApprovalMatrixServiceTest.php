<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\ApprovalMatrixService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Approval Matrix Service Tests
 *
 * Tests approval routing logic based on grade and asset value.
 *
 * @see D03-FR-002.1 Approval matrix logic
 * Requirements: 2.3, 9.2, 16.1, 7.2
 */
class ApprovalMatrixServiceTest extends TestCase
{
    use RefreshDatabase;

    private ApprovalMatrixService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new ApprovalMatrixService;
    }

    #[Test]
    public function it_determines_grade_44_approver_for_grade_41_low_value(): void
    {
        // Arrange
        $approver = User::factory()->create([
            'grade' => '44',
            'role' => 'approver',
            'email' => 'approver44@example.com',
        ]);

        // Act
        $result = $this->service->determineApprover('41', 3000);

        // Assert
        $this->assertEquals('44', $result['grade']);
        $this->assertEquals($approver->email, $result['email']);
    }

    #[Test]
    public function it_determines_grade_48_approver_for_grade_41_medium_value(): void
    {
        // Arrange
        $approver = User::factory()->create([
            'grade' => '48',
            'role' => 'approver',
            'email' => 'approver48@example.com',
        ]);

        // Act
        $result = $this->service->determineApprover('41', 7000);

        // Assert
        $this->assertEquals('48', $result['grade']);
        $this->assertEquals($approver->email, $result['email']);
    }

    #[Test]
    public function it_determines_grade_52_approver_for_grade_41_high_value(): void
    {
        // Arrange
        $approver = User::factory()->create([
            'grade' => '52',
            'role' => 'approver',
            'email' => 'approver52@example.com',
        ]);

        // Act
        $result = $this->service->determineApprover('41', 15000);

        // Assert
        $this->assertEquals('52', $result['grade']);
        $this->assertEquals($approver->email, $result['email']);
    }

    #[Test]
    public function it_determines_grade_48_approver_for_grade_44_low_value(): void
    {
        // Arrange
        $approver = User::factory()->create([
            'grade' => '48',
            'role' => 'approver',
            'email' => 'approver48@example.com',
        ]);

        // Act
        $result = $this->service->determineApprover('44', 8000);

        // Assert
        $this->assertEquals('48', $result['grade']);
        $this->assertEquals($approver->email, $result['email']);
    }

    #[Test]
    public function it_determines_grade_52_approver_for_grade_44_medium_value(): void
    {
        // Arrange
        $approver = User::factory()->create([
            'grade' => '52',
            'role' => 'approver',
            'email' => 'approver52@example.com',
        ]);

        // Act
        $result = $this->service->determineApprover('44', 15000);

        // Assert
        $this->assertEquals('52', $result['grade']);
        $this->assertEquals($approver->email, $result['email']);
    }

    #[Test]
    public function it_determines_grade_54_approver_for_grade_44_high_value(): void
    {
        // Arrange
        $approver = User::factory()->create([
            'grade' => '54',
            'role' => 'approver',
            'email' => 'approver54@example.com',
        ]);

        // Act
        $result = $this->service->determineApprover('44', 25000);

        // Assert
        $this->assertEquals('54', $result['grade']);
        $this->assertEquals($approver->email, $result['email']);
    }

    #[Test]
    public function it_accepts_admin_users_as_approvers(): void
    {
        // Arrange
        $admin = User::factory()->create([
            'grade' => '48',
            'role' => 'admin',
            'email' => 'admin@example.com',
        ]);

        // Act
        $result = $this->service->determineApprover('41', 7000);

        // Assert
        $this->assertEquals('48', $result['grade']);
        $this->assertEquals($admin->email, $result['email']);
    }

    #[Test]
    public function it_accepts_superuser_as_approver(): void
    {
        // Arrange
        $superuser = User::factory()->create([
            'grade' => '52',
            'role' => 'superuser',
            'email' => 'superuser@example.com',
        ]);

        // Act
        $result = $this->service->determineApprover('41', 15000);

        // Assert
        $this->assertEquals('52', $result['grade']);
        $this->assertEquals($superuser->email, $result['email']);
    }

    #[Test]
    public function it_falls_back_to_grade_54_when_specific_grade_not_found(): void
    {
        // Arrange
        $fallbackApprover = User::factory()->create([
            'grade' => '54',
            'role' => 'approver',
            'email' => 'fallback@example.com',
        ]);

        // Act - Request Grade 48 approver but none exists
        $result = $this->service->determineApprover('41', 7000);

        // Assert
        $this->assertEquals('54', $result['grade']);
        $this->assertEquals($fallbackApprover->email, $result['email']);
    }

    #[Test]
    public function it_falls_back_to_any_superuser_when_no_grade_match(): void
    {
        // Arrange
        $superuser = User::factory()->create([
            'grade' => '50', // Different grade
            'role' => 'superuser',
            'email' => 'superuser@example.com',
        ]);

        // Act
        $result = $this->service->determineApprover('41', 7000);

        // Assert
        $this->assertEquals($superuser->id, $result['user_id']);
        $this->assertEquals($superuser->email, $result['email']);
    }

    #[Test]
    public function it_throws_exception_when_no_approver_found(): void
    {
        // Arrange - No users in database

        // Act & Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No approver found in the system');

        $this->service->determineApprover('41', 5000);
    }

    #[Test]
    public function it_defaults_to_grade_54_for_unknown_applicant_grade(): void
    {
        // Arrange
        $approver = User::factory()->create([
            'grade' => '54',
            'role' => 'approver',
            'email' => 'approver54@example.com',
        ]);

        // Act - Use non-standard grade
        $result = $this->service->determineApprover('99', 5000);

        // Assert
        $this->assertEquals('54', $result['grade']);
        $this->assertEquals($approver->email, $result['email']);
    }

    #[Test]
    public function it_validates_user_can_approve_based_on_grade(): void
    {
        // Arrange
        $approver = User::factory()->create([
            'grade' => '48',
            'role' => 'approver',
        ]);

        // Act & Assert
        $this->assertTrue($this->service->canUserApprove($approver, '41', 7000));
        $this->assertFalse($this->service->canUserApprove($approver, '41', 15000)); // Requires Grade 52
    }

    #[Test]
    public function it_rejects_non_approver_users(): void
    {
        // Arrange
        $staff = User::factory()->create([
            'grade' => '48',
            'role' => 'staff', // Not an approver
        ]);

        // Act & Assert
        $this->assertFalse($this->service->canUserApprove($staff, '41', 7000));
    }

    #[Test]
    public function it_handles_exact_threshold_values_correctly(): void
    {
        // Arrange
        $approver44 = User::factory()->create([
            'grade' => '44',
            'role' => 'approver',
        ]);

        $approver48 = User::factory()->create([
            'grade' => '48',
            'role' => 'approver',
        ]);

        // Act & Assert - Test exact threshold values
        $result5000 = $this->service->determineApprover('41', 5000);
        $this->assertEquals('44', $result5000['grade']);

        $result5001 = $this->service->determineApprover('41', 5001);
        $this->assertEquals('48', $result5001['grade']);
    }

    #[Test]
    public function it_returns_complete_approver_information(): void
    {
        // Arrange
        $approver = User::factory()->create([
            'name' => 'John Approver',
            'email' => 'john.approver@example.com',
            'grade' => '48',
            'role' => 'approver',
        ]);

        // Act
        $result = $this->service->determineApprover('41', 7000);

        // Assert
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('grade', $result);
        $this->assertArrayHasKey('user_id', $result);
        $this->assertEquals('John Approver', $result['name']);
        $this->assertEquals('john.approver@example.com', $result['email']);
        $this->assertEquals($approver->id, $result['user_id']);
    }

    #[Test]
    public function it_handles_multiple_approvers_of_same_grade(): void
    {
        // Arrange - Create multiple Grade 48 approvers
        $approver1 = User::factory()->create([
            'grade' => '48',
            'role' => 'approver',
            'email' => 'approver1@example.com',
        ]);

        $approver2 = User::factory()->create([
            'grade' => '48',
            'role' => 'approver',
            'email' => 'approver2@example.com',
        ]);

        // Act
        $result = $this->service->determineApprover('41', 7000);

        // Assert - Should return one of the Grade 48 approvers
        $this->assertEquals('48', $result['grade']);
        $this->assertContains($result['email'], [
            'approver1@example.com',
            'approver2@example.com',
        ]);
    }

    #[Test]
    public function it_handles_grade_52_applications_correctly(): void
    {
        // Arrange
        $approver = User::factory()->create([
            'grade' => '54',
            'role' => 'approver',
            'email' => 'approver54@example.com',
        ]);

        // Act - Grade 52 staff always requires Grade 54 approval
        $result = $this->service->determineApprover('52', 5000);

        // Assert
        $this->assertEquals('54', $result['grade']);
        $this->assertEquals($approver->email, $result['email']);
    }

    #[Test]
    public function it_handles_very_high_value_applications(): void
    {
        // Arrange
        $approver = User::factory()->create([
            'grade' => '54',
            'role' => 'approver',
            'email' => 'approver54@example.com',
        ]);

        // Act - Very high value (PHP_FLOAT_MAX threshold)
        $result = $this->service->determineApprover('41', 1000000);

        // Assert
        $this->assertEquals('54', $result['grade']);
        $this->assertEquals($approver->email, $result['email']);
    }
}
