<?php

declare(strict_types=1);

namespace App\Domain\Shared\Enums;

use Filament\Support\Contracts\HasLabel;

/** The employee status the income-tax return classifies by. */
enum WorkStatus: string implements HasLabel
{
    case Permanent = 'permanent';
    case Temporary = 'temporary';
    case NonEmployeeMlm = 'non_employee_mlm';
    case NonEmployeeInsuranceAgent = 'non_employee_insurance_agent';
    case NonEmployeeHawker = 'non_employee_hawker';
    case NonEmployeeExpert = 'non_employee_expert';
    case Commissioner = 'commissioner';
    case NonEmployeeContinuous = 'non_employee_continuous';
    case NonEmployeeOneOff = 'non_employee_one_off';

    public function getLabel(): string
    {
        return match ($this) {
            self::Permanent => 'Permanent employee',
            self::Temporary => 'Temporary employee',
            self::NonEmployeeMlm => 'Non-employee: MLM distributor',
            self::NonEmployeeInsuranceAgent => 'Non-employee: insurance field agent',
            self::NonEmployeeHawker => 'Non-employee: door-to-door seller',
            self::NonEmployeeExpert => 'Non-employee: expert',
            self::Commissioner => 'Board of commissioners / supervisory board',
            self::NonEmployeeContinuous => 'Non-employee with continuous remuneration',
            self::NonEmployeeOneOff => 'Non-employee with one-off remuneration',
        };
    }
}
