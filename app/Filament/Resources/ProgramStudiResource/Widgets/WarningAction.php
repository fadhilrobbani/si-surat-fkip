<?php

namespace App\Filament\Resources\ProgramStudiResource\Widgets;

use Filament\Widgets\Widget;

class WarningAction extends Widget
{
    protected static string $view = 'filament.resources.program-studi-resource.widgets.warning-action';
    protected int | string | array $columnSpan = 'full';

}
