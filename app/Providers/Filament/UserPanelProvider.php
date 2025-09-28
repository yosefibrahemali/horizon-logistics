<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\MonthlyCostsChart;
use App\Filament\Widgets\ShipmentsChart;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use TomatoPHP\FilamentTranslations\FilamentTranslationsPlugin;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\FontProviders\GoogleFontProvider;


class UserPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('user')
            ->path('user-dashboard')
            ->login()
            ->colors([
                'primary' => '#c03101',
                'secondary' => Color::Gray,
                'success' => Color::Green,
                'danger' => Color::Red,
                'warning' => Color::Yellow,
                'info' => Color::Blue
            ])
            ->favicon(asset('logo.png'))
            ->brandName('Horizon Logistics') // الاسم يظهر مع اللوجو
            ->brandLogo(asset('logo-dark.png'))// مسار اللوجو
            ->brandLogoHeight('7.5rem')
            ->darkModeBrandLogo(asset('logo-dark.png'))
            // ->darkModeBrandLogoHeight('3.5rem')
            
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
              Widgets\AccountWidget::class,

            // الصف الأول: ملخصات
            \App\Filament\Widgets\ShipmentOverview::class,

            // الصف الثاني: رسوم بيانية
            \App\Filament\Widgets\ShipmentStatusChart::class,
            \App\Filament\Widgets\ShipmentsChart::class,
            \App\Filament\Widgets\MonthlyCostsChart::class,

            // الصف الثالث: تقارير تفاعلية
            // \App\Filament\Widgets\TopDeliveryMenWidget::class,
            // \App\Filament\Widgets\TopClientsWidget::class,
            // \App\Filament\Widgets\RecentShipmentsWidget::class,
            ])
            ->plugins([
                // \Statikbe\FilamentTranslationManager\FilamentChainedTranslationManagerPlugin::make(),

                // \TomatoPHP\FilamentLanguageSwitcher\FilamentLanguageSwitcherPlugin::make(),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
          ->font('Tajawal', provider: GoogleFontProvider::class);
    }
    
   
}
