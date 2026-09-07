<?php

namespace App\Filament\Widgets;

use App\Enums\ContactMessageStatus;
use App\Models\ContactMessage;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Lo primero que ve Miguel al entrar: cuántos mensajes tiene sin atender y
 * cuánto contenido hay publicado.
 */
class ResumenBandejaWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    public static function canView(): bool
    {
        return auth()->user()?->can('viewAny', ContactMessage::class) ?? false;
    }

    protected function getStats(): array
    {
        $nuevos = ContactMessage::query()->nuevos()->count();
        $pendientes = ContactMessage::query()->pendientes()->count();
        $estaSemana = ContactMessage::query()->where('created_at', '>=', now()->subWeek())->count();
        $atendidos = ContactMessage::query()->where('status', ContactMessageStatus::Atendido)->count();

        return [
            Stat::make('Mensajes nuevos', $nuevos)
                ->description($pendientes.' pendientes en total')
                ->descriptionIcon('heroicon-m-inbox')
                ->color($nuevos > 0 ? 'warning' : 'success'),

            Stat::make('Recibidos esta semana', $estaSemana)
                ->description('Últimos 7 días')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Atendidos', $atendidos)
                ->description('Histórico')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Contenido publicado', $this->contenidoPublicado())
                ->description('Servicios, productos y proyectos visibles')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('gray'),
        ];
    }

    private function contenidoPublicado(): int
    {
        return Service::query()->published()->count()
            + Product::query()->published()->count()
            + Project::query()->published()->count();
    }
}
