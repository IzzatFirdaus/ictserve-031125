<?php

declare(strict_types=1);

$baseDir = __DIR__;
$vendorDir = $baseDir.'/vendor';

$installedData = [
    'root' => [
        'name' => 'laravel/laravel',
        'pretty_version' => 'dev-main',
        'version' => 'dev-main',
        'reference' => null,
        'type' => 'project',
        'install_path' => $baseDir,
        'aliases' => [],
        'dev' => true,
    ],
    'versions' => [],
];

if (! class_exists('Composer\InstalledVersions')) {
    class ComposerInstalledVersions
    {
        private static array $data = [];

        public static function reload(array $data): void
        {
            self::$data = $data;
        }

        public static function isInstalled(string $packageName, bool $includeDevRequirements = true): bool
        {
            if (! $includeDevRequirements && isset(self::$data['versions'][$packageName]['dev_requirement']) && self::$data['versions'][$packageName]['dev_requirement']) {
                return false;
            }

            return isset(self::$data['versions'][$packageName]);
        }

        public static function getVersion(string $packageName): ?string
        {
            return self::$data['versions'][$packageName]['version'] ?? null;
        }

        public static function getPrettyVersion(string $packageName): ?string
        {
            return self::$data['versions'][$packageName]['pretty_version'] ?? null;
        }

        public static function getReference(string $packageName): ?string
        {
            return self::$data['versions'][$packageName]['reference'] ?? null;
        }

        public static function getInstallPath(string $packageName): ?string
        {
            return self::$data['versions'][$packageName]['install_path'] ?? null;
        }

        public static function getRootPackage(): array
        {
            return self::$data['root'] ?? [];
        }

        public static function getAllRawData(): array
        {
            return [self::$data];
        }

        public static function getInstalledPackages(): array
        {
            return array_keys(self::$data['versions'] ?? []);
        }

        public static function satisfies($versionParser, string $packageName, string $constraint): bool
        {
            return isset(self::$data['versions'][$packageName]);
        }
    }

    class_alias(ComposerInstalledVersions::class, 'Composer\\InstalledVersions');
}

$psr4Map = [
    'App\\' => [$baseDir.'/app/'],
    'Database\\Factories\\' => [$baseDir.'/database/factories/'],
    'Database\\Seeders\\' => [$baseDir.'/database/seeders/'],
];

$autoloadFiles = [];
$devPackages = [];
$installedPackages = [];

$lock = json_decode((string) file_get_contents($baseDir.'/composer.lock'), true);

if (isset($lock['packages-dev']) && is_array($lock['packages-dev'])) {
    $devPackages = array_column($lock['packages-dev'], 'name');
}

$allPackages = array_merge($lock['packages'] ?? [], $lock['packages-dev'] ?? []);

foreach ($allPackages as $package) {
    if (! isset($package['name'])) {
        continue;
    }

    $name = $package['name'];
    $version = $package['version'] ?? 'dev-main';
    $installedPackages[$name] = [
        'pretty_version' => $version,
        'version' => $version,
        'reference' => $package['source']['reference'] ?? $package['dist']['reference'] ?? null,
        'type' => $package['type'] ?? 'library',
        'install_path' => $vendorDir.'/'.$name,
        'aliases' => $package['aliases'] ?? [],
        'dev_requirement' => in_array($name, $devPackages, true),
        'provided' => $package['provide'] ?? [],
        'replaced' => $package['replace'] ?? [],
    ];
}

if (class_exists('Composer\InstalledVersions') && method_exists('Composer\InstalledVersions', 'reload')) {
    $installedData['versions'] = $installedPackages;

    Composer\InstalledVersions::reload($installedData);
}

foreach (glob($vendorDir.'/*/*/composer.json') as $composerFile) {
    $packageDir = dirname($composerFile);
    $data = json_decode((string) file_get_contents($composerFile), true);

    if (! is_array($data) || empty($data['autoload'])) {
        continue;
    }

    $autoload = $data['autoload'];

    if (isset($autoload['psr-4']) && is_array($autoload['psr-4'])) {
        foreach ($autoload['psr-4'] as $prefix => $paths) {
            foreach ((array) $paths as $path) {
                $psr4Map[$prefix][] = rtrim($packageDir.'/'.$path, '/').'/';
            }
        }
    }

    if (isset($autoload['files']) && is_array($autoload['files'])) {
        foreach ($autoload['files'] as $file) {
            $autoloadFiles[] = $packageDir.'/'.$file;
        }
    }
}

spl_autoload_register(static function (string $class) use ($psr4Map): bool {
    foreach ($psr4Map as $prefix => $dirs) {
        if (! str_starts_with($class, $prefix)) {
            continue;
        }

        $relative = substr($class, strlen($prefix));
        $relativePath = str_replace('\\', '/', $relative).'.php';

        foreach ($dirs as $dir) {
            $file = $dir.$relativePath;

            if (is_file($file)) {
                require_once $file;

                return true;
            }
        }
    }

    return false;
});

foreach ($autoloadFiles as $file) {
    if (is_file($file)) {
        require_once $file;
    }
}

$larastanSrc = $vendorDir.'/larastan/larastan/src';

if (is_dir($larastanSrc)) {
    $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($larastanSrc, \FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $fileInfo) {
        if ($fileInfo->getExtension() === 'php') {
            require_once $fileInfo->getPathname();
        }
    }
}
