<?php

namespace Omatech\Hexagon\Domain\File;

use Illuminate\Support\Str;

class File
{
    /** @var string */
    private $type;
    /** @var string */
    private $domain;
    /** @var string */
    private $boundary;
    /** @var string */
    private $name;
    /** @var string */
    private $layer;

    public function __construct(string $name, string $type, string $layer, string $domain, string $boundary = null)
    {
        $this->domain = $domain;
        $this->boundary = $boundary;
        $this->type = $type;
        $this->name = $name;
        $this->layer = $layer;
    }

    public function getPath(): string
    {
        $appPath = rtrim(config('hexagon.directories.app', 'app'), '/');
        $layerPath = rtrim(config('hexagon.directories.' . $this->layer, '/'), '/');
        $typePath = rtrim(config('hexagon.directories.' . $this->type, '/'), '/');

        $path = trim($appPath . '/' . $this->getBoundary() . $layerPath . '/' . $typePath, '/');
        $path = str_replace('//', '/', $path);

        $path = $this->pathHasDomain() ? $path . '/' . $this->getDomain() : $path;
        return $this->pathHasUseCase() ? $path . '/' . $this->getOriginalName() : $path;
    }

    public function getNamespace(string $path = null): string
    {
        $path = $path ?? $this->getPath();
        $path = ucwords($path);

        return str_replace('/', '\\', $path);
    }

    public function getUse(string $path = null): string
    {
        $path = $path ?? $this->getPath();
        $path = ucwords($path);
        $path .= '/'  . $this->getName();

        return 'use ' . str_replace('/', '\\', $path) . ';';
    }

    public function getDependencies(): array
    {
        $dependencies = config('hexagon.dependencies.'.$this->type, []);

        foreach ($dependencies as $key => $dependency) {
            if (!empty($dependency)) {
                $dependencies[$key] = array_merge([
                        'name' => $this->name,
                        'type' => $this->type,
                        'layer' => $this->layer,
                        'domain' => $this->domain,
                        'boundary' => $this->boundary
                    ], $dependency
                );
            }
        }

        return $dependencies;
    }

    public function getLayerNamespace(string $layer): string
    {
        $appPath = rtrim(config('hexagon.directories.app', 'app'), '/');
        $layerPath = rtrim(config('hexagon.directories.' . $layer, ucfirst($layer)), '/');
        $path = $appPath . '/' . $this->getBoundary() . $layerPath;

        return str_replace('/', '\\', $path);
    }

    public function exists(string $path = null, string $fileExtension = null): bool
    {
        $path = $path ?? $this->getPath();

        return file_exists(base_path($path) . '/' . $this->getName($fileExtension));
    }

    public static function strToStudly(string $text): string
    {
        $text = preg_replace(["/[^a-zA-Z\d]/", '/__+/'], ' ', $text);
        $text = ucwords($text);
        $text = str_replace(' ', '', $text);

        return Str::studly($text);
    }

    public function getType(): string
    {
        return self::strToStudly($this->type);
    }

    public function getDomain(): string
    {
        return self::strToStudly($this->domain);
    }

    public function getLayer(): string
    {
        return self::strToStudly($this->layer);
    }

    public function getName(?string $extension = ''): string
    {
        $suffix = config('hexagon.names.' . $this->type . '.suffix', '');
        $prefix = config('hexagon.names.' . $this->type . '.prefix', '');
        return $prefix . self::strToStudly($this->name) . $suffix . $extension;
    }

    public function getBoundary(): string
    {
        if(!empty($this->boundary)) {
            return self::strToStudly(rtrim($this->boundary, '/')) . '/';
        }
        return '';
    }

    public function pathHasDomain(): bool
    {
        return config('hexagon.domain-paths.' . $this->type, true);
    }

    public function pathHasUseCase(): bool
    {
        return config('hexagon.use-case-paths.' . $this->type, false);
    }

    public function withType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function withDomain(string $domain): self
    {
        $this->domain = $domain;
        return $this;
    }

    public function withName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function withLayer(string $layer): self
    {
        $this->layer = $layer;
        return $this;
    }

    public function withBoundary(string $boundary): self
    {
        $this->boundary = $boundary;
        return $this;
    }

    public function getOriginalType(): string
    {
        return $this->type;
    }

    public function getOriginalDomain(): string
    {
        return $this->domain;
    }

    public function getOriginalName(): string
    {
        return $this->name;
    }

    public function getOriginalBoundary(): ?string
    {
        return $this->boundary;
    }
}
