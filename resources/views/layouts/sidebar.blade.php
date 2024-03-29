<div class="deznav">
    <div class="deznav-scroll">
        <ul class="metismenu" id="menu">
            <li>
                <a href="{{ route('dashboard') }}" class="ai-icon" aria-expanded="true">
                    <i class="flaticon-381-networking"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            @if (Auth::user()->level == 'admin')
                <li>
                    <a href="{{ route('struktural') }}" class="ai-icon" aria-expanded="false">
                        <i class="flaticon-381-networking-1"></i>
                        <span class="nav-text">Struktural</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('jabatan') }}" class="ai-icon" aria-expanded="false">
                        <i class="flaticon-381-user-2"></i>
                        <span class="nav-text">Jabatan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('kpiperilaku') }}" class="ai-icon" aria-expanded="false">
                        <i class="flaticon-381-list"></i>
                        <span class="nav-text">KPI Perilaku</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pegawai') }}" class="ai-icon" aria-expanded="false">
                        <i class="flaticon-381-user-8"></i>
                        <span class="nav-text">Pegawai</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('jadwal') }}" class="ai-icon" aria-expanded="false">
                        <i class="flaticon-381-time"></i>
                        <span class="nav-text">Jadwal Penilaian</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('rekap-penilaian') }}" class="ai-icon" aria-expanded="false">
                        <i class="flaticon-381-file-1"></i>
                        <span class="nav-text">Rekap Penilaian</span>
                    </a>
                </li>
            @endif
            @if (Auth::user()->level == 'pegawai')
                @penilai
                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="flaticon-381-notepad"></i>
                        <span class="nav-text">Penilaian</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('belum-dinilai') }}">Belum Dinilai</a></li>
                        <li><a href="{{ route('menunggu-verifikasi') }}">Menunggu Verifikasi</a></li>
                        <li><a href="{{route('banding-penilaian')}}">Banding Penilaian</a></li>
                        <li><a href="{{ route('selesai') }}">Selesai</a></li>
                    </ul>
                </li>
                {{-- <li>
                <a href="javascript:void()" class="ai-icon" aria-expanded="true">
                    <i class="fa fa-history" style="font-size: 24px"></i>
                    <span class="nav-text">Histori Banding</span>
                </a>
            </li> --}}
                @endpenilai
                @atasanpenilai
                <li>
                    <a href="{{ route('approve-penilaian') }}" class="ai-icon" aria-expanded="true">
                        <i class="flaticon-381-list-1"></i>
                        <span class="nav-text">Approve Penilaian</span>
                    </a>
                </li>
                @endatasanpenilai
            @endif
        </ul>
    </div>
</div>
