@auth
    <script>window.aeShellConfig = @js([
        'home' => \App\Filament\Shell\Menu::path(\App\Filament\Pages\Workspace::getUrl()),
        'dashboard' => \App\Filament\Shell\Menu::path(\App\Filament\Pages\Dashboard::getUrl()),
        'login' => \App\Filament\Shell\Menu::path((string) filament()->getLoginUrl()),
        'paths' => \App\Filament\Shell\Menu::paths(),
    ]);</script>
    {{ \App\Filament\Shell\Assets::script('bridge') }}
@endauth
