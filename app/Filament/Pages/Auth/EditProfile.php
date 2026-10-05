<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use App\Domain\Shared\Locales;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

/** The user's own profile: name, email, password, and the language of their screens. */
class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            $this->getNameFormComponent(),
            $this->getEmailFormComponent(),
            Select::make('locale')->label(__('Language'))
                ->options(fn () => Locales::names())
                ->placeholder(fn () => __('As the company: :language', ['language' => Locales::names()[Locales::companyDefault()] ?? Locales::companyDefault()]))
                ->native(false),
            $this->getPasswordFormComponent()
                ->required(fn (): bool => (bool) $this->getUser()->getAttribute('password_change_required'))
                ->helperText(fn (): ?string => $this->getUser()->getAttribute('password_change_required') ? __('Choose your own password before anything else.') : null),
            $this->getPasswordConfirmationFormComponent(),
            $this->getCurrentPasswordFormComponent(),
        ]);
    }

    protected function afterSave(): void
    {
        $user = $this->getUser();
        if ($user->wasChanged('password') && $user->getAttribute('password_change_required')) {
            $user->forceFill(['password_change_required' => false])->save();
        }
        // The new language shows from the next request on.
        $this->redirect(static::getUrl(), navigate: false);
    }
}
