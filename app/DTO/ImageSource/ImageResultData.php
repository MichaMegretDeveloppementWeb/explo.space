<?php

namespace App\DTO\ImageSource;

readonly class ImageResultData
{
    public function __construct(
        public string $url,
        public string $source,
        public string $license,
        public ?string $caption = null,
        public ?int $width = null,
        public ?int $height = null,
    ) {}

    /**
     * @param  array{url: string, source: string, license: string, caption?: string|null, width?: int|null, height?: int|null}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            url: $data['url'],
            source: $data['source'],
            license: $data['license'],
            caption: $data['caption'] ?? null,
            width: $data['width'] ?? null,
            height: $data['height'] ?? null,
        );
    }

    /**
     * @return array{url: string, source: string, license: string, caption: string|null, width: int|null, height: int|null}
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'source' => $this->source,
            'license' => $this->license,
            'caption' => $this->caption,
            'width' => $this->width,
            'height' => $this->height,
        ];
    }
}
