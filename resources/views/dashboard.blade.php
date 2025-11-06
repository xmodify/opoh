@extends('layouts.app')

@section('title', 'Dashboard | AOPOD')

<style>
  .card-hospital {
  border: none;
  backdrop-filter: blur(10px);
  transition: all 0.3s ease-in-out;
  }
  .card-hospital:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
  }

  /* 🌹 สีแดงพาสเทล (Admit) */
  .card-hospital.admit {
    background: linear-gradient(135deg, #f8bbd0, #ffffff);
  }

  /* 💜 สีม่วงพาสเทล (Refer Out) */
  .card-hospital.referout {
    background: linear-gradient(135deg, #d1c4e9, #faf5ff);
  }

  /* 💙 สีฟ้าพาสเทล (Refer In) */
  .card-hospital.referin {
    background: linear-gradient(135deg, #b3e5fc, #eff6ff);
  }

  /* 💚 สีเขียวพาสเทล (Refer Back) */
  .card-hospital.referback {
    background: linear-gradient(135deg, #d0f8ce, #f0fdff);
  }

  /* เพิ่มสีม่วงสำหรับข้อความ */
  .text-purple {
    color: #8b5cf6 !important;
  }

</style>

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

  <!-- SUMMARY (4 blocks, no foreach) -->
  <section id="summary" class="pb-2">
    <div class="container-fluid">
      @php
        $fmtInt   = fn($n) => number_format((int)($n ?? 0));
        $fmtMoney = fn($n) => number_format((float)($n ?? 0), 2);
      @endphp

      <div class="row g-3">
        
        <!-- กำลังรักษาอยู่ (แดงพาสเทล) --------------------------------------------------------------------------------------------->
        <div class="col-12 col-sm-6 col-xl-3">
          <a href="#" data-bs-toggle="modal" data-bs-target="#AdmiitDetailModal" class="text-decoration-none text-dark">
            <div class="card-hospital admit p-3 h-100 rounded-4 shadow-sm">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="mb-0 text-danger"><strong>กำลังรักษาอยู่</strong></h6>
                <i class="fa-solid fa-bed-pulse text-danger fs-5 "></i>
              </div>
              <div class="d-flex align-items-end gap-4">
                <div class="text-end">
                  <div class="small text-secondary text-center">จำนวนเตียง</div>
                  <div class="fw-bold text-primary" style="font-size:1.75rem;">
                    {{ $fmtInt($total_bed_qty ?? 0) }}
                  </div>
                </div>
                <div class="vr d-none d-sm-block"></div>
                <div class="text-end">
                  <div class="small text-secondary text-center">Admit</div>
                  <div class="fw-bold text-danger" style="font-size:1.75rem;">
                    {{ $fmtInt($total_bed_use ?? 0) }}
                  </div>
                </div>
                <div class="vr d-none d-sm-block"></div>
                <div class="text-end">
                  <div class="small text-secondary text-center">เตียงว่าง</div>
                  <div class="fw-bold text-success" style="font-size:1.75rem;">
                    {{ $fmtInt($total_bed_empty ?? 0) }}
                  </div>
                </div>
              </div>              
            </div>
          </a>    
        </div>
        {{-- Modal แสดงรายละเอียด รพ. (โทนน้ำเงินพาสเทลเข้ม / กรอบเล็ก) --}}
        <div class="modal fade" id="AdmiitDetailModal" tabindex="-1" aria-labelledby="hospitalDetailLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-3" style="background-color:#f5f8fc;">
              
              <!-- Header -->
              <div class="modal-header text-white rounded-top-3" 
                  style="background: linear-gradient(135deg, #2f6fb6, #4b8edc);">
                <h5 class="modal-title fw-bold" id="hospitalDetailLabel">
                  <i class="fa-solid fa-bed-pulse fs-5"></i> ข้อมูลเตียง
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              <!-- Body -->
              <div class="modal-body py-3">

                {{-- ✅ หน้ารวมโรงพยาบาล --}}
                <div id="hospital-list">
                  <table class="table table-hover align-middle shadow-sm rounded-3 overflow-hidden mb-0" 
                        style="background-color: #ffffff; border-radius: 0.75rem;">
                    <thead style="background-color:#d9e8fb;">
                      <tr class="text-center text-primary fw-semibold">
                        <th>รหัส</th>
                        <th>ชื่อโรงพยาบาล</th>
                        <th>จำนวนเตียง</th>
                        <th>Admit</th>
                        <th>เตียงว่าง</th>
                        <th>อัตราครองเตียง (%)</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($hospitals as $h)
                        @php
                          $bed_occupancy = $h->bed_qty > 0 ? ($h->bed_use / $h->bed_qty) * 100 : 0;
                          if ($bed_occupancy < 60) {
                            $rate_class = 'text-primary fw-semibold';
                          } elseif ($bed_occupancy < 80) {
                            $rate_class = 'text-warning fw-semibold';
                          } else {
                            $rate_class = 'text-danger fw-semibold';
                          }
                        @endphp

                        <tr>
                          <td align="right" class="text-secondary">{{ $h->hospcode }}</td>
                          <td>
                           <a href="#" 
                              class="fw-semibold text-dark text-decoration-none hosp-detail-link" 
                              data-hospcode="{{ $h->hospcode }}"
                              data-hospname="{{ $h->hospname }}">
                              {{ $h->hospname }}
                            </a><br>
                            <small class="text-muted">
                              {{ \Carbon\Carbon::parse($h->updated_at)->locale('th')->isoFormat('D MMM YYYY H:mm') }} น.
                            </small>
                          </td>
                          <td align="right" class="text-primary">{{ number_format($h->bed_qty) }}</td>
                          <td align="right" class="text-danger">{{ number_format($h->bed_use) }}</td>
                          <td align="right" class="fw-bold text-success">
                            {{ number_format($h->bed_qty - $h->bed_use) }}
                          </td>
                          <td align="right" class="{{ $rate_class }}">
                            {{ number_format($bed_occupancy, 2) }}%
                          </td>
                        </tr>
                      @endforeach

                      {{-- รวม --}}
                      @php
                        $sum_bed_qty = $hospitals->sum('bed_qty');
                        $sum_bed_use = $hospitals->sum('bed_use');
                        $total_occupancy = $sum_bed_qty > 0 ? ($sum_bed_use / $sum_bed_qty) * 100 : 0;
                      @endphp
                      <tr style="background-color:#eef4fb;" class="fw-bold text-end">
                        <td colspan="2" class="text-center text-dark">รวมทั้งหมด</td>
                        <td class="text-primary">{{ number_format($sum_bed_qty) }}</td>
                        <td class="text-danger">{{ number_format($sum_bed_use) }}</td>
                        <td class="text-success">{{ number_format($sum_bed_qty - $sum_bed_use) }}</td>
                        <td class="text-dark">{{ number_format($total_occupancy, 2) }}%</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                {{-- ✅ หน้ารายละเอียดเตียง (ซ่อนเริ่มต้น) --}}
                <div id="bed-detail" style="display: none;">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 id="modal-hospname" class="fw-bold text-success mb-0"></h6>
                    <button id="btn-back" class="btn btn-outline-primary btn-sm rounded-pill">
                      <i class="bi bi-arrow-left"></i> กลับ
                    </button>
                  </div>
                  <table class="table table-sm table-bordered align-middle" id="bedDetailTable">
                    <thead class="table-primary text-center">
                      <tr>
                        <th>รหัสเตียง</th>
                        <th>ชื่อแผนก</th>
                        <th>จำนวนเตียง</th>
                        <th>Admit</th>
                        <th>เตียงว่าง</th>
                        <th>อัตราครองเตียง (%)</th>
                      </tr>
                    </thead>

                    <tbody class="text-center">
                      <tr><td colspan="6" class="text-muted">เลือกโรงพยาบาลเพื่อดูรายละเอียด...</td></tr>
                    </tbody>

                    <tfoot class="table-light text-end fw-bold">
                      <tr>
                        <td colspan="2" class="text-center">รวม</td>
                        <td id="sum-bed"></td>
                        <td id="sum-use"></td>
                        <td id="sum-empty"></td>
                        <td id="sum-rate" class="text-primary"></td>
                      </tr>
                    </tfoot>
                  </table>
                </div>

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

        <script>
          document.addEventListener("DOMContentLoaded", () => {

            const hospitalList = document.getElementById('hospital-list');
            const bedDetail = document.getElementById('bed-detail');
            const btnBack = document.getElementById('btn-back');
            const tbody = document.querySelector('#bedDetailTable tbody');
            const sumBed = document.getElementById('sum-bed');
            const sumUse = document.getElementById('sum-use');
            const sumEmpty = document.getElementById('sum-empty');
            const sumRate = document.getElementById('sum-rate');
            const hospNameEl = document.getElementById('modal-hospname');

            console.log("✅ BedDetail JS Loaded");

            // ✅ คลิกชื่อโรงพยาบาลเพื่อดูรายละเอียดเตียง
            document.addEventListener('click', function (e) {
              const link = e.target.closest('.hosp-detail-link');
              if (!link) return;

              e.preventDefault();
              const hospcode = link.dataset.hospcode;
              const hospname = link.dataset.hospname;

              console.log("🏥 Clicked:", hospcode, hospname);
              hospNameEl.innerText = hospname;
              tbody.innerHTML = `<tr><td colspan="6" class="text-muted">กำลังโหลดข้อมูล...</td></tr>`;

              // ✅ ดึงข้อมูลจาก Controller
              fetch(`{{ url('web/bed_dep') }}/${hospcode}`)
                .then(res => {
                  if (!res.ok) throw new Error(`HTTP ${res.status}`);
                  return res.json();
                })
                .then(data => {
                  tbody.innerHTML = '';

                  if (data.beds && data.beds.length > 0) {
                    data.beds.forEach(b => {
                      const empty = b.bed_qty - b.bed_use;

                      // ✅ ตั้งสีตามอัตราครองเตียง
                      let rateColor = 'text-success';
                      if (b.bed_rate >= 80) rateColor = 'text-danger fw-bold';
                      else if (b.bed_rate >= 60) rateColor = 'text-warning fw-bold';

                      tbody.innerHTML += `
                        <tr>
                          <td class="text-center">${b.bed_code}</td>
                          <td class="text-start">${b.bed_name ?? '-'}</td>
                          <td class="text-end">${b.bed_qty}</td>
                          <td class="text-end text-danger">${b.bed_use}</td>
                          <td class="text-end text-success">${empty}</td>
                          <td class="text-end ${rateColor}">${b.bed_rate}%</td>
                        </tr>`;
                    });
                  } else {
                    tbody.innerHTML = `<tr><td colspan="6" class="text-muted">ไม่พบข้อมูลเตียง</td></tr>`;
                  }

                  // ✅ อัปเดตผลรวม
                  sumBed.innerText = (data.summary?.total || 0).toLocaleString();
                  sumUse.innerText = (data.summary?.used || 0).toLocaleString();
                  sumEmpty.innerText = (data.summary?.empty || 0).toLocaleString();
                  sumRate.innerText = `${data.summary?.rate || 0}%`;

                  // ✅ สลับหน้า
                  hospitalList.style.display = 'none';
                  bedDetail.style.display = 'block';
                })
                .catch(err => {
                  console.error("❌ Fetch error:", err);
                  tbody.innerHTML = `<tr><td colspan="6" class="text-danger">โหลดข้อมูลไม่สำเร็จ</td></tr>`;
                });
            });

            // ✅ ปุ่ม "กลับ" เพื่อกลับมาหน้ารวม
            btnBack.addEventListener('click', () => {
              bedDetail.style.display = 'none';
              hospitalList.style.display = 'block';
            });
          });
        </script>

        <!-- Refer Out ------------------------------------------------------------------------------------------>
        <div class="col-12 col-sm-6 col-xl-3">
          <a href="#" data-bs-toggle="modal" data-bs-target="#ReferOutDetailModal" class="text-decoration-none text-dark">
            <div class="card-hospital referout p-3 h-100 rounded-4 shadow-sm">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="mb-0 text-purple"><strong>การส่งต่อ Refer Out</strong></h6>
                <i class="fa-solid fa-truck-medical text-purple fs-5"></i>
              </div>
              <div class="d-flex align-items-end gap-4">
                <div class="text-end">
                  <div class="small text-secondary text-center">OPD</div>
                  <div class="fw-bold" style="font-size:1.75rem;">
                    {{ $fmtInt($visit_referout_inprov+$visit_referout_outprov ?? 0) }}
                  </div>
                </div>
                <div class="vr d-none d-sm-block"></div>
                <div class="text-end">
                  <div class="small text-secondary text-center">IPD</div>
                  <div class="fw-bold text-purple" style="font-size:1.75rem;">
                    {{ $fmtInt($visit_referout_inprov_ipd+$visit_referout_outprov_ipd ?? 0) }}
                  </div>
                </div>                
              </div>
            </div>
          </a>
        </div>
        {{-- Modal แสดงรายละเอียด รพ. (โทนน้ำเงินพาสเทลเข้ม / modal-lg) --}}
        <div class="modal fade" id="ReferOutDetailModal" tabindex="-1" aria-labelledby="hospitalDetailLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-3" style="background-color:#f5f8fc;">
              <!-- Header -->
              <div class="modal-header text-white rounded-top-3"
                  style="background: linear-gradient(135deg, #2f6fb6, #4b8edc);">
                <h5 class="modal-title fw-bold" id="hospitalDetailLabel">
                  <i class="bi bi-arrow-left-right me-2"></i> การส่งต่อ Refer Out
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              <!-- Body -->
              <div class="modal-body py-3">
                <table class="table table-hover align-middle shadow-sm rounded-3 overflow-hidden mb-0"
                      style="background-color: #ffffff; border-radius: 0.75rem;">
                  <thead style="background-color:#d9e8fb;">
                    <tr class="text-center text-primary fw-semibold">
                      <th rowspan="2" class="text-center align-middle">รหัส</th>
                      <th rowspan="2" class="text-center align-middle">ชื่อโรงพยาบาล</th>
                      <th colspan="2" style="border-right:1px solid #aac6ec;">OPD</th>
                      <th colspan="2">IPD</th>
                    </tr>
                    <tr class="text-center text-primary fw-semibold">
                      <th>ในจังหวัด</th>
                      <th style="border-right:1px solid #aac6ec;">ต่างจังหวัด</th>
                      <th>ในจังหวัด</th>
                      <th>ต่างจังหวัด</th>
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
                        <td align="right" class="text-primary">{{ number_format($h->visit_referout_inprov) }}</td>
                        <td align="right" class="text-success">
                          {{ number_format($h->visit_referout_outprov) }}
                        </td>
                        <td align="right" class="text-primary">{{ number_format($h->visit_referout_inprov_ipd) }}</td>
                        <td align="right" class="fw-bold text-success">{{ number_format($h->visit_referout_outprov_ipd) }}</td>
                      </tr>
                    @endforeach
                    {{-- แถวผลรวม --}}
                    <tr style="background-color:#eef4fb;" class="fw-bold text-end">
                      <td colspan="2" class="text-center text-dark">รวมทั้งหมด</td>
                      <td class="text-primary">{{ number_format($hospitalSummary->sum('visit_referout_inprov')) }}</td>
                      <td class="text-success">
                        {{ number_format($hospitalSummary->sum('visit_referout_outprov')) }}
                      </td>
                      <td class="text-primary">{{ number_format($hospitalSummary->sum('visit_referout_inprov_ipd')) }}</td>
                      <td class="text-success">{{ number_format($hospitalSummary->sum('visit_referout_outprov_ipd')) }}</td>
                    </tr>
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

        <!-- Refer In  --------------------------------------------------------------------------------------->
        <div class="col-12 col-sm-6 col-xl-3">
          <a href="#" data-bs-toggle="modal" data-bs-target="#ReferInDetailModal" class="text-decoration-none text-dark">
            <div class="card-hospital referin p-3 h-100 rounded-4 shadow-sm">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="mb-0 text-primary"><strong>การส่งต่อ Refer In</strong></h6> 
                <i class="fa-solid fa-truck-medical text-primary fs-5"></i>
              </div>
              <div class="d-flex align-items-end gap-4">
                <div class="text-end">
                  <div class="small text-secondary text-center">OPD</div>
                  <div class="fw-bold" style="font-size:1.75rem;">
                    {{ $fmtInt($visit_referin_inprov+$visit_referin_outprov ?? 0) }}
                  </div>
                </div>
                <div class="vr d-none d-sm-block"></div>
                <div class="text-end">
                  <div class="small text-secondary text-center">IPD</div>
                  <div class="fw-bold text-primary" style="font-size:1.75rem;">
                    {{ $fmtInt($visit_referin_inprov_ipd+$visit_referin_outprov_ipd ?? 0) }}
                  </div>
                </div>                
              </div>
            </div>
          </a>
        </div>
        {{-- Modal แสดงรายละเอียด รพ. (โทนน้ำเงินพาสเทลเข้ม / modal-lg) --}}
        <div class="modal fade" id="ReferInDetailModal" tabindex="-1" aria-labelledby="hospitalDetailLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-3" style="background-color:#f5f8fc;">
              <!-- Header -->
              <div class="modal-header text-white rounded-top-3"
                  style="background: linear-gradient(135deg, #2f6fb6, #4b8edc);">
                <h5 class="modal-title fw-bold" id="hospitalDetailLabel">
                  <i class="bi bi-arrow-left-right me-2"></i> การส่งต่อ Refer IN
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <!-- Body -->
              <div class="modal-body py-3">
                <table class="table table-hover align-middle shadow-sm rounded-3 overflow-hidden mb-0"
                      style="background-color: #ffffff; border-radius: 0.75rem;">
                  <thead style="background-color:#d9e8fb;">
                    <tr class="text-center text-primary fw-semibold">
                      <th rowspan="2" class="align-middle">รหัส</th>
                      <th rowspan="2" class="align-middle">ชื่อโรงพยาบาล</th>
                      <th colspan="2" style="border-right:1px solid #aac6ec;">OPD</th>
                      <th colspan="2">IPD</th>
                    </tr>
                    <tr class="text-center text-primary fw-semibold">
                      <th>ในจังหวัด</th>
                      <th style="border-right:1px solid #aac6ec;">ต่างจังหวัด</th>
                      <th>ในจังหวัด</th>
                      <th>ต่างจังหวัด</th>
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
                        <!-- OPD -->
                        <td align="right" class="text-primary">{{ number_format($h->visit_referin_inprov) }}</td>
                        <td align="right" class="text-success" style="border-right:1px solid #aac6ec;">
                          {{ number_format($h->visit_referin_outprov) }}
                        </td>
                        <!-- IPD -->
                        <td align="right" class="text-primary">{{ number_format($h->visit_referin_inprov_ipd) }}</td>
                        <td align="right" class="fw-bold text-success">{{ number_format($h->visit_referin_outprov_ipd) }}</td>
                      </tr>
                    @endforeach
                    {{-- แถวผลรวม --}}
                    <tr style="background-color:#eef4fb;" class="fw-bold text-end">
                      <td colspan="2" class="text-center text-dark">รวมทั้งหมด</td>
                      <td class="text-primary">{{ number_format($hospitalSummary->sum('visit_referin_inprov')) }}</td>
                      <td class="text-success">
                        {{ number_format($hospitalSummary->sum('visit_referin_outprov')) }}
                      </td>
                      <td class="text-primary">{{ number_format($hospitalSummary->sum('visit_referin_inprov_ipd')) }}</td>
                      <td class="text-success">{{ number_format($hospitalSummary->sum('visit_referin_outprov_ipd')) }}</td>
                    </tr>
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

        <!-- Refer Back  --------------------------------------------------------------------------------------------->
        <div class="col-12 col-sm-6 col-xl-3">
          <a href="#" data-bs-toggle="modal" data-bs-target="#ReferBackDetailModal" class="text-decoration-none text-dark">
            <div class="card-hospital referback p-3 h-100 rounded-4 shadow-sm">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="mb-0 text-green"><strong>การส่งต่อ Refer Back</strong></h6> 
                <i class="fa-solid fa-truck-medical text-green fs-5"></i>
              </div>
              <div class="d-flex align-items-end gap-4">
                <div class="text-end">
                  <div class="small text-secondary text-center">ในจังหวัด</div>
                  <div class="fw-bold" style="font-size:1.75rem;">
                    {{ $fmtInt($visit_referback_inprov ?? 0) }}
                  </div>
                </div>
                <div class="vr d-none d-sm-block"></div>
                <div class="text-end">
                  <div class="small text-secondary text-center">ต่างจังหวัด</div>
                  <div class="fw-bold text-green" style="font-size:1.75rem;">
                    {{ $fmtInt($visit_referback_outprov ?? 0) }}
                  </div>
                </div>                
              </div>
            </div>
          </a>
        </div>
        {{-- Modal แสดงรายละเอียด รพ. (โทนน้ำเงินพาสเทลเข้ม / modal-lg) --}}
        <div class="modal fade" id="ReferBackDetailModal" tabindex="-1" aria-labelledby="hospitalDetailLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-3" style="background-color:#f5f8fc;">
              <!-- Header -->
              <div class="modal-header text-white rounded-top-3"
                  style="background: linear-gradient(135deg, #2f6fb6, #4b8edc);">
                <h5 class="modal-title fw-bold" id="hospitalDetailLabel">
                  <i class="bi bi-arrow-left-right me-2"></i> การส่งต่อ Refer Back
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              <!-- Body -->
              <div class="modal-body py-3">
                <table class="table table-hover align-middle shadow-sm rounded-3 overflow-hidden mb-0"
                      style="background-color: #ffffff; border-radius: 0.75rem;">
                  <thead style="background-color:#d9e8fb;">
                    <tr class="text-center text-primary fw-semibold">
                      <th class="text-center">รหัส</th>
                      <th class="text-center">ชื่อโรงพยาบาล</th>
                      <th>ในจังหวัด</th>
                      <th>ต่างจังหวัด</th>
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
                        <td align="right" class="text-primary">{{ number_format($h->visit_referback_inprov) }}</td>
                        <td align="right" class="fw-bold text-success">{{ number_format($h->visit_referback_outprov) }}</td>
                      </tr>
                    @endforeach

                    {{-- แถวผลรวม --}}
                    <tr style="background-color:#eef4fb;" class="fw-bold text-end">
                      <td colspan="2" class="text-center text-dark">รวมทั้งหมด</td>
                      <td class="text-primary">{{ number_format($hospitalSummary->sum('visit_referback_inprov')) }}</td>
                      <td class="text-success">{{ number_format($hospitalSummary->sum('visit_referback_outprov')) }}</td>
                    </tr>
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

        {{-- -------------------------------------------------------------------------------------------------------------- --}}
      </div>
    </div>  
  </section>  
  
<br>
<hr>

  {{-- เลือกปีงบประมาณ ----------------------------------------------------------------------------------------------------------}}
  <section id="summary" class="pb-2">
      <div class="container-fluid">
        <form method="POST" action="{{ route('web.index') }}" enctype="multipart/form-data">
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

        <!-- 10985 -->
        <div class="tab-pane fade show active" id="pane-10985" role="tabpanel" aria-labelledby="tab-10985" tabindex="0">          
          <!-- IPD -->
          <div class="glass p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6>[10985] ข้อมูลบริการผู้ป่วยใน IPD โรงพยาชานุมาน ปีงบประมาณ {{$budget_year}}</h6>
              <span class="text-secondary small">Update {{$update_at10985}}</span>              
            </div>
            <div class="table-responsive">
              <table id="table10985_ipd" class="table table-bordered table-striped my-3" width ="100%">
                <thead class="table-light">
                  <tr class="table-primary">
                    <th class="text-center" rowspan="2" width ="4%">เดือน</th>
                    <th class="text-center" rowspan="2">จำนวน AN</th>
                    <th class="text-center" rowspan="2">วันนอนรวม</th> 
                    <th class="text-center" rowspan="2">อัตราครองเตียง (%)</th>
                    <th class="text-center" rowspan="2">Active Base (เตียง)</th>       
                    <th class="text-center" rowspan="2">AdjRW</th>  
                    <th class="text-center" rowspan="2">CMI</th>
                    <th class="text-center" colspan="3">ค่ารักษาพยาบาล</th>                
                  </tr>    
                  <tr class="table-primary"> 
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>                 
                  </tr>    
                </thead>
                <tbody>
                  <?php 
                    $sum_an_total = 0; 
                    $sum_admdate = 0;   
                    $sum_adjrw = 0; 
                    $sum_inc_total = 0;  
                    $sum_inc_lab_total = 0;
                    $sum_inc_drug_total = 0;
                    $bed_report = $total_10985_ipd[0]->bed_report ?? 30; // ค่าเตียงจาก hospital_config
                  ?>  
                  @foreach($total_10985_ipd as $row) 
                  <tr>
                    <td align="center"width ="4%">{{ $row->month }}</td>
                    <td align="right">{{ number_format($row->an_total) }}</td>
                    <td align="right">{{ number_format($row->admdate) }}</td>
                    <td align="right">{{ number_format($row->bed_occupancy,2) }}</td>
                    <td align="right">{{ number_format($row->active_bed,2) }}</td>
                    <td align="right">{{ number_format($row->adjrw,5) }}</td>
                    <td align="right">{{ number_format($row->cmi,2) }}</td>
                    <td align="right">{{ number_format($row->inc_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_total,2) }}</td>
                  </tr>
                  <?php 
                    $sum_an_total += $row->an_total;
                    $sum_admdate += $row->admdate;
                    $sum_adjrw += $row->adjrw;
                    $sum_inc_total += $row->inc_total;
                    $sum_inc_lab_total += $row->inc_lab_total;
                    $sum_inc_drug_total += $row->inc_drug_total;
                  ?>
                  @endforeach 
                  <?php                   
                  // ✅ จำนวนเตียง
                    $bed_report = 30;
                  // ✅ อัตราครองเตียงรวม
                    $sum_bed_occupancy = ($sum_admdate > 0 && $bed_report > 0) ? round(($sum_admdate * 100) / ($bed_report * $diff_days), 2) : 0;  
                  // ✅ Active Bed = วันนอนรวม ÷ จำนวนวัน
                    $sum_active_bed = ($sum_admdate > 0 && $diff_days > 0) ? round($sum_admdate / $diff_days, 2) : 0;
                  // ✅ CMI รวม
                    $sum_cmi = ($sum_an_total > 0) ? round($sum_adjrw / $sum_an_total, 2) : 0; 
                  ?>   
                  <tr>
                    <td align="right"><strong>รวม</strong></td>
                    <td align="right"><strong>{{number_format($sum_an_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_admdate)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_bed_occupancy,2)}}</td>     
                    <td align="right"><strong>{{number_format($sum_active_bed,2)}}</td>   
                    <td align="right"><strong>{{number_format($sum_adjrw,4)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_cmi,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_inc_total,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_total,2)}}</strong></td>
                  </tr>   
                </tbody>
              </table>
              <!-- กราฟ -->
              <div class="row mt-4">
                <!-- กราฟอัตราครองเตียง -->
                <div class="col-md-6 mb-4">
                  <div class="card shadow-sm">
                    <div class="card-body">
                      <h6 class="text-center text-primary mb-1">
                        📈 อัตราครองเตียง (%)
                      </h6>
                      <div id="bed_occupancy_10985"></div>
                    </div>
                  </div>
                </div>
                <!-- กราฟ CMI -->
                <div class="col-md-6 mb-4">
                  <div class="card shadow-sm">
                    <div class="card-body">
                      <h6 class="text-center text-danger mb-1">
                        📊 CMI
                      </h6>
                      <div id="cmi_chart_10985"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>          
        <!-- END 10985 -->
        </div>

        <!-- 10986 -->
        <div class="tab-pane fade" id="pane-10986" role="tabpanel" aria-labelledby="tab-10986" tabindex="0">          
          <!-- IPD -->
          <div class="glass p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6>[10986] ข้อมูลบริการผู้ป่วยใน IPD โรงพยาบาลปทุมราช ปีงบประมาณ {{$budget_year}}</h6>
              <span class="text-secondary small">Update {{$update_at10986}}</span>              
            </div>
            <div class="table-responsive">
              <table id="table10986_ipd" class="table table-bordered table-striped my-3" width ="100%">
                <thead class="table-light">
                  <tr class="table-primary">
                    <th class="text-center" rowspan="2" width ="4%">เดือน</th>
                    <th class="text-center" rowspan="2">จำนวน AN</th>
                    <th class="text-center" rowspan="2">วันนอนรวม</th> 
                    <th class="text-center" rowspan="2">อัตราครองเตียง (%)</th>
                    <th class="text-center" rowspan="2">Active Base (เตียง)</th>       
                    <th class="text-center" rowspan="2">AdjRW</th>  
                    <th class="text-center" rowspan="2">CMI</th>
                    <th class="text-center" colspan="3">ค่ารักษาพยาบาล</th>                
                  </tr>    
                  <tr class="table-primary"> 
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>                 
                  </tr>    
                </thead>
                <tbody>
                  <?php 
                    $sum_an_total = 0; 
                    $sum_admdate = 0;   
                    $sum_adjrw = 0; 
                    $sum_inc_total = 0;  
                    $sum_inc_lab_total = 0;
                    $sum_inc_drug_total = 0;
                    $bed_report = $total_10986_ipd[0]->bed_report ?? 30; // ค่าเตียงจาก hospital_config
                  ?>  
                  @foreach($total_10986_ipd as $row) 
                  <tr>
                    <td align="center"width ="4%">{{ $row->month }}</td>
                    <td align="right">{{ number_format($row->an_total) }}</td>
                    <td align="right">{{ number_format($row->admdate) }}</td>
                    <td align="right">{{ number_format($row->bed_occupancy,2) }}</td>
                    <td align="right">{{ number_format($row->active_bed,2) }}</td>
                    <td align="right">{{ number_format($row->adjrw,5) }}</td>
                    <td align="right">{{ number_format($row->cmi,2) }}</td>
                    <td align="right">{{ number_format($row->inc_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_total,2) }}</td>
                  </tr>
                  <?php 
                    $sum_an_total += $row->an_total;
                    $sum_admdate += $row->admdate;
                    $sum_adjrw += $row->adjrw;
                    $sum_inc_total += $row->inc_total;
                    $sum_inc_lab_total += $row->inc_lab_total;
                    $sum_inc_drug_total += $row->inc_drug_total;
                  ?>
                  @endforeach 
                  <?php                   
                  // ✅ จำนวนเตียง
                    $bed_report = 50;
                  // ✅ อัตราครองเตียงรวม
                    $sum_bed_occupancy = ($sum_admdate > 0 && $bed_report > 0) ? round(($sum_admdate * 100) / ($bed_report * $diff_days), 2) : 0;  
                  // ✅ Active Bed = วันนอนรวม ÷ จำนวนวัน
                    $sum_active_bed = ($sum_admdate > 0 && $diff_days > 0) ? round($sum_admdate / $diff_days, 2) : 0;
                  // ✅ CMI รวม
                    $sum_cmi = ($sum_an_total > 0) ? round($sum_adjrw / $sum_an_total, 2) : 0; 
                  ?>   
                  <tr>
                    <td align="right"><strong>รวม</strong></td>
                    <td align="right"><strong>{{number_format($sum_an_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_admdate)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_bed_occupancy,2)}}</td>     
                    <td align="right"><strong>{{number_format($sum_active_bed,2)}}</td>   
                    <td align="right"><strong>{{number_format($sum_adjrw,4)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_cmi,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_inc_total,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_total,2)}}</strong></td>
                  </tr>   
                </tbody>
              </table>
              <!-- กราฟ -->
              <div class="row mt-4">
                <!-- กราฟอัตราครองเตียง -->
                <div class="col-md-6 mb-4">
                  <div class="card shadow-sm">
                    <div class="card-body">
                      <h6 class="text-center text-primary mb-1">
                        📈 อัตราครองเตียง (%)
                      </h6>
                      <div id="bed_occupancy_10986"></div>
                    </div>
                  </div>
                </div>
                <!-- กราฟ CMI -->
                <div class="col-md-6 mb-4">
                  <div class="card shadow-sm">
                    <div class="card-body">
                      <h6 class="text-center text-danger mb-1">
                        📊 CMI
                      </h6>
                      <div id="cmi_chart_10986"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <!-- END 10986-->
        </div>

        <!-- 10987-->
        <div class="tab-pane fade" id="pane-10987" role="tabpanel" aria-labelledby="tab-10987" tabindex="0">
          <!-- IPD-->
          <div class="glass p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6>[10987] ข้อมูลบริการผู้ป่วยใน IPD โรงพยาบาลพนา ปีงบประมาณ {{$budget_year}}</h6>
              <span class="text-secondary small">Update {{$update_at10987}}</span>              
            </div>
            <div class="table-responsive">
              <table id="table10987_ipd" class="table table-bordered table-striped my-3" width ="100%">
                <thead class="table-light">
                  <tr class="table-primary">
                    <th class="text-center" rowspan="2" width ="4%">เดือน</th>
                    <th class="text-center" rowspan="2">จำนวน AN</th>
                    <th class="text-center" rowspan="2">วันนอนรวม</th> 
                    <th class="text-center" rowspan="2">อัตราครองเตียง (%)</th>
                    <th class="text-center" rowspan="2">Active Base (เตียง)</th>        
                    <th class="text-center" rowspan="2">AdjRW</th>  
                    <th class="text-center" rowspan="2">CMI</th>
                    <th class="text-center" colspan="3">ค่ารักษาพยาบาล</th>                
                  </tr>    
                  <tr class="table-primary"> 
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>                 
                  </tr>    
                </thead>
                <tbody>
                  <?php 
                    $sum_an_total = 0; 
                    $sum_admdate = 0;   
                    $sum_adjrw = 0; 
                    $sum_inc_total = 0;  
                    $sum_inc_lab_total = 0;
                    $sum_inc_drug_total = 0;
                    $bed_report = $total_10987_ipd[0]->bed_report ?? 30; // ค่าเตียงจาก hospital_config
                  ?>  
                  @foreach($total_10987_ipd as $row) 
                  <tr>
                    <td align="center"width ="4%">{{ $row->month }}</td>
                    <td align="right">{{ number_format($row->an_total) }}</td>
                    <td align="right">{{ number_format($row->admdate) }}</td>
                    <td align="right">{{ number_format($row->bed_occupancy,2) }}</td>
                    <td align="right">{{ number_format($row->active_bed,2) }}</td>
                    <td align="right">{{ number_format($row->adjrw,5) }}</td>
                    <td align="right">{{ number_format($row->cmi,2) }}</td>
                    <td align="right">{{ number_format($row->inc_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_total,2) }}</td>
                  </tr>
                  <?php 
                    $sum_an_total += $row->an_total;
                    $sum_admdate += $row->admdate;
                    $sum_adjrw += $row->adjrw;
                    $sum_inc_total += $row->inc_total;
                    $sum_inc_lab_total += $row->inc_lab_total;
                    $sum_inc_drug_total += $row->inc_drug_total;
                  ?>
                  @endforeach 
                  <?php                   
                  // ✅ จำนวนเตียง
                    $bed_report = 30;
                  // ✅ อัตราครองเตียงรวม
                    $sum_bed_occupancy = ($sum_admdate > 0 && $bed_report > 0) ? round(($sum_admdate * 100) / ($bed_report * $diff_days), 2) : 0;  
                  // ✅ Active Bed = วันนอนรวม ÷ จำนวนวัน
                    $sum_active_bed = ($sum_admdate > 0 && $diff_days > 0) ? round($sum_admdate / $diff_days, 2) : 0;
                  // ✅ CMI รวม
                    $sum_cmi = ($sum_an_total > 0) ? round($sum_adjrw / $sum_an_total, 2) : 0; 
                  ?>   
                  <tr>
                    <td align="right"><strong>รวม</strong></td>
                    <td align="right"><strong>{{number_format($sum_an_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_admdate)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_bed_occupancy,2)}}</td>     
                    <td align="right"><strong>{{number_format($sum_active_bed,2)}}</td>   
                    <td align="right"><strong>{{number_format($sum_adjrw,4)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_cmi,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_inc_total,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_total,2)}}</strong></td>
                  </tr>   
                </tbody>
              </table>
              <!-- กราฟ -->
              <div class="row mt-4">
                <!-- กราฟอัตราครองเตียง -->
                <div class="col-md-6 mb-4">
                  <div class="card shadow-sm">
                    <div class="card-body">
                      <h6 class="text-center text-primary mb-1">
                        📈 อัตราครองเตียง (%)
                      </h6>
                      <div id="bed_occupancy_10987"></div>
                    </div>
                  </div>
                </div>
                <!-- กราฟ CMI -->
                <div class="col-md-6 mb-4">
                  <div class="card shadow-sm">
                    <div class="card-body">
                      <h6 class="text-center text-danger mb-1">
                        📊 CMI
                      </h6>
                      <div id="cmi_chart_10987"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <!-- END 10987 -->
        </div>

        <!-- 10988 -->
        <div class="tab-pane fade" id="pane-10988" role="tabpanel" aria-labelledby="tab-10988" tabindex="0">
          <!-- IPD -->          
          <div class="glass p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6>[10988] ข้อมูลบริการผู้ป่วยใน IPD โรงพยาบาลเสนางคนิคม ปีงบประมาณ {{$budget_year}}</h6>
              <span class="text-secondary small">Update {{$update_at10988}}</span>              
            </div>
            <div class="table-responsive">
              <table id="table10988_ipd" class="table table-bordered table-striped my-3" width ="100%">
                <thead class="table-light">
                  <tr class="table-primary">
                    <th class="text-center" rowspan="2" width ="4%">เดือน</th>
                    <th class="text-center" rowspan="2">จำนวน AN</th>
                    <th class="text-center" rowspan="2">วันนอนรวม</th> 
                    <th class="text-center" rowspan="2">อัตราครองเตียง (%)</th>
                    <th class="text-center" rowspan="2">Active Base (เตียง)</th>     
                    <th class="text-center" rowspan="2">AdjRW</th>  
                    <th class="text-center" rowspan="2">CMI</th>
                    <th class="text-center" colspan="3">ค่ารักษาพยาบาล</th>                
                  </tr>    
                  <tr class="table-primary"> 
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>                 
                  </tr>    
                </thead>
                <tbody>
                  <?php 
                    $sum_an_total = 0; 
                    $sum_admdate = 0;   
                    $sum_adjrw = 0; 
                    $sum_inc_total = 0;  
                    $sum_inc_lab_total = 0;
                    $sum_inc_drug_total = 0;
                    $bed_report = $total_10988_ipd[0]->bed_report ?? 30; // ค่าเตียงจาก hospital_config
                  ?>  
                  @foreach($total_10988_ipd as $row) 
                  <tr>
                    <td align="center"width ="4%">{{ $row->month }}</td>
                    <td align="right">{{ number_format($row->an_total) }}</td>
                    <td align="right">{{ number_format($row->admdate) }}</td>
                    <td align="right">{{ number_format($row->bed_occupancy,2) }}</td>
                    <td align="right">{{ number_format($row->active_bed,2) }}</td>
                    <td align="right">{{ number_format($row->adjrw,5) }}</td>
                    <td align="right">{{ number_format($row->cmi,2) }}</td>
                    <td align="right">{{ number_format($row->inc_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_total,2) }}</td>
                  </tr>
                  <?php 
                    $sum_an_total += $row->an_total;
                    $sum_admdate += $row->admdate;
                    $sum_adjrw += $row->adjrw;
                    $sum_inc_total += $row->inc_total;
                    $sum_inc_lab_total += $row->inc_lab_total;
                    $sum_inc_drug_total += $row->inc_drug_total;
                  ?>
                  @endforeach 
                  <?php                   
                  // ✅ จำนวนเตียง
                    $bed_report = 30;
                  // ✅ อัตราครองเตียงรวม
                    $sum_bed_occupancy = ($sum_admdate > 0 && $bed_report > 0) ? round(($sum_admdate * 100) / ($bed_report * $diff_days), 2) : 0;  
                  // ✅ Active Bed = วันนอนรวม ÷ จำนวนวัน
                    $sum_active_bed = ($sum_admdate > 0 && $diff_days > 0) ? round($sum_admdate / $diff_days, 2) : 0;
                  // ✅ CMI รวม
                    $sum_cmi = ($sum_an_total > 0) ? round($sum_adjrw / $sum_an_total, 2) : 0; 
                  ?>   
                  <tr>
                    <td align="right"><strong>รวม</strong></td>
                    <td align="right"><strong>{{number_format($sum_an_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_admdate)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_bed_occupancy,2)}}</td>     
                    <td align="right"><strong>{{number_format($sum_active_bed,2)}}</td>   
                    <td align="right"><strong>{{number_format($sum_adjrw,4)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_cmi,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_inc_total,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_total,2)}}</strong></td>
                  </tr>   
                </tbody>
              </table>
              <!-- กราฟ -->
              <div class="row mt-4">
                <!-- กราฟอัตราครองเตียง -->
                <div class="col-md-6 mb-4">
                  <div class="card shadow-sm">
                    <div class="card-body">
                      <h6 class="text-center text-primary mb-1">
                        📈 อัตราครองเตียง (%)
                      </h6>
                      <div id="bed_occupancy_10988"></div>
                    </div>
                  </div>
                </div>
                <!-- กราฟ CMI -->
                <div class="col-md-6 mb-4">
                  <div class="card shadow-sm">
                    <div class="card-body">
                      <h6 class="text-center text-danger mb-1">
                        📊 CMI
                      </h6>
                      <div id="cmi_chart_10988"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div> 
        <!-- END 10988 -->       
        </div>

        <!-- 10989 -->
        <div class="tab-pane fade" id="pane-10989" role="tabpanel" aria-labelledby="tab-10989" tabindex="0">
          <!-- IPD -->          
          <div class="glass p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6>[10989] ข้อมูลบริการผู้ป่วยใน IPD โรงพยาบาลหัวตะพาน ปีงบประมาณ {{$budget_year}}</h6>
              <span class="text-secondary small">Update {{$update_at10989}}</span>              
            </div>
            <div class="table-responsive">
              <table id="table10989_ipd" class="table table-bordered table-striped my-3" width ="100%">
                <thead class="table-light">
                  <tr class="table-primary">
                    <th class="text-center" rowspan="2" width ="4%">เดือน</th>
                    <th class="text-center" rowspan="2">จำนวน AN</th>
                    <th class="text-center" rowspan="2">วันนอนรวม</th> 
                    <th class="text-center" rowspan="2">อัตราครองเตียง (%)</th>
                    <th class="text-center" rowspan="2">Active Base (เตียง)</th>       
                    <th class="text-center" rowspan="2">AdjRW</th>  
                    <th class="text-center" rowspan="2">CMI</th>
                    <th class="text-center" colspan="3">ค่ารักษาพยาบาล</th>                
                  </tr>    
                  <tr class="table-primary"> 
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>                 
                  </tr>    
                </thead>
                <tbody>
                  <?php 
                    $sum_an_total = 0; 
                    $sum_admdate = 0;   
                    $sum_adjrw = 0; 
                    $sum_inc_total = 0;  
                    $sum_inc_lab_total = 0;
                    $sum_inc_drug_total = 0;
                    $bed_report = $total_10989_ipd[0]->bed_report ?? 30; // ค่าเตียงจาก hospital_config
                  ?>  
                  @foreach($total_10989_ipd as $row) 
                  <tr>
                    <td align="center"width ="4%">{{ $row->month }}</td>
                    <td align="right">{{ number_format($row->an_total) }}</td>
                    <td align="right">{{ number_format($row->admdate) }}</td>
                    <td align="right">{{ number_format($row->bed_occupancy,2) }}</td>
                    <td align="right">{{ number_format($row->active_bed,2) }}</td>
                    <td align="right">{{ number_format($row->adjrw,5) }}</td>
                    <td align="right">{{ number_format($row->cmi,2) }}</td>
                    <td align="right">{{ number_format($row->inc_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_total,2) }}</td>
                  </tr>
                  <?php 
                    $sum_an_total += $row->an_total;
                    $sum_admdate += $row->admdate;
                    $sum_adjrw += $row->adjrw;
                    $sum_inc_total += $row->inc_total;
                    $sum_inc_lab_total += $row->inc_lab_total;
                    $sum_inc_drug_total += $row->inc_drug_total;
                  ?>
                  @endforeach 
                  <?php                   
                  // ✅ จำนวนเตียง
                    $bed_report = 60;
                  // ✅ อัตราครองเตียงรวม
                    $sum_bed_occupancy = ($sum_admdate > 0 && $bed_report > 0) ? round(($sum_admdate * 100) / ($bed_report * $diff_days), 2) : 0;  
                  // ✅ Active Bed = วันนอนรวม ÷ จำนวนวัน
                    $sum_active_bed = ($sum_admdate > 0 && $diff_days > 0) ? round($sum_admdate / $diff_days, 2) : 0;
                  // ✅ CMI รวม
                    $sum_cmi = ($sum_an_total > 0) ? round($sum_adjrw / $sum_an_total, 2) : 0; 
                  ?>   
                  <tr>
                    <td align="right"><strong>รวม</strong></td>
                    <td align="right"><strong>{{number_format($sum_an_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_admdate)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_bed_occupancy,2)}}</td>     
                    <td align="right"><strong>{{number_format($sum_active_bed,2)}}</td>   
                    <td align="right"><strong>{{number_format($sum_adjrw,4)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_cmi,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_inc_total,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_total,2)}}</strong></td>
                  </tr>   
                </tbody>
              </table>
              <!-- กราฟ -->
              <div class="row mt-4">
                <!-- กราฟอัตราครองเตียง -->
                <div class="col-md-6 mb-4">
                  <div class="card shadow-sm">
                    <div class="card-body">
                      <h6 class="text-center text-primary mb-1">
                        📈 อัตราครองเตียง (%)
                      </h6>
                      <div id="bed_occupancy_10989"></div>
                    </div>
                  </div>
                </div>
                <!-- กราฟ CMI -->
                <div class="col-md-6 mb-4">
                  <div class="card shadow-sm">
                    <div class="card-body">
                      <h6 class="text-center text-danger mb-1">
                        📊 CMI
                      </h6>
                      <div id="cmi_chart_10989"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <!-- END 10989 --> 
        </div>

        <!-- 10990 -->
        <div class="tab-pane fade" id="pane-10990" role="tabpanel" aria-labelledby="tab-10990" tabindex="0">
          <!-- IPD -->          
          <div class="glass p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6>[10990] ข้อมูลบริการผู้ป่วยใน IPD โรงพยาบาลลืออำนาจ ปีงบประมาณ {{$budget_year}}</h6>
              <span class="text-secondary small">Update {{$update_at10990}}</span>              
            </div>
            <div class="table-responsive">
              <table id="table10990_ipd" class="table table-bordered table-striped my-3" width ="100%">
                <thead class="table-light">
                  <tr class="table-primary">
                    <th class="text-center" rowspan="2" width ="4%">เดือน</th>
                    <th class="text-center" rowspan="2">จำนวน AN</th>
                    <th class="text-center" rowspan="2">วันนอนรวม</th> 
                    <th class="text-center" rowspan="2">อัตราครองเตียง (%)</th>
                    <th class="text-center" rowspan="2">Active Base (เตียง)</th>      
                    <th class="text-center" rowspan="2">AdjRW</th>  
                    <th class="text-center" rowspan="2">CMI</th>
                    <th class="text-center" colspan="3">ค่ารักษาพยาบาล</th>                
                  </tr>    
                  <tr class="table-primary"> 
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>                 
                  </tr>    
                </thead>
                <tbody>
                  <?php 
                    $sum_an_total = 0; 
                    $sum_admdate = 0;   
                    $sum_adjrw = 0; 
                    $sum_inc_total = 0;  
                    $sum_inc_lab_total = 0;
                    $sum_inc_drug_total = 0;
                    $bed_report = $total_10990_ipd[0]->bed_report ?? 30; // ค่าเตียงจาก hospital_config
                  ?>  
                  @foreach($total_10990_ipd as $row) 
                  <tr>
                    <td align="center"width ="4%">{{ $row->month }}</td>
                    <td align="right">{{ number_format($row->an_total) }}</td>
                    <td align="right">{{ number_format($row->admdate) }}</td>
                    <td align="right">{{ number_format($row->bed_occupancy,2) }}</td>
                    <td align="right">{{ number_format($row->active_bed,2) }}</td>
                    <td align="right">{{ number_format($row->adjrw,5) }}</td>
                    <td align="right">{{ number_format($row->cmi,2) }}</td>
                    <td align="right">{{ number_format($row->inc_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_total,2) }}</td>
                  </tr>
                  <?php 
                    $sum_an_total += $row->an_total;
                    $sum_admdate += $row->admdate;
                    $sum_adjrw += $row->adjrw;
                    $sum_inc_total += $row->inc_total;
                    $sum_inc_lab_total += $row->inc_lab_total;
                    $sum_inc_drug_total += $row->inc_drug_total;
                  ?>
                  @endforeach 
                  <?php                   
                  // ✅ จำนวนเตียง
                    $bed_report = 30;
                  // ✅ อัตราครองเตียงรวม
                    $sum_bed_occupancy = ($sum_admdate > 0 && $bed_report > 0) ? round(($sum_admdate * 100) / ($bed_report * $diff_days), 2) : 0;  
                  // ✅ Active Bed = วันนอนรวม ÷ จำนวนวัน
                    $sum_active_bed = ($sum_admdate > 0 && $diff_days > 0) ? round($sum_admdate / $diff_days, 2) : 0;
                  // ✅ CMI รวม
                    $sum_cmi = ($sum_an_total > 0) ? round($sum_adjrw / $sum_an_total, 2) : 0; 
                  ?>   
                  <tr>
                    <td align="right"><strong>รวม</strong></td>
                    <td align="right"><strong>{{number_format($sum_an_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_admdate)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_bed_occupancy,2)}}</td>     
                    <td align="right"><strong>{{number_format($sum_active_bed,2)}}</td>   
                    <td align="right"><strong>{{number_format($sum_adjrw,4)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_cmi,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_inc_total,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_total,2)}}</strong></td>
                  </tr>   
                </tbody>
              </table>
              <!-- กราฟ -->
              <div class="row mt-4">
                <!-- กราฟอัตราครองเตียง -->
                <div class="col-md-6 mb-4">
                  <div class="card shadow-sm">
                    <div class="card-body">
                      <h6 class="text-center text-primary mb-1">
                        📈 อัตราครองเตียง (%)
                      </h6>
                      <div id="bed_occupancy_10990"></div>
                    </div>
                  </div>
                </div>
                <!-- กราฟ CMI -->
                <div class="col-md-6 mb-4">
                  <div class="card shadow-sm">
                    <div class="card-body">
                      <h6 class="text-center text-danger mb-1">
                        📊 CMI
                      </h6>
                      <div id="cmi_chart_10990"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <!-- END 10990 --> 
        </div>

        <!-- 10703 -->
        <div class="tab-pane fade" id="pane-10703" role="tabpanel" aria-labelledby="tab-10703" tabindex="0">
          <!-- IPD -->          
          <div class="glass p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6>[10703] ข้อมูลบริการผู้ป่วยใน IPD โรงพยาบาลอำนาจเจริญ ปีงบประมาณ {{$budget_year}}</h6>
              <span class="text-secondary small">Update {{$update_at10990}}</span>              
            </div>
            <div class="table-responsive">
              <table id="table10703_ipd" class="table table-bordered table-striped my-3" width ="100%">
                <thead class="table-light">
                  <tr class="table-primary">
                    <th class="text-center" rowspan="2" width ="4%">เดือน</th>
                    <th class="text-center" rowspan="2">จำนวน AN</th>
                    <th class="text-center" rowspan="2">วันนอนรวม</th> 
                    <th class="text-center" rowspan="2">อัตราครองเตียง (%)</th>
                    <th class="text-center" rowspan="2">Active Base (เตียง)</th>      
                    <th class="text-center" rowspan="2">AdjRW</th>  
                    <th class="text-center" rowspan="2">CMI</th>
                    <th class="text-center" colspan="3">ค่ารักษาพยาบาล</th>                
                  </tr>    
                  <tr class="table-primary"> 
                    <td class="text-center text-primary">ค่ารักษารวม</td>
                    <td class="text-center text-primary">ค่า Lab</td>
                    <td class="text-center text-primary">ค่า ยา</td>                 
                  </tr>    
                </thead>
                <tbody>
                  <?php 
                    $sum_an_total = 0; 
                    $sum_admdate = 0;   
                    $sum_adjrw = 0; 
                    $sum_inc_total = 0;  
                    $sum_inc_lab_total = 0;
                    $sum_inc_drug_total = 0;
                    $bed_report = $total_10703_ipd[0]->bed_report ?? 432; // ค่าเตียงจาก hospital_config
                  ?>  
                  @foreach($total_10703_ipd as $row) 
                  <tr>
                    <td align="center"width ="4%">{{ $row->month }}</td>
                    <td align="right">{{ number_format($row->an_total) }}</td>
                    <td align="right">{{ number_format($row->admdate) }}</td>
                    <td align="right">{{ number_format($row->bed_occupancy,2) }}</td>
                    <td align="right">{{ number_format($row->active_bed,2) }}</td>
                    <td align="right">{{ number_format($row->adjrw,5) }}</td>
                    <td align="right">{{ number_format($row->cmi,2) }}</td>
                    <td align="right">{{ number_format($row->inc_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_lab_total,2) }}</td>
                    <td align="right">{{ number_format($row->inc_drug_total,2) }}</td>
                  </tr>
                  <?php 
                    $sum_an_total += $row->an_total;
                    $sum_admdate += $row->admdate;
                    $sum_adjrw += $row->adjrw;
                    $sum_inc_total += $row->inc_total;
                    $sum_inc_lab_total += $row->inc_lab_total;
                    $sum_inc_drug_total += $row->inc_drug_total;
                  ?>
                  @endforeach 
                  <?php                   
                  // ✅ จำนวนเตียง
                    $bed_report = 432;
                  // ✅ อัตราครองเตียงรวม
                    $sum_bed_occupancy = ($sum_admdate > 0 && $bed_report > 0) ? round(($sum_admdate * 100) / ($bed_report * $diff_days), 2) : 0;  
                  // ✅ Active Bed = วันนอนรวม ÷ จำนวนวัน
                    $sum_active_bed = ($sum_admdate > 0 && $diff_days > 0) ? round($sum_admdate / $diff_days, 2) : 0;
                  // ✅ CMI รวม
                    $sum_cmi = ($sum_an_total > 0) ? round($sum_adjrw / $sum_an_total, 2) : 0; 
                  ?>   
                  <tr>
                    <td align="right"><strong>รวม</strong></td>
                    <td align="right"><strong>{{number_format($sum_an_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_admdate)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_bed_occupancy,2)}}</td>     
                    <td align="right"><strong>{{number_format($sum_active_bed,2)}}</td>   
                    <td align="right"><strong>{{number_format($sum_adjrw,4)}}</strong></td>  
                    <td align="right"><strong>{{number_format($sum_cmi,2)}}</strong></td> 
                    <td align="right"><strong>{{number_format($sum_inc_total,2)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_lab_total)}}</strong></td>
                    <td align="right"><strong>{{number_format($sum_inc_drug_total,2)}}</strong></td>
                  </tr>   
                </tbody>
              </table>
              <!-- กราฟ -->
              <div class="row mt-4">
                <!-- กราฟอัตราครองเตียง -->
                <div class="col-md-6 mb-4">
                  <div class="card shadow-sm">
                    <div class="card-body">
                      <h6 class="text-center text-primary mb-1">
                        📈 อัตราครองเตียง (%)
                      </h6>
                      <div id="bed_occupancy_10703"></div>
                    </div>
                  </div>
                </div>
                <!-- กราฟ CMI -->
                <div class="col-md-6 mb-4">
                  <div class="card shadow-sm">
                    <div class="card-body">
                      <h6 class="text-center text-danger mb-1">
                        📊 CMI
                      </h6>
                      <div id="cmi_chart_10703"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <!-- END 10703 --> 
        </div>

      <!-- TAB PANES -->
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
      $('#table10985_ipd').DataTable({
        dom: '<"d-flex justify-content-end mb-2"B>rt',
        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-excel"></i> ส่งออก Excel',
            className: 'btn btn-success btn-sm',
            title: 'ข้อมูลบริการผู้ป่วยใน IPD โรงพยาบาลชานุมาน {{ $budget_year ?? "" }}'
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
      $('#table10986_ipd').DataTable({
        dom: '<"d-flex justify-content-end mb-2"B>rt',
        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-excel"></i> ส่งออก Excel',
            className: 'btn btn-success btn-sm',
            title: 'ข้อมูลบริการผู้ป่วยใน IPD โรงพยาบาลปทุมราชวงศา {{ $budget_year ?? "" }}'
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
      $('#table10987_ipd').DataTable({
        dom: '<"d-flex justify-content-end mb-2"B>rt',
        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-excel"></i> ส่งออก Excel',
            className: 'btn btn-success btn-sm',
            title: 'ข้อมูลบริการผู้ป่วยใน IPD โรงพยาบาลพนา {{ $budget_year ?? "" }}'
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
      $('#table10988_ipd').DataTable({
        dom: '<"d-flex justify-content-end mb-2"B>rt',
        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-excel"></i> ส่งออก Excel',
            className: 'btn btn-success btn-sm',
            title: 'ข้อมูลบริการผู้ป่วยใน IPD โรงพยาบาลเสนางคนิคม {{ $budget_year ?? "" }}'
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
      $('#table10989_ipd').DataTable({
        dom: '<"d-flex justify-content-end mb-2"B>rt',
        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-excel"></i> ส่งออก Excel',
            className: 'btn btn-success btn-sm',
            title: 'ข้อมูลบริการผู้ป่วยใน IPD โรงพยาบาลหัวตะพาน {{ $budget_year ?? "" }}'
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
      $('#table10990_ipd').DataTable({
        dom: '<"d-flex justify-content-end mb-2"B>rt',
        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-excel"></i> ส่งออก Excel',
            className: 'btn btn-success btn-sm',
            title: 'ข้อมูลบริการผู้ป่วยใน IPD โรงพยาบาลลืออำนาจ {{ $budget_year ?? "" }}'
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
      $('#table10703_ipd').DataTable({
        dom: '<"d-flex justify-content-end mb-2"B>rt',
        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-excel"></i> ส่งออก Excel',
            className: 'btn btn-success btn-sm',
            title: 'ข้อมูลบริการผู้ป่วยใน IPD โรงพยาบาลอำนาจเจริญ {{ $budget_year ?? "" }}'
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

<!-- script กราฟ  ---------------------------------------------------------------------------------------->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      // ✅ ดึงข้อมูลจาก PHP
      const months = {!! json_encode(array_column($total_10985_ipd, 'month')) !!};
      const bed_occupancy = {!! json_encode(array_column($total_10985_ipd, 'bed_occupancy')) !!};
      const cmi = {!! json_encode(array_column($total_10985_ipd, 'cmi')) !!};
      // 🩵 กราฟอัตราครองเตียง
      const bedChart = new ApexCharts(document.querySelector("#bed_occupancy_10985"), {
        series: [{
          name: 'อัตราครองเตียง (%)',
          data: bed_occupancy
        }],
        chart: {
          height: 250,
          type: 'area',
          toolbar: { show: false },
          animations: { enabled: true, easing: 'easeinout', speed: 800 }
        },
        colors: ['#4154f1'],
        markers: { size: 4 },
        fill: {
          type: "gradient",
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.3,
            opacityTo: 0.4,
            stops: [0, 90, 100]
          }
        },
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: {
          enabled: true,
          style: { fontSize: '12px', colors: ['#4154f1'] },
          background: { enabled: true, foreColor: '#fff', borderRadius: 2 }
        },
        xaxis: {
          categories: months,
          labels: { style: { fontSize: '13px' } }
        },
        yaxis: {
          title: { text: 'ร้อยละ (%)' },
          labels: { formatter: val => val.toFixed(1) }
        }
      });
      bedChart.render();


      // ❤️ กราฟ CMI
      const cmiChart = new ApexCharts(document.querySelector("#cmi_chart_10985"), {
        series: [{
          name: 'อัตราครองเตียง (%)',
          data: cmi
        }],
        chart: {
          height: 250,
          type: 'area',
          toolbar: { show: false },
          animations: { enabled: true, easing: 'easeinout', speed: 800 }
        },
        colors: ['#ff6384'],
        markers: { size: 4 },
        fill: {
          type: "gradient",
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.3,
            opacityTo: 0.4,
            stops: [0, 90, 100]
          }
        },
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: {
          enabled: true,
          style: { fontSize: '12px', colors: ['#ff6384'] },
          background: { enabled: true, foreColor: '#fff', borderRadius: 2 }
        },
        xaxis: {
          categories: months,
          labels: { style: { fontSize: '13px' } }
        },
        yaxis: {
          title: { text: 'ร้อยละ (%)' },
          labels: { formatter: val => val.toFixed(1) }
        }
      });
      cmiChart.render();

    });
  </script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      // ✅ ดึงข้อมูลจาก PHP
      const months = {!! json_encode(array_column($total_10986_ipd, 'month')) !!};
      const bed_occupancy = {!! json_encode(array_column($total_10986_ipd, 'bed_occupancy')) !!};
      const cmi = {!! json_encode(array_column($total_10986_ipd, 'cmi')) !!};
      // 🩵 กราฟอัตราครองเตียง
      const bedChart = new ApexCharts(document.querySelector("#bed_occupancy_10986"), {
        series: [{
          name: 'อัตราครองเตียง (%)',
          data: bed_occupancy
        }],
        chart: {
          height: 250,
          type: 'area',
          toolbar: { show: false },
          animations: { enabled: true, easing: 'easeinout', speed: 800 }
        },
        colors: ['#4154f1'],
        markers: { size: 4 },
        fill: {
          type: "gradient",
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.3,
            opacityTo: 0.4,
            stops: [0, 90, 100]
          }
        },
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: {
          enabled: true,
          style: { fontSize: '12px', colors: ['#4154f1'] },
          background: { enabled: true, foreColor: '#fff', borderRadius: 2 }
        },
        xaxis: {
          categories: months,
          labels: { style: { fontSize: '13px' } }
        },
        yaxis: {
          title: { text: 'ร้อยละ (%)' },
          labels: { formatter: val => val.toFixed(1) }
        }
      });
      bedChart.render();


      // ❤️ กราฟ CMI
      const cmiChart = new ApexCharts(document.querySelector("#cmi_chart_10986"), {
        series: [{
          name: 'อัตราครองเตียง (%)',
          data: cmi
        }],
        chart: {
          height: 250,
          type: 'area',
          toolbar: { show: false },
          animations: { enabled: true, easing: 'easeinout', speed: 800 }
        },
        colors: ['#ff6384'],
        markers: { size: 4 },
        fill: {
          type: "gradient",
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.3,
            opacityTo: 0.4,
            stops: [0, 90, 100]
          }
        },
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: {
          enabled: true,
          style: { fontSize: '12px', colors: ['#ff6384'] },
          background: { enabled: true, foreColor: '#fff', borderRadius: 2 }
        },
        xaxis: {
          categories: months,
          labels: { style: { fontSize: '13px' } }
        },
        yaxis: {
          title: { text: 'ร้อยละ (%)' },
          labels: { formatter: val => val.toFixed(1) }
        }
      });
      cmiChart.render();

    });
  </script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      // ✅ ดึงข้อมูลจาก PHP
      const months = {!! json_encode(array_column($total_10987_ipd, 'month')) !!};
      const bed_occupancy = {!! json_encode(array_column($total_10987_ipd, 'bed_occupancy')) !!};
      const cmi = {!! json_encode(array_column($total_10987_ipd, 'cmi')) !!};
      // 🩵 กราฟอัตราครองเตียง
      const bedChart = new ApexCharts(document.querySelector("#bed_occupancy_10987"), {
        series: [{
          name: 'อัตราครองเตียง (%)',
          data: bed_occupancy
        }],
        chart: {
          height: 250,
          type: 'area',
          toolbar: { show: false },
          animations: { enabled: true, easing: 'easeinout', speed: 800 }
        },
        colors: ['#4154f1'],
        markers: { size: 4 },
        fill: {
          type: "gradient",
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.3,
            opacityTo: 0.4,
            stops: [0, 90, 100]
          }
        },
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: {
          enabled: true,
          style: { fontSize: '12px', colors: ['#4154f1'] },
          background: { enabled: true, foreColor: '#fff', borderRadius: 2 }
        },
        xaxis: {
          categories: months,
          labels: { style: { fontSize: '13px' } }
        },
        yaxis: {
          title: { text: 'ร้อยละ (%)' },
          labels: { formatter: val => val.toFixed(1) }
        }
      });
      bedChart.render();


      // ❤️ กราฟ CMI
      const cmiChart = new ApexCharts(document.querySelector("#cmi_chart_10987"), {
        series: [{
          name: 'อัตราครองเตียง (%)',
          data: cmi
        }],
        chart: {
          height: 250,
          type: 'area',
          toolbar: { show: false },
          animations: { enabled: true, easing: 'easeinout', speed: 800 }
        },
        colors: ['#ff6384'],
        markers: { size: 4 },
        fill: {
          type: "gradient",
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.3,
            opacityTo: 0.4,
            stops: [0, 90, 100]
          }
        },
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: {
          enabled: true,
          style: { fontSize: '12px', colors: ['#ff6384'] },
          background: { enabled: true, foreColor: '#fff', borderRadius: 2 }
        },
        xaxis: {
          categories: months,
          labels: { style: { fontSize: '13px' } }
        },
        yaxis: {
          title: { text: 'ร้อยละ (%)' },
          labels: { formatter: val => val.toFixed(1) }
        }
      });
      cmiChart.render();

    });
  </script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      // ✅ ดึงข้อมูลจาก PHP
      const months = {!! json_encode(array_column($total_10988_ipd, 'month')) !!};
      const bed_occupancy = {!! json_encode(array_column($total_10988_ipd, 'bed_occupancy')) !!};
      const cmi = {!! json_encode(array_column($total_10988_ipd, 'cmi')) !!};
      // 🩵 กราฟอัตราครองเตียง
      const bedChart = new ApexCharts(document.querySelector("#bed_occupancy_10988"), {
        series: [{
          name: 'อัตราครองเตียง (%)',
          data: bed_occupancy
        }],
        chart: {
          height: 250,
          type: 'area',
          toolbar: { show: false },
          animations: { enabled: true, easing: 'easeinout', speed: 800 }
        },
        colors: ['#4154f1'],
        markers: { size: 4 },
        fill: {
          type: "gradient",
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.3,
            opacityTo: 0.4,
            stops: [0, 90, 100]
          }
        },
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: {
          enabled: true,
          style: { fontSize: '12px', colors: ['#4154f1'] },
          background: { enabled: true, foreColor: '#fff', borderRadius: 2 }
        },
        xaxis: {
          categories: months,
          labels: { style: { fontSize: '13px' } }
        },
        yaxis: {
          title: { text: 'ร้อยละ (%)' },
          labels: { formatter: val => val.toFixed(1) }
        }
      });
      bedChart.render();


      // ❤️ กราฟ CMI
      const cmiChart = new ApexCharts(document.querySelector("#cmi_chart_10988"), {
        series: [{
          name: 'อัตราครองเตียง (%)',
          data: cmi
        }],
        chart: {
          height: 250,
          type: 'area',
          toolbar: { show: false },
          animations: { enabled: true, easing: 'easeinout', speed: 800 }
        },
        colors: ['#ff6384'],
        markers: { size: 4 },
        fill: {
          type: "gradient",
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.3,
            opacityTo: 0.4,
            stops: [0, 90, 100]
          }
        },
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: {
          enabled: true,
          style: { fontSize: '12px', colors: ['#ff6384'] },
          background: { enabled: true, foreColor: '#fff', borderRadius: 2 }
        },
        xaxis: {
          categories: months,
          labels: { style: { fontSize: '13px' } }
        },
        yaxis: {
          title: { text: 'ร้อยละ (%)' },
          labels: { formatter: val => val.toFixed(1) }
        }
      });
      cmiChart.render();

    });
  </script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      // ✅ ดึงข้อมูลจาก PHP
      const months = {!! json_encode(array_column($total_10989_ipd, 'month')) !!};
      const bed_occupancy = {!! json_encode(array_column($total_10989_ipd, 'bed_occupancy')) !!};
      const cmi = {!! json_encode(array_column($total_10989_ipd, 'cmi')) !!};
      // 🩵 กราฟอัตราครองเตียง
      const bedChart = new ApexCharts(document.querySelector("#bed_occupancy_10989"), {
        series: [{
          name: 'อัตราครองเตียง (%)',
          data: bed_occupancy
        }],
        chart: {
          height: 250,
          type: 'area',
          toolbar: { show: false },
          animations: { enabled: true, easing: 'easeinout', speed: 800 }
        },
        colors: ['#4154f1'],
        markers: { size: 4 },
        fill: {
          type: "gradient",
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.3,
            opacityTo: 0.4,
            stops: [0, 90, 100]
          }
        },
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: {
          enabled: true,
          style: { fontSize: '12px', colors: ['#4154f1'] },
          background: { enabled: true, foreColor: '#fff', borderRadius: 2 }
        },
        xaxis: {
          categories: months,
          labels: { style: { fontSize: '13px' } }
        },
        yaxis: {
          title: { text: 'ร้อยละ (%)' },
          labels: { formatter: val => val.toFixed(1) }
        }
      });
      bedChart.render();


      // ❤️ กราฟ CMI
      const cmiChart = new ApexCharts(document.querySelector("#cmi_chart_10989"), {
        series: [{
          name: 'อัตราครองเตียง (%)',
          data: cmi
        }],
        chart: {
          height: 250,
          type: 'area',
          toolbar: { show: false },
          animations: { enabled: true, easing: 'easeinout', speed: 800 }
        },
        colors: ['#ff6384'],
        markers: { size: 4 },
        fill: {
          type: "gradient",
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.3,
            opacityTo: 0.4,
            stops: [0, 90, 100]
          }
        },
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: {
          enabled: true,
          style: { fontSize: '12px', colors: ['#ff6384'] },
          background: { enabled: true, foreColor: '#fff', borderRadius: 2 }
        },
        xaxis: {
          categories: months,
          labels: { style: { fontSize: '13px' } }
        },
        yaxis: {
          title: { text: 'ร้อยละ (%)' },
          labels: { formatter: val => val.toFixed(1) }
        }
      });
      cmiChart.render();

    });
  </script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      // ✅ ดึงข้อมูลจาก PHP
      const months = {!! json_encode(array_column($total_10990_ipd, 'month')) !!};
      const bed_occupancy = {!! json_encode(array_column($total_10990_ipd, 'bed_occupancy')) !!};
      const cmi = {!! json_encode(array_column($total_10990_ipd, 'cmi')) !!};
      // 🩵 กราฟอัตราครองเตียง
      const bedChart = new ApexCharts(document.querySelector("#bed_occupancy_10990"), {
        series: [{
          name: 'อัตราครองเตียง (%)',
          data: bed_occupancy
        }],
        chart: {
          height: 250,
          type: 'area',
          toolbar: { show: false },
          animations: { enabled: true, easing: 'easeinout', speed: 800 }
        },
        colors: ['#4154f1'],
        markers: { size: 4 },
        fill: {
          type: "gradient",
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.3,
            opacityTo: 0.4,
            stops: [0, 90, 100]
          }
        },
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: {
          enabled: true,
          style: { fontSize: '12px', colors: ['#4154f1'] },
          background: { enabled: true, foreColor: '#fff', borderRadius: 2 }
        },
        xaxis: {
          categories: months,
          labels: { style: { fontSize: '13px' } }
        },
        yaxis: {
          title: { text: 'ร้อยละ (%)' },
          labels: { formatter: val => val.toFixed(1) }
        }
      });
      bedChart.render();


      // ❤️ กราฟ CMI
      const cmiChart = new ApexCharts(document.querySelector("#cmi_chart_10990"), {
        series: [{
          name: 'อัตราครองเตียง (%)',
          data: cmi
        }],
        chart: {
          height: 250,
          type: 'area',
          toolbar: { show: false },
          animations: { enabled: true, easing: 'easeinout', speed: 800 }
        },
        colors: ['#ff6384'],
        markers: { size: 4 },
        fill: {
          type: "gradient",
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.3,
            opacityTo: 0.4,
            stops: [0, 90, 100]
          }
        },
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: {
          enabled: true,
          style: { fontSize: '12px', colors: ['#ff6384'] },
          background: { enabled: true, foreColor: '#fff', borderRadius: 2 }
        },
        xaxis: {
          categories: months,
          labels: { style: { fontSize: '13px' } }
        },
        yaxis: {
          title: { text: 'ร้อยละ (%)' },
          labels: { formatter: val => val.toFixed(1) }
        }
      });
      cmiChart.render();

    });
  </script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      // ✅ ดึงข้อมูลจาก PHP
      const months = {!! json_encode(array_column($total_10703_ipd, 'month')) !!};
      const bed_occupancy = {!! json_encode(array_column($total_10703_ipd, 'bed_occupancy')) !!};
      const cmi = {!! json_encode(array_column($total_10703_ipd, 'cmi')) !!};
      // 🩵 กราฟอัตราครองเตียง
      const bedChart = new ApexCharts(document.querySelector("#bed_occupancy_10703"), {
        series: [{
          name: 'อัตราครองเตียง (%)',
          data: bed_occupancy
        }],
        chart: {
          height: 250,
          type: 'area',
          toolbar: { show: false },
          animations: { enabled: true, easing: 'easeinout', speed: 800 }
        },
        colors: ['#4154f1'],
        markers: { size: 4 },
        fill: {
          type: "gradient",
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.3,
            opacityTo: 0.4,
            stops: [0, 90, 100]
          }
        },
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: {
          enabled: true,
          style: { fontSize: '12px', colors: ['#4154f1'] },
          background: { enabled: true, foreColor: '#fff', borderRadius: 2 }
        },
        xaxis: {
          categories: months,
          labels: { style: { fontSize: '13px' } }
        },
        yaxis: {
          title: { text: 'ร้อยละ (%)' },
          labels: { formatter: val => val.toFixed(1) }
        }
      });
      bedChart.render();


      // ❤️ กราฟ CMI
      const cmiChart = new ApexCharts(document.querySelector("#cmi_chart_10703"), {
        series: [{
          name: 'อัตราครองเตียง (%)',
          data: cmi
        }],
        chart: {
          height: 250,
          type: 'area',
          toolbar: { show: false },
          animations: { enabled: true, easing: 'easeinout', speed: 800 }
        },
        colors: ['#ff6384'],
        markers: { size: 4 },
        fill: {
          type: "gradient",
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.3,
            opacityTo: 0.4,
            stops: [0, 90, 100]
          }
        },
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: {
          enabled: true,
          style: { fontSize: '12px', colors: ['#ff6384'] },
          background: { enabled: true, foreColor: '#fff', borderRadius: 2 }
        },
        xaxis: {
          categories: months,
          labels: { style: { fontSize: '13px' } }
        },
        yaxis: {
          title: { text: 'ร้อยละ (%)' },
          labels: { formatter: val => val.toFixed(1) }
        }
      });
      cmiChart.render();

    });
  </script>