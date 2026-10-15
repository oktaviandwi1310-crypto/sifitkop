<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LandingPage;

class LandingPageSeeder extends Seeder
{
    public function run(): void
    {
        LandingPage::create([
            'hero_title' => 'Tingkatkan tata kelola Koperasi Anda hari ini.',
            'hero_subtitle' => 'SIFITKOP hadir untuk membantu koperasi di Jawa Tengah melakukan Uji Kelayakan dan Kepatuhan secara transparan, akuntabel, dan terintegrasi untuk mewujudkan koperasi yang modern dan sehat.',
            'about_title' => 'Membangun Koperasi yang Sehat, Kuat, dan Mandiri.',
            'about_description' => 'SIFITKOP diinisiasi oleh Dinas Koperasi dan UKM Jawa Tengah untuk memfasilitasi pelaksanaan uji kelayakan (fit and proper test) bagi pengurus dan pengawas koperasi.',
            'services' => [
                [
                    'tag' => 'Penilaian',
                    'title' => 'Uji Kelayakan Pengurus',
                    'description' => 'Evaluasi kompetensi, integritas, dan kelayakan calon pengurus atau pengawas koperasi.',
                ],
                [
                    'tag' => 'Kesehatan',
                    'title' => 'Penilaian Kesehatan Koperasi',
                    'description' => 'Audit dan penilaian kesehatan finansial serta kepatuhan tata kelola koperasi.',
                ],
            ],
            'features' => [
                [
                    'icon' => 'bi-eye-fill',
                    'title' => 'Transparan & Akuntabel',
                    'description' => 'Setiap proses penilaian dan hasil evaluasi dapat dilacak dan dipertanggungjawabkan.',
                ],
            ],
            'contact_address' => 'Jl. Sisingamangaraja No. 3A, Semarang, Jawa Tengah',
            'contact_email' => 'diskopukm@jatengprov.go.id',
            'contact_phone' => '(024) 8318771',
            'footer_description' => 'SIFITKOP adalah Sistem Informasi Fasilitasi Uji Kelayakan dan Kepatuhan Koperasi yang dikembangkan untuk memastikan tata kelola koperasi yang baik, berintegritas, dan akuntabel di Provinsi Jawa Tengah.'
        ]);
    }
}
