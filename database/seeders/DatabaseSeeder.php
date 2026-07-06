<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\RegisteredUser;
use App\Models\GatePoint;
use App\Models\Vehicle;
use App\Models\QrCode;
use App\Models\AccessLog;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin + officer login accounts
        User::firstOrCreate(
            ['email' => 'admin@vams.test'],
            ['name' => 'Admin User', 'password' => bcrypt('password'), 'role' => 'admin']
        );

        $officerNames = ['Ibrahim Musa', 'Chidinma Okeke', 'Adebayo Fashola', 'Grace Umeh'];
        $officers = collect($officerNames)->map(function ($name, $i) {
            return User::firstOrCreate(
                ['email' => 'officer' . ($i + 1) . '@vams.test'],
                ['name' => $name, 'password' => bcrypt('password'), 'role' => 'officer']
            );
        });

        // Gate points (section 3.6.4 ERD entity)
        $gates = collect([
    ['gate_name' => 'Gidan Kwano Main Gate', 'location' => 'Gidan Kwano Campus, Minna–Bida Road', 'status' => 'active'],
    ['gate_name' => 'Bosso Campus Gate', 'location' => 'Bosso Campus, Minna', 'status' => 'active'],
    ['gate_name' => 'Staff Quarters Gate', 'location' => 'FUT Minna Staff Quarters', 'status' => 'active'],
])->map(fn ($g) => GatePoint::create($g));

        // Registered users — the doc's "Users" entity (students/staff/visitors)
        $registeredUsers = RegisteredUser::factory()->count(15)->create();

        // Vehicles, each assigned to a random registered user, some users get 2 vehicles
        $vehicles = collect();
        foreach ($registeredUsers as $user) {
            $vehicleCount = fake()->boolean(25) ? 2 : 1; // ~25% of users own 2 vehicles
            $vehicles = $vehicles->merge(
                Vehicle::factory()->count($vehicleCount)->create(['registered_user_id' => $user->id])
            );
        }

        // Issue a QR code for every vehicle. Visitors get an expiring pass; students/staff get permanent.
        foreach ($vehicles as $vehicle) {
            $vehicle->load('registeredUser');
            $expiry = $vehicle->registeredUser->user_category === 'visitor'
                ? now()->addDays(rand(1, 14))
                : null;

            QrCode::issueFor($vehicle, $expiry);
        }

        // Revoke and reissue a few QR codes, to populate "Recently Revoked" in Reports
        $vehicles->random(3)->each(function ($vehicle) {
            $vehicle->activeQrCode?->update(['status' => 'revoked']);
            QrCode::issueFor($vehicle);
        });

        // Access logs across the past 14 days
        foreach ($vehicles as $vehicle) {
            $qr = $vehicle->activeQrCode;
            if (!$qr) {
                continue;
            }

            $scanCount = rand(2, 8);
            $direction = 'in';

            for ($i = 0; $i < $scanCount; $i++) {
                $scanTime = now()->subDays(rand(0, 13))->setTime(rand(6, 20), rand(0, 59));

                // Occasionally simulate a denial (expired/revoked check happening naturally, plus some random denials)
                $isDenied = fake()->boolean(8);

                AccessLog::create([
                    'qr_code_id'     => $qr->id,
                    'gate_point_id'  => $gates->random()->id,
                    'operator_id'    => $officers->random()->id,
                    'scan_timestamp' => $scanTime,
                    'access_decision'=> $isDenied ? 'denied' : 'granted',
                    'direction'      => $isDenied ? null : $direction,
                    'denial_reason'  => $isDenied ? fake()->randomElement(['QR code has expired.', 'QR code has been revoked.', 'Vehicle flagged for review.']) : null,
                    'created_at'     => $scanTime,
                    'updated_at'     => $scanTime,
                ]);

                if (!$isDenied) {
                    $direction = $direction === 'in' ? 'out' : 'in';
                }
            }
        }

        $this->command->info('Seeded: 1 admin, ' . $officers->count() . ' officers, ' . $gates->count() . ' gates, ' . $registeredUsers->count() . ' registered users, ' . $vehicles->count() . ' vehicles, and access log history.');
    }
}
