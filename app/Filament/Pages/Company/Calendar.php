<?php

declare(strict_types=1);

namespace App\Filament\Pages\Company;

use App\Domain\Access\MenuKey;
use App\Domain\Company\CalendarFeed;
use App\Domain\Shared\Format;
use App\Filament\Support\ErpPage;
use App\Models\Company\CalendarEvent;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

/**
 * Calendar: the month with invoices falling due, giros maturing, recurring
 * transactions scheduled, the month's end and the company's own notes.
 */
class Calendar extends ErpPage
{
    protected string $view = 'filament.pages.company.calendar';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    public int $year;

    public int $month;

    public static function menuKey(): MenuKey
    {
        return MenuKey::Calendar;
    }

    public function mount(): void
    {
        $year = request()->integer('year');
        $month = request()->integer('month');
        $this->year = $year >= 1900 && $year <= 2200 ? $year : today()->year;
        $this->month = $month >= 1 && $month <= 12 ? $month : today()->month;
    }

    public function previousMonth(): void
    {
        $this->moveTo($this->firstOfMonth()->subMonth());
    }

    public function nextMonth(): void
    {
        $this->moveTo($this->firstOfMonth()->addMonth());
    }

    public function today(): void
    {
        $this->moveTo(CarbonImmutable::today());
    }

    private function moveTo(CarbonImmutable $date): void
    {
        $this->year = $date->year;
        $this->month = $date->month;
    }

    public function firstOfMonth(): CarbonImmutable
    {
        return CarbonImmutable::create($this->year, $this->month, 1);
    }

    /** @return array<string, list<array{kind: string, title: string, url: ?string}>> date → events */
    public function events(): array
    {
        return CalendarFeed::month($this->year, $this->month);
    }

    /** @return list<list<CarbonImmutable>> the month's weeks, Monday to Sunday, padded with the neighbouring months' days */
    public function weeks(): array
    {
        $first = $this->firstOfMonth();
        $day = $first->startOfWeek(CarbonImmutable::MONDAY);
        $last = $first->endOfMonth()->endOfWeek(CarbonImmutable::SUNDAY);
        $weeks = [];
        while ($day->lte($last)) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $week[] = $day;
                $day = $day->addDay();
            }
            $weeks[] = $week;
        }

        return $weeks;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addNote')
                ->label(__('New note'))
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->visible(fn (): bool => static::canUpdate())
                ->schema([
                    TextInput::make('title')->label(__('Title'))->required()->maxLength(200),
                    DatePicker::make('starts_on')->label(__('Date'))->required()->native(false)->displayFormat(Format::DATE_INPUT)->default(today()),
                    Textarea::make('notes')->label(__('Notes'))->rows(3),
                ])
                ->action(function (array $data): void {
                    CalendarEvent::query()->create([
                        'title' => $data['title'],
                        'starts_on' => $data['starts_on'],
                        'notes' => $data['notes'] ?? null,
                        'created_by' => auth()->id(),
                    ]);
                    $this->moveTo(CarbonImmutable::parse($data['starts_on']));
                    Notification::make()->title(__('Note added'))->success()->send();
                }),
            Action::make('today')->label(__('Today'))->color('gray')->action(fn () => $this->today()),
            Action::make('previous')->label(__('Previous month'))->icon('heroicon-m-chevron-left')->color('gray')->iconButton()->action(fn () => $this->previousMonth()),
            Action::make('next')->label(__('Next month'))->icon('heroicon-m-chevron-right')->color('gray')->iconButton()->action(fn () => $this->nextMonth()),
        ];
    }
}
