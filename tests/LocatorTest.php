<?php declare(strict_types=1);

namespace Tests;

use Mage\Locator\Locator;
use Mage\Locator\Paths;
use SplFileInfo;
use Tests\Data\Context\Application\TestUseCase;
use Tests\Data\Context\Application\TestUseCase2;
use Tests\Data\Context\Domain\TestDomain;
use Tests\Data\Context\Infrastructure\TestImplementation;
use Tests\Data\Context\Infrastructure\TestImplementation2;
use function Lambdish\Phunctional\map;

final class LocatorTest extends TestCase
{
    public function test_files_are_found_given_paths_and_patterns(): void
    {
        $locator = new Locator(new Paths([[
            'path' => __DIR__ . '/Data/src',
            'pattern' => '/.*\/Infrastructure\/.*/',
        ], [
            'path' => __DIR__ . '/Data/src',
            'pattern' => '/.*\/Application\/.*/',
        ]]));

        $files = map(fn (SplFileInfo $file): string => $file->getFilename(), $locator->files());

        $this->assertContains('TestImplementation.php', $files);
        $this->assertContains('TestImplementation2.php', $files);
        $this->assertContains('TestUseCase.php', $files);
        $this->assertContains('TestUseCase2.php', $files);
    }

    public function test_classes_are_found_given_paths_and_patterns(): void
    {
        $locator = new Locator(new Paths([[
            'path' => __DIR__ . '/Data/src',
            'pattern' => '/.*\/Infrastructure\/.*/',
        ], [
            'path' => __DIR__ . '/Data/src',
            'pattern' => '/.*\/Application\/.*/',
        ]]));

        $classes = $locator->classes();
        $this->assertContains(TestImplementation::class, $classes);
        $this->assertContains(TestImplementation2::class, $classes);
        $this->assertContains(TestUseCase::class, $classes);
        $this->assertContains(TestUseCase2::class, $classes);
        $this->assertNotContains(TestDomain::class, $classes);
    }
}
