<?php

namespace Database\Seeders;

use App\Models\KpiPerilaku;
use Illuminate\Database\Seeder;

class KpiperilakuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $perilaku = [
            [
                'id_perilaku'=>1,
                'nama_kpi' => 'Amanah',
                'ekselen' => 'Dapat diandalkan untuk mengerjakan tugas-tugas yang sulit, bekerja melampaui target yang di tetapkan, tidak terdapat kesalahan dalam pekerjaan',
                'baik' => 'Pekerjaan yang dijalankan, dijalankan dengan baik dan sesuai dengan ketentuan yang berlaku, kesalahan dalam pekerjaan kecil dan masih dapat di',
                'cukup' => 'Pekerjaan yang dilaksanakan sebagaimana mestinya, bertanggung jawab atas kelalaian yang dilakukannya',
                'kurang' =>'Sering melimpahkan tanggung jawabnya kepada orang lain, memiliki banyak alasan (selalu beralasan) ketika melakukan kesalahan, menyalahkan orang lain atas',
                'kurang_sekali' => 'Kurang dapat dipercaya untuk melaksanakan tanggung jawab, selalu berbohong, yang dikatakan tidak selaras dengan yang dikerjakan'
            ],
            [
                'id_perilaku'=>2,
                'nama_kpi' => 'Kompeten',
                'ekselen' => 'Dapat mengksekusi pekerjaan dengan cermat dan melampaui target',
                'baik' => 'Dapat mengeksekusi pekerjaan dengan cermat dan sesuai target',
                'cukup' => 'Dapat mengeksekusi pekerjaan sesuai standar yang berlaku dan kurang mencapai target',
                'kurang' =>'Kurang dapat mengeksekusi pekerjaan sesuai standar dan tidak mencapai target',
                'kurang_sekali' => 'Tidak dapat melaksanakan pekerjaan sama sekali dan target selalu tidak terpenuhi'
            ],
            [
                'id_perilaku'=>3,
                'nama_kpi' => 'Harmonis',
                'ekselen' => 'Memberikan apresiasi dan menghargai segala tindakan yang dilakukan oleh karyawan lain serta memiliki kelekatan yang sangat erat antar satu karyawan dan karyawan',
                'baik' => 'Memberikan apresiasi dan menghargai setiap tindakan yang dilakukan oleh karyawan lain',
                'cukup' => 'Menghargai tindakan yang dimunculkan karyawan tertentu',
                'kurang' =>'Kurang menghargai karyawan lain dan acuh tak acuh',
                'kurang_sekali' => 'Tidak menyukai hasil kinerja karyawan lain dan memunculkan sikap negatif'
            ],
            [
                'id_perilaku'=>4,
                'nama_kpi' => 'Loyal',
                'ekselen' => 'Mematuhi segala kebijakan yang ada berkomitmen serta berkonstribusi dalam mencapai tujuan organisasi',
                'baik' => 'Mematuhi kebijakan yang ada dan berkonsribusi dalam mencapai tujuan organisasi',
                'cukup' => 'Mematuhi kebijakan yang ada dan berkomitmen dalam mencapai tujuan organisasi',
                'kurang' =>'Kurang mematuhi kebijakan yang ada dan tidak berkomitmen maupun berkonstribusi dalam mencapai tujuan organisasi',
                'kurang_sekali' => 'Tidak mematuhi kebijakan yang telah ditetapkan serta tidak berkomitmen maupun konstribusi dalam mencapai tujuan organisasi'
            ],
            [
                'id_perilaku'=>5,
                'nama_kpi' => 'Adaptif',
                'ekselen' => 'Inovasi yang dilakukan berjalan secara konsisten cepat dan tepat sehingga tercipta perubahan menjadi lebih baik',
                'baik' => 'Inovasi yang dijalankan sedang berjalan sehingga akan tercipatanya perubahan yang lebih baik',
                'cukup' => 'Inovasi atau perubahan yang dijalankan belum tejadi secara signifikan namum para pekerja sudah proaktif dalam menggerakkan perubahan',
                'kurang' =>'Kurang melakukan inovasi yang mengarah ke perubahan yang lebih baik, tetapi pekerja sudah bersifat proaktif dalam menggerakkan perubahan',
                'kurang_sekali' => 'Kurang terdapat inovasi atau perubahan dan pekerja memiliki sifat kurang proaktif dalam mendukung terjadinya perubahan sehingga tercapainya perubahan dalam perusahaan kearah yang lebih baik menjadi terhambat'
            ],
            [
                'id_perilaku'=>6,
                'nama_kpi' => 'Kolaboratif',
                'ekselen' => 'Mampu menjalin kerja sama yang baik dengan rekan kerja di lembaga berbeda sehingga pekerjaan tuntas. Sering membantu rekan kerja kesulitan, sering mengambil peran pemimpin dalam tim dan mampu mendelegasikan tugas dengan sangat baik sehingga tujuan bersama tercapai dan memberikan nilai tambah bagi perusahaan',
                'baik' => 'Mampu menjalin kerja sama yang baik dengan rekan kerja satu lembaga sehingga pekerjaan tuntas. Sering membantu rekan kerja yang kesulitan dalam pekerjaan. Kadang-kadang mengambil peran pemimpin dalam tim dan mampu mendelegasikan tugas sesuai dengan cukup',
                'cukup' => 'Mampu menjalin kerja sama yang baik dengan rekan kerja satu lembaga sehingga pekerjaan tuntas. Kadang-kadang berinisiatif membantu rekan kerja yang kesulitan dalam pekerjaan',
                'kurang' =>'Kurang mampu bekerja sama dengan rekan kerja satu lembaga. Kadang-kadang kesulitan dalam mencapai kesepakatan dengan rekan kerja sehingga pekerjaan terhambat',
                'kurang_sekali' => 'Kurang mampu bekerja sama dengan rekan kerja satu lembaga, sering mengalami kesulitan dalam mencapai kesepakatan dengan rekan kerja yang menyebabkan pekerjaan terhambat'
            ]
        ];
        foreach ($perilaku as $key => $value) {
            KpiPerilaku::create($value);
        }
    }
}
