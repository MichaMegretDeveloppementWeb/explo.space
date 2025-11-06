<?php

namespace Tests\Unit\Helpers;

use App\Helpers\UploadHelper;
use Tests\TestCase;

/**
 * Tests pour UploadHelper
 *
 * Note: UploadHelper lit les limites PHP (upload_max_filesize, post_max_size)
 * et fournit des conversions en KB/MB. Il ne gère PAS les uploads de fichiers.
 * Les uploads sont gérés par PhotoProcessingService (testé séparément).
 */
class UploadHelperTest extends TestCase
{
    public function test_get_php_upload_max_size_returns_positive_integer(): void
    {
        // Act
        $size = UploadHelper::getPhpUploadMaxSize();

        // Assert
        $this->assertIsInt($size);
        $this->assertGreaterThan(0, $size);
    }

    public function test_get_php_upload_max_size_kb_returns_positive_integer(): void
    {
        // Act
        $sizeKB = UploadHelper::getPhpUploadMaxSizeKB();

        // Assert
        $this->assertIsInt($sizeKB);
        $this->assertGreaterThan(0, $sizeKB);
    }

    public function test_get_php_upload_max_size_mb_returns_positive_float(): void
    {
        // Act
        $sizeMB = UploadHelper::getPhpUploadMaxSizeMB();

        // Assert
        $this->assertIsFloat($sizeMB);
        $this->assertGreaterThan(0, $sizeMB);
    }

    public function test_get_php_upload_max_size_conversions_are_consistent(): void
    {
        // Act
        $sizeBytes = UploadHelper::getPhpUploadMaxSize();
        $sizeKB = UploadHelper::getPhpUploadMaxSizeKB();
        $sizeMB = UploadHelper::getPhpUploadMaxSizeMB();

        // Assert - KB devrait être environ bytes / 1024
        $expectedKB = (int) round($sizeBytes / 1024);
        $this->assertEquals($expectedKB, $sizeKB);

        // Assert - MB devrait être environ bytes / (1024 * 1024)
        $expectedMB = round($sizeBytes / (1024 * 1024), 2);
        $this->assertEquals($expectedMB, $sizeMB);
    }

    public function test_get_post_max_size_returns_positive_integer(): void
    {
        // Act
        $size = UploadHelper::getPostMaxSize();

        // Assert
        $this->assertIsInt($size);
        $this->assertGreaterThan(0, $size);
    }

    public function test_get_post_max_size_kb_returns_positive_integer(): void
    {
        // Act
        $sizeKB = UploadHelper::getPostMaxSizeKB();

        // Assert
        $this->assertIsInt($sizeKB);
        $this->assertGreaterThan(0, $sizeKB);
    }

    public function test_get_post_max_size_mb_returns_positive_float(): void
    {
        // Act
        $sizeMB = UploadHelper::getPostMaxSizeMB();

        // Assert
        $this->assertIsFloat($sizeMB);
        $this->assertGreaterThan(0, $sizeMB);
    }

    public function test_get_post_max_size_conversions_are_consistent(): void
    {
        // Act
        $sizeBytes = UploadHelper::getPostMaxSize();
        $sizeKB = UploadHelper::getPostMaxSizeKB();
        $sizeMB = UploadHelper::getPostMaxSizeMB();

        // Assert - KB devrait être environ bytes / 1024
        $expectedKB = (int) round($sizeBytes / 1024);
        $this->assertEquals($expectedKB, $sizeKB);

        // Assert - MB devrait être environ bytes / (1024 * 1024)
        $expectedMB = round($sizeBytes / (1024 * 1024), 2);
        $this->assertEquals($expectedMB, $sizeMB);
    }

    public function test_post_max_size_is_usually_greater_than_upload_max_filesize(): void
    {
        // Act
        $uploadMax = UploadHelper::getPhpUploadMaxSize();
        $postMax = UploadHelper::getPostMaxSize();

        // Assert
        // Dans une configuration PHP standard, post_max_size >= upload_max_filesize
        // (car POST peut contenir plusieurs fichiers + autres données)
        $this->assertGreaterThanOrEqual($uploadMax, $postMax);
    }

    public function test_mb_values_have_two_decimal_places(): void
    {
        // Act
        $uploadMB = UploadHelper::getPhpUploadMaxSizeMB();
        $postMB = UploadHelper::getPostMaxSizeMB();

        // Assert - Vérifier que les valeurs ont max 2 décimales
        $this->assertEquals($uploadMB, round($uploadMB, 2));
        $this->assertEquals($postMB, round($postMB, 2));
    }
}
