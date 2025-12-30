<?php

require 'vendor/autoload.php';

try {
    // Load configuration
    $config = require 'config/autoload/local.php';
    if (!isset($config['db'])) {
        throw new Exception("Database configuration not found in config/autoload/local.php");
    }

    $dbConfig = $config['db'];
    $dsn = $dbConfig['dsn'];
    $username = $dbConfig['username'];
    $password = $dbConfig['password'];

    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to database.\n";

    // 0. Ensure Schema Exists
    echo "Initializing Schema...\n";
    $sqlSchema = file_get_contents('data/schema.sql');
    if ($sqlSchema) {
        // Split by semicolon and execute each statement
        $statements = array_filter(array_map('trim', explode(';', $sqlSchema)));
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    // Ignore "table already exists" errors roughly, or just let IF NOT EXISTS handle it
                    // The schema.sql uses IF NOT EXISTS, so simple exec is fine.
                    // However, we catch generic errors just in case.
                     if (strpos($e->getMessage(), 'already exists') === false) {
                        // Re-throw if it's not an "already exists" error (though IF NOT EXISTS prevents this usually)
                        // Actually, IF NOT EXISTS suppresses the error on the DB side, so exec() shouldn't fail.
                        throw $e;
                     }
                }
            }
        }
        echo "Schema verified.\n";
    }

    // 1. Seed Users
    echo "Seeding Users...\n";
    $users = [
        [
            'email' => 'admin@mkgo.cl',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'full_name' => 'Administrador Arica',
            'role' => 'admin',
            'google_id' => null
        ],
        [
            'email' => 'conductor1@mkgo.cl',
            'password' => password_hash('conductor123', PASSWORD_DEFAULT),
            'full_name' => 'Juan Pérez (Conductor)',
            'role' => 'driver',
            'google_id' => null
        ],
        [
            'email' => 'pasajero1@mkgo.cl',
            'password' => password_hash('pasajero123', PASSWORD_DEFAULT),
            'full_name' => 'María González (Pasajero)',
            'role' => 'user',
            'google_id' => null
        ]
    ];

    $stmtUser = $pdo->prepare("INSERT INTO users (email, password, full_name, role, google_id) VALUES (:email, :password, :full_name, :role, :google_id)");

    foreach ($users as $user) {
        // Check if user exists to avoid duplicates
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$user['email']]);
        if (!$check->fetch()) {
            $stmtUser->execute($user);
            echo " - Created user: {$user['email']}\n";
        } else {
            echo " - Skipped existing user: {$user['email']}\n";
        }
    }

    // Get Driver ID for trips
    $stmtGetDriver = $pdo->prepare("SELECT id FROM users WHERE email = 'conductor1@mkgo.cl'");
    $stmtGetDriver->execute();
    $driverId = $stmtGetDriver->fetchColumn();

    // 2. Seed Vehicles
    echo "Seeding Vehicles...\n";
    $vehicles = [
        [
            'plate' => 'JJ-KL-99',
            'brand' => 'Toyota',
            'model' => 'Yaris',
            'year' => 2020,
            'status' => 'active'
        ],
        [
            'plate' => 'BB-CC-12',
            'brand' => 'Hyundai',
            'model' => 'Accent',
            'year' => 2019,
            'status' => 'active'
        ],
        [
            'plate' => 'DD-EE-34',
            'brand' => 'Kia',
            'model' => 'Rio 5',
            'year' => 2022,
            'status' => 'maintenance'
        ]
    ];

    $stmtVehicle = $pdo->prepare("INSERT INTO vehicles (plate, brand, model, year, status) VALUES (:plate, :brand, :model, :year, :status)");

    foreach ($vehicles as $vehicle) {
        $check = $pdo->prepare("SELECT id FROM vehicles WHERE plate = ?");
        $check->execute([$vehicle['plate']]);
        if (!$check->fetch()) {
            $stmtVehicle->execute($vehicle);
            echo " - Created vehicle: {$vehicle['plate']}\n";
        } else {
            echo " - Skipped existing vehicle: {$vehicle['plate']}\n";
        }
    }

    // Get Vehicle ID for trips
    $stmtGetVehicle = $pdo->prepare("SELECT id FROM vehicles WHERE plate = 'JJ-KL-99'");
    $stmtGetVehicle->execute();
    $vehicleId = $stmtGetVehicle->fetchColumn();

    // 3. Seed Trips (Arica Locations)
    echo "Seeding Trips (Arica)...\n";
    $trips = [
        [
            'vehicle_id' => $vehicleId,
            'driver_id' => $driverId,
            'start_location' => 'Terminal Asoagro, Arica (-18.4907, -70.2965)',
            'end_location' => 'Mall Plaza Arica (-18.4688, -70.3060)',
            'start_time' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'end_time' => date('Y-m-d H:i:s', strtotime('-1 day +30 minutes')),
            'status' => 'completed'
        ],
        [
            'vehicle_id' => $vehicleId,
            'driver_id' => $driverId,
            'start_location' => 'Plaza Colón, Arica (-18.4802, -70.3150)',
            'end_location' => 'Playa Chinchorro, Arica (-18.4550, -70.2900)',
            'start_time' => date('Y-m-d H:i:s', strtotime('+2 hours')),
            'end_time' => null,
            'status' => 'scheduled'
        ],
        [
            'vehicle_id' => $vehicleId,
            'driver_id' => $driverId,
            'start_location' => 'Aeropuerto Chacalluta (-18.3475, -70.3392)',
            'end_location' => 'Hotel Diego de Almagro, Arica (-18.4650, -70.3050)',
            'start_time' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'end_time' => null,
            'status' => 'scheduled'
        ]
    ];

    $stmtTrip = $pdo->prepare("INSERT INTO trips (vehicle_id, driver_id, start_location, end_location, start_time, end_time, status) VALUES (:vehicle_id, :driver_id, :start_location, :end_location, :start_time, :end_time, :status)");

    if ($vehicleId && $driverId) {
        foreach ($trips as $trip) {
            // Simple check to avoid spamming duplicates on re-run (optional, simple logic here)
            $check = $pdo->prepare("SELECT id FROM trips WHERE start_location = ? AND start_time = ?");
            $check->execute([$trip['start_location'], $trip['start_time']]);
            if (!$check->fetch()) {
                $stmtTrip->execute($trip);
                echo " - Created trip from: {$trip['start_location']}\n";
            } else {
                 echo " - Skipped existing trip from: {$trip['start_location']}\n";
            }
        }
    } else {
        echo "Error: Could not find created vehicle or driver to assign trips.\n";
    }

    echo "Seeding completed successfully.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
