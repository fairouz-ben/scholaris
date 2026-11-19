<?php

namespace Tests\Concerns;

use App\Enums\OccurrenceStatusEnum;
use App\Models\AcademicYear;
use App\Models\AcademicYearTerm;
use App\Models\Canvas;
use App\Models\CanvasModule;
use App\Models\CanvasTerm;
use App\Models\CanvasUnit;
use App\Models\Cycle;
use App\Models\Cycle_level;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Foundation;
use App\Models\Grade;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Section;
use App\Models\SessionCategory;
use App\Models\SessionOccurrence;
use App\Models\SessionType;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherStatus;
use App\Models\Term;
use App\Models\TimeSlot;
use App\Models\UnitEnsType;

trait CreatesTimetableFixtures
{
    private int $sequence = 0;

    private ?Department $department = null;

    private ?Grade $grade = null;

    private ?TeacherStatus $teacherStatus = null;

    /** @var array<string, mixed> */
    private array $context = [];

    /** @var array<string, Schedule> */
    private array $schedules = [];

    protected function makeTeacher(string $contract = 'permanent'): Teacher
    {
        $n = ++$this->sequence;

        $this->ensureOrganisation();

        return Teacher::create([
            'department_id'     => $this->department->id,
            'last_name_ar'      => "لقب{$n}",
            'first_name_ar'     => "اسم{$n}",
            'last_name_fr'      => "Nom{$n}",
            'first_name_fr'     => "Prenom{$n}",
            'birth_date'        => '1980-01-01',
            'email'             => "teacher{$n}@example.test",
            'password'          => 'secret-password',
            'teacher_status_id' => $this->teacherStatus->id,
            'grade_id'          => $this->grade->id,
            'contract_type'     => $contract,
        ]);
    }

    protected function makeStudent(): Student
    {
        $n = ++$this->sequence;

        return Student::create([
            'matricule'     => "MAT{$n}",
            'password'      => 'secret-password',
            'last_name_ar'  => "لقب{$n}",
            'first_name_ar' => "اسم{$n}",
            'last_name_fr'  => "Nom{$n}",
            'first_name_fr' => "Prenom{$n}",
            'birth_date'    => '2002-01-01',
            'status'        => 'active',
        ]);
    }

    /**
     * Shared academic chain (year, term, canvas, module, section, room, category), built once per test.
     *
     * @return array<string, mixed>
     */
    protected function timetableContext(): array
    {
        if ($this->context !== []) {
            return $this->context;
        }

        $this->ensureOrganisation();

        $year = AcademicYear::create([
            'label'      => '2026-2027',
            'start_date' => '2026-09-01',
            'end_date'   => '2027-06-30',
            'is_current' => true,
        ]);

        $term = Term::create([
            'term_type'   => 'semester',
            'name_ar'     => 'السداسي 1',
            'name_fr'     => 'Semestre 1',
            'term_number' => 1,
            'code'        => 'S1',
        ]);

        $yearTerm = AcademicYearTerm::create([
            'academic_year_id' => $year->id,
            'term_id'          => $term->id,
            'start_date'       => '2026-09-13',
            'end_date'         => '2026-12-31',
            'teaching_weeks'   => 12,
            'is_current'       => true,
        ]);

        $cycle = Cycle::create(['name_ar' => 'ماستر', 'name_fr' => 'Master', 'code' => 'M']);

        $level = Cycle_level::create([
            'cycle_id' => $cycle->id,
            'name_ar'  => 'ماستر 1',
            'name_fr'  => 'Master 1',
            'code_ar'  => 'M1',
            'code_fr'  => 'M1',
            'status'   => 'active',
        ]);

        $canvas = Canvas::create([
            'reference'      => 'CAN-TEST',
            'name_ar'        => 'تحليل وعلوم المعطيات',
            'name_fr'        => 'Analyse et Sciences de Données',
            'canvas_year'    => '2026-2027',
            'cycle_level_id' => $level->id,
        ]);

        $canvasTerm = CanvasTerm::create(['canvas_id' => $canvas->id, 'term_id' => $term->id]);

        $unitType = UnitEnsType::create([
            'name_ar' => 'وحدة أساسية',
            'name_fr' => 'UE fondamentales',
            'code_ar' => 'UEF',
            'code_fr' => 'UEF',
        ]);

        $unit = CanvasUnit::create([
            'canvas_term_id'   => $canvasTerm->id,
            'unit_ens_type_id' => $unitType->id,
            'code'             => 'UEF1.1',
            'position'         => 1,
            'coefficient'      => 3,
            'credits'          => 5,
        ]);

        $module = CanvasModule::create([
            'canvas_unit_id'     => $unit->id,
            'code'               => 'UEF1.1.1',
            'name_ar'            => 'احتمالات',
            'name_fr'            => 'Probabilités',
            'position'           => 1,
            'coefficient'        => 3,
            'credits'            => 5,
            'weekly_cm_minutes'  => 90,
            'continuous_percent' => 40,
            'exam_percent'       => 60,
        ]);

        return $this->context = [
            'year'     => $year,
            'yearTerm' => $yearTerm,
            'module'   => $module,
            'section'  => Section::create([
                'academic_year_id' => $year->id,
                'canvas_id'        => $canvas->id,
                'name'             => 'A',
            ]),
            'room' => Room::create([
                'department_id' => $this->department->id,
                'type'          => 'cm',
                'name'          => 'Amphi 1',
                'capacity'      => 200,
            ]),
            'category' => SessionCategory::create([
                'code'    => 'ord',
                'name_ar' => 'حصة عادية',
                'name_fr' => 'Ordinaire',
            ]),
        ];
    }

    /** Weekly schedule of a teacher for a session type code: cm (ratio 1), td (1.5) or tp (2), each in its own 90-minute slot. */
    protected function makeSchedule(Teacher $teacher, string $code = 'cm'): Schedule
    {
        $key = "{$teacher->id}:{$code}";

        if (isset($this->schedules[$key])) {
            return $this->schedules[$key];
        }

        $context = $this->timetableContext();

        [$ratio, $slotCode, $start, $end] = match ($code) {
            'cm'    => [1, 'S1', '08:00:00', '09:30:00'],
            'td'    => [1.5, 'S2', '09:30:00', '11:00:00'],
            'tp'    => [2, 'S3', '11:00:00', '12:30:00'],
        };

        $sessionType = SessionType::firstOrCreate(
            ['code' => $code],
            ['name_ar' => $code, 'name_fr' => $code, 'hours_per_cm_hour' => $ratio],
        );

        $slot = TimeSlot::firstOrCreate(
            ['code' => $slotCode],
            ['start_time' => $start, 'end_time' => $end, 'position' => (int) substr($slotCode, 1)],
        );

        return $this->schedules[$key] = Schedule::create([
            'academic_year_term_id' => $context['yearTerm']->id,
            'canvas_module_id'      => $context['module']->id,
            'section_id'            => $context['section']->id,
            'teacher_id'            => $teacher->id,
            'room_id'               => $context['room']->id,
            'session_type_id'       => $sessionType->id,
            'session_category_id'   => $context['category']->id,
            'day_of_week'           => 1,
            'time_slot_id'          => $slot->id,
            'week_pattern'          => 'every',
            'start_date'            => '2026-09-13',
        ]);
    }

    protected function makeOccurrence(
        ?Teacher $teacher = null,
        string $date = '2026-10-05',
        OccurrenceStatusEnum $status = OccurrenceStatusEnum::PLANNED,
        ?Schedule $schedule = null,
    ): SessionOccurrence {
        $teacher ??= $this->makeTeacher();
        $schedule ??= $this->makeSchedule($teacher);

        return SessionOccurrence::create([
            'schedule_id' => $schedule->id,
            'date'        => $date,
            'status'      => $status,
            'teacher_id'  => $teacher->id,
            'room_id'     => $schedule->room_id,
        ]);
    }

    private function ensureOrganisation(): void
    {
        $this->department ??= Department::create([
            'faculty_id' => Faculty::create([
                'foundation_id'    => Foundation::create(['name_ar' => 'جامعة', 'name_fr' => 'Université'])->id,
                'name_ar'          => 'كلية العلوم',
                'name_fr'          => 'Faculté des Sciences',
                'default_language' => 'fr',
            ])->id,
            'name_ar' => 'قسم',
            'name_fr' => 'Département',
        ]);

        $this->grade ??= Grade::create([
            'name_ar'          => 'أستاذ',
            'name_fr'          => 'Professeur',
            'code_ar'          => 'prof',
            'code_fr'          => 'prof',
            'priority'         => 10,
            'can_teach_cm'     => true,
            'priority_for_uef' => true,
        ]);

        $this->teacherStatus ??= TeacherStatus::create([
            'is_active' => true,
            'name_ar'   => 'نشط',
            'name_fr'   => 'Actif(ve)',
        ]);
    }
}
