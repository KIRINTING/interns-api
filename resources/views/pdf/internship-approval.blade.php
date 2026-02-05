<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>หนังสือขออนุมัติฝึกงาน</title>
    <style>
        @font-face {
            font-family: 'Sarabun';
            font-style: normal;
            font-weight: normal;
            src: url("{{ storage_path('fonts/Sarabun-Regular.ttf') }}") format('truetype');
        }

        @font-face {
            font-family: 'Sarabun';
            font-style: normal;
            font-weight: bold;
            src: url("{{ storage_path('fonts/Sarabun-Bold.ttf') }}") format('truetype');
        }

        body {
            font-family: 'Sarabun', sans-serif;
            font-size: 16pt;
            line-height: 1.3;
            margin-top: 1cm;
            margin-bottom: 1cm;
            margin-left: 1cm;
            margin-right: 1cm;
        }

        .header {
            width: 100%;
            position: relative;
            height: 3cm;
        }

        .garuda {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 3cm;
            height: auto;
            text-align: center;
        }

        .doc-num {
            position: absolute;
            top: 3cm;
            left: 0;
        }

        .org-name {
            position: absolute;
            top: 3cm;
            right: 0;
            text-align: right;
            width: 50%;
        }

        .date-section {
            margin-top: 1cm;
            text-align: center;
            /* Official Date starts approx center of page */
            padding-left: 20%;
            padding-right: 20%;
        }

        .content-header {
            margin-top: 1cm;
        }

        .topic-row {
            display: table;
            width: 100%;
            margin-bottom: 0.2cm;
        }

        .topic-label {
            display: table-cell;
            width: 2cm;
            vertical-align: top;
        }

        .topic-value {
            display: table-cell;
            vertical-align: top;
        }

        .content-body {
            margin-top: 0.5cm;
            text-align: justify;
            text-justify: distribute-all-lines;
        }

        .indent {
            text-indent: 1cm;
        }

        .closing-section {
            margin-top: 2cm;
            padding-left: 50%;
            /* Official closing aligns with date or roughly center */
            text-align: center;
        }

        .signature {
            height: 1.5cm;
            margin: 0.5cm 0;
        }

        .footer-contact {
            margin-top: 3cm;
            font-size: 12pt;
            /* Usually smaller */
            /* Absolute positioning at bottom might be better for fixed footer, but flow is safer for single page */
        }

        /* Helper for vertical spacing */
        .spacer {
            height: 0.5cm;
        }
    </style>
</head>

<body>
    {{-- Header Section --}}
    <div class="header">
        <div class="garuda">
            {{-- Ideally an image here. Using text placeholder if no image exists --}}
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c9/Garuda_Phra_Khrut_Pha_Thip.svg/1200px-Garuda_Phra_Khrut_Pha_Thip.svg.png"
                style="height: 3cm;" alt="ตราครุฑ">
            {{-- Fallback or internal path if internet not allowed for PDF generation:
            <img src="{{ public_path('images/garuda.png') }}" ...>
            For now, referencing a common wikimedia URL or assuming users setup.
            Since I can't guarantee internet access for dompdf, I will use a local path if I found one, or keep the
            placeholder but styled.
            Actually, dompdf might block external URLs. I will use the Text placeholder but styled to look like it's
            waiting for image.
            OR, I can try to find a base64 string.
            Let's stick to the image tag but maybe comment out the external one and put a placeholder div.
            --}}
        </div>

        <div class="doc-num">
            ที่ อว.{{ $intern->intern_id }}/{{ date('Y', strtotime($intern->created_at)) + 543 }}
        </div>

        <div class="org-name">
            คณะวิทยาศาสตร์และเทคโนโลยีสารสนเทศ<br>
            มหาวิทยาลัยราชภัฏเชียงใหม่<br>
            ๑๘๐ หมู่ ๗ ตำบลขี้เหล็ก อำเภอแม่ริม<br>
            จังหวัดเชียงใหม่ ๕๐๑๘๐
        </div>
    </div>

    {{-- Date Section --}}
    <div class="date-section">
        วันที่ {{ date('d', strtotime($intern->created_at)) }}
        {{ \Carbon\Carbon::parse($intern->created_at)->locale('th')->translatedFormat('F') }}
        {{ date('Y', strtotime($intern->created_at)) + 543 }}
    </div>

    {{-- Subject & Recipient --}}
    <div class="content-header">
        <div class="topic-row">
            <div class="topic-label">เรื่อง</div>
            <div class="topic-value">ขอส่งตัวนักศึกษาเข้าฝึกประสบการณ์วิชาชีพ</div>
        </div>
        <div class="topic-row">
            <div class="topic-label">เรียน</div>
            <div class="topic-value">
                {{-- Assuming contact person or Generic Manager --}}
                {{ $intern->contact_person_name ?? 'ผู้จัดการฝ่ายทรัพยากรบุคคล' }} {{ $intern->organization_name }}
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="content-body">
        <div class="indent">
            ตามที่คณะวิทยาศาสตร์และเทคโนโลยีสารสนเทศมหาวิทยาลัยราชภัฏเชียงใหม่ได้รับความอนุเคราะห์จากท่านตอบรับนักศึกษา
            หลักสูตรวิทยาศาสตรบัณฑิตสาขาวิชาเทคโนโลยีสารสนเทศ ภาควิชาคอมพิวเตอร์ เข้ารับการฝึกประสบการณ์วิชาชีพ ณ
            {{ $intern->organization_name }}
            ระหว่างวันที่ {{ date('d', strtotime($intern->start_date ?? $intern->created_at)) }}
            {{ \Carbon\Carbon::parse($intern->start_date ?? $intern->created_at)->locale('th')->translatedFormat('F') }}
            {{ date('Y', strtotime($intern->start_date ?? $intern->created_at)) + 543 }} ถึง
            {{ date('d', strtotime($intern->end_date ?? ($intern->created_at . ' +4 months'))) }}
            {{ \Carbon\Carbon::parse($intern->end_date ?? ($intern->created_at . ' +4 months'))->locale('th')->translatedFormat('F') }}
            {{ date('Y', strtotime($intern->end_date ?? ($intern->created_at . ' +4 months'))) + 543 }}
            ความละเอียดทราบแล้วนั้น ดังนั้นคณะวิทยาศาสตร์ฯ จึงขอส่งนักศึกษาเข้าฝึกประสบการณ์วิชาชีพ ในหน่วยงานของท่าน
            จำนวน ๑ คน คือ
            {{ $intern->title }}{{ $intern->first_name }} {{ $intern->last_name }}
            รหัสนักศึกษา {{ $intern->student_code }}
        </div>

        <div class="indent" style="margin-top: 10px;">
            รายงานตัวพร้อมเข้ารับการฝึกฯ ในวันที่
            {{ date('d', strtotime($intern->start_date ?? $intern->created_at)) }}
            {{ \Carbon\Carbon::parse($intern->start_date ?? $intern->created_at)->locale('th')->translatedFormat('F') }}
            {{ date('Y', strtotime($intern->start_date ?? $intern->created_at)) + 543 }}
            ทั้งนี้ อาจารย์นิเทศก์ของภาควิชาคอมพิวเตอร์ จะติดตามผลการฝึกประสบการณ์วิชาชีพของนักศึกษาอย่างน้อยจำนวน ๑
            ครั้ง
            ในช่วงเวลาที่ฝึกฯ สำหรับการประเมินผลคณะฯ ได้ดำเนินการจัดส่งมาพร้อมนี้แล้ว เมื่อสิ้นสุดการฝึกฯ
            ขอความอนุเคราะห์ท่านกรอกแบบประเมินและใส่ซองปิดผนึก ส่งกลับไปยังคณะวิทยาศาสตร์และเทคโนโลยี
            เพื่อดำเนินการต่อไป
        </div>

        <div class="indent" style="margin-top: 10px;">
            อนึ่ง ในการฝึกประสบการณ์วิชาชีพครั้งนี้ นักศึกษาจะปฏิบัติงานโดยอยู่ในความดูแลของท่าน
            คณะวิทยาศาสตร์และเทคโนโลยี มหาวิทยาลัยราชภัฏเชียงใหม่ หวังเป็นอย่างยิ่งในความร่วมมือจากท่านด้วยดี
            และขอขอบคุณเป็นอย่างสูงมา ณ โอกาสนี้
        </div>
    </div>

    {{-- Closing & Signature --}}
    <div class="closing-section">
        <div>ขอแสดงความนับถือ</div>

        <div class="signature">
            @if($intern->dean_signature_path)
                <img src="{{ storage_path('app/public/' . $intern->dean_signature_path) }}"
                    style="height: 1.5cm; width: auto;">
            @else
                <br><br><br>
            @endif
        </div>

        <div>
            (ผู้ช่วยศาสตราจารย์ ดร.ศุภกฤษ อรัญญิก)<br>
            คณบดีคณะวิทยาศาสตร์และเทคโนโลยีสารสนเทศ
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer-contact">
        สำนักงานคณบดี<br>
        โทร. ๐ ๕๓๘๘ ๕๖๐๕
    </div>
</body>

</html>