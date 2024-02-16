<?php declare(strict_types=1);

namespace Mage\Class;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use function Lambdish\Phunctional\reduce;

final readonly class Locator
{
    public function __construct(private array $paths) {}

    public function getClasses(): array
    {
        /** @psalm-var array<int, SplFileInfo> $files */
        $files = reduce(
            /** @psalm-param array<string, string> $path */
            function (array $acc, array $path): array {
                return [...$acc, ...$this->searchFiles($path)];
            },
            $this->paths,
            []
        );

        return $this->filesToClasses($files);
    }

    /** @psalm-param array<string, string> $pathOptions */
    private function searchFiles(array $pathOptions): array
    {
        if (!is_dir($pathOptions['path'])) {
            return [];
        }

        /** @psalm-var array<int, SplFileInfo> */
        return reduce(function (array $acc, SplFileInfo $file) use ($pathOptions): array {
            if ($file->isFile() && $this->matchFiles($pathOptions['pattern'], $file)) {
                $acc[] = $file;
            }
            return $acc;
        }, new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pathOptions['path'])), []);
    }

    private function matchFiles(string $pattern, SplFileInfo $file): bool
    {
        /** @infection-ignore-all */
        if ($pattern === '') {
            return false;
        }
        /** @infection-ignore-all */
        return preg_match($pattern, $file->getPathname()) === 1;
    }

    private function filesToClasses(array $files): array
    {
        /** @psalm-var array<int, string> */
        return reduce(function (array $acc, SplFileInfo $file): array {
            $src = $file->openFile()->fread($file->getSize());
            if (preg_match('#(namespace)(\\s+)([A-Za-z0-9\\\\]+?)(\\s*);#', $src, $matches)) {
                $className = $matches[3] . '\\' . pathinfo($file->getPathname(), PATHINFO_FILENAME);
                if (class_exists($className)) {
                    $acc[] = $className;
                }
            }
            return $acc;
        }, $files, []);
    }
}
