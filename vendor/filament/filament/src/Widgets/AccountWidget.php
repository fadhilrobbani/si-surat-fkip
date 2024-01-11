<?php

namespace Filament\Widgets;

class AccountWidget extends Widget
{
    protected static ?int $sort = -3;

    /**
     * @var view-string
     */
    protected static string $view = 'filament-panels::widgets.account-widget';
    protected int | string | array $columnSpan = 'full';
}
