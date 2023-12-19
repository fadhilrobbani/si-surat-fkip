<?php

namespace App\Filament\Resources\JurusanResource\Widgets;

use Filament\Widgets\Widget;

class WarningAction extends Widget
{
    protected static string $view = 'filament.resources.jurusan-resource.widgets.warning-action';
    protected int | string | array $columnSpan = 'full';

}
