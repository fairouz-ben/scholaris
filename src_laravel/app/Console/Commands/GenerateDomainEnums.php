<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateDomainEnums extends Command
{
    protected $signature = 'enums:generate {--force : Overwrite existing files}';

    protected $description = 'Generate all domain enums with the HasEnumHelpers trait';

    /**
     * Enum name => list of string case values.
     */
    protected array $enums = [
        'LanguageEnum'          => ['ar', 'fr'],
        'EntityStatusEnum'      => ['active', 'disabled', 'archived'],
        'GenderEnum'            => ['M', 'F'],
        'CivilityEnum'          => ['M', 'MME'],
        'MaritalStatusEnum'     => ['married', 'single', 'widowed', 'divorced'],
        'ContractTypeEnum'      => ['permanent', 'vacataire'],
        'RoomTypeEnum'          => ['cm', 'td', 'tp', 'amphi', 'labo', 'salle'],
        'TermTypeEnum'          => ['semester', 'trimester', 'quadrimester'],
        'HolidayCategoryEnum'   => ['academic', 'national', 'religious', 'exception'],
        'CanvasKindEnum'        => ['base', 'amendment'],
        'WeekPatternEnum'       => ['every', 'odd', 'even'],
        'OccurrenceStatusEnum'  => ['planned', 'held', 'cancelled'],
        'RequestTypeEnum'       => ['absence', 'replacement', 'makeup', 'supplementary'],
        'RequestStatusEnum'     => ['pending', 'validated', 'rejected', 'cancelled'],
        'AttendanceStatusEnum'  => ['present', 'absent', 'justified'],
        'StudentStatusEnum'     => ['active', 'suspended', 'graduated', 'dropped'],
        'EnrollmentStatusEnum'  => ['enrolled', 'withdrawn', 'completed'],
        'ExamTypeEnum'          => ['normal', 'resit'],
        'ExamRoleEnum'          => ['head', 'supervisor'],
        'JuryRoleEnum'          => ['president', 'examiner', 'supervisor', 'co_supervisor'],
        'CalendarEventTypeEnum' => ['teaching', 'exam', 'resit', 'defense'],
        'ContractStatusEnum'    => ['draft', 'signed', 'terminated'],
        'StatementStatusEnum'   => ['draft', 'final'],
        'WorkloadPeriodEnum'    => ['week', 'month', 'term', 'year'],
        'WorkloadStatusEnum'    => ['normal', 'overload', 'critical'],
        'DocumentTypeEnum'      => [
            'canvas',
            'additional_lessons_license',
            'vacation_list',
            'supplementary_lessons_form',
            'pedagogical_resource_sheet',
            'employment_contract',
        ],
    ];

    public function handle(): int
    {
        $dir = app_path('Enums');
        File::ensureDirectoryExists($dir);
        File::ensureDirectoryExists($dir . '/Concerns');

        $this->writeTrait($dir . '/Concerns/HasEnumHelpers.php');

        $created = $skipped = 0;

        foreach ($this->enums as $name => $values) {
            $path = "{$dir}/{$name}.php";

            if (File::exists($path) && ! $this->option('force')) {
                $this->warn("↷ Skipped {$name} (already exists, use --force to overwrite)");
                $skipped++;
                continue;
            }

            File::put($path, $this->buildEnum($name, $values));
            $this->info("✔ Created {$name}");
            $created++;
        }

        $this->newLine();
        $this->info("Done. Created: {$created}, Skipped: {$skipped}");
        $this->comment('Delete this command any time after running it once.');

        return self::SUCCESS;
    }

    protected function buildEnum(string $name, array $values): string
    {
        $cases = $this->renderCases($values);

        return <<<PHP
        <?php

        namespace App\Enums;

        use App\Enums\Concerns\HasEnumHelpers;
        use Illuminate\Contracts\Support\HasLabel;

        enum {$name}: string implements HasLabel
        {
            use HasEnumHelpers;

        {$cases}
        }

        PHP;
    }

    protected function renderCases(array $values): string
    {
        $lines = [];

        foreach ($values as $value) {
            $lines[] = "    case {$this->toCaseName($value)} = '{$value}';";
        }

        return implode("\n", $lines);
    }

    protected function toCaseName(string $value): string
    {
        return Str::upper(Str::replace('-', '_', $value));
    }

    protected function writeTrait(string $path): void
    {
        if (File::exists($path) && ! $this->option('force')) {
            return;
        }

        File::put($path, <<<'PHP'
        <?php

        namespace App\Enums\Concerns;

        use Illuminate\Support\Str;

        trait HasEnumHelpers
        {
            public static function translationKey(): string
            {
                return Str::of(class_basename(static::class))
                    ->replaceLast('Enum', '')
                    ->snake()
                    ->toString();
            }

            public function label(): string
            {
                $key = 'enums.' . static::translationKey() . '.' . $this->value;

                return trans()->has($key) ? __($key) : $this->value;
            }

            public static function values(): array
            {
                return array_column(self::cases(), 'value');
            }

            public static function options(): array
            {
                return collect(self::cases())
                    ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
                    ->all();
            }
        }
        PHP);
    }
}