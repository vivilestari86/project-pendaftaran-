<?php

namespace Tests\Unit;

use App\Support\ProfessionRequirements;
use PHPUnit\Framework\TestCase;

class ProfessionRequirementsTest extends TestCase
{
    public function test_doctor_has_the_expected_special_requirements(): void
    {
        $requirement = ProfessionRequirements::for('Dokter umum');

        $this->assertNotNull($requirement);
        $this->assertSame('Persyaratan Khusus (Dokter Umum)', $requirement['title']);
        $this->assertSame([
            'dokter_ijazah_s1_profesi',
            'dokter_str_aktif',
            'dokter_acls_atls',
            'dokter_sertifikat_lainnya',
        ], array_keys($requirement['documents']));
        $this->assertSame([
            'dokter_ijazah_s1_profesi' => 'Ijazah S1 dan Profesi',
            'dokter_str_aktif' => 'Sertifikat STR Aktif',
            'dokter_acls_atls' => 'Sertifikat ACLS/ATLS',
            'dokter_sertifikat_lainnya' => 'Sertifikat Lainnya',
        ], ProfessionRequirements::labelsFor('Dokter umum'));
        $this->assertTrue($requirement['documents']['dokter_ijazah_s1_profesi']['multiple']);
        $this->assertSame(5, $requirement['documents']['dokter_ijazah_s1_profesi']['max_files']);
        $this->assertTrue($requirement['documents']['dokter_sertifikat_lainnya']['multiple']);
        $this->assertSame(5, $requirement['documents']['dokter_sertifikat_lainnya']['max_files']);
    }

    public function test_ners_has_the_expected_special_requirements(): void
    {
        $requirement = ProfessionRequirements::for('Ners');

        $this->assertNotNull($requirement);
        $this->assertSame('Persyaratan Khusus (NERS)', $requirement['title']);
        $this->assertSame([
            'ners_ijazah_s1_profesi',
            'ners_str_aktif',
            'ners_bcls_btcls_aktif',
            'ners_sertifikat_kompetensi',
            'ners_sertifikat_lainnya',
        ], array_keys($requirement['documents']));
        $this->assertSame([
            'ners_ijazah_s1_profesi' => 'Ijazah S1 dan Profesi',
            'ners_str_aktif' => 'STR Aktif',
            'ners_bcls_btcls_aktif' => 'Sertifikat BCLS/BTCLS Aktif',
            'ners_sertifikat_kompetensi' => 'Sertifikat (ICU, PICU, PERINA, HD)',
            'ners_sertifikat_lainnya' => 'Sertifikat Lainnya',
        ], ProfessionRequirements::labelsFor('Ners'));
        $this->assertTrue($requirement['documents']['ners_ijazah_s1_profesi']['multiple']);
        $this->assertSame(5, $requirement['documents']['ners_ijazah_s1_profesi']['max_files']);
        $this->assertTrue($requirement['documents']['ners_sertifikat_kompetensi']['multiple']);
        $this->assertArrayNotHasKey('max_files', $requirement['documents']['ners_sertifikat_kompetensi']);
        $this->assertTrue($requirement['documents']['ners_sertifikat_lainnya']['multiple']);
        $this->assertArrayNotHasKey('max_files', $requirement['documents']['ners_sertifikat_lainnya']);
        $this->assertTrue($requirement['documents']['ners_sertifikat_lainnya']['wide']);
    }

    public function test_perawat_d3_has_the_expected_special_requirements(): void
    {
        $requirement = ProfessionRequirements::for('Perawat D3');

        $this->assertNotNull($requirement);
        $this->assertSame('Persyaratan Khusus (Perawat D3)', $requirement['title']);
        $this->assertSame([
            'perawat_d3_ijazah',
            'perawat_d3_str_aktif',
            'perawat_d3_bcls_btcls_aktif',
            'perawat_d3_sertifikat_kompetensi',
            'perawat_d3_sertifikat_lainnya',
        ], array_keys($requirement['documents']));
        $this->assertSame([
            'perawat_d3_ijazah' => 'Ijazah D3',
            'perawat_d3_str_aktif' => 'STR Aktif',
            'perawat_d3_bcls_btcls_aktif' => 'Sertifikat BCLS/BTCLS Aktif',
            'perawat_d3_sertifikat_kompetensi' => 'Sertifikat (ICU, PICU, PERINA, HD)',
            'perawat_d3_sertifikat_lainnya' => 'Sertifikat Lainnya',
        ], ProfessionRequirements::labelsFor('Perawat D3'));
        $this->assertTrue($requirement['documents']['perawat_d3_sertifikat_kompetensi']['multiple']);
        $this->assertArrayNotHasKey('max_files', $requirement['documents']['perawat_d3_sertifikat_kompetensi']);
        $this->assertTrue($requirement['documents']['perawat_d3_sertifikat_lainnya']['multiple']);
        $this->assertArrayNotHasKey('max_files', $requirement['documents']['perawat_d3_sertifikat_lainnya']);
        $this->assertTrue($requirement['documents']['perawat_d3_sertifikat_lainnya']['wide']);
    }

    public function test_bidan_d4_s1_profesi_has_the_expected_special_requirements(): void
    {
        $requirement = ProfessionRequirements::for('Bidan D4/S1 + Profesi');

        $this->assertNotNull($requirement);
        $this->assertSame('Persyaratan Khusus (Bidan D4/S1 + Profesi)', $requirement['title']);
        $this->assertSame([
            'bidan_d4_s1_ijazah_profesi',
            'bidan_d4_s1_str_aktif',
            'bidan_d4_s1_sertifikat_apn_ppgdon_mu',
            'bidan_d4_s1_sertifikat_lainnya',
        ], array_keys($requirement['documents']));
        $this->assertSame([
            'bidan_d4_s1_ijazah_profesi' => 'Ijazah D4/S1 + Profesi',
            'bidan_d4_s1_str_aktif' => 'STR Aktif',
            'bidan_d4_s1_sertifikat_apn_ppgdon_mu' => 'Sertifikat APN, PPGDON, dan MU Aktif',
            'bidan_d4_s1_sertifikat_lainnya' => 'Sertifikat Lainnya',
        ], ProfessionRequirements::labelsFor('Bidan D4/S1 + Profesi'));
        $this->assertTrue($requirement['documents']['bidan_d4_s1_ijazah_profesi']['multiple']);
        $this->assertSame(5, $requirement['documents']['bidan_d4_s1_ijazah_profesi']['max_files']);
        $this->assertTrue($requirement['documents']['bidan_d4_s1_sertifikat_apn_ppgdon_mu']['multiple']);
        $this->assertArrayNotHasKey('max_files', $requirement['documents']['bidan_d4_s1_sertifikat_apn_ppgdon_mu']);
        $this->assertTrue($requirement['documents']['bidan_d4_s1_sertifikat_lainnya']['multiple']);
        $this->assertArrayNotHasKey('max_files', $requirement['documents']['bidan_d4_s1_sertifikat_lainnya']);
        $this->assertSame($requirement, ProfessionRequirements::for('bidan D4/S1 + Profesi'));
    }

    public function test_bidan_d3_has_the_expected_special_requirements(): void
    {
        $requirement = ProfessionRequirements::for('Bidan D3');

        $this->assertNotNull($requirement);
        $this->assertSame('Persyaratan Khusus (Bidan D3)', $requirement['title']);
        $this->assertSame([
            'bidan_d3_ijazah',
            'bidan_d3_str_aktif',
            'bidan_d3_sertifikat_apn_ppgdon_mu',
            'bidan_d3_sertifikat_lainnya',
        ], array_keys($requirement['documents']));
        $this->assertSame([
            'bidan_d3_ijazah' => 'Ijazah D3',
            'bidan_d3_str_aktif' => 'STR Aktif',
            'bidan_d3_sertifikat_apn_ppgdon_mu' => 'Sertifikat APN, PPGDON, dan MU Aktif',
            'bidan_d3_sertifikat_lainnya' => 'Sertifikat Lainnya',
        ], ProfessionRequirements::labelsFor('Bidan D3'));
        $this->assertTrue($requirement['documents']['bidan_d3_sertifikat_apn_ppgdon_mu']['multiple']);
        $this->assertArrayNotHasKey('max_files', $requirement['documents']['bidan_d3_sertifikat_apn_ppgdon_mu']);
        $this->assertTrue($requirement['documents']['bidan_d3_sertifikat_lainnya']['multiple']);
        $this->assertArrayNotHasKey('max_files', $requirement['documents']['bidan_d3_sertifikat_lainnya']);
    }

    public function test_pranata_komputer_it_has_the_expected_special_requirements(): void
    {
        $requirement = ProfessionRequirements::for('Pranata Komputer IT');

        $this->assertNotNull($requirement);
        $this->assertSame('Persyaratan Khusus (Pranata Komputer IT)', $requirement['title']);
        $this->assertSame([
            'pranata_komputer_ijazah_min_d3',
            'pranata_komputer_sertifikat_lainnya',
        ], array_keys($requirement['documents']));
        $this->assertSame([
            'pranata_komputer_ijazah_min_d3' => 'Ijazah Min D3 Komputer',
            'pranata_komputer_sertifikat_lainnya' => 'Sertifikat Lainnya',
        ], ProfessionRequirements::labelsFor('Pranata Komputer IT'));
        $this->assertArrayNotHasKey('wide', $requirement['documents']['pranata_komputer_ijazah_min_d3']);
        $this->assertTrue($requirement['documents']['pranata_komputer_sertifikat_lainnya']['multiple']);
        $this->assertArrayNotHasKey('wide', $requirement['documents']['pranata_komputer_sertifikat_lainnya']);
    }

    public function test_akuntansi_has_the_expected_special_requirements(): void
    {
        $requirement = ProfessionRequirements::for('Akuntansi');

        $this->assertNotNull($requirement);
        $this->assertSame('Persyaratan Khusus (Akuntansi)', $requirement['title']);
        $this->assertSame([
            'akuntansi_ijazah_min_d3',
            'akuntansi_sertifikat_lainnya',
        ], array_keys($requirement['documents']));
        $this->assertSame([
            'akuntansi_ijazah_min_d3' => 'Ijazah Min D3 Akuntansi',
            'akuntansi_sertifikat_lainnya' => 'Sertifikat Lainnya',
        ], ProfessionRequirements::labelsFor('Akuntansi'));
        $this->assertArrayNotHasKey('wide', $requirement['documents']['akuntansi_ijazah_min_d3']);
        $this->assertTrue($requirement['documents']['akuntansi_sertifikat_lainnya']['multiple']);
        $this->assertArrayNotHasKey('wide', $requirement['documents']['akuntansi_sertifikat_lainnya']);
    }

    public function test_ttk_has_the_expected_special_requirements(): void
    {
        $requirement = ProfessionRequirements::for('TTK (Asisten Apoteker)');

        $this->assertNotNull($requirement);
        $this->assertSame('Persyaratan Khusus (TTK/Asisten Apoteker)', $requirement['title']);
        $this->assertSame([
            'ttk_ijazah_d3',
            'ttk_str_aktif',
            'ttk_sertifikat_lainnya',
        ], array_keys($requirement['documents']));
        $this->assertSame([
            'ttk_ijazah_d3' => 'Ijazah D3',
            'ttk_str_aktif' => 'STR Aktif',
            'ttk_sertifikat_lainnya' => 'Sertifikat Lainnya',
        ], ProfessionRequirements::labelsFor('TTK (Asisten Apoteker)'));
        $this->assertTrue($requirement['documents']['ttk_sertifikat_lainnya']['multiple']);
        $this->assertTrue($requirement['documents']['ttk_sertifikat_lainnya']['wide']);
    }

    public function test_administrasi_perkantoran_has_the_expected_special_requirements(): void
    {
        $requirement = ProfessionRequirements::for('Administrasi Perkantoran');

        $this->assertNotNull($requirement);
        $this->assertSame('Persyaratan Khusus (Administrasi Perkantoran)', $requirement['title']);
        $this->assertSame([
            'administrasi_perkantoran_ijazah_min_d3',
            'administrasi_perkantoran_sertifikat_lainnya',
        ], array_keys($requirement['documents']));
        $this->assertSame([
            'administrasi_perkantoran_ijazah_min_d3' => 'Ijazah Min D3 Semua Jurusan',
            'administrasi_perkantoran_sertifikat_lainnya' => 'Sertifikat Lainnya',
        ], ProfessionRequirements::labelsFor('Administrasi Perkantoran'));
        $this->assertTrue($requirement['documents']['administrasi_perkantoran_sertifikat_lainnya']['multiple']);
        $this->assertArrayNotHasKey('wide', $requirement['documents']['administrasi_perkantoran_sertifikat_lainnya']);
        $this->assertSame($requirement, ProfessionRequirements::for('Adminstrasi Perkantoran'));
    }

    public function test_teknik_pendingin_has_the_expected_special_requirements(): void
    {
        $requirement = ProfessionRequirements::for('Teknik Pendingin');

        $this->assertNotNull($requirement);
        $this->assertSame('Persyaratan Khusus (Teknik Pendingin)', $requirement['title']);
        $this->assertSame([
            'teknik_pendingin_ijazah_d3',
            'teknik_pendingin_sertifikat_lainnya',
        ], array_keys($requirement['documents']));
        $this->assertSame([
            'teknik_pendingin_ijazah_d3' => 'Ijazah D3 Teknik Pendingin/Refrigerasi',
            'teknik_pendingin_sertifikat_lainnya' => 'Sertifikat Lainnya',
        ], ProfessionRequirements::labelsFor('Teknik Pendingin'));
        $this->assertArrayNotHasKey('wide', $requirement['documents']['teknik_pendingin_ijazah_d3']);
        $this->assertTrue($requirement['documents']['teknik_pendingin_sertifikat_lainnya']['multiple']);
        $this->assertArrayNotHasKey('wide', $requirement['documents']['teknik_pendingin_sertifikat_lainnya']);
    }

    public function test_bdrs_has_the_expected_special_requirements(): void
    {
        $requirement = ProfessionRequirements::for('BDRS');

        $this->assertNotNull($requirement);
        $this->assertSame('Persyaratan Khusus (BDRS)', $requirement['title']);
        $this->assertSame([
            'bdrs_ijazah_d3',
            'bdrs_str_aktif',
            'bdrs_sertifikat_lainnya',
        ], array_keys($requirement['documents']));
        $this->assertSame([
            'bdrs_ijazah_d3' => 'Ijazah D3',
            'bdrs_str_aktif' => 'STR Aktif',
            'bdrs_sertifikat_lainnya' => 'Sertifikat Lainnya',
        ], ProfessionRequirements::labelsFor('BDRS'));
        $this->assertTrue($requirement['documents']['bdrs_sertifikat_lainnya']['multiple']);
        $this->assertTrue($requirement['documents']['bdrs_sertifikat_lainnya']['wide']);
    }

    public function test_profession_without_configuration_has_no_special_requirements_yet(): void
    {
        $this->assertNull(ProfessionRequirements::for('Profesi Lain'));
        $this->assertSame([], ProfessionRequirements::documentsFor('Profesi Lain'));
        $this->assertSame([], ProfessionRequirements::labelsFor('Profesi Lain'));
    }
}
