<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrolment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin 
        $admin = User::factory()->create([
            'name'  => 'Admin User',
            'email' => 'admin@studentms.test',
            'role'  => 'admin',
        ]);

        // Instructors
        $instructors = collect([
            ['name' => 'Prof. Itumeleng Maine',  'email' => 'i.maine@studentms.test'],
            ['name' => 'Dr. Sarah Okonkwo',      'email' => 's.okonkwo@studentms.test'],
            ['name' => 'Mr. David van Wyk',      'email' => 'd.vanwyk@studentms.test'],
        ])->map(fn ($data) => User::factory()->create([
            ...$data,
            'role'     => 'instructor',
            'password' => Hash::make('password'),
        ]));

        // Students 
        $students = User::factory(20)->create(['role' => 'student']);

        // Courses
        $courses = collect([
            ['code' => 'ICE360S', 'name' => 'Web Frameworks',              'credits' => 15, 'instructor_id' => $instructors[0]->id],
            ['code' => 'ICE250S', 'name' => 'Database Systems',            'credits' => 15, 'instructor_id' => $instructors[0]->id],
            ['code' => 'ICE120S', 'name' => 'Introduction to Programming', 'credits' => 15, 'instructor_id' => $instructors[1]->id],
            ['code' => 'MAT201S', 'name' => 'Applied Mathematics',         'credits' => 12, 'instructor_id' => $instructors[1]->id],
            ['code' => 'NET310S', 'name' => 'Network Administration',      'credits' => 15, 'instructor_id' => $instructors[2]->id],
            ['code' => 'SEC400S', 'name' => 'Cybersecurity Fundamentals',  'credits' => 12, 'instructor_id' => $instructors[2]->id],
        ])->map(fn ($data) => Course::create([
            ...$data,
            'description'  => "A comprehensive course covering " . strtolower($data['name']) . " principles.",
            'max_capacity' => rand(25, 40),
            'status'       => 'active',
        ]));

        // Enrolments with realistic marks
        $statuses = ['approved', 'approved', 'approved', 'pending', 'rejected'];

        $students->each(function (User $student) use ($courses, $statuses) {
            // Each student enrols in 1–3 random courses
            $courses->random(rand(1, 3))->each(function (Course $course) use ($student, $statuses) {
                $status = $statuses[array_rand($statuses)];
                $mark   = $status === 'approved' ? round(rand(35, 98) + rand(0, 9) / 10, 1) : null;

                Enrolment::create([
                    'student_id'   => $student->id,
                    'course_id'    => $course->id,
                    'status'       => $status,
                    'mark'         => $mark,
                    'enrolled_at'  => now()->subDays(rand(1, 90))->toDateString(),
                    'completed_at' => $mark !== null ? now()->subDays(rand(0, 30))->toDateString() : null,
                ]);
            });
        });

        $this->command->info('✅ Seeding complete. Login: admin@studentms.test / password');
    }
}
