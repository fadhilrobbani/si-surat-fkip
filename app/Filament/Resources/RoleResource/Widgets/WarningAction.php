<?php

namespace App\Filament\Resources\RoleResource\Widgets;

use Filament\Widgets\Widget;

class WarningAction extends Widget
{
    protected static string $view = 'filament.resources.role-resource.widgets.warning-action';
    protected int | string | array $columnSpan = 'full';
}
