<?php

namespace App\Support;

final class ProfessionRequirements
{
    private const PROFESSION_ALIASES = [
        'bidan D4/S1 + Profesi' => 'Bidan D4/S1 + Profesi',
        'Adminstrasi Perkantoran' => 'Administrasi Perkantoran',
    ];

    private const REQUIREMENTS = [
        'Dokter umum' => [
            'title' => 'Persyaratan Khusus (Dokter Umum)',
            'description' => 'Kualifikasi dan dokumen pendukung spesifik untuk profesi pilihan Anda.',
            'documents' => [
                'dokter_ijazah_s1_profesi' => [
                    'title' => 'Ijazah S1 dan Profesi',
                    'description' => 'Ijazah asli pendidikan dokter S1 dan profesi.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'indigo',
                    'icon' => 'graduation',
                    'multiple' => true,
                    'max_files' => 5,
                ],
                'dokter_str_aktif' => [
                    'title' => 'Sertifikat STR Aktif',
                    'description' => 'Surat Tanda Registrasi yang masih berlaku.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'blue',
                    'icon' => 'shield',
                ],
                'dokter_acls_atls' => [
                    'title' => 'Sertifikat ACLS/ATLS',
                    'description' => 'Sertifikat pelatihan ACLS atau ATLS yang masih berlaku.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'red',
                    'icon' => 'medical',
                ],
                'dokter_sertifikat_lainnya' => [
                    'title' => 'Sertifikat Lainnya',
                    'description' => 'Sertifikat keahlian tambahan yang mendukung kualifikasi.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'gray',
                    'icon' => 'more',
                    'multiple' => true,
                    'max_files' => 5,
                ],
            ],
        ],
        'Ners' => [
            'title' => 'Persyaratan Khusus (NERS)',
            'description' => 'Kualifikasi dan dokumen pendukung spesifik untuk profesi pilihan Anda.',
            'documents' => [
                'ners_ijazah_s1_profesi' => [
                    'title' => 'Ijazah S1 dan Profesi',
                    'description' => 'Ijazah asli pendidikan keperawatan S1 dan profesi NERS.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'indigo',
                    'icon' => 'graduation',
                    'multiple' => true,
                    'max_files' => 5,
                ],
                'ners_str_aktif' => [
                    'title' => 'STR Aktif',
                    'description' => 'Surat Tanda Registrasi perawat yang masih berlaku.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'blue',
                    'icon' => 'shield',
                ],
                'ners_bcls_btcls_aktif' => [
                    'title' => 'Sertifikat BCLS/BTCLS Aktif',
                    'description' => 'Sertifikat BCLS atau BTCLS yang masih berlaku.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'olive',
                    'icon' => 'medical',
                ],
                'ners_sertifikat_kompetensi' => [
                    'title' => 'Sertifikat (ICU, PICU, PERINA, HD)',
                    'description' => 'Sertifikat kompetensi ICU, PICU, PERINA, atau HD.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'red',
                    'icon' => 'medical',
                    'multiple' => true,
                ],
                'ners_sertifikat_lainnya' => [
                    'title' => 'Sertifikat Lainnya',
                    'description' => 'Sertifikat keahlian tambahan yang mendukung kualifikasi.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'gray',
                    'icon' => 'more',
                    'multiple' => true,
                    'wide' => true,
                ],
            ],
        ],
        'Perawat D3' => [
            'title' => 'Persyaratan Khusus (Perawat D3)',
            'description' => 'Kualifikasi dan dokumen pendukung spesifik untuk profesi pilihan Anda.',
            'documents' => [
                'perawat_d3_ijazah' => [
                    'title' => 'Ijazah D3',
                    'description' => 'Ijazah asli pendidikan Diploma III Keperawatan.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'indigo',
                    'icon' => 'graduation',
                ],
                'perawat_d3_str_aktif' => [
                    'title' => 'STR Aktif',
                    'description' => 'Surat Tanda Registrasi perawat yang masih berlaku.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'blue',
                    'icon' => 'shield',
                ],
                'perawat_d3_bcls_btcls_aktif' => [
                    'title' => 'Sertifikat BCLS/BTCLS Aktif',
                    'description' => 'Sertifikat BCLS atau BTCLS yang masih berlaku.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'olive',
                    'icon' => 'medical',
                ],
                'perawat_d3_sertifikat_kompetensi' => [
                    'title' => 'Sertifikat (ICU, PICU, PERINA, HD)',
                    'description' => 'Sertifikat kompetensi ICU, PICU, PERINA, atau HD.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'red',
                    'icon' => 'medical',
                    'multiple' => true,
                ],
                'perawat_d3_sertifikat_lainnya' => [
                    'title' => 'Sertifikat Lainnya',
                    'description' => 'Sertifikat keahlian tambahan yang mendukung kualifikasi.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'gray',
                    'icon' => 'more',
                    'multiple' => true,
                    'wide' => true,
                ],
            ],
        ],
        'Bidan D4/S1 + Profesi' => [
            'title' => 'Persyaratan Khusus (Bidan D4/S1 + Profesi)',
            'description' => 'Kualifikasi dan dokumen pendukung spesifik untuk profesi pilihan Anda.',
            'documents' => [
                'bidan_d4_s1_ijazah_profesi' => [
                    'title' => 'Ijazah D4/S1 + Profesi',
                    'description' => 'Ijazah asli pendidikan kebidanan D4/S1 dan profesi.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'indigo',
                    'icon' => 'graduation',
                    'multiple' => true,
                    'max_files' => 5,
                ],
                'bidan_d4_s1_str_aktif' => [
                    'title' => 'STR Aktif',
                    'description' => 'Surat Tanda Registrasi bidan yang masih berlaku.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'blue',
                    'icon' => 'shield',
                ],
                'bidan_d4_s1_sertifikat_apn_ppgdon_mu' => [
                    'title' => 'Sertifikat APN, PPGDON, dan MU Aktif',
                    'description' => 'Sertifikat APN, PPGDON, dan MU yang masih berlaku.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'olive',
                    'icon' => 'medical',
                    'multiple' => true,
                ],
                'bidan_d4_s1_sertifikat_lainnya' => [
                    'title' => 'Sertifikat Lainnya',
                    'description' => 'Sertifikat keahlian tambahan yang mendukung kualifikasi.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'gray',
                    'icon' => 'more',
                    'multiple' => true,
                ],
            ],
        ],
        'Bidan D3' => [
            'title' => 'Persyaratan Khusus (Bidan D3)',
            'description' => 'Kualifikasi dan dokumen pendukung spesifik untuk profesi pilihan Anda.',
            'documents' => [
                'bidan_d3_ijazah' => [
                    'title' => 'Ijazah D3',
                    'description' => 'Ijazah asli pendidikan Diploma III Kebidanan.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'indigo',
                    'icon' => 'graduation',
                ],
                'bidan_d3_str_aktif' => [
                    'title' => 'STR Aktif',
                    'description' => 'Surat Tanda Registrasi bidan yang masih berlaku.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'blue',
                    'icon' => 'shield',
                ],
                'bidan_d3_sertifikat_apn_ppgdon_mu' => [
                    'title' => 'Sertifikat APN, PPGDON, dan MU Aktif',
                    'description' => 'Sertifikat APN, PPGDON, dan MU yang masih berlaku.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'olive',
                    'icon' => 'medical',
                    'multiple' => true,
                ],
                'bidan_d3_sertifikat_lainnya' => [
                    'title' => 'Sertifikat Lainnya',
                    'description' => 'Sertifikat keahlian tambahan yang mendukung kualifikasi.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'gray',
                    'icon' => 'more',
                    'multiple' => true,
                ],
            ],
        ],
        'Pranata Komputer IT' => [
            'title' => 'Persyaratan Khusus (Pranata Komputer IT)',
            'description' => 'Kualifikasi dan dokumen pendukung spesifik untuk profesi pilihan Anda.',
            'documents' => [
                'pranata_komputer_ijazah_min_d3' => [
                    'title' => 'Ijazah Min D3 Komputer',
                    'description' => 'Ijazah minimal Diploma III bidang komputer atau teknologi informasi.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'indigo',
                    'icon' => 'graduation',
                ],
                'pranata_komputer_sertifikat_lainnya' => [
                    'title' => 'Sertifikat Lainnya',
                    'description' => 'Sertifikat keahlian tambahan yang mendukung kualifikasi.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'gray',
                    'icon' => 'more',
                    'multiple' => true,
                ],
            ],
        ],
        'Akuntansi' => [
            'title' => 'Persyaratan Khusus (Akuntansi)',
            'description' => 'Kualifikasi dan dokumen pendukung spesifik untuk profesi pilihan Anda.',
            'documents' => [
                'akuntansi_ijazah_min_d3' => [
                    'title' => 'Ijazah Min D3 Akuntansi',
                    'description' => 'Ijazah minimal Diploma III bidang Akuntansi.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'indigo',
                    'icon' => 'graduation',
                ],
                'akuntansi_sertifikat_lainnya' => [
                    'title' => 'Sertifikat Lainnya',
                    'description' => 'Sertifikat keahlian tambahan yang mendukung kualifikasi.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'gray',
                    'icon' => 'more',
                    'multiple' => true,
                ],
            ],
        ],
        'TTK (Asisten Apoteker)' => [
            'title' => 'Persyaratan Khusus (TTK/Asisten Apoteker)',
            'description' => 'Kualifikasi dan dokumen pendukung spesifik untuk profesi pilihan Anda.',
            'documents' => [
                'ttk_ijazah_d3' => [
                    'title' => 'Ijazah D3',
                    'description' => 'Ijazah asli pendidikan Diploma III Farmasi.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'indigo',
                    'icon' => 'graduation',
                ],
                'ttk_str_aktif' => [
                    'title' => 'STR Aktif',
                    'description' => 'Surat Tanda Registrasi tenaga teknis kefarmasian yang masih berlaku.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'blue',
                    'icon' => 'shield',
                ],
                'ttk_sertifikat_lainnya' => [
                    'title' => 'Sertifikat Lainnya',
                    'description' => 'Sertifikat keahlian tambahan yang mendukung kualifikasi.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'gray',
                    'icon' => 'more',
                    'multiple' => true,
                    'wide' => true,
                ],
            ],
        ],
        'Administrasi Perkantoran' => [
            'title' => 'Persyaratan Khusus (Administrasi Perkantoran)',
            'description' => 'Kualifikasi dan dokumen pendukung spesifik untuk profesi pilihan Anda.',
            'documents' => [
                'administrasi_perkantoran_ijazah_min_d3' => [
                    'title' => 'Ijazah Min D3 Semua Jurusan',
                    'description' => 'Ijazah minimal Diploma III dari semua jurusan.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'indigo',
                    'icon' => 'graduation',
                ],
                'administrasi_perkantoran_sertifikat_lainnya' => [
                    'title' => 'Sertifikat Lainnya',
                    'description' => 'Sertifikat keahlian tambahan yang mendukung kualifikasi.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'gray',
                    'icon' => 'more',
                    'multiple' => true,
                ],
            ],
        ],
        'Teknik Pendingin' => [
            'title' => 'Persyaratan Khusus (Teknik Pendingin)',
            'description' => 'Kualifikasi dan dokumen pendukung spesifik untuk profesi pilihan Anda.',
            'documents' => [
                'teknik_pendingin_ijazah_d3' => [
                    'title' => 'Ijazah D3 Teknik Pendingin/Refrigerasi',
                    'description' => 'Ijazah Diploma III bidang Teknik Pendingin atau Refrigerasi.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'indigo',
                    'icon' => 'graduation',
                ],
                'teknik_pendingin_sertifikat_lainnya' => [
                    'title' => 'Sertifikat Lainnya',
                    'description' => 'Sertifikat keahlian tambahan yang mendukung kualifikasi.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'gray',
                    'icon' => 'more',
                    'multiple' => true,
                ],
            ],
        ],
        'BDRS' => [
            'title' => 'Persyaratan Khusus (BDRS)',
            'description' => 'Kualifikasi dan dokumen pendukung spesifik untuk profesi pilihan Anda.',
            'documents' => [
                'bdrs_ijazah_d3' => [
                    'title' => 'Ijazah D3',
                    'description' => 'Ijazah asli pendidikan Diploma III yang sesuai dengan kualifikasi BDRS.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'indigo',
                    'icon' => 'graduation',
                ],
                'bdrs_str_aktif' => [
                    'title' => 'STR Aktif',
                    'description' => 'Surat Tanda Registrasi yang masih berlaku.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'blue',
                    'icon' => 'shield',
                ],
                'bdrs_sertifikat_lainnya' => [
                    'title' => 'Sertifikat Lainnya',
                    'description' => 'Sertifikat keahlian tambahan yang mendukung kualifikasi.',
                    'format' => 'PDF/JPG/PNG',
                    'mimes' => 'pdf,jpg,jpeg,png',
                    'tone' => 'gray',
                    'icon' => 'more',
                    'multiple' => true,
                    'wide' => true,
                ],
            ],
        ],
    ];

    public static function for(?string $profession): ?array
    {
        $profession = self::PROFESSION_ALIASES[$profession] ?? $profession;

        return self::REQUIREMENTS[$profession] ?? null;
    }

    public static function documentsFor(?string $profession): array
    {
        return self::for($profession)['documents'] ?? [];
    }

    public static function labelsFor(?string $profession): array
    {
        return collect(self::documentsFor($profession))
            ->mapWithKeys(fn (array $document, string $type): array => [$type => $document['title']])
            ->all();
    }
}
