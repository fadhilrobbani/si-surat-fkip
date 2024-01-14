<?php

namespace App\Filament\Resources\JenisSuratResource\Widgets;

use Filament\Widgets\Widget;

class WarningAction extends Widget
{
    protected static string $view = 'filament.resources.jenis-surat-resource.widgets.warning-action';
    protected int | string | array $columnSpan = 'full';
}
