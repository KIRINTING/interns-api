<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Info;
use Carbon\Carbon;

class InfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $infos = [
            [
                'info_id' => 'INFO001',
                'title' => 'ประกาศผลการคัดเลือกสถานประกอบการ',
                'category' => 'Announce',
                'detail' => 'ประกาศรายชื่อนักศึกษาที่ผ่านการคัดเลือกเข้าฝึกงาน ณ สถานประกอบการต่างๆ สามารถตรวจสอบรายชื่อได้ที่บอร์ดประกาศหน้าภาควิชา',
                'due_date' => Carbon::now()->addDays(7),
            ],
            [
                'info_id' => 'INFO002',
                'title' => 'กำหนดการปฐมนิเทศนักศึกษาฝึกงาน',
                'category' => 'Important',
                'detail' => 'ขอให้นักศึกษาทุกคนเข้าร่วมการปฐมนิเทศในวันที่ 15 กุมภาพันธ์ 2569 เวลา 09:00 น. ณ ห้องประชุมใหญ่ อาคาร 1 ชั้น 3',
                'due_date' => Carbon::now()->addDays(13),
            ],
            [
                'info_id' => 'INFO003',
                'title' => 'ตัวอย่างการเขียนรายงานฝึกงาน',
                'category' => 'Guide',
                'detail' => 'สามารถดาวน์โหลดรูปแบบรายงานได้ที่เมนู เอกสาร หรือติดต่อขอรับที่ห้องภาควิชา รายงานต้องส่งภายในวันที่ 30 เมษายน 2569',
                'due_date' => Carbon::now()->addDays(87),
            ],
            [
                'info_id' => 'INFO004',
                'title' => 'การประเมินผลการฝึกงาน',
                'category' => 'Important',
                'detail' => 'นักศึกษาจะได้รับการประเมินจากพี่เลี้ยงในสถานประกอบการ และอาจารย์นิเทศก์ กรุณาเตรียมเอกสารประกอบการประเมินให้พร้อม',
                'due_date' => Carbon::now()->addDays(45),
            ],
            [
                'info_id' => 'INFO005',
                'title' => 'แนวทางการเลือกสถานประกอบการ',
                'category' => 'Guide',
                'detail' => 'คู่มือแนะนำการเลือกสถานประกอบการที่เหมาะสม พร้อมเทคนิคการสัมภาษณ์และการเตรียมตัวก่อนเข้าฝึกงาน',
                'due_date' => Carbon::now()->subDays(5),
            ],
        ];

        foreach ($infos as $info) {
            Info::create($info);
        }
    }
}
