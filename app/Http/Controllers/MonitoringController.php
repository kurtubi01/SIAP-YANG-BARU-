<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\Sop;
use App\Services\UserActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MonitoringController extends Controller
{
    public function __construct(
        private UserActivityService $userActivityService
    ) {
    }

    private function routePrefix(): string
    {
        return strtolower((string) Auth::user()?->role ?: 'admin');
    }

    private function currentTeamId(): ?int
    {
        return Auth::user()?->id_timkerja;
    }

    private function isScopedRole(): bool
    {
        return $this->routePrefix() === 'operator';
    }

    private function applyRoleScope($query)
    {
        if (!$this->isScopedRole()) {
            return $query;
        }

        $teamId = $this->currentTeamId();

        if (!$teamId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('sop.subjek', function ($subQuery) use ($teamId) {
            $subQuery->where('id_timkerja', $teamId);
        });
    }

    private function visibleSopQuery()
    {
        $query = Sop::query()->where('status', 'aktif')->orderBy('nama_sop');

        if (!$this->isScopedRole()) {
            return $query;
        }

        $teamId = $this->currentTeamId();

        if (!$teamId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('subjek', function ($subQuery) use ($teamId) {
            $subQuery->where('id_timkerja', $teamId);
        });
    }

    private function visibleSopIds(): array
    {
        return $this->visibleSopQuery()->pluck('id_sop')->map(fn ($id) => (int) $id)->all();
    }

    private function findVisibleMonitoringOrFail(int $id): Monitoring
    {
        $query = Monitoring::query()->where('id_monitoring', $id);
        $this->applyRoleScope($query);

        return $query->firstOrFail();
    }

    public function index()
    {
        $monitorings = Monitoring::with(['sop.subjek.timkerja', 'user.timkerja'])
            ->orderBy('id_monitoring', 'desc');

        
        $this->applyRoleScope($monitorings);

        $monitorings = $monitorings
            ->get();

        return view('pages.monitoring.index', compact('monitorings'));
    }

    public function create()
    {
        $sops = $this->visibleSopQuery()->get();

        return view('pages.monitoring.create', compact('sops'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_sop' => ['required', Rule::in($this->visibleSopIds())],
            'kriteria_penilaian' => 'required|in:Berjalan dengan baik,Tidak berjalan dengan baik',
            'hasil_monitoring' => 'required',
        ]);

        $monitoring = Monitoring::create([
            'id_sop' => $request->id_sop,
            'id_user' => Auth::id(),
            'tanggal' => now(),
            'kriteria_penilaian' => $request->kriteria_penilaian,
            'hasil_monitoring' => $request->hasil_monitoring,
            'catatan' => $request->catatan,
        ]);

        $this->userActivityService->log(
            $request->user(),
            'Tambah monitoring',
            'Menambahkan monitoring untuk SOP ID ' . $monitoring->id_sop . '.',
            $request
        );

        $prefix = $this->routePrefix();

        return redirect()->route($prefix . '.monitoring.index')->with('success', 'Data Monitoring berhasil disimpan!');
    }

    public function destroy(Request $request, $id)
    {
        $monitoring = $this->findVisibleMonitoringOrFail((int) $id);
        $targetId = $monitoring->id_sop;
        $monitoring->delete();

        $this->userActivityService->log(
            $request->user(),
            'Hapus monitoring',
            'Menghapus monitoring untuk SOP ID ' . $targetId . '.',
            $request
        );

        return redirect()->back()->with('success', 'Data Monitoring berhasil dihapus!');
    }

    public function show($id)
    {
        return redirect()->route($this->routePrefix() . '.monitoring.index');
    }

    public function edit($id)
    {
        return redirect()->route($this->routePrefix() . '.monitoring.index');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route($this->routePrefix() . '.monitoring.index');
    }
}
