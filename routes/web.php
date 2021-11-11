<?php

use App\Http\Controllers\ApprovePenilaianController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BidangController;
use App\Http\Controllers\StrukturalController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KpiperformanceController;
use App\Http\Controllers\KpiperilakuController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PenilaianPenilaiBelumDinilaiController;
use App\Http\Controllers\PenilaianPenilaiMenungguVerifikasi;
use App\Http\Controllers\PenilaiController;
use Illuminate\Support\Facades\Route;

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
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function () {
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
    Route::group(['middleware' => ['cek_login:pegawai', 'cek_atasanpenilai:ya']], function () {
        Route::get('/approve-penilaian', [ApprovePenilaianController::class, 'show_approve_penilaian'])->name('approve-penilaian');
    });

    Route::group(['middleware' => ['cek_login:pegawai', 'cek_penilai:ya']], function () {
        Route::group(['middleware' => ['cek_berlangsung:ya']], function () {
            //start belum dinilai
            Route::get('/belum-dinilai', [PenilaianPenilaiBelumDinilaiController::class, 'show_belum_dinilai'])->name('belum-dinilai');
            
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

            //start menunggu verifikasi
            Route::get('/menunggu-verifikasi', [PenilaianPenilaiMenungguVerifikasi::class, 'show_menunggu_verifikasi'])->name('menunggu-verifikasi');
            //end menunggu verifikasi
        });
    });

    Route::group(['middleware' => ['cek_login:pegawai', 'cek_dinilai:ya']], function () {
        Route::get('/greeting2', function () {
            return 'Hello World2 test';
        });
    });
});
