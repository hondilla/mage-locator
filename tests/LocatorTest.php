<?php declare(strict_types=1);

namespace Tests;

use Mage\Class\Locator;
use Tests\Data\Context\Application\TestUseCase;
use Tests\Data\Context\Application\TestUseCase2;
use Tests\Data\Context\Domain\TestDomain;
use Tests\Data\Context\Infrastructure\TestImplementation;
use Tests\Data\Context\Infrastructure\TestImplementation2;

final class LocatorTest extends TestCase
{
    public function test_classes_are_found_given_paths_and_patterns(): void
    {
        $locator = new Locator([[
            'path' => __DIR__ . '/Data/src',
            'pattern' => '/.*\/Infrastructure\/.*/',
        ], [
            'path' => __DIR__ . '/Data/src',
            'pattern' => '/.*\/Application\/.*/',
        ]]);

        $classes = $locator->getClasses();
        $this->assertContains(TestImplementation::class, $classes);
        $this->assertContains(TestImplementation2::class, $classes);
        $this->assertContains(TestUseCase::class, $classes);
        $this->assertContains(TestUseCase2::class, $classes);
        $this->assertNotContains(TestDomain::class, $classes);
    }
}
