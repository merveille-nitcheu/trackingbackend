<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'Dashboard']);
        Permission::create(['name' => 'Historique des parcours']);
        Permission::create(['name' => 'CRUD des sites']);
        Permission::create(['name' => 'CRUD des capteurs']);
        Permission::create(['name' => 'Geolocalisation des sites']);
        Permission::create(['name' => 'Consultation bete par site']);
        Permission::create(['name' => 'CRUD utilisateur']);
        Permission::create(['name' => 'Alertes']);
        Permission::create(['name' => 'Notifications']);
        Permission::create(['name' => 'Profil utilisateur']);
        Permission::create(['name' => 'Deconnexion']);

        // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'Responsable de site']);
        $role1->givePermissionTo('Dashboard');
        $role1->givePermissionTo('Historique des parcours');
        $role1->givePermissionTo('Alertes');
        $role1->givePermissionTo('Notifications');
        $role1->givePermissionTo('Profil utilisateur');
        $role1->givePermissionTo('Deconnexion');

        $role2 = Role::create(['name' => 'Responsable general']);
        $role2->givePermissionTo('Dashboard');
        $role2->givePermissionTo('Historique des parcours');
        $role2->givePermissionTo('CRUD des sites');
        $role2->givePermissionTo('CRUD des capteurs');
        $role2->givePermissionTo('Geolocalisation des sites');
        $role2->givePermissionTo('Consultation bete par site');
        $role2->givePermissionTo('CRUD utilisateur');
        $role2->givePermissionTo('Alertes');
        $role2->givePermissionTo('Notifications');
        $role2->givePermissionTo('Profil utilisateur');
        $role2->givePermissionTo('Deconnexion');

        $role3 = Role::create(['name' => 'Super-Admin']);
        // gets all permissions via Gate::before rule; see AuthServiceProvider

        // create company
        $company1 = \App\Models\Compagny::create([
            'name' => 'Light Group',
            'email' => 'lightgroup@gmail.com',
            'address' => 'Akwa Douala',
            'website_link' => 'https://lightgroup.com',
            'contact' => '+237 656 551 787'
        ]);

        $company2 = \App\Models\Compagny::create([
            'name' => 'Light Group2',
            'email' => 'lightgroup2@gmail.com',
            'address' => 'Akwa2 Douala',
            'website_link' => 'https://lightgroup2.com',
            'contact' => '+237 656 552 787'
        ]);

        // create sites
        $site1 = \App\Models\Site::create([
            "name" => 'Site 1',
            "description" => 'Paturages 500 boeufs',
            "address" => 'address 1',
            "radius" => 32000,
            "longitude" => 13.3,
            "latitude" => 9.40,
            "gmt" => 1,
            "compagny_id" => $company1->id
        ]);

        $site2 = \App\Models\Site::create([
            "name" => 'Site 2',
            "description" => 'Paturages 1500 boeufs',
            "address" => 'address 2',
            "radius" => 25000,
            "longitude" => 10.16,
            "latitude" => 5.96,
            "gmt" => 1,
            "compagny_id" => $company1->id
        ]);

        $site3 = \App\Models\Site::create([
            "name" => 'Site 3',
            "description" => 'Paturages 325 boeufs',
            "address" => 'address 3',
            "radius" => 18000,
            "longitude" => 13.3,
            "latitude" => 14.46,
            "gmt" => 1,
            "compagny_id" => $company1->id
        ]);

        $site4 = \App\Models\Site::create([
            "name" => 'Site 1',
            "description" => 'Paturages 55 boeufs',
            "address" => 'address 1',
            "radius" => 15000,
            "longitude" => 9.7,
            "latitude" => 4.0,
            "gmt" => 1,
            "compagny_id" => $company2->id
        ]);

        $site5 = \App\Models\Site::create([
            "name" => 'Site 2',
            "description" => 'Paturages 172 boeufs',
            "address" => 'address 2',
            "radius" => 25000,
            "longitude" => 11.15,
            "latitude" => 3.8,
            "gmt" => 1,
            "compagny_id" => $company2->id
        ]);

        // create users
        $user1 = \App\Models\User::create([
            'name' => 'Yann ALIM 1',
            'email' => 'yannalim26@gmail.com',
            'password' => 'password',
            'address' => 'Bp cite Douala',
            'contact' => '+237 656 897 565',
            'compagny_id' => $company1->id
        ]);
        $user1->assignRole($role1);

        $user2 = \App\Models\User::create([
            'name' => 'Yann ALIM 2',
            'email' => 'yannalim27@gmail.com',
            'password' => 'password',
            'address' => 'Bp cite Douala',
            'contact' => '+237 656 897 565',
            'compagny_id' => $company2->id
        ]);
        $user2->assignRole($role1);

        $user3 = \App\Models\User::create([
            'name' => 'Yann ALIM',
            'email' => 'yannalim25@gmail.com',
            'password' => 'password',
            'address' => 'Bp cite Douala',
            'contact' => '+237 656 897 565',
            'compagny_id' => $company1->id
        ]);
        $user3->assignRole($role2);

        $user4 = \App\Models\User::create([
            'name' => 'Yves-Bertrand SOLANGA',
            'email' => 'yvesbertrand@gmail.com',
            'password' => 'password',
            'address' => 'Bp cite Douala',
            'contact' => '+237 656 897 565',
            'compagny_id' => $company1->id
        ]);
        $user4->assignRole($role3);

        //create user site
        $userSite1 = \App\Models\UserSite::create([
            "user_id" => $user1->id,
            "site_id" => $site1->id
        ]);

        $userSite2 = \App\Models\UserSite::create([
            "user_id" => $user2->id,
            "site_id" => $site5->id
        ]);

        //create sensors
        $sensor1 = \App\Models\Sensor::create([
            "sensor_reference" => "sensor 1",
            "description" => "Description sensor 1",
            "site_id" => $site1->id
        ]);

        $rs1 = \App\Models\SensorRecord::create([
            "sensor_id" => $sensor1->id,
            "longitude" => 13.3,
            "latitude" => 9.46,
            "temperature" => 28,
            "battery" => 75
        ]);

        $sensor2 = \App\Models\Sensor::create([
            "sensor_reference" => "sensor ",
            "description" => "Description sensor 2",
            "site_id" => $site1->id
        ]);

        $rs2 = \App\Models\SensorRecord::create([
            "sensor_id" => $sensor2->id,
            "longitude" => 13.0,
            "latitude" => 9.46,
            "temperature" => 28,
            "battery" => 35
        ]);

        $sensor3 = \App\Models\Sensor::create([
            "sensor_reference" => "sensor 3",
            "description" => "Description sensor 3",
            "site_id" => $site1->id
        ]);

        $rs3 = \App\Models\SensorRecord::create([
            "sensor_id" => $sensor3->id,
            "longitude" => 13.3,
            "latitude" => 9.16,
            "temperature" => 28,
            "battery" => 15
        ]);

        $sensor4 = \App\Models\Sensor::create([
            "sensor_reference" => "sensor 4",
            "description" => "Description sensor 4",
            "site_id" => $site1->id
        ]);

        $rs4 = \App\Models\SensorRecord::create([
            "sensor_id" => $sensor4->id,
            "longitude" => 13.1,
            "latitude" => 9.06,
            "temperature" => 28,
            "battery" => 75
        ]);

        $sensor5 = \App\Models\Sensor::create([
            "sensor_reference" => "sensor 5",
            "description" => "Description sensor 5",
            "site_id" => $site1->id
        ]);

        $rs5 = \App\Models\SensorRecord::create([
            "sensor_id" => $sensor5->id,
            "longitude" => 12.8,
            "latitude" => 9.30,
            "temperature" => 28,
            "battery" => 75
        ]);

        $sensor6 = \App\Models\Sensor::create([
            "sensor_reference" => "sensor 6",
            "description" => "Description sensor 6",
            "site_id" => $site2->id
        ]);

        $rs6 = \App\Models\SensorRecord::create([
            "sensor_id" => $sensor6->id,
            "longitude" => 10.16,
            "latitude" => 5.96,
            "temperature" => 28,
            "battery" => 75
        ]);

        $sensor7 = \App\Models\Sensor::create([
            "sensor_reference" => "sensor 7",
            "description" => "Description sensor 7",
            "site_id" => $site2->id
        ]);

        $rs7 = \App\Models\SensorRecord::create([
            "sensor_id" => $sensor7->id,
            "longitude" => 10.16,
            "latitude" => 5.56,
            "temperature" => 28,
            "battery" => 75
        ]);

        $sensor8 = \App\Models\Sensor::create([
            "sensor_reference" => "sensor 8",
            "description" => "Description sensor 8",
            "site_id" => $site3->id
        ]);

        $rs8 = \App\Models\SensorRecord::create([
            "sensor_id" => $sensor8->id,
            "longitude" => 13.3,
            "latitude" => 14.46,
            "temperature" => 28,
            "battery" => 45
        ]);

        $sensor9 = \App\Models\Sensor::create([
            "sensor_reference" => "sensor 9",
            "description" => "Description sensor 9",
            "site_id" => $site3->id
        ]);

        $rs9 = \App\Models\SensorRecord::create([
            "sensor_id" => $sensor9->id,
            "longitude" => 13.0,
            "latitude" => 14.1,
            "temperature" => 28,
            "battery" => 45
        ]);

        $sensor10 = \App\Models\Sensor::create([
            "sensor_reference" => "sensor 10",
            "description" => "Description sensor 10",
            "site_id" => $site4->id
        ]);

        $rs10 = \App\Models\SensorRecord::create([
            "sensor_id" => $sensor10->id,
            "longitude" => 9.4,
            "latitude" => 4.0,
            "temperature" => 28,
            "battery" => 45
        ]);

        $sensor11 = \App\Models\Sensor::create([
            "sensor_reference" => "sensor 11",
            "description" => "Description sensor 11",
            "site_id" => $site4->id
        ]);

        $rs11 = \App\Models\SensorRecord::create([
            "sensor_id" => $sensor11->id,
            "longitude" => 9.7,
            "latitude" => 3.8,
            "temperature" => 28,
            "battery" => 05
        ]);

        $sensor12 = \App\Models\Sensor::create([
            "sensor_reference" => "sensor 12",
            "description" => "Description sensor 12",
            "site_id" => $site5->id
        ]);

        $rs12 = \App\Models\SensorRecord::create([
            "sensor_id" => $sensor12->id,
            "longitude" => 11.15,
            "latitude" => 3.8,
            "temperature" => 28,
            "battery" => 05
        ]);

        $sensor13 = \App\Models\Sensor::create([
            "sensor_reference" => "sensor 13",
            "description" => "Description sensor 13",
            "site_id" => $site5->id
        ]);

        $rs13 = \App\Models\SensorRecord::create([
            "sensor_id" => $sensor13->id,
            "longitude" => 11.0,
            "latitude" => 3.7,
            "temperature" => 28,
            "battery" => 75
        ]);

        $sensor14 = \App\Models\Sensor::create([
            "sensor_reference" => "sensor 14",
            "description" => "Description sensor 14",
            "site_id" => $site5->id
        ]);

        $rs14 = \App\Models\SensorRecord::create([
            "sensor_id" => $sensor14->id,
            "longitude" => 10.80,
            "latitude" => 3.7,
            "temperature" => 28,
            "battery" => 35
        ]);

        $sensor15 = \App\Models\Sensor::create([
            "sensor_reference" => "sensor 15",
            "description" => "Description sensor 15",
            "site_id" => $site5->id
        ]);

        $rs15 = \App\Models\SensorRecord::create([
            "sensor_id" => $sensor15->id,
            "longitude" => 10.80,
            "latitude" => 4.0,
            "temperature" => 28,
            "battery" => 25
        ]);

        $sensor16 = \App\Models\Sensor::create([
            "sensor_reference" => "sensor 16",
            "description" => "Description sensor 16",
            "site_id" => $site5->id
        ]);

        $rs16 = \App\Models\SensorRecord::create([
            "sensor_id" => $sensor16->id,
            "longitude" => 10.60,
            "latitude" => 3.8,
            "temperature" => 28,
            "battery" => 15
        ]);

        $user1->update(["created_by"=> 1]);
        $user2->update(["created_by"=> 1]);
        $user3->update(["created_by"=> 1]);
        $user4->update(["created_by"=> 1]);

    }
}
