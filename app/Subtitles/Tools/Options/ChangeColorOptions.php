<?php

namespace App\Subtitles\Tools\Options;

use RuntimeException;

class ChangeColorOptions extends ToolOptions
{
    public $newColor;

    public function rules(): array
    {
        return [
            'newColor' => 'required|string|regex:/^#[0-9a-f]{6}$/i',
        ];
    }

    public function setColor($hexColor)
    {
        if (! preg_match('/^#[0-9a-f]{6}$/i', $hexColor)) {
            throw new RuntimeException('Invalid hex color');
        }

        $this->newColor = strtolower($hexColor);

        return $this;
    }
}
