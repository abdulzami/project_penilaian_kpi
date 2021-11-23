<?php

use App\Http\Controllers\ApprovePenilaianController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BidangController;
use App\Http\Controllers\DinilaiController;
use App\Http\Controllers\StrukturalController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KpiperformanceController;
use App\Http\Controllers\KpiperilakuController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PenilaianPenilaiBandingController;
use App\Http\Controllers\PenilaianPenilaiBelumDinilaiController;
use App\Http\Controllers\PenilaianPenilaiMenungguVerifikasi;
use App\Http\Controllers\PenilaianPenilaiSelesai;
use App\Http\Controllers\ProfilController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Profiler\Profile;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/proses_login', [AuthController::class, 'proses_login'])->name('proses_login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
    Route::put('/profil/ganti_password', [ProfilController::class, 'ganti_password'])->name('ganti-password');
    Route::group(['middleware' => ['cek_login:admin']], function () {
        //start struktural
        Route::get('/struktural', [StrukturalController::class, 'index'])->name('struktural');
        Route::group(['middleware' => ['cek_berlangsungno:tidak']], function () {
            Route::get('/struktural/tambah', [StrukturalController::class, 'create'])->name('create-struktural');
            Route::post('/struktural/store', [StrukturalController::class, 'store'])->name('store-struktural');
            Route::get('/struktural/edit/{id}', [StrukturalController::class, 'edit'])->name('edit-struktural');
            Route::delete('/struktural/delete/{id}', [StrukturalController::class, 'destroy'])->name('delete-struktural');
            Route::put('/struktural/update/{id}', [StrukturalController::class, 'update'])->name('update-struktural');
        });
        //end struktural

        //start bidang
        Route::get('/struktural/{id}/bidang', [BidangController::class, 'index'])->name('bidang');
        Route::group(['middleware' => ['cek_berlangsungno:tidak']], function () {
            Route::get('/struktural/{id}/bidang/tambah', [BidangController::class, 'create'])->name('create-bidang');
            Route::post('/struktural/{id}/bidang/store', [BidangController::class, 'store'])->name('store-bidang');
            Route::get('/struktural/{id}/bidang/edit/{id2}', [BidangController::class, 'edit'])->name('edit-bidang');
            Route::delete('/struktural/{id}/bidang/delete/{id2}', [BidangController::class, 'destroy'])->name('delete-bidang');
            Route::put('/struktural/{id}/bidang/update/{id2}', [BidangController::class, 'update'])->name('update-bidang');
        });
        //end bidang

        //start jabatan
        Route::get('/jabatan', [JabatanController::class, 'index'])->name('jabatan');
        Route::group(['middleware' => ['cek_berlangsungno:tidak']], function () {
            Route::get('/jabatan/tambah', [JabatanController::class, 'create'])->name('create-jabatan');
            Route::post('/jabatan/store', [JabatanController::class, 'store'])->name('store-jabatan');
            Route::get('/jabatan/edit/{id}', [JabatanController::class, 'edit'])->name('edit-jabatan');
            Route::delete('/jabatan/delete/{id}', [JabatanController::class, 'destroy'])->name('delete-jabatan');
            Route::put('/jabatan/update/{id}', [JabatanController::class, 'update'])->name('update-jabatan');
            Route::get('/jabatan/hirarki/{id}', [JabatanController::class, 'hirarki'])->name('hirarki-jabatan');
        });
        //end jabatan

        //start kpi-performance
        Route::get('/jabatan/{id}/kpi-performance', [KpiperformanceController::class, 'index'])->name('kpiperformance');
        Route::group(['middleware' => ['cek_berlangsungno:tidak']], function () {
            Route::get('/jabatan/{id}/kpi-performance/tambah', [KpiperformanceController::class, 'create'])->name('create-kpiperformance');
            Route::post('/jabatan/{id}/kpi-performance/store', [KpiperformanceController::class, 'store'])->name('store-kpiperformance');
            Route::get('/jabatan/{id}/kpi-performance/edit/{id2}', [KpiperformanceController::class, 'edit'])->name('edit-kpiperformance');
            Route::delete('/jabatan/{id}/kpi-performance/delete/{id2}', [KpiperformanceController::class, 'destroy'])->name('delete-kpiperformance');
            Route::put('/jabatan/{id}/kpi-performance/update/{id2}', [KpiperformanceController::class, 'update'])->name('update-kpiperformance');
        });
        //end kpi-performance

        //start pegawai
        Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai');
        Route::group(['middleware' => ['cek_berlangsungno:tidak']], function () {
            Route::get('/pegawai/tambah', [PegawaiController::class, 'create'])->name('create-pegawai');
            Route::post('/pegawai/store', [PegawaiController::class, 'store'])->name('store-pegawai');
            Route::get('/pegawai/edit/{id}', [PegawaiController::class, 'edit'])->name('edit-pegawai');
            Route::delete('/pegawai/delete/{id}', [PegawaiController::class, 'destroy'])->name('delete-pegawai');
            Route::put('/pegawai/update/{id}', [PegawaiController::class, 'update'])->name('update-pegawai');
            Route::put('/pegawai/reset-password/{id}', [PegawaiController::class, 'resetpassword'])->name('reset-password-pegawai');
            //end pegawai
        });
        //start jadwal
        Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal');
        Route::get('/jadwal/tambah', [JadwalController::class, 'create'])->name('create-jadwal');
        Route::post('/jadwal/store', [JadwalController::class, 'store'])->name('store-jadwal');
        Route::get('/jadwal/edit/{id}', [JadwalController::class, 'edit'])->name('edit-jadwal');
        Route::delete('/jadwal/delete/{id}', [JadwalController::class, 'destroy'])->name('delete-jadwal');
        Route::put('/jadwal/update/{id}', [JadwalController::class, 'update'])->name('update-jadwal');
        //end jadwal

        //start kpi perilaku
        Route::get('/kpi-perilaku', [KpiperilakuController::class, 'index'])->name('kpiperilaku');
        //end kpi perilaku

    });
    Route::group(['middleware' => ['cek_login:pegawai', 'cek_atasanpenilai:ya', 'cek_berlangsung:ya']], function () {
        Route::get('/approve-penilaian', [ApprovePenilaianController::class, 'show_approve_penilaian'])->name('approve-penilaian');
        Route::get('/approve-penilaian/{id}/review', [ApprovePenilaianController::class, 'review_penilaian'])->name('approve-penilaian-review')->middleware(['cek_menunggu_verifikasi', 'cek_catatan_penting']);
        Route::put('/approve-penilaian/{id}/review/approve', [ApprovePenilaianController::class, 'approve_penilaian'])->name('approve-penilaian-approve')->middleware(['cek_menunggu_verifikasi', 'cek_catatan_penting']);
        Route::put('/approve-penilaian/{id}/approve', [ApprovePenilaianController::class, 'approve_penilaian_langsung'])->name('approve-penilaian-approve-langsung')->middleware('cek_menunggu_verifikasi');
        Route::put('/approve-penilaian/{id}/approve-banding-penilaian', [ApprovePenilaianController::class, 'approve_banding_penilaian'])->name('approve-banding-penilaian');
    });

    Route::group(['middleware' => ['cek_login:pegawai', 'cek_penilai:ya']], function () {
        Route::group(['middleware' => ['cek_berlangsung:ya']], function () {
            //start belum dinilai

            Route::get('/belum-dinilai', [PenilaianPenilaiBelumDinilaiController::class, 'show_belum_dinilai'])->name('belum-dinilai');
            Route::group(['middleware' => ['cek_belum_dinilai']], function () {
                Route::get('/belum-dinilai/{id}/kpi-performance', [PenilaianPenilaiBelumDinilaiController::class, 'create_penilaian_kpi_performance'])->name('belum-dinilai-kpi-performance');
                Route::post('/belum-dinilai/{id}/kpi-performance/store', [PenilaianPenilaiBelumDinilaiController::class, 'store_penilaian_kpi_performance'])->name('belum-dinilai-kpi-performance-store');
                Route::put('/belum-dinilai/{id}/kpi-performance/update', [PenilaianPenilaiBelumDinilaiController::class, 'update_penilaian_kpi_performance'])->name('belum-dinilai-kpi-performance-update');

                Route::get('/belum-dinilai/{id}/kpi-perilaku', [PenilaianPenilaiBelumDinilaiController::class, 'create_penilaian_kpi_perilaku'])->name('belum-dinilai-kpi-perilaku');
                Route::post('/belum-dinilai/{id}/kpi-perilaku/store', [PenilaianPenilaiBelumDinilaiController::class, 'store_penilaian_kpi_perilaku'])->name('belum-dinilai-kpi-perilaku-store');
                Route::put('/belum-dinilai/{id}/kpi-perilaku/update', [PenilaianPenilaiBelumDinilaiController::class, 'update_penilaian_kpi_perilaku'])->name('belum-dinilai-kpi-perilaku-update');

                Route::get('/belum-dinilai/{id}/catatan-penting', [PenilaianPenilaiBelumDinilaiController::class, 'penilaian_catatan_penting'])->name('belum-dinilai-catatan-penting');
                Route::put('/belum-dinilai/{id}/catatan-penting/update', [PenilaianPenilaiBelumDinilaiController::class, 'update_penilaian_catatan_penting'])->name('belum-dinilai-catatan-penting-update');

                Route::put('/belum-dinilai/{id}/approve', [PenilaianPenilaiBelumDinilaiController::class, 'approve_to_menunggu_verifikasi'])->name('belum-dinilai-approve');
                //end belum dinilai
            });

            //start banding penilaian
            Route::get('/banding-penilaian', [PenilaianPenilaiBandingController::class, 'show_banding_penilaian'])->name('banding-penilaian');
            Route::group(['middleware' => ['cek_action_banding_penilaian']], function () {
                Route::get('/banding-penilaian/{id}/edit-kpi-performance', [PenilaianPenilaiBandingController::class, 'edit_kpi_performance'])->name('bp-edit-kpi-performance');
                Route::put('/banding-penilaian/{id}/edit-kpi-performance/update', [PenilaianPenilaiBandingController::class, 'update_kpi_performance_setuju_pengajuan'])->name('bp-update-kpi-performance-sp');

                Route::get('/banding-penilaian/{id}/lihat-catatan', [PenilaianPenilaiBandingController::class, 'lihat_catatan'])->name('bp-lihat-catatan')->middleware('cek_catatan_penting');
                Route::get('/banding-penilaian/{id}/review-pengajuan', [PenilaianPenilaiBandingController::class, 'review_pengajuan'])->name('bp-review-pengajuan');
                Route::put('/banding-penilaian/{id}/review-pengajuan/tolak-pengajuan', [PenilaianPenilaiBandingController::class, 'tolak_pengajuan'])->name('bp-tolak-pengajuan');
            });
            //end banding penilaian


            //start menunggu verifikasi
            Route::get('/menunggu-verifikasi', [PenilaianPenilaiMenungguVerifikasi::class, 'show_menunggu_verifikasi'])->name('menunggu-verifikasi');
            //end menunggu verifikasi

            //start selesai
            Route::get('/selesai', [PenilaianPenilaiSelesai::class, 'show_selesai'])->name('selesai');
            //end selesai
        });
    });

    Route::group(['middleware' => ['cek_login:pegawai', 'cek_dinilai:ya']], function () {
        Route::put('/{id}/approve', [ApprovePenilaianController::class, 'approve_penilaian_dinilai'])->name('approve-penilaian-dinilai')->middleware('cek_pengajuan_banding');

        Route::get('/{id}/pengajuan-banding', [DinilaiController::class, 'create_pengajuan_banding'])->name('create-pengajuan-banding')->middleware('cek_pengajuan_banding');
        Route::post('/{id}/pengajuan-banding/store', [DinilaiController::class, 'store_pengajuan_banding'])->name('store-pengajuan-banding')->middleware('cek_pengajuan_banding');
    });
});
