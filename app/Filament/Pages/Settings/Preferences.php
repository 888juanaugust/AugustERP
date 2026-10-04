<?php

declare(strict_types=1);

namespace App\Filament\Pages\Settings;

use App\Domain\Access\MenuKey;
use App\Domain\Pengaturan\Preferensi;
use App\Domain\Pengaturan\PreferensiKey;
use App\Domain\Pengaturan\PreferensiTab;
use App\Domain\Pengaturan\PreferensiType;
use App\Domain\Shared\Format;
use App\Filament\Support\ErpPage;
use App\Models\GeneralLedger\Account;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * The Preferences screen: the reference system's ten tabs plus the Business
 * Rules tab, every field a PreferensiKey, saved through the audited store.
 */
class Preferences extends ErpPage
{
    protected string $view = 'filament.pages.settings.preferences';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public static function menuKey(): MenuKey
    {
        return MenuKey::Preferences;
    }

    public function mount(): void
    {
        $this->form->fill(self::toForm(app(Preferensi::class)->all()));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('preferences')
                    ->persistTabInQueryString()
                    ->tabs(array_map(fn (PreferensiTab $tab) => $this->tab($tab), PreferensiTab::cases())),
            ])
            ->statePath('data')
            ->disabled(! static::canUpdate());
    }

    public function save(): void
    {
        abort_unless(static::canUpdate(), 403);

        app(Preferensi::class)->setMany(self::fromForm($this->form->getState()));

        Notification::make()->title('Preferences saved')->success()->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save preferences')
                ->action('save')
                ->visible(static::canUpdate()),
        ];
    }

    private function tab(PreferensiTab $tab): Tab
    {
        $fields = array_map(fn (PreferensiKey $key) => $this->field($key), $tab->keys());

        return Tab::make($tab->value)
            ->label($tab->label())
            ->schema([
                Section::make()
                    ->schema($fields)
                    ->columns($tab === PreferensiTab::Features || $tab === PreferensiTab::Attachments ? 2 : 1),
            ]);
    }

    private function field(PreferensiKey $key): Component
    {
        $name = self::fieldName($key);
        $component = match ($key->type()) {
            PreferensiType::Bool => Toggle::make($name)->inline(),
            PreferensiType::Int => TextInput::make($name)->numeric()->minValue(0)->maxWidth('xs'),
            PreferensiType::Date => DatePicker::make($name)->native(false)->displayFormat(Format::DATE_INPUT),
            PreferensiType::Time => TimePicker::make($name)->seconds(false),
            PreferensiType::Select => Select::make($name)->options($key->options())->native(false)->selectablePlaceholder(false),
            PreferensiType::Account => Select::make($name)->options(fn () => Account::options())->searchable()->native(false),
            PreferensiType::TextList => Repeater::make($name)
                ->simple(TextInput::make('label')->maxLength(60))
                ->addable(false)
                ->deletable(false)
                ->reorderable(false)
                ->columns(5),
            PreferensiType::Text => $key === PreferensiKey::CompanyAddress
                ? Textarea::make($name)->rows(3)
                : TextInput::make($name)->maxLength(200),
        };

        $component->label($key->label());
        if ($key->help()) {
            $component->helperText($key->help());
        }

        return match ($key) {
            PreferensiKey::AccessFrom, PreferensiKey::AccessUntil => $component->visible(fn (Get $get) => $get(self::fieldName(PreferensiKey::AccessRestriction)) === 'time_window'),
            PreferensiKey::ReturnCostAccount => $component->visible(fn (Get $get) => $get(self::fieldName(PreferensiKey::ReturnCostCharge)) === 'account'),
            PreferensiKey::LastPriceCutoffDate => $component->visible(fn (Get $get) => (bool) $get(self::fieldName(PreferensiKey::LastPriceUpdatedByBill))),
            default => $component,
        };
    }

    /** Form field names cannot hold dots, so "company.name" travels as "company__name". */
    public static function fieldName(PreferensiKey $key): string
    {
        return str_replace('.', '__', $key->value);
    }

    /** @param  array<string, mixed>  $values */
    public static function toForm(array $values): array
    {
        $out = [];
        foreach ($values as $key => $value) {
            $out[str_replace('.', '__', $key)] = $value;
        }

        return $out;
    }

    /** @param  array<string, mixed>  $state */
    public static function fromForm(array $state): array
    {
        $out = [];
        foreach ($state as $name => $value) {
            $out[str_replace('__', '.', $name)] = $value;
        }

        return $out;
    }
}
