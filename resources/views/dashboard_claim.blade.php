@extends('layouts.app')

<style>
  .card-claim {
    border: none;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease-in-out;
    border-radius: 1rem;
  }

  .card-claim:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
  }

  /* 💵 1 - Income (เขียวมินต์พาสเทล) */
  .card-claim.card1 {
    background: linear-gradient(135deg, #d0d9ff, #ffffff);
  }

  /* 💸 2 - Claim (เหลืองทองพาสเทล) */
  .card-claim.card2 {
    background: linear-gradient(135deg, #dcedc8, #ffffff);
  }

  /* 📊 3 - Total Income (ฟ้าเทอร์ควอยซ์พาสเทล) */
  .card-claim.card3 {
    background: linear-gradient(135deg, #b2ebf2, #ffffff);
  }

  .text-card1 {
    color: #d0d9ff !important;
  }
  .text-card2 {
    color: #b2dfdb !important;
  }
  .text-card3 {
    color: #b2ebf2 !important;
  }
</style>

@section('title', 'Dashboard | AOPOD')

@section('content')

  <!-- HERO -->
  <header class="py-4">
    <div class="container-fluid">      
        <div class="row g-4 align-items-center">
          <div class="col-lg-9">          
            <h4 class="text-success mb-2"><strong>Amnatcharoen One Province One Data : AOPOD</strong></h4>          
          </div>
          {{-- ขวาสุด: select + ปุ่ม ติดกันและชิดขวา --}}
          <div class="col-lg-3 d-flex justify-content-lg-end">
            <span class="text-secondary my-1">
                วันที่ {{ \Carbon\Carbon::now()->locale('th')->isoFormat('D MMM YYYY เวลา H:mm') }} น.&nbsp;&nbsp;
            </span>
            <button type="button" class="btn btn-sm btn-outline-success" onclick="location.reload();">
              <i class="bi bi-arrow-clockwise"></i> โหลดใหม่
            </button>
          </div>
        </div>
    </div>
  </header>
  
  <!-- SUMMARY (3 blocks, no foreach) ------------------------------------------------------------------------------------>
  <section id="summary" class="pb-2">
    <div class="container-fluid">
      @php
        $fmtInt   = fn($n) => number_format((int)($n ?? 0));
        $fmtMoney = fn($n) => number_format((float)($n ?? 0), 2);
      @endphp

      <div class="row g-3">      

        {{--  PP Fee Schedule : ครั้ง -------------------------------------------------------------------------------}}
        <div class="col-12 col-sm-6 col-xl-4">
          <a href="#" data-bs-toggle="modal" data-bs-target="#PPFSDetailModal" class="text-decoration-none text-dark">
            <div class="card-claim card1 p-3 h-100">   <!-- 👈 เพิ่มตรงนี้ -->
              <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="mb-0 text-primary"><strong>PP Fee Schedule</strong></h6>
                <span><i class="bi bi-diagram-3 fs-5 text-warning"></i></span>
              </div>
              <div class="d-flex align-items-end gap-4">
                <div class="text-end">
                  <div class="small text-secondary text-center">Visit</div>
                  <div class="fw-bold" style="font-size:1.5rem;">
                    {{ $fmtInt($visit_ppfs ?? 0) }}
                  </div>
                </div> 
                <div class="vr d-none d-sm-block"></div>
                <div class="text-end">
                  <div class="small text-secondary text-center">บาท</div>
                  <div class="fw-bold text-success" style="font-size:1.5rem;">
                    {{ $fmtMoney($inc_ppfs ?? 0) }}
                  </div>
                </div>
                <div class="vr d-none d-sm-block"></div>
                <div class="text-end">
                  <div class="small text-secondary text-center">Claim</div>
                  <div class="fw-bold" style="font-size:1.5rem;">
                    0
                  </div>
                </div>
                <div class="vr d-none d-sm-block"></div>
                <div class="text-end">
                  <div class="small text-secondary text-center">บาท</div>
                  <div class="fw-bold text-success" style="font-size:1.5rem;">
                    0
                  </div>
                </div>
              </div>
            </div>
          </a>
        </div>
        {{-- Modal แสดงรายละเอียด รพ. (โทนน้ำเงินพาสเทลเข้ม / modal-lg) --}}
        <div class="modal fade" id="PPFSDetailModal" tabindex="-1" aria-labelledby="hospitalDetailLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-3" style="background-color:#f5f8fc;">

              <!-- Header -->
              <div class="modal-header text-white rounded-top-3"
                  style="background: linear-gradient(135deg, #2f6fb6, #4b8edc);">
                <h5 class="modal-title fw-bold" id="hospitalDetailLabel">
                  <i class="bi bi-clipboard-data me-2"></i> PP Fee Schedule
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              <!-- Body -->
              <div class="modal-body py-3">
                <table class="table table-hover align-middle shadow-sm rounded-3 overflow-hidden mb-0"
                      style="background-color: #ffffff; border-radius: 0.75rem;">
                  <thead style="background-color:#d9e8fb;">
                    <tr class="text-center text-primary fw-semibold">
                      <th>รหัส</th>
                      <th>ชื่อโรงพยาบาล</th>
                      <th>Visit</th>
                      <th>ค่ารักษารวม (บาท)</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($hospitalSummary as $h)
                      <tr>
                        <td align="right" class="text-secondary">{{ $h->hospcode }}</td>
                        <td>
                          <span class="fw-semibold text-dark">{{ $h->hospname }}</span><br>
                          <small class="text-muted">
                            {{ \Carbon\Carbon::parse($h->last_updated_at)->locale('th')->isoFormat('D MMM YYYY H:mm') }} น.
                          </small>
                        </td>
                        <td align="right" class="text-primary">{{ number_format($h->visit_ppfs) }}</td>
                        <td align="right" class="fw-bold text-success">{{ number_format($h->inc_ppfs,2) }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              <!-- Footer -->
              <div class="modal-footer" style="background-color:#eef4fb;">
                <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm"
                        style="background-color:#3e7cc1; border-color:#3e7cc1;"
                        data-bs-dismiss="modal">
                  ปิด
                </button>
              </div>
            </div>
          </div>
        </div>

        {{-- UC-บริการเฉพาะ CR : ครั้ง | บาท ---------------------------------------------------------------------------------------}}
        <div class="col-12 col-sm-6 col-xl-4">
          <a href="#" data-bs-toggle="modal" data-bs-target="#CrDetailModal" class="text-decoration-none text-dark">
            <div class="card-claim card2 p-3 h-100">   <!-- 👈 เพิ่มตรงนี้ -->
              <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="mb-0 text-primary"><strong>UC-บริการเฉพาะ CR </strong></h6>
                <span><i class="bi bi-hospital fs-5 text-danger"></i> </span>
              </div>
              <div class="d-flex align-items-end gap-4">
                <div class="text-end">
                  <div class="small text-secondary text-center">visit</div>
                  <div class="fw-bold " style="font-size:1.5rem;">
                    {{ $fmtInt($visit_ucs_cr ?? 0) }}
                  </div>
                </div>
                <div class="vr d-none d-sm-block"></div>
                <div class="text-end">
                  <div class="small text-secondary text-center">บาท</div>
                  <div class="fw-bold text-success" style="font-size:1.5rem;">
                    {{ $fmtMoney($inc_uccr ?? 0) }}
                  </div>
                </div>
                <div class="vr d-none d-sm-block"></div>
                <div class="text-end">
                  <div class="small text-secondary text-center">Claim</div>
                  <div class="fw-bold" style="font-size:1.5rem;">
                    0
                  </div>
                </div>
                <div class="vr d-none d-sm-block"></div>
                <div class="text-end">
                  <div class="small text-secondary text-center">บาท</div>
                  <div class="fw-bold text-success" style="font-size:1.5rem;">
                    0
                  </div>
                </div>
              </div>
            </div>
            </a>
        </div>
        {{-- Modal แสดงรายละเอียด รพ. (โทนน้ำเงินพาสเทลเข้ม / modal-lg) --}}
        <div class="modal fade" id="CrDetailModal" tabindex="-1" aria-labelledby="hospitalDetailLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-3" style="background-color:#f5f8fc;">

              <!-- Header -->
              <div class="modal-header text-white rounded-top-3"
                  style="background: linear-gradient(135deg, #2f6fb6, #4b8edc);">
                <h5 class="modal-title fw-bold" id="hospitalDetailLabel">
                  <i class="bi bi-activity me-2"></i> UC - บริการเฉพาะ (CR)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              <!-- Body -->
              <div class="modal-body py-3">
                <table class="table table-hover align-middle shadow-sm rounded-3 overflow-hidden mb-0"
                      style="background-color: #ffffff; border-radius: 0.75rem;">
                  <thead style="background-color:#d9e8fb;">
                    <tr class="text-center text-primary fw-semibold">
                      <th>รหัส</th>
                      <th>ชื่อโรงพยาบาล</th>
                      <th>Visit</th>
                      <th>ค่ารักษารวม (บาท)</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($hospitalSummary as $h)
                      <tr>
                        <td align="right" class="text-secondary">{{ $h->hospcode }}</td>
                        <td>
                          <span class="fw-semibold text-dark">{{ $h->hospname }}</span><br>
                          <small class="text-muted">
                            {{ \Carbon\Carbon::parse($h->last_updated_at)->locale('th')->isoFormat('D MMM YYYY H:mm') }} น.
                          </small>
                        </td>
                        <td align="right" class="text-primary">{{ number_format($h->visit_ucs_cr) }}</td>
                        <td align="right" class="fw-bold text-success">{{ number_format($h->inc_uccr,2) }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              <!-- Footer -->
              <div class="modal-footer" style="background-color:#eef4fb;">
                <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm"
                        style="background-color:#3e7cc1; border-color:#3e7cc1;"
                        data-bs-dismiss="modal">
                  ปิด
                </button>
              </div>
            </div>
          </div>
        </div>

        {{-- UC-สมุนไพร 32 รายการ : ครั้ง | บาท -----------------------------------------------------------------------------------}}
        <div class="col-12 col-sm-6 col-xl-4">
          <a href="#" data-bs-toggle="modal" data-bs-target="#HerbDetailModal" class="text-decoration-none text-dark">
            <div class="card-claim card3 p-3 h-100">   <!-- 👈 เพิ่มตรงนี้ -->
              <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="mb-0 text-primary"><strong>UC-สมุนไพร 32 รายการ</strong></h6>
                <span><i class="bi bi-capsule fs-5 text-danger"></i></span></span>
              </div>
              <div class="d-flex align-items-end gap-4">
                <div class="text-end">
                  <div class="small text-secondary text-center">visit</div>
                  <div class="fw-bold" style="font-size:1.5rem;">
                    {{ $fmtInt($visit_ucs_herb ?? 0) }}
                  </div>
                </div>
                <div class="vr d-none d-sm-block"></div>
                <div class="text-end">
                  <div class="small text-secondary text-center">บาท</div>
                  <div class="fw-bold text-success" style="font-size:1.5rem;">
                    {{ $fmtMoney($inc_herb ?? 0) }}
                  </div>
                </div>
                <div class="vr d-none d-sm-block"></div>
                <div class="text-end">
                  <div class="small text-secondary text-center">Claim</div>
                  <div class="fw-bold" style="font-size:1.5rem;">
                    0
                  </div>
                </div>
                <div class="vr d-none d-sm-block"></div>
                <div class="text-end">
                  <div class="small text-secondary text-center">บาท</div>
                  <div class="fw-bold text-success" style="font-size:1.5rem;">
                    0
                  </div>
                </div>
              </div>
            </div>
          </a>
        </div>   
        {{-- Modal แสดงรายละเอียด รพ. (โทนน้ำเงินพาสเทลเข้ม / modal-lg) --}}
        <div class="modal fade" id="HerbDetailModal" tabindex="-1" aria-labelledby="hospitalDetailLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-3" style="background-color:#f5f8fc;">

              <!-- Header -->
              <div class="modal-header text-white rounded-top-3"
                  style="background: linear-gradient(135deg, #2f6fb6, #4b8edc);">
                <h5 class="modal-title fw-bold" id="hospitalDetailLabel">
                  <i class="bi bi-capsule me-2"></i> UC - สมุนไพร 32 รายการ
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              <!-- Body -->
              <div class="modal-body py-3">
                <table class="table table-hover align-middle shadow-sm rounded-3 overflow-hidden mb-0"
                      style="background-color: #ffffff; border-radius: 0.75rem;">
                  <thead style="background-color:#d9e8fb;">
                    <tr class="text-center text-primary fw-semibold">
                      <th>รหัส</th>
                      <th>ชื่อโรงพยาบาล</th>
                      <th>Visit</th>
                      <th>ค่ารักษารวม (บาท)</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($hospitalSummary as $h)
                      <tr>
                        <td align="right" class="text-secondary">{{ $h->hospcode }}</td>
                        <td>
                          <span class="fw-semibold text-dark">{{ $h->hospname }}</span><br>
                          <small class="text-muted">
                            {{ \Carbon\Carbon::parse($h->last_updated_at)->locale('th')->isoFormat('D MMM YYYY H:mm') }} น.
                          </small>
                        </td>
                        <td align="right" class="text-primary">{{ number_format($h->visit_ucs_herb) }}</td>
                        <td align="right" class="fw-bold text-success">{{ number_format($h->inc_herb,2) }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>

              <!-- Footer -->
              <div class="modal-footer" style="background-color:#eef4fb;">
                <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm"
                        style="background-color:#3e7cc1; border-color:#3e7cc1;"
                        data-bs-dismiss="modal">
                  ปิด
                </button>
              </div>
            </div>
          </div>
        </div>

      {{------------------------------------------------------------------------------------------------------------}}

      </div>
    </div>  
  </section>

<br>
<hr>

  {{-- เลือกปีงบประมาณ ----------------------------------------------------------------------------------------------------------}}
  <section id="summary" class="pb-2">
      <div class="container-fluid">
        <form method="POST" action="{{ url('web/opd') }}" enctype="multipart/form-data">
        @csrf
          <div class="row g-4 align-items-center">
            <div class="col-lg-9">          
              <h6 class="text-success mb-2"><strong></strong></h6>          
            </div>
            {{-- ขวาสุด: select + ปุ่ม ติดกันและชิดขวา --}}
            <div class="col-lg-3 d-flex justify-content-lg-end">
              <div class="d-flex align-items-center gap-2">
                <select class="form-select" name="budget_year">
                  @foreach ($budget_year_select as $row)
                    <option value="{{ $row->LEAVE_YEAR_ID }}"
                      {{ (int)$budget_year === (int)$row->LEAVE_YEAR_ID ? 'selected' : '' }}>
                      {{ $row->LEAVE_YEAR_NAME }}
                    </option>
                  @endforeach
                </select>
                <button type="submit" class="btn btn-primary">{{ __('ค้นหา') }}</button>
              </div>
            </div>
          </div>
        </form>
      </div>
  </section>

  {{-- ข้อมูลบริการ----------------------------------------------------------------------------------------------------------}}
  <section id="hospital" class="pb-2">
    <div class="container-fluid">
    
      <!-- NAV PILLS -->
      <ul class="nav nav-pills overflow-auto flex-nowrap" id="hospPills" role="tablist">
        <li class="nav-item me-2" role="presentation">
          <button class="nav-link active" id="tab-10985" data-bs-toggle="pill" data-bs-target="#pane-10985" type="button" role="tab" aria-controls="pane-10985" aria-selected="true">
            รพ.ชานุมาน
          </button>
        </li>
        <li class="nav-item me-2" role="presentation">
          <button class="nav-link" id="tab-10986" data-bs-toggle="pill" data-bs-target="#pane-10986" type="button" role="tab" aria-controls="pane-10986" aria-selected="false">
            รพ.ปทุมราชวงศา
          </button>
        </li>
        <li class="nav-item me-2" role="presentation">
          <button class="nav-link" id="tab-10987" data-bs-toggle="pill" data-bs-target="#pane-10987" type="button" role="tab" aria-controls="pane-10987" aria-selected="false">
            รพ.พนา
          </button>
        </li>
        <li class="nav-item me-2" role="presentation">
          <button class="nav-link" id="tab-10988" data-bs-toggle="pill" data-bs-target="#pane-10988" type="button" role="tab" aria-controls="pane-10988" aria-selected="false">
            รพ.เสนางคนิคม
          </button>
        </li>
        <li class="nav-item me-2" role="presentation">
          <button class="nav-link" id="tab-10989" data-bs-toggle="pill" data-bs-target="#pane-10989" type="button" role="tab" aria-controls="pane-10989" aria-selected="false">
            รพ.หัวตะพาน
          </button>
        </li>
        <li class="nav-item me-2" role="presentation">
          <button class="nav-link" id="tab-10990" data-bs-toggle="pill" data-bs-target="#pane-10990" type="button" role="tab" aria-controls="pane-10990" aria-selected="false">
            รพ.ลืออำนาจ
          </button>
        </li>
        <li class="nav-item me-2" role="presentation">
          <button class="nav-link" id="tab-10703" data-bs-toggle="pill" data-bs-target="#pane-10703" type="button" role="tab" aria-controls="pane-10703" aria-selected="false">
            รพ.อำนาจเจริญ
          </button>
        </li>
      </ul>

      <!-- TAB PANES -->
      <div class="tab-content mt-3" id="hospPillsContent">

        <!-- 10985 OPD -->
        <div class="tab-pane fade show active" id="pane-10985" role="tabpanel" aria-labelledby="tab-10985" tabindex="0">
          <div class="glass p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6>[10985] ข้อมูลบริการผู้ป่วยนอก OPD โรงพยาชานุมาน ปีงบประมาณ {{$budget_year}}</h6>
              <span class="text-secondary small">Update {{$update_at10985}}</span>              
            </div>
            <div class="table-responsive">
              <table id="table10985" class="table table-bordered table-striped my-3" width ="100%">
                <thead class="table-light">
                  <tr class="table-primary">
                    <th class="text-center" rowspan="2" width ="4%">เดือน</th>
                    <th class="text-center" colspan="7">ทั้งหมด</th>
                    <th class="text-center" colspan="4">UCS ใน CUP</th> 
                    <th class="text-center" colspan="4">UCS ในจังหวัด</th>
                    <th class="text-center" colspan="4">UCS นอกจังหวัด</th>       
                    <th class="text-center" colspan="4">OFC ข้าราชการ</th>  
                    <th class="text-center" colspan="4">BKK กทม.</th>
                    <th class="text-center" colspan="4">BMT ขสมก.</th>
                    <th class="text-center" colspan="4">SSS ประกันสังคม</th>
                    <th class="text-center" colspan="4">LGO อปท.</th>
                    <th class="text-center" colspan="4">FSS ต่างด้าว</th>
                    <th class="text-center" colspan="4">STP Stateless</th>
                    <th class="text-center" colspan="4">ชำระเงิน/พรบ.</th>                 
                  </tr>    
                  <tr class="table-primary">            
                    <td class="text-center text-primary">HN Total</td>
                    <td class="text-center text-primary">Visit Total</td>
                    <td class="text-center text-primary">Visit OP</td>
                    <td class="text-center text-primary">Visit PP</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                  </tr>    
                </thead>
                <tbody>
                  <?php $count = 1 ; ?> 
                  <?php $sum_hn_total = 0 ; ?> 
                  <?php $sum_visit_total = 0 ; ?>   
                  <?php $sum_visit_total_op = 0 ; ?>  
                  <?php $sum_visit_total_pp = 0 ; ?> 
                  <?php $sum_inc_total = 0 ; ?>  
                  <?php $sum_inc_lab_total = 0 ; ?>
                  <?php $sum_inc_drug_total = 0 ; ?> 
                  <?php $sum_visit_ucs_incup = 0 ; ?>  
                  <?php $sum_inc_ucs_incup = 0 ; ?>  
                  <?php $sum_inc_lab_ucs_incup = 0 ; ?>  
                  <?php $sum_inc_drug_ucs_incup = 0 ; ?>  
                  <?php $sum_visit_ucs_inprov = 0 ; ?>  
                  <?php $sum_inc_ucs_inprov = 0 ; ?>  
                  <?php $sum_inc_lab_ucs_inprov = 0 ; ?> 
                  <?php $sum_inc_drug_ucs_inprov = 0 ; ?>   
                  <?php $sum_visit_ucs_outprov = 0 ; ?>  
                  <?php $sum_inc_ucs_outprov = 0 ; ?>
                  <?php $sum_inc_lab_ucs_outprov = 0 ; ?>
                  <?php $sum_inc_drug_ucs_outprov = 0 ; ?>  
                  <?php $sum_visit_ofc = 0 ; ?>  
                  <?php $sum_inc_ofc = 0 ; ?>
                  <?php $sum_inc_lab_ofc = 0 ; ?>
                  <?php $sum_inc_drug_ofc = 0 ; ?>
                  <?php $sum_visit_bkk = 0 ; ?>  
                  <?php $sum_inc_bkk = 0 ; ?>
                  <?php $sum_inc_lab_bkk = 0 ; ?>
                  <?php $sum_inc_drug_bkk = 0 ; ?> 
                  <?php $sum_visit_bmt = 0 ; ?>  
                  <?php $sum_inc_bmt = 0 ; ?>
                  <?php $sum_inc_lab_bmt = 0 ; ?>
                  <?php $sum_inc_drug_bmt = 0 ; ?>  
                  <?php $sum_visit_sss = 0 ; ?>  
                  <?php $sum_inc_sss = 0 ; ?>
                  <?php $sum_inc_lab_sss = 0 ; ?>
                  <?php $sum_inc_drug_sss = 0 ; ?> 
                  <?php $sum_visit_lgo = 0 ; ?>  
                  <?php $sum_inc_lgo = 0 ; ?>
                  <?php $sum_inc_lab_lgo = 0 ; ?>
                  <?php $sum_inc_drug_lgo = 0 ; ?> 
                  <?php $sum_visit_fss = 0 ; ?>  
                  <?php $sum_inc_fss = 0 ; ?>
                  <?php $sum_inc_lab_fss = 0 ; ?>
                  <?php $sum_inc_drug_fss = 0 ; ?> 
                  <?php $sum_visit_stp = 0 ; ?>  
                  <?php $sum_inc_stp = 0 ; ?>
                  <?php $sum_inc_lab_stp = 0 ; ?>
                  <?php $sum_inc_drug_stp = 0 ; ?>
                  <?php $sum_visit_pay = 0 ; ?>  
                  <?php $sum_inc_pay = 0 ; ?>
                  <?php $sum_inc_lab_pay = 0 ; ?>
                  <?php $sum_inc_drug_pay = 0 ; ?>  
                  @foreach($total_10985 as $row) 
                  <tr>
                    <td align="center"width ="4%">{{ $row->month }}</td>
                    <td align="right">{{ number_format($row->hn_total) }}</td>
                    <td align="right">{{ number_format($row->visit_total) }}</td>
                    <td align="right">{{ number_format($row->visit_total_op) }}</td>
                    <td align="right">{{ number_format($row->visit_total_pp) }}</td>
                    <td align="right">{{ number_format($row->inc_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_total,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ucs_incup) }}</td>
                    <td align="right">{{ number_format($row->inc_ucs_incup,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ucs_incup,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ucs_incup,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ucs_inprov) }}</td>
                    <td align="right">{{ number_format($row->inc_ucs_inprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ucs_inprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ucs_inprov,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ucs_outprov) }}</td>
                    <td align="right">{{ number_format($row->inc_ucs_outprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ucs_outprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ucs_outprov,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ofc) }}</td>
                    <td align="right">{{ number_format($row->inc_ofc,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ofc,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ofc,2) }}</td>
                    <td align="right">{{ number_format($row->visit_bkk) }}</td>
                    <td align="right">{{ number_format($row->inc_bkk,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_bkk,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_bkk,2) }}</td>
                    <td align="right">{{ number_format($row->visit_bmt) }}</td>
                    <td align="right">{{ number_format($row->inc_bmt,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_bmt,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_bmt,2) }}</td>
                    <td align="right">{{ number_format($row->visit_sss) }}</td>
                    <td align="right">{{ number_format($row->inc_sss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_sss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_sss,2) }}</td>
                    <td align="right">{{ number_format($row->visit_lgo) }}</td>
                    <td align="right">{{ number_format($row->inc_lgo,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_lgo,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_lgo,2) }}</td>
                    <td align="right">{{ number_format($row->visit_fss) }}</td>
                    <td align="right">{{ number_format($row->inc_fss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_fss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_fss,2) }}</td>
                    <td align="right">{{ number_format($row->visit_stp) }}</td>
                    <td align="right">{{ number_format($row->inc_stp,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_stp,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_stp,2) }}</td>
                    <td align="right">{{ number_format($row->visit_pay) }}</td>
                    <td align="right">{{ number_format($row->inc_pay,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_pay,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_pay,2) }}</td>
                  </tr>
                  <?php $count++; ?>
                  <?php $sum_hn_total += $row->hn_total ; ?>
                  <?php $sum_visit_total += $row->visit_total ; ?>
                  <?php $sum_visit_total_op += $row->visit_total_op ; ?>
                  <?php $sum_visit_total_pp += $row->visit_total_pp ; ?>
                  <?php $sum_inc_total += $row->inc_total ; ?>
                  <?php $sum_inc_lab_total += $row->inc_lab_total ; ?>
                  <?php $sum_inc_drug_total += $row->inc_drug_total ; ?>
                  <?php $sum_visit_ucs_incup += $row->visit_ucs_incup ; ?>
                  <?php $sum_inc_ucs_incup += $row->inc_ucs_incup ; ?>
                  <?php $sum_inc_lab_ucs_incup += $row->inc_lab_ucs_incup ; ?>
                  <?php $sum_inc_drug_ucs_incup += $row->inc_drug_ucs_incup ; ?>   
                  <?php $sum_visit_ucs_inprov += $row->visit_ucs_inprov ; ?>
                  <?php $sum_inc_ucs_inprov += $row->inc_ucs_inprov ; ?>
                  <?php $sum_inc_lab_ucs_inprov += $row->inc_lab_ucs_inprov ; ?>
                  <?php $sum_inc_drug_ucs_inprov += $row->inc_drug_ucs_inprov ; ?>
                  <?php $sum_visit_ucs_outprov += $row->visit_ucs_outprov ; ?>
                  <?php $sum_inc_ucs_outprov += $row->inc_ucs_outprov ; ?>
                  <?php $sum_inc_lab_ucs_outprov += $row->inc_lab_ucs_outprov ; ?>
                  <?php $sum_inc_drug_ucs_outprov += $row->inc_drug_ucs_outprov ; ?> 
                  <?php $sum_visit_ofc += $row->visit_ofc ; ?>
                  <?php $sum_inc_ofc += $row->inc_ofc ; ?>
                  <?php $sum_inc_lab_ofc += $row->inc_lab_ofc ; ?>
                  <?php $sum_inc_drug_ofc += $row->inc_drug_ofc ; ?> 
                  <?php $sum_visit_bkk += $row->visit_bkk ; ?>
                  <?php $sum_inc_bkk += $row->inc_bkk ; ?>
                  <?php $sum_inc_lab_bkk += $row->inc_lab_bkk ; ?>
                  <?php $sum_inc_drug_bkk += $row->inc_drug_bkk ; ?>  
                  <?php $sum_visit_bmt += $row->visit_bmt ; ?>
                  <?php $sum_inc_bmt += $row->inc_bmt ; ?>
                  <?php $sum_inc_lab_bmt += $row->inc_lab_bmt ; ?>
                  <?php $sum_inc_drug_bmt += $row->inc_drug_bmt ; ?> 
                  <?php $sum_visit_sss += $row->visit_sss ; ?>
                  <?php $sum_inc_sss += $row->inc_sss ; ?>
                  <?php $sum_inc_lab_sss += $row->inc_lab_sss ; ?>
                  <?php $sum_inc_drug_sss += $row->inc_drug_sss ; ?>   
                  <?php $sum_visit_lgo += $row->visit_lgo ; ?>
                  <?php $sum_inc_lgo += $row->inc_lgo ; ?>
                  <?php $sum_inc_lab_lgo += $row->inc_lab_lgo ; ?>
                  <?php $sum_inc_drug_lgo += $row->inc_drug_lgo ; ?>
                  <?php $sum_visit_fss += $row->visit_fss ; ?>
                  <?php $sum_inc_fss += $row->inc_fss ; ?>
                  <?php $sum_inc_lab_fss += $row->inc_lab_fss ; ?>
                  <?php $sum_inc_drug_fss += $row->inc_drug_fss ; ?>    
                  <?php $sum_visit_stp += $row->visit_stp ; ?>
                  <?php $sum_inc_stp += $row->inc_stp ; ?>
                  <?php $sum_inc_lab_stp += $row->inc_lab_stp ; ?>
                  <?php $sum_inc_drug_stp += $row->inc_drug_stp ; ?>   
                  <?php $sum_visit_pay += $row->visit_pay ; ?>
                  <?php $sum_inc_pay += $row->inc_pay ; ?>
                  <?php $sum_inc_lab_pay += $row->inc_lab_pay ; ?>
                  <?php $sum_inc_drug_pay += $row->inc_drug_pay ; ?> 
                  @endforeach    
                  <tr>
                    <td align="right"><strong>รวม</strong></td>
                    <td align="right"><strong>{{number_format($sum_hn_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_total_op)}}</strong></td>     
                    <td align="right"><strong>{{number_format($sum_visit_total_pp)}}</strong></td>   
                    <td align="right"><strong>{{number_format($sum_inc_total,2)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_inc_lab_total,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_inc_drug_total,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_ucs_incup)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ucs_incup,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ucs_incup,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ucs_incup,2)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_visit_ucs_inprov)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ucs_inprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ucs_inprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ucs_inprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_ucs_outprov)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ucs_outprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ucs_outprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ucs_outprov,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_visit_ofc)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ofc,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ofc,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ofc,2)}}</strong></td>   
                    <td align="right"><strong>{{number_format($sum_visit_bkk)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_bkk,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_bkk,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_bkk,2)}}</strong></td>   
                    <td align="right"><strong>{{number_format($sum_visit_bmt)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_bmt,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_bmt,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_bmt,2)}}</strong></td>    
                    <td align="right"><strong>{{number_format($sum_visit_sss)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_sss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_sss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_sss,2)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_visit_lgo)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lgo,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_lgo,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_lgo,2)}}</strong></td>       
                    <td align="right"><strong>{{number_format($sum_visit_fss)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_fss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_fss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_fss,2)}}</strong></td>    
                    <td align="right"><strong>{{number_format($sum_visit_stp)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_stp,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_stp,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_stp,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_visit_pay)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_pay,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_pay,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_pay,2)}}</strong></td> 
                  </tr>   
                </tbody>
              </table>
            </div>
          </div>                   
        <!-- END 10985 -->
        </div>

        <!-- 10986 OPD -->
        <div class="tab-pane fade" id="pane-10986" role="tabpanel" aria-labelledby="tab-10986" tabindex="0">
          <div class="glass p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6>[10986] ข้อมูลบริการผู้ป่วยนอก OPD โรงพยาปทุมราชวงศา ปีงบประมาณ {{$budget_year}}</h6>
              <span class="text-secondary small">Update {{$update_at10986}}</span>            
            </div>
            <div class="table-responsive">
              <table id="table10986" class="table table-bordered table-striped my-3" width ="100%">
                <thead class="table-light">
                  <tr class="table-primary">
                    <th class="text-center" rowspan="2" width ="4%">เดือน</th>
                    <th class="text-center" colspan="7">ทั้งหมด</th>
                    <th class="text-center" colspan="4">UCS ใน CUP</th> 
                    <th class="text-center" colspan="4">UCS ในจังหวัด</th>
                    <th class="text-center" colspan="4">UCS นอกจังหวัด</th>       
                    <th class="text-center" colspan="4">OFC ข้าราชการ</th>  
                    <th class="text-center" colspan="4">BKK กทม.</th>
                    <th class="text-center" colspan="4">BMT ขสมก.</th>
                    <th class="text-center" colspan="4">SSS ประกันสังคม</th>
                    <th class="text-center" colspan="4">LGO อปท.</th>
                    <th class="text-center" colspan="4">FSS ต่างด้าว</th>
                    <th class="text-center" colspan="4">STP Stateless</th>
                    <th class="text-center" colspan="4">ชำระเงิน/พรบ.</th>                 
                  </tr>    
                  <tr class="table-primary">            
                    <td class="text-center text-primary">HN Total</td>
                    <td class="text-center text-primary">Visit Total</td>
                    <td class="text-center text-primary">Visit OP</td>
                    <td class="text-center text-primary">Visit PP</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                  </tr>    
                </thead>
                <tbody>
                  <?php $count = 1 ; ?> 
                  <?php $sum_hn_total = 0 ; ?> 
                  <?php $sum_visit_total = 0 ; ?>   
                  <?php $sum_visit_total_op = 0 ; ?>  
                  <?php $sum_visit_total_pp = 0 ; ?> 
                  <?php $sum_inc_total = 0 ; ?>  
                  <?php $sum_inc_lab_total = 0 ; ?>
                  <?php $sum_inc_drug_total = 0 ; ?> 
                  <?php $sum_visit_ucs_incup = 0 ; ?>  
                  <?php $sum_inc_ucs_incup = 0 ; ?>  
                  <?php $sum_inc_lab_ucs_incup = 0 ; ?>  
                  <?php $sum_inc_drug_ucs_incup = 0 ; ?>  
                  <?php $sum_visit_ucs_inprov = 0 ; ?>  
                  <?php $sum_inc_ucs_inprov = 0 ; ?>  
                  <?php $sum_inc_lab_ucs_inprov = 0 ; ?> 
                  <?php $sum_inc_drug_ucs_inprov = 0 ; ?>   
                  <?php $sum_visit_ucs_outprov = 0 ; ?>  
                  <?php $sum_inc_ucs_outprov = 0 ; ?>
                  <?php $sum_inc_lab_ucs_outprov = 0 ; ?>
                  <?php $sum_inc_drug_ucs_outprov = 0 ; ?>  
                  <?php $sum_visit_ofc = 0 ; ?>  
                  <?php $sum_inc_ofc = 0 ; ?>
                  <?php $sum_inc_lab_ofc = 0 ; ?>
                  <?php $sum_inc_drug_ofc = 0 ; ?>
                  <?php $sum_visit_bkk = 0 ; ?>  
                  <?php $sum_inc_bkk = 0 ; ?>
                  <?php $sum_inc_lab_bkk = 0 ; ?>
                  <?php $sum_inc_drug_bkk = 0 ; ?> 
                  <?php $sum_visit_bmt = 0 ; ?>  
                  <?php $sum_inc_bmt = 0 ; ?>
                  <?php $sum_inc_lab_bmt = 0 ; ?>
                  <?php $sum_inc_drug_bmt = 0 ; ?>  
                  <?php $sum_visit_sss = 0 ; ?>  
                  <?php $sum_inc_sss = 0 ; ?>
                  <?php $sum_inc_lab_sss = 0 ; ?>
                  <?php $sum_inc_drug_sss = 0 ; ?> 
                  <?php $sum_visit_lgo = 0 ; ?>  
                  <?php $sum_inc_lgo = 0 ; ?>
                  <?php $sum_inc_lab_lgo = 0 ; ?>
                  <?php $sum_inc_drug_lgo = 0 ; ?> 
                  <?php $sum_visit_fss = 0 ; ?>  
                  <?php $sum_inc_fss = 0 ; ?>
                  <?php $sum_inc_lab_fss = 0 ; ?>
                  <?php $sum_inc_drug_fss = 0 ; ?> 
                  <?php $sum_visit_stp = 0 ; ?>  
                  <?php $sum_inc_stp = 0 ; ?>
                  <?php $sum_inc_lab_stp = 0 ; ?>
                  <?php $sum_inc_drug_stp = 0 ; ?>
                  <?php $sum_visit_pay = 0 ; ?>  
                  <?php $sum_inc_pay = 0 ; ?>
                  <?php $sum_inc_lab_pay = 0 ; ?>
                  <?php $sum_inc_drug_pay = 0 ; ?>  
                  @foreach($total_10986 as $row) 
                  <tr>
                    <td align="center"width ="4%">{{ $row->month }}</td>
                    <td align="right">{{ number_format($row->hn_total) }}</td>
                    <td align="right">{{ number_format($row->visit_total) }}</td>
                    <td align="right">{{ number_format($row->visit_total_op) }}</td>
                    <td align="right">{{ number_format($row->visit_total_pp) }}</td>
                    <td align="right">{{ number_format($row->inc_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_total,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ucs_incup) }}</td>
                    <td align="right">{{ number_format($row->inc_ucs_incup,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ucs_incup,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ucs_incup,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ucs_inprov) }}</td>
                    <td align="right">{{ number_format($row->inc_ucs_inprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ucs_inprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ucs_inprov,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ucs_outprov) }}</td>
                    <td align="right">{{ number_format($row->inc_ucs_outprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ucs_outprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ucs_outprov,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ofc) }}</td>
                    <td align="right">{{ number_format($row->inc_ofc,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ofc,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ofc,2) }}</td>
                    <td align="right">{{ number_format($row->visit_bkk) }}</td>
                    <td align="right">{{ number_format($row->inc_bkk,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_bkk,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_bkk,2) }}</td>
                    <td align="right">{{ number_format($row->visit_bmt) }}</td>
                    <td align="right">{{ number_format($row->inc_bmt,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_bmt,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_bmt,2) }}</td>
                    <td align="right">{{ number_format($row->visit_sss) }}</td>
                    <td align="right">{{ number_format($row->inc_sss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_sss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_sss,2) }}</td>
                    <td align="right">{{ number_format($row->visit_lgo) }}</td>
                    <td align="right">{{ number_format($row->inc_lgo,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_lgo,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_lgo,2) }}</td>
                    <td align="right">{{ number_format($row->visit_fss) }}</td>
                    <td align="right">{{ number_format($row->inc_fss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_fss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_fss,2) }}</td>
                    <td align="right">{{ number_format($row->visit_stp) }}</td>
                    <td align="right">{{ number_format($row->inc_stp,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_stp,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_stp,2) }}</td>
                    <td align="right">{{ number_format($row->visit_pay) }}</td>
                    <td align="right">{{ number_format($row->inc_pay,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_pay,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_pay,2) }}</td>
                  </tr>
                  <?php $count++; ?>
                  <?php $sum_hn_total += $row->hn_total ; ?>
                  <?php $sum_visit_total += $row->visit_total ; ?>
                  <?php $sum_visit_total_op += $row->visit_total_op ; ?>
                  <?php $sum_visit_total_pp += $row->visit_total_pp ; ?>
                  <?php $sum_inc_total += $row->inc_total ; ?>
                  <?php $sum_inc_lab_total += $row->inc_lab_total ; ?>
                  <?php $sum_inc_drug_total += $row->inc_drug_total ; ?>
                  <?php $sum_visit_ucs_incup += $row->visit_ucs_incup ; ?>
                  <?php $sum_inc_ucs_incup += $row->inc_ucs_incup ; ?>
                  <?php $sum_inc_lab_ucs_incup += $row->inc_lab_ucs_incup ; ?>
                  <?php $sum_inc_drug_ucs_incup += $row->inc_drug_ucs_incup ; ?>   
                  <?php $sum_visit_ucs_inprov += $row->visit_ucs_inprov ; ?>
                  <?php $sum_inc_ucs_inprov += $row->inc_ucs_inprov ; ?>
                  <?php $sum_inc_lab_ucs_inprov += $row->inc_lab_ucs_inprov ; ?>
                  <?php $sum_inc_drug_ucs_inprov += $row->inc_drug_ucs_inprov ; ?>
                  <?php $sum_visit_ucs_outprov += $row->visit_ucs_outprov ; ?>
                  <?php $sum_inc_ucs_outprov += $row->inc_ucs_outprov ; ?>
                  <?php $sum_inc_lab_ucs_outprov += $row->inc_lab_ucs_outprov ; ?>
                  <?php $sum_inc_drug_ucs_outprov += $row->inc_drug_ucs_outprov ; ?> 
                  <?php $sum_visit_ofc += $row->visit_ofc ; ?>
                  <?php $sum_inc_ofc += $row->inc_ofc ; ?>
                  <?php $sum_inc_lab_ofc += $row->inc_lab_ofc ; ?>
                  <?php $sum_inc_drug_ofc += $row->inc_drug_ofc ; ?> 
                  <?php $sum_visit_bkk += $row->visit_bkk ; ?>
                  <?php $sum_inc_bkk += $row->inc_bkk ; ?>
                  <?php $sum_inc_lab_bkk += $row->inc_lab_bkk ; ?>
                  <?php $sum_inc_drug_bkk += $row->inc_drug_bkk ; ?>  
                  <?php $sum_visit_bmt += $row->visit_bmt ; ?>
                  <?php $sum_inc_bmt += $row->inc_bmt ; ?>
                  <?php $sum_inc_lab_bmt += $row->inc_lab_bmt ; ?>
                  <?php $sum_inc_drug_bmt += $row->inc_drug_bmt ; ?> 
                  <?php $sum_visit_sss += $row->visit_sss ; ?>
                  <?php $sum_inc_sss += $row->inc_sss ; ?>
                  <?php $sum_inc_lab_sss += $row->inc_lab_sss ; ?>
                  <?php $sum_inc_drug_sss += $row->inc_drug_sss ; ?>   
                  <?php $sum_visit_lgo += $row->visit_lgo ; ?>
                  <?php $sum_inc_lgo += $row->inc_lgo ; ?>
                  <?php $sum_inc_lab_lgo += $row->inc_lab_lgo ; ?>
                  <?php $sum_inc_drug_lgo += $row->inc_drug_lgo ; ?>
                  <?php $sum_visit_fss += $row->visit_fss ; ?>
                  <?php $sum_inc_fss += $row->inc_fss ; ?>
                  <?php $sum_inc_lab_fss += $row->inc_lab_fss ; ?>
                  <?php $sum_inc_drug_fss += $row->inc_drug_fss ; ?>    
                  <?php $sum_visit_stp += $row->visit_stp ; ?>
                  <?php $sum_inc_stp += $row->inc_stp ; ?>
                  <?php $sum_inc_lab_stp += $row->inc_lab_stp ; ?>
                  <?php $sum_inc_drug_stp += $row->inc_drug_stp ; ?>   
                  <?php $sum_visit_pay += $row->visit_pay ; ?>
                  <?php $sum_inc_pay += $row->inc_pay ; ?>
                  <?php $sum_inc_lab_pay += $row->inc_lab_pay ; ?>
                  <?php $sum_inc_drug_pay += $row->inc_drug_pay ; ?> 
                  @endforeach    
                  <tr>
                    <td align="right"><strong>รวม</strong></td>
                    <td align="right"><strong>{{number_format($sum_hn_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_total_op)}}</strong></td>     
                    <td align="right"><strong>{{number_format($sum_visit_total_pp)}}</strong></td>   
                    <td align="right"><strong>{{number_format($sum_inc_total,2)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_inc_lab_total,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_inc_drug_total,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_ucs_incup)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ucs_incup,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ucs_incup,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ucs_incup,2)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_visit_ucs_inprov)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ucs_inprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ucs_inprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ucs_inprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_ucs_outprov)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ucs_outprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ucs_outprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ucs_outprov,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_visit_ofc)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ofc,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ofc,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ofc,2)}}</strong></td>   
                    <td align="right"><strong>{{number_format($sum_visit_bkk)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_bkk,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_bkk,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_bkk,2)}}</strong></td>   
                    <td align="right"><strong>{{number_format($sum_visit_bmt)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_bmt,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_bmt,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_bmt,2)}}</strong></td>    
                    <td align="right"><strong>{{number_format($sum_visit_sss)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_sss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_sss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_sss,2)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_visit_lgo)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lgo,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_lgo,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_lgo,2)}}</strong></td>       
                    <td align="right"><strong>{{number_format($sum_visit_fss)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_fss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_fss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_fss,2)}}</strong></td>    
                    <td align="right"><strong>{{number_format($sum_visit_stp)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_stp,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_stp,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_stp,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_visit_pay)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_pay,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_pay,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_pay,2)}}</strong></td> 
                  </tr>                  
                </tbody>
              </table>
            </div>
          </div> 
        <!-- END 10986 -->         
        </div>

        <!-- 10987 OPD-->
        <div class="tab-pane fade" id="pane-10987" role="tabpanel" aria-labelledby="tab-10987" tabindex="0">
          <div class="glass p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6>[10987] ข้อมูลบริการผู้ป่วยนอก OPD โรงพยาพนา ปีงบประมาณ {{$budget_year}}</h6>
              <span class="text-secondary small">Update {{$update_at10987}}</span>   
            </div>
            <div class="table-responsive">
              <table id="table10987" class="table table-bordered table-striped my-3" width ="100%">
                <thead class="table-light">
                  <tr class="table-primary">
                    <th class="text-center" rowspan="2" width ="4%">เดือน</th>
                    <th class="text-center" colspan="7">ทั้งหมด</th>
                    <th class="text-center" colspan="4">UCS ใน CUP</th> 
                    <th class="text-center" colspan="4">UCS ในจังหวัด</th>
                    <th class="text-center" colspan="4">UCS นอกจังหวัด</th>       
                    <th class="text-center" colspan="4">OFC ข้าราชการ</th>  
                    <th class="text-center" colspan="4">BKK กทม.</th>
                    <th class="text-center" colspan="4">BMT ขสมก.</th>
                    <th class="text-center" colspan="4">SSS ประกันสังคม</th>
                    <th class="text-center" colspan="4">LGO อปท.</th>
                    <th class="text-center" colspan="4">FSS ต่างด้าว</th>
                    <th class="text-center" colspan="4">STP Stateless</th>
                    <th class="text-center" colspan="4">ชำระเงิน/พรบ.</th>                 
                  </tr>    
                  <tr class="table-primary">            
                    <td class="text-center text-primary">HN Total</td>
                    <td class="text-center text-primary">Visit Total</td>
                    <td class="text-center text-primary">Visit OP</td>
                    <td class="text-center text-primary">Visit PP</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                  </tr>    
                </thead>
                <tbody>
                  <?php $count = 1 ; ?> 
                  <?php $sum_hn_total = 0 ; ?> 
                  <?php $sum_visit_total = 0 ; ?>   
                  <?php $sum_visit_total_op = 0 ; ?>  
                  <?php $sum_visit_total_pp = 0 ; ?> 
                  <?php $sum_inc_total = 0 ; ?>  
                  <?php $sum_inc_lab_total = 0 ; ?>
                  <?php $sum_inc_drug_total = 0 ; ?> 
                  <?php $sum_visit_ucs_incup = 0 ; ?>  
                  <?php $sum_inc_ucs_incup = 0 ; ?>  
                  <?php $sum_inc_lab_ucs_incup = 0 ; ?>  
                  <?php $sum_inc_drug_ucs_incup = 0 ; ?>  
                  <?php $sum_visit_ucs_inprov = 0 ; ?>  
                  <?php $sum_inc_ucs_inprov = 0 ; ?>  
                  <?php $sum_inc_lab_ucs_inprov = 0 ; ?> 
                  <?php $sum_inc_drug_ucs_inprov = 0 ; ?>   
                  <?php $sum_visit_ucs_outprov = 0 ; ?>  
                  <?php $sum_inc_ucs_outprov = 0 ; ?>
                  <?php $sum_inc_lab_ucs_outprov = 0 ; ?>
                  <?php $sum_inc_drug_ucs_outprov = 0 ; ?>  
                  <?php $sum_visit_ofc = 0 ; ?>  
                  <?php $sum_inc_ofc = 0 ; ?>
                  <?php $sum_inc_lab_ofc = 0 ; ?>
                  <?php $sum_inc_drug_ofc = 0 ; ?>
                  <?php $sum_visit_bkk = 0 ; ?>  
                  <?php $sum_inc_bkk = 0 ; ?>
                  <?php $sum_inc_lab_bkk = 0 ; ?>
                  <?php $sum_inc_drug_bkk = 0 ; ?> 
                  <?php $sum_visit_bmt = 0 ; ?>  
                  <?php $sum_inc_bmt = 0 ; ?>
                  <?php $sum_inc_lab_bmt = 0 ; ?>
                  <?php $sum_inc_drug_bmt = 0 ; ?>  
                  <?php $sum_visit_sss = 0 ; ?>  
                  <?php $sum_inc_sss = 0 ; ?>
                  <?php $sum_inc_lab_sss = 0 ; ?>
                  <?php $sum_inc_drug_sss = 0 ; ?> 
                  <?php $sum_visit_lgo = 0 ; ?>  
                  <?php $sum_inc_lgo = 0 ; ?>
                  <?php $sum_inc_lab_lgo = 0 ; ?>
                  <?php $sum_inc_drug_lgo = 0 ; ?> 
                  <?php $sum_visit_fss = 0 ; ?>  
                  <?php $sum_inc_fss = 0 ; ?>
                  <?php $sum_inc_lab_fss = 0 ; ?>
                  <?php $sum_inc_drug_fss = 0 ; ?> 
                  <?php $sum_visit_stp = 0 ; ?>  
                  <?php $sum_inc_stp = 0 ; ?>
                  <?php $sum_inc_lab_stp = 0 ; ?>
                  <?php $sum_inc_drug_stp = 0 ; ?>
                  <?php $sum_visit_pay = 0 ; ?>  
                  <?php $sum_inc_pay = 0 ; ?>
                  <?php $sum_inc_lab_pay = 0 ; ?>
                  <?php $sum_inc_drug_pay = 0 ; ?>  
                  @foreach($total_10987 as $row) 
                  <tr>
                    <td align="center"width ="4%">{{ $row->month }}</td>
                    <td align="right">{{ number_format($row->hn_total) }}</td>
                    <td align="right">{{ number_format($row->visit_total) }}</td>
                    <td align="right">{{ number_format($row->visit_total_op) }}</td>
                    <td align="right">{{ number_format($row->visit_total_pp) }}</td>
                    <td align="right">{{ number_format($row->inc_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_total,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ucs_incup) }}</td>
                    <td align="right">{{ number_format($row->inc_ucs_incup,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ucs_incup,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ucs_incup,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ucs_inprov) }}</td>
                    <td align="right">{{ number_format($row->inc_ucs_inprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ucs_inprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ucs_inprov,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ucs_outprov) }}</td>
                    <td align="right">{{ number_format($row->inc_ucs_outprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ucs_outprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ucs_outprov,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ofc) }}</td>
                    <td align="right">{{ number_format($row->inc_ofc,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ofc,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ofc,2) }}</td>
                    <td align="right">{{ number_format($row->visit_bkk) }}</td>
                    <td align="right">{{ number_format($row->inc_bkk,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_bkk,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_bkk,2) }}</td>
                    <td align="right">{{ number_format($row->visit_bmt) }}</td>
                    <td align="right">{{ number_format($row->inc_bmt,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_bmt,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_bmt,2) }}</td>
                    <td align="right">{{ number_format($row->visit_sss) }}</td>
                    <td align="right">{{ number_format($row->inc_sss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_sss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_sss,2) }}</td>
                    <td align="right">{{ number_format($row->visit_lgo) }}</td>
                    <td align="right">{{ number_format($row->inc_lgo,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_lgo,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_lgo,2) }}</td>
                    <td align="right">{{ number_format($row->visit_fss) }}</td>
                    <td align="right">{{ number_format($row->inc_fss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_fss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_fss,2) }}</td>
                    <td align="right">{{ number_format($row->visit_stp) }}</td>
                    <td align="right">{{ number_format($row->inc_stp,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_stp,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_stp,2) }}</td>
                    <td align="right">{{ number_format($row->visit_pay) }}</td>
                    <td align="right">{{ number_format($row->inc_pay,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_pay,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_pay,2) }}</td>
                  </tr>
                  <?php $count++; ?>
                  <?php $sum_hn_total += $row->hn_total ; ?>
                  <?php $sum_visit_total += $row->visit_total ; ?>
                  <?php $sum_visit_total_op += $row->visit_total_op ; ?>
                  <?php $sum_visit_total_pp += $row->visit_total_pp ; ?>
                  <?php $sum_inc_total += $row->inc_total ; ?>
                  <?php $sum_inc_lab_total += $row->inc_lab_total ; ?>
                  <?php $sum_inc_drug_total += $row->inc_drug_total ; ?>
                  <?php $sum_visit_ucs_incup += $row->visit_ucs_incup ; ?>
                  <?php $sum_inc_ucs_incup += $row->inc_ucs_incup ; ?>
                  <?php $sum_inc_lab_ucs_incup += $row->inc_lab_ucs_incup ; ?>
                  <?php $sum_inc_drug_ucs_incup += $row->inc_drug_ucs_incup ; ?>   
                  <?php $sum_visit_ucs_inprov += $row->visit_ucs_inprov ; ?>
                  <?php $sum_inc_ucs_inprov += $row->inc_ucs_inprov ; ?>
                  <?php $sum_inc_lab_ucs_inprov += $row->inc_lab_ucs_inprov ; ?>
                  <?php $sum_inc_drug_ucs_inprov += $row->inc_drug_ucs_inprov ; ?>
                  <?php $sum_visit_ucs_outprov += $row->visit_ucs_outprov ; ?>
                  <?php $sum_inc_ucs_outprov += $row->inc_ucs_outprov ; ?>
                  <?php $sum_inc_lab_ucs_outprov += $row->inc_lab_ucs_outprov ; ?>
                  <?php $sum_inc_drug_ucs_outprov += $row->inc_drug_ucs_outprov ; ?> 
                  <?php $sum_visit_ofc += $row->visit_ofc ; ?>
                  <?php $sum_inc_ofc += $row->inc_ofc ; ?>
                  <?php $sum_inc_lab_ofc += $row->inc_lab_ofc ; ?>
                  <?php $sum_inc_drug_ofc += $row->inc_drug_ofc ; ?> 
                  <?php $sum_visit_bkk += $row->visit_bkk ; ?>
                  <?php $sum_inc_bkk += $row->inc_bkk ; ?>
                  <?php $sum_inc_lab_bkk += $row->inc_lab_bkk ; ?>
                  <?php $sum_inc_drug_bkk += $row->inc_drug_bkk ; ?>  
                  <?php $sum_visit_bmt += $row->visit_bmt ; ?>
                  <?php $sum_inc_bmt += $row->inc_bmt ; ?>
                  <?php $sum_inc_lab_bmt += $row->inc_lab_bmt ; ?>
                  <?php $sum_inc_drug_bmt += $row->inc_drug_bmt ; ?> 
                  <?php $sum_visit_sss += $row->visit_sss ; ?>
                  <?php $sum_inc_sss += $row->inc_sss ; ?>
                  <?php $sum_inc_lab_sss += $row->inc_lab_sss ; ?>
                  <?php $sum_inc_drug_sss += $row->inc_drug_sss ; ?>   
                  <?php $sum_visit_lgo += $row->visit_lgo ; ?>
                  <?php $sum_inc_lgo += $row->inc_lgo ; ?>
                  <?php $sum_inc_lab_lgo += $row->inc_lab_lgo ; ?>
                  <?php $sum_inc_drug_lgo += $row->inc_drug_lgo ; ?>
                  <?php $sum_visit_fss += $row->visit_fss ; ?>
                  <?php $sum_inc_fss += $row->inc_fss ; ?>
                  <?php $sum_inc_lab_fss += $row->inc_lab_fss ; ?>
                  <?php $sum_inc_drug_fss += $row->inc_drug_fss ; ?>    
                  <?php $sum_visit_stp += $row->visit_stp ; ?>
                  <?php $sum_inc_stp += $row->inc_stp ; ?>
                  <?php $sum_inc_lab_stp += $row->inc_lab_stp ; ?>
                  <?php $sum_inc_drug_stp += $row->inc_drug_stp ; ?>   
                  <?php $sum_visit_pay += $row->visit_pay ; ?>
                  <?php $sum_inc_pay += $row->inc_pay ; ?>
                  <?php $sum_inc_lab_pay += $row->inc_lab_pay ; ?>
                  <?php $sum_inc_drug_pay += $row->inc_drug_pay ; ?> 
                  @endforeach    
                  <tr>
                    <td align="right"><strong>รวม</strong></td>
                    <td align="right"><strong>{{number_format($sum_hn_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_total_op)}}</strong></td>     
                    <td align="right"><strong>{{number_format($sum_visit_total_pp)}}</strong></td>   
                    <td align="right"><strong>{{number_format($sum_inc_total,2)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_inc_lab_total,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_inc_drug_total,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_ucs_incup)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ucs_incup,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ucs_incup,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ucs_incup,2)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_visit_ucs_inprov)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ucs_inprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ucs_inprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ucs_inprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_ucs_outprov)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ucs_outprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ucs_outprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ucs_outprov,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_visit_ofc)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ofc,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ofc,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ofc,2)}}</strong></td>   
                    <td align="right"><strong>{{number_format($sum_visit_bkk)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_bkk,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_bkk,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_bkk,2)}}</strong></td>   
                    <td align="right"><strong>{{number_format($sum_visit_bmt)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_bmt,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_bmt,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_bmt,2)}}</strong></td>    
                    <td align="right"><strong>{{number_format($sum_visit_sss)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_sss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_sss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_sss,2)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_visit_lgo)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lgo,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_lgo,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_lgo,2)}}</strong></td>       
                    <td align="right"><strong>{{number_format($sum_visit_fss)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_fss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_fss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_fss,2)}}</strong></td>    
                    <td align="right"><strong>{{number_format($sum_visit_stp)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_stp,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_stp,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_stp,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_visit_pay)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_pay,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_pay,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_pay,2)}}</strong></td> 
                  </tr> 
                </tbody>
              </table>
            </div>
          </div> 
          <!-- END 10987 -->         
        </div>

        <!-- 10988 OPD -->
        <div class="tab-pane fade" id="pane-10988" role="tabpanel" aria-labelledby="tab-10988" tabindex="0">
          <div class="glass p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6>[10988] ข้อมูลบริการผู้ป่วยนอก OPD โรงพยาบาลเสนางคนิคม ปีงบประมาณ {{$budget_year}}</h6>
              <span class="text-secondary small">Update {{$update_at10988}}</span>   
            </div>
            <div class="table-responsive">
              <table id="table10988" class="table table-bordered table-striped my-3" width ="100%">
                <thead class="table-light">
                  <tr class="table-primary">
                    <th class="text-center" rowspan="2" width ="4%">เดือน</th>
                    <th class="text-center" colspan="7">ทั้งหมด</th>
                    <th class="text-center" colspan="4">UCS ใน CUP</th> 
                    <th class="text-center" colspan="4">UCS ในจังหวัด</th>
                    <th class="text-center" colspan="4">UCS นอกจังหวัด</th>       
                    <th class="text-center" colspan="4">OFC ข้าราชการ</th>  
                    <th class="text-center" colspan="4">BKK กทม.</th>
                    <th class="text-center" colspan="4">BMT ขสมก.</th>
                    <th class="text-center" colspan="4">SSS ประกันสังคม</th>
                    <th class="text-center" colspan="4">LGO อปท.</th>
                    <th class="text-center" colspan="4">FSS ต่างด้าว</th>
                    <th class="text-center" colspan="4">STP Stateless</th>
                    <th class="text-center" colspan="4">ชำระเงิน/พรบ.</th>                 
                  </tr>    
                  <tr class="table-primary">            
                    <td class="text-center text-primary">HN Total</td>
                    <td class="text-center text-primary">Visit Total</td>
                    <td class="text-center text-primary">Visit OP</td>
                    <td class="text-center text-primary">Visit PP</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                  </tr>    
                </thead>
                <tbody>
                  <?php $count = 1 ; ?> 
                  <?php $sum_hn_total = 0 ; ?> 
                  <?php $sum_visit_total = 0 ; ?>   
                  <?php $sum_visit_total_op = 0 ; ?>  
                  <?php $sum_visit_total_pp = 0 ; ?> 
                  <?php $sum_inc_total = 0 ; ?>  
                  <?php $sum_inc_lab_total = 0 ; ?>
                  <?php $sum_inc_drug_total = 0 ; ?> 
                  <?php $sum_visit_ucs_incup = 0 ; ?>  
                  <?php $sum_inc_ucs_incup = 0 ; ?>  
                  <?php $sum_inc_lab_ucs_incup = 0 ; ?>  
                  <?php $sum_inc_drug_ucs_incup = 0 ; ?>  
                  <?php $sum_visit_ucs_inprov = 0 ; ?>  
                  <?php $sum_inc_ucs_inprov = 0 ; ?>  
                  <?php $sum_inc_lab_ucs_inprov = 0 ; ?> 
                  <?php $sum_inc_drug_ucs_inprov = 0 ; ?>   
                  <?php $sum_visit_ucs_outprov = 0 ; ?>  
                  <?php $sum_inc_ucs_outprov = 0 ; ?>
                  <?php $sum_inc_lab_ucs_outprov = 0 ; ?>
                  <?php $sum_inc_drug_ucs_outprov = 0 ; ?>  
                  <?php $sum_visit_ofc = 0 ; ?>  
                  <?php $sum_inc_ofc = 0 ; ?>
                  <?php $sum_inc_lab_ofc = 0 ; ?>
                  <?php $sum_inc_drug_ofc = 0 ; ?>
                  <?php $sum_visit_bkk = 0 ; ?>  
                  <?php $sum_inc_bkk = 0 ; ?>
                  <?php $sum_inc_lab_bkk = 0 ; ?>
                  <?php $sum_inc_drug_bkk = 0 ; ?> 
                  <?php $sum_visit_bmt = 0 ; ?>  
                  <?php $sum_inc_bmt = 0 ; ?>
                  <?php $sum_inc_lab_bmt = 0 ; ?>
                  <?php $sum_inc_drug_bmt = 0 ; ?>  
                  <?php $sum_visit_sss = 0 ; ?>  
                  <?php $sum_inc_sss = 0 ; ?>
                  <?php $sum_inc_lab_sss = 0 ; ?>
                  <?php $sum_inc_drug_sss = 0 ; ?> 
                  <?php $sum_visit_lgo = 0 ; ?>  
                  <?php $sum_inc_lgo = 0 ; ?>
                  <?php $sum_inc_lab_lgo = 0 ; ?>
                  <?php $sum_inc_drug_lgo = 0 ; ?> 
                  <?php $sum_visit_fss = 0 ; ?>  
                  <?php $sum_inc_fss = 0 ; ?>
                  <?php $sum_inc_lab_fss = 0 ; ?>
                  <?php $sum_inc_drug_fss = 0 ; ?> 
                  <?php $sum_visit_stp = 0 ; ?>  
                  <?php $sum_inc_stp = 0 ; ?>
                  <?php $sum_inc_lab_stp = 0 ; ?>
                  <?php $sum_inc_drug_stp = 0 ; ?>
                  <?php $sum_visit_pay = 0 ; ?>  
                  <?php $sum_inc_pay = 0 ; ?>
                  <?php $sum_inc_lab_pay = 0 ; ?>
                  <?php $sum_inc_drug_pay = 0 ; ?>  
                  @foreach($total_10988 as $row) 
                  <tr>
                    <td align="center"width ="4%">{{ $row->month }}</td>
                    <td align="right">{{ number_format($row->hn_total) }}</td>
                    <td align="right">{{ number_format($row->visit_total) }}</td>
                    <td align="right">{{ number_format($row->visit_total_op) }}</td>
                    <td align="right">{{ number_format($row->visit_total_pp) }}</td>
                    <td align="right">{{ number_format($row->inc_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_total,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ucs_incup) }}</td>
                    <td align="right">{{ number_format($row->inc_ucs_incup,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ucs_incup,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ucs_incup,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ucs_inprov) }}</td>
                    <td align="right">{{ number_format($row->inc_ucs_inprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ucs_inprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ucs_inprov,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ucs_outprov) }}</td>
                    <td align="right">{{ number_format($row->inc_ucs_outprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ucs_outprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ucs_outprov,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ofc) }}</td>
                    <td align="right">{{ number_format($row->inc_ofc,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ofc,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ofc,2) }}</td>
                    <td align="right">{{ number_format($row->visit_bkk) }}</td>
                    <td align="right">{{ number_format($row->inc_bkk,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_bkk,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_bkk,2) }}</td>
                    <td align="right">{{ number_format($row->visit_bmt) }}</td>
                    <td align="right">{{ number_format($row->inc_bmt,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_bmt,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_bmt,2) }}</td>
                    <td align="right">{{ number_format($row->visit_sss) }}</td>
                    <td align="right">{{ number_format($row->inc_sss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_sss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_sss,2) }}</td>
                    <td align="right">{{ number_format($row->visit_lgo) }}</td>
                    <td align="right">{{ number_format($row->inc_lgo,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_lgo,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_lgo,2) }}</td>
                    <td align="right">{{ number_format($row->visit_fss) }}</td>
                    <td align="right">{{ number_format($row->inc_fss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_fss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_fss,2) }}</td>
                    <td align="right">{{ number_format($row->visit_stp) }}</td>
                    <td align="right">{{ number_format($row->inc_stp,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_stp,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_stp,2) }}</td>
                    <td align="right">{{ number_format($row->visit_pay) }}</td>
                    <td align="right">{{ number_format($row->inc_pay,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_pay,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_pay,2) }}</td>
                  </tr>
                  <?php $count++; ?>
                  <?php $sum_hn_total += $row->hn_total ; ?>
                  <?php $sum_visit_total += $row->visit_total ; ?>
                  <?php $sum_visit_total_op += $row->visit_total_op ; ?>
                  <?php $sum_visit_total_pp += $row->visit_total_pp ; ?>
                  <?php $sum_inc_total += $row->inc_total ; ?>
                  <?php $sum_inc_lab_total += $row->inc_lab_total ; ?>
                  <?php $sum_inc_drug_total += $row->inc_drug_total ; ?>
                  <?php $sum_visit_ucs_incup += $row->visit_ucs_incup ; ?>
                  <?php $sum_inc_ucs_incup += $row->inc_ucs_incup ; ?>
                  <?php $sum_inc_lab_ucs_incup += $row->inc_lab_ucs_incup ; ?>
                  <?php $sum_inc_drug_ucs_incup += $row->inc_drug_ucs_incup ; ?>   
                  <?php $sum_visit_ucs_inprov += $row->visit_ucs_inprov ; ?>
                  <?php $sum_inc_ucs_inprov += $row->inc_ucs_inprov ; ?>
                  <?php $sum_inc_lab_ucs_inprov += $row->inc_lab_ucs_inprov ; ?>
                  <?php $sum_inc_drug_ucs_inprov += $row->inc_drug_ucs_inprov ; ?>
                  <?php $sum_visit_ucs_outprov += $row->visit_ucs_outprov ; ?>
                  <?php $sum_inc_ucs_outprov += $row->inc_ucs_outprov ; ?>
                  <?php $sum_inc_lab_ucs_outprov += $row->inc_lab_ucs_outprov ; ?>
                  <?php $sum_inc_drug_ucs_outprov += $row->inc_drug_ucs_outprov ; ?> 
                  <?php $sum_visit_ofc += $row->visit_ofc ; ?>
                  <?php $sum_inc_ofc += $row->inc_ofc ; ?>
                  <?php $sum_inc_lab_ofc += $row->inc_lab_ofc ; ?>
                  <?php $sum_inc_drug_ofc += $row->inc_drug_ofc ; ?> 
                  <?php $sum_visit_bkk += $row->visit_bkk ; ?>
                  <?php $sum_inc_bkk += $row->inc_bkk ; ?>
                  <?php $sum_inc_lab_bkk += $row->inc_lab_bkk ; ?>
                  <?php $sum_inc_drug_bkk += $row->inc_drug_bkk ; ?>  
                  <?php $sum_visit_bmt += $row->visit_bmt ; ?>
                  <?php $sum_inc_bmt += $row->inc_bmt ; ?>
                  <?php $sum_inc_lab_bmt += $row->inc_lab_bmt ; ?>
                  <?php $sum_inc_drug_bmt += $row->inc_drug_bmt ; ?> 
                  <?php $sum_visit_sss += $row->visit_sss ; ?>
                  <?php $sum_inc_sss += $row->inc_sss ; ?>
                  <?php $sum_inc_lab_sss += $row->inc_lab_sss ; ?>
                  <?php $sum_inc_drug_sss += $row->inc_drug_sss ; ?>   
                  <?php $sum_visit_lgo += $row->visit_lgo ; ?>
                  <?php $sum_inc_lgo += $row->inc_lgo ; ?>
                  <?php $sum_inc_lab_lgo += $row->inc_lab_lgo ; ?>
                  <?php $sum_inc_drug_lgo += $row->inc_drug_lgo ; ?>
                  <?php $sum_visit_fss += $row->visit_fss ; ?>
                  <?php $sum_inc_fss += $row->inc_fss ; ?>
                  <?php $sum_inc_lab_fss += $row->inc_lab_fss ; ?>
                  <?php $sum_inc_drug_fss += $row->inc_drug_fss ; ?>    
                  <?php $sum_visit_stp += $row->visit_stp ; ?>
                  <?php $sum_inc_stp += $row->inc_stp ; ?>
                  <?php $sum_inc_lab_stp += $row->inc_lab_stp ; ?>
                  <?php $sum_inc_drug_stp += $row->inc_drug_stp ; ?>   
                  <?php $sum_visit_pay += $row->visit_pay ; ?>
                  <?php $sum_inc_pay += $row->inc_pay ; ?>
                  <?php $sum_inc_lab_pay += $row->inc_lab_pay ; ?>
                  <?php $sum_inc_drug_pay += $row->inc_drug_pay ; ?> 
                  @endforeach    
                  <tr>
                    <td align="right"><strong>รวม</strong></td>
                    <td align="right"><strong>{{number_format($sum_hn_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_total_op)}}</strong></td>     
                    <td align="right"><strong>{{number_format($sum_visit_total_pp)}}</strong></td>   
                    <td align="right"><strong>{{number_format($sum_inc_total,2)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_inc_lab_total,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_inc_drug_total,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_ucs_incup)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ucs_incup,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ucs_incup,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ucs_incup,2)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_visit_ucs_inprov)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ucs_inprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ucs_inprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ucs_inprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_ucs_outprov)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ucs_outprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ucs_outprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ucs_outprov,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_visit_ofc)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ofc,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ofc,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ofc,2)}}</strong></td>   
                    <td align="right"><strong>{{number_format($sum_visit_bkk)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_bkk,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_bkk,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_bkk,2)}}</strong></td>   
                    <td align="right"><strong>{{number_format($sum_visit_bmt)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_bmt,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_bmt,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_bmt,2)}}</strong></td>    
                    <td align="right"><strong>{{number_format($sum_visit_sss)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_sss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_sss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_sss,2)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_visit_lgo)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lgo,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_lgo,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_lgo,2)}}</strong></td>       
                    <td align="right"><strong>{{number_format($sum_visit_fss)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_fss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_fss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_fss,2)}}</strong></td>    
                    <td align="right"><strong>{{number_format($sum_visit_stp)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_stp,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_stp,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_stp,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_visit_pay)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_pay,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_pay,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_pay,2)}}</strong></td> 
                  </tr>   
                </tbody>
              </table>
            </div>
          </div>
        <!-- END 10988 -->                
        </div>

        <!-- 10989 OPD -->
        <div class="tab-pane fade" id="pane-10989" role="tabpanel" aria-labelledby="tab-10989" tabindex="0">
          <div class="glass p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6>[10989] ข้อมูลบริการผู้ป่วยนอก OPD โรงพยาบาลหัวตะพาน ปีงบประมาณ {{$budget_year}}</h6>
              <span class="text-secondary small">Update {{$update_at10989}}</span>   
            </div>
            <div class="table-responsive">
              <table id="table10989" class="table table-bordered table-striped my-3" width ="100%">
                <thead class="table-light">
                  <tr class="table-primary">
                    <th class="text-center" rowspan="2" width ="4%">เดือน</th>
                    <th class="text-center" colspan="7">ทั้งหมด</th>
                    <th class="text-center" colspan="4">UCS ใน CUP</th> 
                    <th class="text-center" colspan="4">UCS ในจังหวัด</th>
                    <th class="text-center" colspan="4">UCS นอกจังหวัด</th>       
                    <th class="text-center" colspan="4">OFC ข้าราชการ</th>  
                    <th class="text-center" colspan="4">BKK กทม.</th>
                    <th class="text-center" colspan="4">BMT ขสมก.</th>
                    <th class="text-center" colspan="4">SSS ประกันสังคม</th>
                    <th class="text-center" colspan="4">LGO อปท.</th>
                    <th class="text-center" colspan="4">FSS ต่างด้าว</th>
                    <th class="text-center" colspan="4">STP Stateless</th>
                    <th class="text-center" colspan="4">ชำระเงิน/พรบ.</th>                 
                  </tr>    
                  <tr class="table-primary">            
                    <td class="text-center text-primary">HN Total</td>
                    <td class="text-center text-primary">Visit Total</td>
                    <td class="text-center text-primary">Visit OP</td>
                    <td class="text-center text-primary">Visit PP</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                  </tr>    
                </thead>
                <tbody>
                  <?php $count = 1 ; ?> 
                  <?php $sum_hn_total = 0 ; ?> 
                  <?php $sum_visit_total = 0 ; ?>   
                  <?php $sum_visit_total_op = 0 ; ?>  
                  <?php $sum_visit_total_pp = 0 ; ?> 
                  <?php $sum_inc_total = 0 ; ?>  
                  <?php $sum_inc_lab_total = 0 ; ?>
                  <?php $sum_inc_drug_total = 0 ; ?> 
                  <?php $sum_visit_ucs_incup = 0 ; ?>  
                  <?php $sum_inc_ucs_incup = 0 ; ?>  
                  <?php $sum_inc_lab_ucs_incup = 0 ; ?>  
                  <?php $sum_inc_drug_ucs_incup = 0 ; ?>  
                  <?php $sum_visit_ucs_inprov = 0 ; ?>  
                  <?php $sum_inc_ucs_inprov = 0 ; ?>  
                  <?php $sum_inc_lab_ucs_inprov = 0 ; ?> 
                  <?php $sum_inc_drug_ucs_inprov = 0 ; ?>   
                  <?php $sum_visit_ucs_outprov = 0 ; ?>  
                  <?php $sum_inc_ucs_outprov = 0 ; ?>
                  <?php $sum_inc_lab_ucs_outprov = 0 ; ?>
                  <?php $sum_inc_drug_ucs_outprov = 0 ; ?>  
                  <?php $sum_visit_ofc = 0 ; ?>  
                  <?php $sum_inc_ofc = 0 ; ?>
                  <?php $sum_inc_lab_ofc = 0 ; ?>
                  <?php $sum_inc_drug_ofc = 0 ; ?>
                  <?php $sum_visit_bkk = 0 ; ?>  
                  <?php $sum_inc_bkk = 0 ; ?>
                  <?php $sum_inc_lab_bkk = 0 ; ?>
                  <?php $sum_inc_drug_bkk = 0 ; ?> 
                  <?php $sum_visit_bmt = 0 ; ?>  
                  <?php $sum_inc_bmt = 0 ; ?>
                  <?php $sum_inc_lab_bmt = 0 ; ?>
                  <?php $sum_inc_drug_bmt = 0 ; ?>  
                  <?php $sum_visit_sss = 0 ; ?>  
                  <?php $sum_inc_sss = 0 ; ?>
                  <?php $sum_inc_lab_sss = 0 ; ?>
                  <?php $sum_inc_drug_sss = 0 ; ?> 
                  <?php $sum_visit_lgo = 0 ; ?>  
                  <?php $sum_inc_lgo = 0 ; ?>
                  <?php $sum_inc_lab_lgo = 0 ; ?>
                  <?php $sum_inc_drug_lgo = 0 ; ?> 
                  <?php $sum_visit_fss = 0 ; ?>  
                  <?php $sum_inc_fss = 0 ; ?>
                  <?php $sum_inc_lab_fss = 0 ; ?>
                  <?php $sum_inc_drug_fss = 0 ; ?> 
                  <?php $sum_visit_stp = 0 ; ?>  
                  <?php $sum_inc_stp = 0 ; ?>
                  <?php $sum_inc_lab_stp = 0 ; ?>
                  <?php $sum_inc_drug_stp = 0 ; ?>
                  <?php $sum_visit_pay = 0 ; ?>  
                  <?php $sum_inc_pay = 0 ; ?>
                  <?php $sum_inc_lab_pay = 0 ; ?>
                  <?php $sum_inc_drug_pay = 0 ; ?>  
                  @foreach($total_10989 as $row) 
                  <tr>
                    <td align="center"width ="4%">{{ $row->month }}</td>
                    <td align="right">{{ number_format($row->hn_total) }}</td>
                    <td align="right">{{ number_format($row->visit_total) }}</td>
                    <td align="right">{{ number_format($row->visit_total_op) }}</td>
                    <td align="right">{{ number_format($row->visit_total_pp) }}</td>
                    <td align="right">{{ number_format($row->inc_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_total,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ucs_incup) }}</td>
                    <td align="right">{{ number_format($row->inc_ucs_incup,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ucs_incup,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ucs_incup,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ucs_inprov) }}</td>
                    <td align="right">{{ number_format($row->inc_ucs_inprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ucs_inprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ucs_inprov,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ucs_outprov) }}</td>
                    <td align="right">{{ number_format($row->inc_ucs_outprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ucs_outprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ucs_outprov,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ofc) }}</td>
                    <td align="right">{{ number_format($row->inc_ofc,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ofc,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ofc,2) }}</td>
                    <td align="right">{{ number_format($row->visit_bkk) }}</td>
                    <td align="right">{{ number_format($row->inc_bkk,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_bkk,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_bkk,2) }}</td>
                    <td align="right">{{ number_format($row->visit_bmt) }}</td>
                    <td align="right">{{ number_format($row->inc_bmt,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_bmt,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_bmt,2) }}</td>
                    <td align="right">{{ number_format($row->visit_sss) }}</td>
                    <td align="right">{{ number_format($row->inc_sss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_sss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_sss,2) }}</td>
                    <td align="right">{{ number_format($row->visit_lgo) }}</td>
                    <td align="right">{{ number_format($row->inc_lgo,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_lgo,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_lgo,2) }}</td>
                    <td align="right">{{ number_format($row->visit_fss) }}</td>
                    <td align="right">{{ number_format($row->inc_fss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_fss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_fss,2) }}</td>
                    <td align="right">{{ number_format($row->visit_stp) }}</td>
                    <td align="right">{{ number_format($row->inc_stp,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_stp,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_stp,2) }}</td>
                    <td align="right">{{ number_format($row->visit_pay) }}</td>
                    <td align="right">{{ number_format($row->inc_pay,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_pay,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_pay,2) }}</td>
                  </tr>
                  <?php $count++; ?>
                  <?php $sum_hn_total += $row->hn_total ; ?>
                  <?php $sum_visit_total += $row->visit_total ; ?>
                  <?php $sum_visit_total_op += $row->visit_total_op ; ?>
                  <?php $sum_visit_total_pp += $row->visit_total_pp ; ?>
                  <?php $sum_inc_total += $row->inc_total ; ?>
                  <?php $sum_inc_lab_total += $row->inc_lab_total ; ?>
                  <?php $sum_inc_drug_total += $row->inc_drug_total ; ?>
                  <?php $sum_visit_ucs_incup += $row->visit_ucs_incup ; ?>
                  <?php $sum_inc_ucs_incup += $row->inc_ucs_incup ; ?>
                  <?php $sum_inc_lab_ucs_incup += $row->inc_lab_ucs_incup ; ?>
                  <?php $sum_inc_drug_ucs_incup += $row->inc_drug_ucs_incup ; ?>   
                  <?php $sum_visit_ucs_inprov += $row->visit_ucs_inprov ; ?>
                  <?php $sum_inc_ucs_inprov += $row->inc_ucs_inprov ; ?>
                  <?php $sum_inc_lab_ucs_inprov += $row->inc_lab_ucs_inprov ; ?>
                  <?php $sum_inc_drug_ucs_inprov += $row->inc_drug_ucs_inprov ; ?>
                  <?php $sum_visit_ucs_outprov += $row->visit_ucs_outprov ; ?>
                  <?php $sum_inc_ucs_outprov += $row->inc_ucs_outprov ; ?>
                  <?php $sum_inc_lab_ucs_outprov += $row->inc_lab_ucs_outprov ; ?>
                  <?php $sum_inc_drug_ucs_outprov += $row->inc_drug_ucs_outprov ; ?> 
                  <?php $sum_visit_ofc += $row->visit_ofc ; ?>
                  <?php $sum_inc_ofc += $row->inc_ofc ; ?>
                  <?php $sum_inc_lab_ofc += $row->inc_lab_ofc ; ?>
                  <?php $sum_inc_drug_ofc += $row->inc_drug_ofc ; ?> 
                  <?php $sum_visit_bkk += $row->visit_bkk ; ?>
                  <?php $sum_inc_bkk += $row->inc_bkk ; ?>
                  <?php $sum_inc_lab_bkk += $row->inc_lab_bkk ; ?>
                  <?php $sum_inc_drug_bkk += $row->inc_drug_bkk ; ?>  
                  <?php $sum_visit_bmt += $row->visit_bmt ; ?>
                  <?php $sum_inc_bmt += $row->inc_bmt ; ?>
                  <?php $sum_inc_lab_bmt += $row->inc_lab_bmt ; ?>
                  <?php $sum_inc_drug_bmt += $row->inc_drug_bmt ; ?> 
                  <?php $sum_visit_sss += $row->visit_sss ; ?>
                  <?php $sum_inc_sss += $row->inc_sss ; ?>
                  <?php $sum_inc_lab_sss += $row->inc_lab_sss ; ?>
                  <?php $sum_inc_drug_sss += $row->inc_drug_sss ; ?>   
                  <?php $sum_visit_lgo += $row->visit_lgo ; ?>
                  <?php $sum_inc_lgo += $row->inc_lgo ; ?>
                  <?php $sum_inc_lab_lgo += $row->inc_lab_lgo ; ?>
                  <?php $sum_inc_drug_lgo += $row->inc_drug_lgo ; ?>
                  <?php $sum_visit_fss += $row->visit_fss ; ?>
                  <?php $sum_inc_fss += $row->inc_fss ; ?>
                  <?php $sum_inc_lab_fss += $row->inc_lab_fss ; ?>
                  <?php $sum_inc_drug_fss += $row->inc_drug_fss ; ?>    
                  <?php $sum_visit_stp += $row->visit_stp ; ?>
                  <?php $sum_inc_stp += $row->inc_stp ; ?>
                  <?php $sum_inc_lab_stp += $row->inc_lab_stp ; ?>
                  <?php $sum_inc_drug_stp += $row->inc_drug_stp ; ?>   
                  <?php $sum_visit_pay += $row->visit_pay ; ?>
                  <?php $sum_inc_pay += $row->inc_pay ; ?>
                  <?php $sum_inc_lab_pay += $row->inc_lab_pay ; ?>
                  <?php $sum_inc_drug_pay += $row->inc_drug_pay ; ?> 
                  @endforeach    
                  <tr>
                    <td align="right"><strong>รวม</strong></td>
                    <td align="right"><strong>{{number_format($sum_hn_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_total_op)}}</strong></td>     
                    <td align="right"><strong>{{number_format($sum_visit_total_pp)}}</strong></td>   
                    <td align="right"><strong>{{number_format($sum_inc_total,2)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_inc_lab_total,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_inc_drug_total,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_ucs_incup)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ucs_incup,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ucs_incup,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ucs_incup,2)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_visit_ucs_inprov)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ucs_inprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ucs_inprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ucs_inprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_ucs_outprov)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ucs_outprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ucs_outprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ucs_outprov,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_visit_ofc)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ofc,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ofc,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ofc,2)}}</strong></td>   
                    <td align="right"><strong>{{number_format($sum_visit_bkk)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_bkk,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_bkk,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_bkk,2)}}</strong></td>   
                    <td align="right"><strong>{{number_format($sum_visit_bmt)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_bmt,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_bmt,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_bmt,2)}}</strong></td>    
                    <td align="right"><strong>{{number_format($sum_visit_sss)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_sss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_sss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_sss,2)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_visit_lgo)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lgo,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_lgo,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_lgo,2)}}</strong></td>       
                    <td align="right"><strong>{{number_format($sum_visit_fss)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_fss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_fss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_fss,2)}}</strong></td>    
                    <td align="right"><strong>{{number_format($sum_visit_stp)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_stp,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_stp,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_stp,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_visit_pay)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_pay,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_pay,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_pay,2)}}</strong></td> 
                  </tr>   
                </tbody>
              </table>
            </div>
          </div> 
        <!-- END 10989 -->          
        </div>

        <!-- 10990 OPD -->
        <div class="tab-pane fade" id="pane-10990" role="tabpanel" aria-labelledby="tab-10990" tabindex="0">
          <div class="glass p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6>[10990] ข้อมูลบริการผู้ป่วยนอก OPD โรงพยาบาลลืออำนาจ ปีงบประมาณ {{$budget_year}}</h6>
              <span class="text-secondary small">Update {{$update_at10990}}</span>   
            </div>
            <div class="table-responsive">
              <table id="table10990" class="table table-bordered table-striped my-3" width ="100%">
                <thead class="table-light">
                  <tr class="table-primary">
                    <th class="text-center" rowspan="2" width ="4%">เดือน</th>
                    <th class="text-center" colspan="7">ทั้งหมด</th>
                    <th class="text-center" colspan="4">UCS ใน CUP</th> 
                    <th class="text-center" colspan="4">UCS ในจังหวัด</th>
                    <th class="text-center" colspan="4">UCS นอกจังหวัด</th>       
                    <th class="text-center" colspan="4">OFC ข้าราชการ</th>  
                    <th class="text-center" colspan="4">BKK กทม.</th>
                    <th class="text-center" colspan="4">BMT ขสมก.</th>
                    <th class="text-center" colspan="4">SSS ประกันสังคม</th>
                    <th class="text-center" colspan="4">LGO อปท.</th>
                    <th class="text-center" colspan="4">FSS ต่างด้าว</th>
                    <th class="text-center" colspan="4">STP Stateless</th>
                    <th class="text-center" colspan="4">ชำระเงิน/พรบ.</th>                 
                  </tr>    
                  <tr class="table-primary">            
                    <td class="text-center text-primary">HN Total</td>
                    <td class="text-center text-primary">Visit Total</td>
                    <td class="text-center text-primary">Visit OP</td>
                    <td class="text-center text-primary">Visit PP</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                  </tr>    
                </thead>
                <tbody>
                  <?php $count = 1 ; ?> 
                  <?php $sum_hn_total = 0 ; ?> 
                  <?php $sum_visit_total = 0 ; ?>   
                  <?php $sum_visit_total_op = 0 ; ?>  
                  <?php $sum_visit_total_pp = 0 ; ?> 
                  <?php $sum_inc_total = 0 ; ?>  
                  <?php $sum_inc_lab_total = 0 ; ?>
                  <?php $sum_inc_drug_total = 0 ; ?> 
                  <?php $sum_visit_ucs_incup = 0 ; ?>  
                  <?php $sum_inc_ucs_incup = 0 ; ?>  
                  <?php $sum_inc_lab_ucs_incup = 0 ; ?>  
                  <?php $sum_inc_drug_ucs_incup = 0 ; ?>  
                  <?php $sum_visit_ucs_inprov = 0 ; ?>  
                  <?php $sum_inc_ucs_inprov = 0 ; ?>  
                  <?php $sum_inc_lab_ucs_inprov = 0 ; ?> 
                  <?php $sum_inc_drug_ucs_inprov = 0 ; ?>   
                  <?php $sum_visit_ucs_outprov = 0 ; ?>  
                  <?php $sum_inc_ucs_outprov = 0 ; ?>
                  <?php $sum_inc_lab_ucs_outprov = 0 ; ?>
                  <?php $sum_inc_drug_ucs_outprov = 0 ; ?>  
                  <?php $sum_visit_ofc = 0 ; ?>  
                  <?php $sum_inc_ofc = 0 ; ?>
                  <?php $sum_inc_lab_ofc = 0 ; ?>
                  <?php $sum_inc_drug_ofc = 0 ; ?>
                  <?php $sum_visit_bkk = 0 ; ?>  
                  <?php $sum_inc_bkk = 0 ; ?>
                  <?php $sum_inc_lab_bkk = 0 ; ?>
                  <?php $sum_inc_drug_bkk = 0 ; ?> 
                  <?php $sum_visit_bmt = 0 ; ?>  
                  <?php $sum_inc_bmt = 0 ; ?>
                  <?php $sum_inc_lab_bmt = 0 ; ?>
                  <?php $sum_inc_drug_bmt = 0 ; ?>  
                  <?php $sum_visit_sss = 0 ; ?>  
                  <?php $sum_inc_sss = 0 ; ?>
                  <?php $sum_inc_lab_sss = 0 ; ?>
                  <?php $sum_inc_drug_sss = 0 ; ?> 
                  <?php $sum_visit_lgo = 0 ; ?>  
                  <?php $sum_inc_lgo = 0 ; ?>
                  <?php $sum_inc_lab_lgo = 0 ; ?>
                  <?php $sum_inc_drug_lgo = 0 ; ?> 
                  <?php $sum_visit_fss = 0 ; ?>  
                  <?php $sum_inc_fss = 0 ; ?>
                  <?php $sum_inc_lab_fss = 0 ; ?>
                  <?php $sum_inc_drug_fss = 0 ; ?> 
                  <?php $sum_visit_stp = 0 ; ?>  
                  <?php $sum_inc_stp = 0 ; ?>
                  <?php $sum_inc_lab_stp = 0 ; ?>
                  <?php $sum_inc_drug_stp = 0 ; ?>
                  <?php $sum_visit_pay = 0 ; ?>  
                  <?php $sum_inc_pay = 0 ; ?>
                  <?php $sum_inc_lab_pay = 0 ; ?>
                  <?php $sum_inc_drug_pay = 0 ; ?>  
                  @foreach($total_10990 as $row) 
                  <tr>
                    <td align="center"width ="4%">{{ $row->month }}</td>
                    <td align="right">{{ number_format($row->hn_total) }}</td>
                    <td align="right">{{ number_format($row->visit_total) }}</td>
                    <td align="right">{{ number_format($row->visit_total_op) }}</td>
                    <td align="right">{{ number_format($row->visit_total_pp) }}</td>
                    <td align="right">{{ number_format($row->inc_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_total,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ucs_incup) }}</td>
                    <td align="right">{{ number_format($row->inc_ucs_incup,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ucs_incup,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ucs_incup,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ucs_inprov) }}</td>
                    <td align="right">{{ number_format($row->inc_ucs_inprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ucs_inprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ucs_inprov,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ucs_outprov) }}</td>
                    <td align="right">{{ number_format($row->inc_ucs_outprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ucs_outprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ucs_outprov,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ofc) }}</td>
                    <td align="right">{{ number_format($row->inc_ofc,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ofc,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ofc,2) }}</td>
                    <td align="right">{{ number_format($row->visit_bkk) }}</td>
                    <td align="right">{{ number_format($row->inc_bkk,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_bkk,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_bkk,2) }}</td>
                    <td align="right">{{ number_format($row->visit_bmt) }}</td>
                    <td align="right">{{ number_format($row->inc_bmt,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_bmt,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_bmt,2) }}</td>
                    <td align="right">{{ number_format($row->visit_sss) }}</td>
                    <td align="right">{{ number_format($row->inc_sss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_sss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_sss,2) }}</td>
                    <td align="right">{{ number_format($row->visit_lgo) }}</td>
                    <td align="right">{{ number_format($row->inc_lgo,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_lgo,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_lgo,2) }}</td>
                    <td align="right">{{ number_format($row->visit_fss) }}</td>
                    <td align="right">{{ number_format($row->inc_fss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_fss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_fss,2) }}</td>
                    <td align="right">{{ number_format($row->visit_stp) }}</td>
                    <td align="right">{{ number_format($row->inc_stp,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_stp,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_stp,2) }}</td>
                    <td align="right">{{ number_format($row->visit_pay) }}</td>
                    <td align="right">{{ number_format($row->inc_pay,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_pay,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_pay,2) }}</td>
                  </tr>
                  <?php $count++; ?>
                  <?php $sum_hn_total += $row->hn_total ; ?>
                  <?php $sum_visit_total += $row->visit_total ; ?>
                  <?php $sum_visit_total_op += $row->visit_total_op ; ?>
                  <?php $sum_visit_total_pp += $row->visit_total_pp ; ?>
                  <?php $sum_inc_total += $row->inc_total ; ?>
                  <?php $sum_inc_lab_total += $row->inc_lab_total ; ?>
                  <?php $sum_inc_drug_total += $row->inc_drug_total ; ?>
                  <?php $sum_visit_ucs_incup += $row->visit_ucs_incup ; ?>
                  <?php $sum_inc_ucs_incup += $row->inc_ucs_incup ; ?>
                  <?php $sum_inc_lab_ucs_incup += $row->inc_lab_ucs_incup ; ?>
                  <?php $sum_inc_drug_ucs_incup += $row->inc_drug_ucs_incup ; ?>   
                  <?php $sum_visit_ucs_inprov += $row->visit_ucs_inprov ; ?>
                  <?php $sum_inc_ucs_inprov += $row->inc_ucs_inprov ; ?>
                  <?php $sum_inc_lab_ucs_inprov += $row->inc_lab_ucs_inprov ; ?>
                  <?php $sum_inc_drug_ucs_inprov += $row->inc_drug_ucs_inprov ; ?>
                  <?php $sum_visit_ucs_outprov += $row->visit_ucs_outprov ; ?>
                  <?php $sum_inc_ucs_outprov += $row->inc_ucs_outprov ; ?>
                  <?php $sum_inc_lab_ucs_outprov += $row->inc_lab_ucs_outprov ; ?>
                  <?php $sum_inc_drug_ucs_outprov += $row->inc_drug_ucs_outprov ; ?> 
                  <?php $sum_visit_ofc += $row->visit_ofc ; ?>
                  <?php $sum_inc_ofc += $row->inc_ofc ; ?>
                  <?php $sum_inc_lab_ofc += $row->inc_lab_ofc ; ?>
                  <?php $sum_inc_drug_ofc += $row->inc_drug_ofc ; ?> 
                  <?php $sum_visit_bkk += $row->visit_bkk ; ?>
                  <?php $sum_inc_bkk += $row->inc_bkk ; ?>
                  <?php $sum_inc_lab_bkk += $row->inc_lab_bkk ; ?>
                  <?php $sum_inc_drug_bkk += $row->inc_drug_bkk ; ?>  
                  <?php $sum_visit_bmt += $row->visit_bmt ; ?>
                  <?php $sum_inc_bmt += $row->inc_bmt ; ?>
                  <?php $sum_inc_lab_bmt += $row->inc_lab_bmt ; ?>
                  <?php $sum_inc_drug_bmt += $row->inc_drug_bmt ; ?> 
                  <?php $sum_visit_sss += $row->visit_sss ; ?>
                  <?php $sum_inc_sss += $row->inc_sss ; ?>
                  <?php $sum_inc_lab_sss += $row->inc_lab_sss ; ?>
                  <?php $sum_inc_drug_sss += $row->inc_drug_sss ; ?>   
                  <?php $sum_visit_lgo += $row->visit_lgo ; ?>
                  <?php $sum_inc_lgo += $row->inc_lgo ; ?>
                  <?php $sum_inc_lab_lgo += $row->inc_lab_lgo ; ?>
                  <?php $sum_inc_drug_lgo += $row->inc_drug_lgo ; ?>
                  <?php $sum_visit_fss += $row->visit_fss ; ?>
                  <?php $sum_inc_fss += $row->inc_fss ; ?>
                  <?php $sum_inc_lab_fss += $row->inc_lab_fss ; ?>
                  <?php $sum_inc_drug_fss += $row->inc_drug_fss ; ?>    
                  <?php $sum_visit_stp += $row->visit_stp ; ?>
                  <?php $sum_inc_stp += $row->inc_stp ; ?>
                  <?php $sum_inc_lab_stp += $row->inc_lab_stp ; ?>
                  <?php $sum_inc_drug_stp += $row->inc_drug_stp ; ?>   
                  <?php $sum_visit_pay += $row->visit_pay ; ?>
                  <?php $sum_inc_pay += $row->inc_pay ; ?>
                  <?php $sum_inc_lab_pay += $row->inc_lab_pay ; ?>
                  <?php $sum_inc_drug_pay += $row->inc_drug_pay ; ?> 
                  @endforeach    
                  <tr>
                    <td align="right"><strong>รวม</strong></td>
                    <td align="right"><strong>{{number_format($sum_hn_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_total_op)}}</strong></td>     
                    <td align="right"><strong>{{number_format($sum_visit_total_pp)}}</strong></td>   
                    <td align="right"><strong>{{number_format($sum_inc_total,2)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_inc_lab_total,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_inc_drug_total,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_ucs_incup)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ucs_incup,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ucs_incup,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ucs_incup,2)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_visit_ucs_inprov)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ucs_inprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ucs_inprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ucs_inprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_ucs_outprov)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ucs_outprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ucs_outprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ucs_outprov,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_visit_ofc)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ofc,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ofc,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ofc,2)}}</strong></td>   
                    <td align="right"><strong>{{number_format($sum_visit_bkk)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_bkk,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_bkk,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_bkk,2)}}</strong></td>   
                    <td align="right"><strong>{{number_format($sum_visit_bmt)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_bmt,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_bmt,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_bmt,2)}}</strong></td>    
                    <td align="right"><strong>{{number_format($sum_visit_sss)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_sss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_sss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_sss,2)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_visit_lgo)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lgo,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_lgo,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_lgo,2)}}</strong></td>       
                    <td align="right"><strong>{{number_format($sum_visit_fss)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_fss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_fss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_fss,2)}}</strong></td>    
                    <td align="right"><strong>{{number_format($sum_visit_stp)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_stp,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_stp,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_stp,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_visit_pay)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_pay,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_pay,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_pay,2)}}</strong></td> 
                  </tr>   
                </tbody>
              </table>
            </div>
          </div>
        <!-- END 10990 --> 
        </div>

        <!-- 10703 OPD -->
        <div class="tab-pane fade" id="pane-10703" role="tabpanel" aria-labelledby="tab-10703" tabindex="0">
          <div class="glass p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6>[10703] ข้อมูลบริการผู้ป่วยนอก OPD โรงพยาบาลอำนาจเจริญ ปีงบประมาณ {{$budget_year}}</h6>
              <span class="text-secondary small">Update {{$update_at10703}}</span>   
            </div>
            <div class="table-responsive">
              <table id="table10703" class="table table-bordered table-striped my-3" width ="100%">
                <thead class="table-light">
                  <tr class="table-primary">
                    <th class="text-center" rowspan="2" width ="4%">เดือน</th>
                    <th class="text-center" colspan="7">ทั้งหมด</th>
                    <th class="text-center" colspan="4">UCS ใน CUP</th> 
                    <th class="text-center" colspan="4">UCS ในจังหวัด</th>
                    <th class="text-center" colspan="4">UCS นอกจังหวัด</th>       
                    <th class="text-center" colspan="4">OFC ข้าราชการ</th>  
                    <th class="text-center" colspan="4">BKK กทม.</th>
                    <th class="text-center" colspan="4">BMT ขสมก.</th>
                    <th class="text-center" colspan="4">SSS ประกันสังคม</th>
                    <th class="text-center" colspan="4">LGO อปท.</th>
                    <th class="text-center" colspan="4">FSS ต่างด้าว</th>
                    <th class="text-center" colspan="4">STP Stateless</th>
                    <th class="text-center" colspan="4">ชำระเงิน/พรบ.</th>                 
                  </tr>    
                  <tr class="table-primary">            
                    <td class="text-center text-primary">HN Total</td>
                    <td class="text-center text-primary">Visit Total</td>
                    <td class="text-center text-primary">Visit OP</td>
                    <td class="text-center text-primary">Visit PP</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                    <td class="text-center text-primary">Visit</td>
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>
                  </tr>    
                </thead>
                <tbody>
                  <?php $count = 1 ; ?> 
                  <?php $sum_hn_total = 0 ; ?> 
                  <?php $sum_visit_total = 0 ; ?>   
                  <?php $sum_visit_total_op = 0 ; ?>  
                  <?php $sum_visit_total_pp = 0 ; ?> 
                  <?php $sum_inc_total = 0 ; ?>  
                  <?php $sum_inc_lab_total = 0 ; ?>
                  <?php $sum_inc_drug_total = 0 ; ?> 
                  <?php $sum_visit_ucs_incup = 0 ; ?>  
                  <?php $sum_inc_ucs_incup = 0 ; ?>  
                  <?php $sum_inc_lab_ucs_incup = 0 ; ?>  
                  <?php $sum_inc_drug_ucs_incup = 0 ; ?>  
                  <?php $sum_visit_ucs_inprov = 0 ; ?>  
                  <?php $sum_inc_ucs_inprov = 0 ; ?>  
                  <?php $sum_inc_lab_ucs_inprov = 0 ; ?> 
                  <?php $sum_inc_drug_ucs_inprov = 0 ; ?>   
                  <?php $sum_visit_ucs_outprov = 0 ; ?>  
                  <?php $sum_inc_ucs_outprov = 0 ; ?>
                  <?php $sum_inc_lab_ucs_outprov = 0 ; ?>
                  <?php $sum_inc_drug_ucs_outprov = 0 ; ?>  
                  <?php $sum_visit_ofc = 0 ; ?>  
                  <?php $sum_inc_ofc = 0 ; ?>
                  <?php $sum_inc_lab_ofc = 0 ; ?>
                  <?php $sum_inc_drug_ofc = 0 ; ?>
                  <?php $sum_visit_bkk = 0 ; ?>  
                  <?php $sum_inc_bkk = 0 ; ?>
                  <?php $sum_inc_lab_bkk = 0 ; ?>
                  <?php $sum_inc_drug_bkk = 0 ; ?> 
                  <?php $sum_visit_bmt = 0 ; ?>  
                  <?php $sum_inc_bmt = 0 ; ?>
                  <?php $sum_inc_lab_bmt = 0 ; ?>
                  <?php $sum_inc_drug_bmt = 0 ; ?>  
                  <?php $sum_visit_sss = 0 ; ?>  
                  <?php $sum_inc_sss = 0 ; ?>
                  <?php $sum_inc_lab_sss = 0 ; ?>
                  <?php $sum_inc_drug_sss = 0 ; ?> 
                  <?php $sum_visit_lgo = 0 ; ?>  
                  <?php $sum_inc_lgo = 0 ; ?>
                  <?php $sum_inc_lab_lgo = 0 ; ?>
                  <?php $sum_inc_drug_lgo = 0 ; ?> 
                  <?php $sum_visit_fss = 0 ; ?>  
                  <?php $sum_inc_fss = 0 ; ?>
                  <?php $sum_inc_lab_fss = 0 ; ?>
                  <?php $sum_inc_drug_fss = 0 ; ?> 
                  <?php $sum_visit_stp = 0 ; ?>  
                  <?php $sum_inc_stp = 0 ; ?>
                  <?php $sum_inc_lab_stp = 0 ; ?>
                  <?php $sum_inc_drug_stp = 0 ; ?>
                  <?php $sum_visit_pay = 0 ; ?>  
                  <?php $sum_inc_pay = 0 ; ?>
                  <?php $sum_inc_lab_pay = 0 ; ?>
                  <?php $sum_inc_drug_pay = 0 ; ?>  
                  @foreach($total_10703 as $row) 
                  <tr>
                    <td align="center"width ="4%">{{ $row->month }}</td>
                    <td align="right">{{ number_format($row->hn_total) }}</td>
                    <td align="right">{{ number_format($row->visit_total) }}</td>
                    <td align="right">{{ number_format($row->visit_total_op) }}</td>
                    <td align="right">{{ number_format($row->visit_total_pp) }}</td>
                    <td align="right">{{ number_format($row->inc_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_total,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ucs_incup) }}</td>
                    <td align="right">{{ number_format($row->inc_ucs_incup,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ucs_incup,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ucs_incup,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ucs_inprov) }}</td>
                    <td align="right">{{ number_format($row->inc_ucs_inprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ucs_inprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ucs_inprov,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ucs_outprov) }}</td>
                    <td align="right">{{ number_format($row->inc_ucs_outprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ucs_outprov,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ucs_outprov,2) }}</td>
                    <td align="right">{{ number_format($row->visit_ofc) }}</td>
                    <td align="right">{{ number_format($row->inc_ofc,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_ofc,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_ofc,2) }}</td>
                    <td align="right">{{ number_format($row->visit_bkk) }}</td>
                    <td align="right">{{ number_format($row->inc_bkk,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_bkk,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_bkk,2) }}</td>
                    <td align="right">{{ number_format($row->visit_bmt) }}</td>
                    <td align="right">{{ number_format($row->inc_bmt,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_bmt,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_bmt,2) }}</td>
                    <td align="right">{{ number_format($row->visit_sss) }}</td>
                    <td align="right">{{ number_format($row->inc_sss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_sss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_sss,2) }}</td>
                    <td align="right">{{ number_format($row->visit_lgo) }}</td>
                    <td align="right">{{ number_format($row->inc_lgo,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_lgo,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_lgo,2) }}</td>
                    <td align="right">{{ number_format($row->visit_fss) }}</td>
                    <td align="right">{{ number_format($row->inc_fss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_fss,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_fss,2) }}</td>
                    <td align="right">{{ number_format($row->visit_stp) }}</td>
                    <td align="right">{{ number_format($row->inc_stp,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_stp,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_stp,2) }}</td>
                    <td align="right">{{ number_format($row->visit_pay) }}</td>
                    <td align="right">{{ number_format($row->inc_pay,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_pay,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_pay,2) }}</td>
                  </tr>
                  <?php $count++; ?>
                  <?php $sum_hn_total += $row->hn_total ; ?>
                  <?php $sum_visit_total += $row->visit_total ; ?>
                  <?php $sum_visit_total_op += $row->visit_total_op ; ?>
                  <?php $sum_visit_total_pp += $row->visit_total_pp ; ?>
                  <?php $sum_inc_total += $row->inc_total ; ?>
                  <?php $sum_inc_lab_total += $row->inc_lab_total ; ?>
                  <?php $sum_inc_drug_total += $row->inc_drug_total ; ?>
                  <?php $sum_visit_ucs_incup += $row->visit_ucs_incup ; ?>
                  <?php $sum_inc_ucs_incup += $row->inc_ucs_incup ; ?>
                  <?php $sum_inc_lab_ucs_incup += $row->inc_lab_ucs_incup ; ?>
                  <?php $sum_inc_drug_ucs_incup += $row->inc_drug_ucs_incup ; ?>   
                  <?php $sum_visit_ucs_inprov += $row->visit_ucs_inprov ; ?>
                  <?php $sum_inc_ucs_inprov += $row->inc_ucs_inprov ; ?>
                  <?php $sum_inc_lab_ucs_inprov += $row->inc_lab_ucs_inprov ; ?>
                  <?php $sum_inc_drug_ucs_inprov += $row->inc_drug_ucs_inprov ; ?>
                  <?php $sum_visit_ucs_outprov += $row->visit_ucs_outprov ; ?>
                  <?php $sum_inc_ucs_outprov += $row->inc_ucs_outprov ; ?>
                  <?php $sum_inc_lab_ucs_outprov += $row->inc_lab_ucs_outprov ; ?>
                  <?php $sum_inc_drug_ucs_outprov += $row->inc_drug_ucs_outprov ; ?> 
                  <?php $sum_visit_ofc += $row->visit_ofc ; ?>
                  <?php $sum_inc_ofc += $row->inc_ofc ; ?>
                  <?php $sum_inc_lab_ofc += $row->inc_lab_ofc ; ?>
                  <?php $sum_inc_drug_ofc += $row->inc_drug_ofc ; ?> 
                  <?php $sum_visit_bkk += $row->visit_bkk ; ?>
                  <?php $sum_inc_bkk += $row->inc_bkk ; ?>
                  <?php $sum_inc_lab_bkk += $row->inc_lab_bkk ; ?>
                  <?php $sum_inc_drug_bkk += $row->inc_drug_bkk ; ?>  
                  <?php $sum_visit_bmt += $row->visit_bmt ; ?>
                  <?php $sum_inc_bmt += $row->inc_bmt ; ?>
                  <?php $sum_inc_lab_bmt += $row->inc_lab_bmt ; ?>
                  <?php $sum_inc_drug_bmt += $row->inc_drug_bmt ; ?> 
                  <?php $sum_visit_sss += $row->visit_sss ; ?>
                  <?php $sum_inc_sss += $row->inc_sss ; ?>
                  <?php $sum_inc_lab_sss += $row->inc_lab_sss ; ?>
                  <?php $sum_inc_drug_sss += $row->inc_drug_sss ; ?>   
                  <?php $sum_visit_lgo += $row->visit_lgo ; ?>
                  <?php $sum_inc_lgo += $row->inc_lgo ; ?>
                  <?php $sum_inc_lab_lgo += $row->inc_lab_lgo ; ?>
                  <?php $sum_inc_drug_lgo += $row->inc_drug_lgo ; ?>
                  <?php $sum_visit_fss += $row->visit_fss ; ?>
                  <?php $sum_inc_fss += $row->inc_fss ; ?>
                  <?php $sum_inc_lab_fss += $row->inc_lab_fss ; ?>
                  <?php $sum_inc_drug_fss += $row->inc_drug_fss ; ?>    
                  <?php $sum_visit_stp += $row->visit_stp ; ?>
                  <?php $sum_inc_stp += $row->inc_stp ; ?>
                  <?php $sum_inc_lab_stp += $row->inc_lab_stp ; ?>
                  <?php $sum_inc_drug_stp += $row->inc_drug_stp ; ?>   
                  <?php $sum_visit_pay += $row->visit_pay ; ?>
                  <?php $sum_inc_pay += $row->inc_pay ; ?>
                  <?php $sum_inc_lab_pay += $row->inc_lab_pay ; ?>
                  <?php $sum_inc_drug_pay += $row->inc_drug_pay ; ?> 
                  @endforeach    
                  <tr>
                    <td align="right"><strong>รวม</strong></td>
                    <td align="right"><strong>{{number_format($sum_hn_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_total_op)}}</strong></td>     
                    <td align="right"><strong>{{number_format($sum_visit_total_pp)}}</strong></td>   
                    <td align="right"><strong>{{number_format($sum_inc_total,2)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_inc_lab_total,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_inc_drug_total,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_ucs_incup)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ucs_incup,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ucs_incup,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ucs_incup,2)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_visit_ucs_inprov)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ucs_inprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ucs_inprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ucs_inprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_visit_ucs_outprov)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ucs_outprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ucs_outprov,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ucs_outprov,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_visit_ofc)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_ofc,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_ofc,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_ofc,2)}}</strong></td>   
                    <td align="right"><strong>{{number_format($sum_visit_bkk)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_bkk,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_bkk,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_bkk,2)}}</strong></td>   
                    <td align="right"><strong>{{number_format($sum_visit_bmt)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_bmt,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_bmt,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_bmt,2)}}</strong></td>    
                    <td align="right"><strong>{{number_format($sum_visit_sss)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_sss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_sss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_sss,2)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_visit_lgo)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lgo,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_lgo,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_lgo,2)}}</strong></td>       
                    <td align="right"><strong>{{number_format($sum_visit_fss)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_fss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_fss,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_fss,2)}}</strong></td>    
                    <td align="right"><strong>{{number_format($sum_visit_stp)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_stp,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_stp,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_stp,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_visit_pay)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_pay,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_pay,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_pay,2)}}</strong></td> 
                  </tr>   
                </tbody>
              </table>
            </div>
          </div>
        <!-- END 10703 --> 
        </div>

      </div>
    </div>
  </section>

<!-- แจังเตือน login--------------------------------------------------------------------------------------------------- -->
   @if($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'ผิดพลาด',
            text: '{{ $errors->first() }}', // แสดง error แรก
            confirmButtonText: 'ตกลง'
        });
    </script>
    @endif

@endsection

<!-- script datatable  ---------------------------------------------------------------------------------------->
@push('scripts')
  <script>
    $(function () {
      $('#table10985').DataTable({
        dom: '<"d-flex justify-content-end mb-2"B>rt',
        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-excel"></i> ส่งออก Excel',
            className: 'btn btn-success btn-sm',
            title: 'ข้อมูลบริการผู้ป่วยนอก OPD โรงพยาบาลชานุมาน {{ $budget_year ?? "" }}'
          }
        ],
        ordering: false,
        paging: false,
        info: false,
        lengthChange: false,
        language: { search: "ค้นหา:" }
      });
    });
  </script>
  <script>
    $(function () {
      $('#table10986').DataTable({
        dom: '<"d-flex justify-content-end mb-2"B>rt',
        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-excel"></i> ส่งออก Excel',
            className: 'btn btn-success btn-sm',
            title: 'ข้อมูลบริการผู้ป่วยนอก OPD โรงพยาบาลปทุมราชวงศา {{ $budget_year ?? "" }}'
          }
        ],
        ordering: false,
        paging: false,
        info: false,
        lengthChange: false,
        language: { search: "ค้นหา:" }
      });
    });
  </script>
  <script>
    $(function () {
      $('#table10987').DataTable({
        dom: '<"d-flex justify-content-end mb-2"B>rt',
        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-excel"></i> ส่งออก Excel',
            className: 'btn btn-success btn-sm',
            title: 'ข้อมูลบริการผู้ป่วยนอก OPD โรงพยาบาลพนา {{ $budget_year ?? "" }}'
          }
        ],
        ordering: false,
        paging: false,
        info: false,
        lengthChange: false,
        language: { search: "ค้นหา:" }
      });
    });
  </script>
  <script>
    $(function () {
      $('#table10988').DataTable({
        dom: '<"d-flex justify-content-end mb-2"B>rt',
        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-excel"></i> ส่งออก Excel',
            className: 'btn btn-success btn-sm',
            title: 'ข้อมูลบริการผู้ป่วยนอก OPD โรงพยาบาลเสนางคนิคม {{ $budget_year ?? "" }}'
          }
        ],
        ordering: false,
        paging: false,
        info: false,
        lengthChange: false,
        language: { search: "ค้นหา:" }
      });
    });
  </script>
  <script>
    $(function () {
      $('#table10989').DataTable({
        dom: '<"d-flex justify-content-end mb-2"B>rt',
        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-excel"></i> ส่งออก Excel',
            className: 'btn btn-success btn-sm',
            title: 'ข้อมูลบริการผู้ป่วยนอก OPD โรงพยาบาลหัวตะพาน {{ $budget_year ?? "" }}'
          }
        ],
        ordering: false,
        paging: false,
        info: false,
        lengthChange: false,
        language: { search: "ค้นหา:" }
      });
    });
  </script>
  <script>
    $(function () {
      $('#table10990').DataTable({
        dom: '<"d-flex justify-content-end mb-2"B>rt',
        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-excel"></i> ส่งออก Excel',
            className: 'btn btn-success btn-sm',
            title: 'ข้อมูลบริการผู้ป่วยนอก OPD โรงพยาบาลลืออำนาจ {{ $budget_year ?? "" }}'
          }
        ],
        ordering: false,
        paging: false,
        info: false,
        lengthChange: false,
        language: { search: "ค้นหา:" }
      });
    });
  </script>
  <script>
    $(function () {
      $('#table10703').DataTable({
        dom: '<"d-flex justify-content-end mb-2"B>rt',
        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-excel"></i> ส่งออก Excel',
            className: 'btn btn-success btn-sm',
            title: 'ข้อมูลบริการผู้ป่วยนอก OPD โรงพยาบาลอำนาจเจริญ {{ $budget_year ?? "" }}'
          }
        ],
        ordering: false,
        paging: false,
        info: false,
        lengthChange: false,
        language: { search: "ค้นหา:" }
      });
    });
  </script>
@endpush

