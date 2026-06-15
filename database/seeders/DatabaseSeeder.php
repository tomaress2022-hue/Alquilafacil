<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Equipment;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ================================
        // USUARIOS
        // ================================

        // Crear administrador
        User::create([
            'name'      => 'Administrador AlquilaFácil',
            'email'     => 'admin@alquilafacil.com',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'documento' => '12345678',
            'phone'     => '3001234567',
        ]);

        // Crear clientes de prueba
        User::create([
            'name'      => 'Carlos Gómez',
            'email'     => 'carlos@cliente.com',
            'password'  => Hash::make('password'),
            'role'      => 'client',
            'documento' => '98765432',
            'phone'     => '3109876543',
        ]);

        User::create([
            'name'      => 'María López',
            'email'     => 'maria@cliente.com',
            'password'  => Hash::make('password'),
            'role'      => 'client',
            'documento' => '11223344',
            'phone'     => '3201122334',
        ]);

        // ================================
        // CATEGORÍAS
        // ================================

        $categories = [
            ['name' => 'Informática',       'description' => 'Computadores, laptops, tablets y accesorios tecnológicos'],
            ['name' => 'Fotografía',        'description' => 'Cámaras fotográficas, lentes, flashes y trípodes'],
            ['name' => 'Equipos de Sonido', 'description' => 'Parlantes, micrófonos, mezcladores y auriculares profesionales'],
            ['name' => 'Video y Streaming', 'description' => 'Cámaras de video, drones, estabilizadores y luces'],
            ['name' => 'Herramientas',      'description' => 'Taladros, sierras, medidores y herramientas eléctricas'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // ================================
        // EQUIPOS
        // ================================

        $equipment = [
            // Informática (category_id: 1)
            ['category_id' => 1, 'name' => 'MacBook Pro 14" M3',       'code' => 'COMP-001', 'daily_price' => 85000,  'status' => 'available',   'description' => 'Laptop Apple con chip M3, 16GB RAM, 512GB SSD'],
            ['category_id' => 1, 'name' => 'Dell XPS 15',              'code' => 'COMP-002', 'daily_price' => 65000,  'status' => 'available',   'description' => 'Laptop Dell Core i7, 16GB RAM, 1TB SSD'],
            ['category_id' => 1, 'name' => 'iPad Pro 12.9"',           'code' => 'TAB-001',  'daily_price' => 45000,  'status' => 'rented',      'description' => 'Tablet Apple con Apple Pencil incluido'],

            // Fotografía (category_id: 2)
            ['category_id' => 2, 'name' => 'Canon EOS R5',             'code' => 'CAM-001',  'daily_price' => 120000, 'status' => 'available',   'description' => 'Cámara mirrorless 45MP, video 8K RAW'],
            ['category_id' => 2, 'name' => 'Sony Alpha A7 IV',         'code' => 'CAM-002',  'daily_price' => 95000,  'status' => 'available',   'description' => 'Cámara mirrorless full-frame 33MP'],
            ['category_id' => 2, 'name' => 'Lente Canon 24-70mm f/2.8','code' => 'LEN-001',  'daily_price' => 35000,  'status' => 'available',   'description' => 'Lente zoom profesional L series'],
            ['category_id' => 2, 'name' => 'Trípode Manfrotto Pro',    'code' => 'TRI-001',  'daily_price' => 15000,  'status' => 'available',   'description' => 'Trípode carbono hasta 8kg de carga'],

            // Equipos de Sonido (category_id: 3)
            ['category_id' => 3, 'name' => 'Micrófono Shure SM7B',     'code' => 'MIC-001',  'daily_price' => 25000,  'status' => 'available',   'description' => 'Micrófono dinámico para grabación vocal profesional'],
            ['category_id' => 3, 'name' => 'Mezclador Behringer 16CH', 'code' => 'MIX-001',  'daily_price' => 55000,  'status' => 'maintenance', 'description' => 'Consola de mezcla 16 canales con efectos'],
            ['category_id' => 3, 'name' => 'Sistema de Parlantes JBL', 'code' => 'SPK-001',  'daily_price' => 75000,  'status' => 'available',   'description' => 'Par de parlantes activos 1000W RMS'],

            // Video (category_id: 4)
            ['category_id' => 4, 'name' => 'DJI Ronin 4D',             'code' => 'VID-001',  'daily_price' => 180000, 'status' => 'available',   'description' => 'Cámara de cine 6K con estabilizador integrado'],
            ['category_id' => 4, 'name' => 'DJI Mavic 3 Pro (Drone)',  'code' => 'DRN-001',  'daily_price' => 95000,  'status' => 'available',   'description' => 'Drone con triple cámara Hasselblad'],

            // Herramientas (category_id: 5)
            ['category_id' => 5, 'name' => 'Taladro DeWalt 20V',       'code' => 'HER-001',  'daily_price' => 18000,  'status' => 'available',   'description' => 'Taladro percutor inalámbrico con 2 baterías'],
            ['category_id' => 5, 'name' => 'Sierra Circular Makita',   'code' => 'HER-002',  'daily_price' => 22000,  'status' => 'available',   'description' => 'Sierra circular 7-1/4" con guía láser'],
        ];

        foreach ($equipment as $eq) {
            Equipment::create($eq);
        }
    }
}