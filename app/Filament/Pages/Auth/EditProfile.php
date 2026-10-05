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
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),
            $this->getCurrentPasswordFormComponent(),
        ]);
    }

    protected function afterSave(): void
    {
        // The new language shows from the next request on.
        $this->redirect(static::getUrl(), navigate: false);
    }
}
