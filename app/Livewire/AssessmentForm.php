<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Peserta;
use App\Models\SelfAssessment;
use Livewire\WithFileUploads;

class AssessmentForm extends Component
{
    use WithFileUploads;

    public $token;
    public $peserta;
    public $isSubmitted = false;
    public $dokumen_paparan;

    // Integritas (9 indikator)
    public $int_1, $int_2, $int_3, $int_4, $int_5, $int_6, $int_7, $int_8, $int_9;
    public $uraian_int_1, $uraian_int_2, $uraian_int_3, $uraian_int_4, $uraian_int_5, $uraian_int_6, $uraian_int_7, $uraian_int_8, $uraian_int_9;
    
    // Reputasi (2 indikator)
    public $rep_1, $rep_2;
    public $uraian_rep_1, $uraian_rep_2;
    
    // Kompetensi (9 indikator)
    public $kom_1, $kom_2, $kom_3, $kom_4, $kom_5, $kom_6, $kom_7, $kom_8, $kom_9;
    public $uraian_kom_1, $uraian_kom_2, $uraian_kom_3, $uraian_kom_4, $uraian_kom_5, $uraian_kom_6, $uraian_kom_7, $uraian_kom_8, $uraian_kom_9;
    
    // Inovasi (3 indikator)
    public $ino_1, $ino_2, $ino_3;
    public $uraian_ino_1, $uraian_ino_2, $uraian_ino_3;

    // Klarifikasi (15 pertanyaan essai)
    public $klarifikasi_1, $klarifikasi_2, $klarifikasi_3, $klarifikasi_4, $klarifikasi_5, $klarifikasi_6, $klarifikasi_7, $klarifikasi_8, $klarifikasi_9, $klarifikasi_10, $klarifikasi_11, $klarifikasi_12, $klarifikasi_13, $klarifikasi_14, $klarifikasi_15;

    public $currentStep = 1;

    public function mount($token)
    {
        $this->token = $token;
        $this->peserta = Peserta::with('koperasi')->where('token_assessment', $token)->firstOrFail();

        if (SelfAssessment::where('peserta_id', $this->peserta->id)->exists()) {
            $this->isSubmitted = true;
        }
    }

    public function nextStep()
    {
        if ($this->currentStep == 1) {
            $this->validate([
                'int_1' => 'required|numeric', 'int_2' => 'required|numeric', 'int_3' => 'required|numeric', 'int_4' => 'required|numeric', 'int_5' => 'required|numeric', 'int_6' => 'required|numeric', 'int_7' => 'required|numeric', 'int_8' => 'required|numeric', 'int_9' => 'required|numeric',
            ], ['required' => 'Wajib diisi.']);
        } elseif ($this->currentStep == 2) {
            $this->validate([
                'rep_1' => 'required|numeric', 'rep_2' => 'required|numeric',
            ], ['required' => 'Wajib diisi.']);
        } elseif ($this->currentStep == 3) {
            $this->validate([
                'kom_1' => 'required|numeric', 'kom_2' => 'required|numeric', 'kom_3' => 'required|numeric', 'kom_4' => 'required|numeric', 'kom_5' => 'required|numeric', 'kom_6' => 'required|numeric', 'kom_7' => 'required|numeric', 'kom_8' => 'required|numeric', 'kom_9' => 'required|numeric',
            ], ['required' => 'Wajib diisi.']);
        } elseif ($this->currentStep == 4) {
            $this->validate([
                'ino_1' => 'required|numeric', 'ino_2' => 'required|numeric', 'ino_3' => 'required|numeric',
            ], ['required' => 'Wajib diisi.']);
        } elseif ($this->currentStep == 5) {
            $this->validate([
                'klarifikasi_1' => 'required', 'klarifikasi_2' => 'required', 'klarifikasi_3' => 'required', 'klarifikasi_4' => 'required', 'klarifikasi_5' => 'required', 'klarifikasi_6' => 'required', 'klarifikasi_7' => 'required', 'klarifikasi_8' => 'required', 'klarifikasi_9' => 'required', 'klarifikasi_10' => 'required', 'klarifikasi_11' => 'required', 'klarifikasi_12' => 'required', 'klarifikasi_13' => 'required', 'klarifikasi_14' => 'required', 'klarifikasi_15' => 'required',
            ], ['required' => 'Wajib diisi (isi "Tidak Ada" jika kosong).']);
        }

        $this->currentStep++;
    }

    public function previousStep()
    {
        $this->currentStep--;
    }

    public function submit()
    {
        $this->validate([
            'int_1' => 'required|numeric', 'int_2' => 'required|numeric', 'int_3' => 'required|numeric', 'int_4' => 'required|numeric', 'int_5' => 'required|numeric', 'int_6' => 'required|numeric', 'int_7' => 'required|numeric', 'int_8' => 'required|numeric', 'int_9' => 'required|numeric',
            'rep_1' => 'required|numeric', 'rep_2' => 'required|numeric',
            'kom_1' => 'required|numeric', 'kom_2' => 'required|numeric', 'kom_3' => 'required|numeric', 'kom_4' => 'required|numeric', 'kom_5' => 'required|numeric', 'kom_6' => 'required|numeric', 'kom_7' => 'required|numeric', 'kom_8' => 'required|numeric', 'kom_9' => 'required|numeric',
            'ino_1' => 'required|numeric', 'ino_2' => 'required|numeric', 'ino_3' => 'required|numeric',
            'klarifikasi_1' => 'required', 'klarifikasi_2' => 'required', 'klarifikasi_3' => 'required', 'klarifikasi_4' => 'required', 'klarifikasi_5' => 'required', 'klarifikasi_6' => 'required', 'klarifikasi_7' => 'required', 'klarifikasi_8' => 'required', 'klarifikasi_9' => 'required', 'klarifikasi_10' => 'required', 'klarifikasi_11' => 'required', 'klarifikasi_12' => 'required', 'klarifikasi_13' => 'required', 'klarifikasi_14' => 'required', 'klarifikasi_15' => 'required',
            'dokumen_paparan' => 'nullable|mimes:pdf,ppt,pptx|max:10240',
        ], [
            'required' => 'Pertanyaan wajib diisi.',
            'dokumen_paparan.mimes' => 'Format paparan harus berupa PDF, PPT, atau PPTX.',
        ]);

        $path = null;
        if ($this->dokumen_paparan) {
            $path = $this->dokumen_paparan->store('paparan-peserta', 'public');
        }

        $this->peserta->update([
            'dokumen_paparan' => $path,
<<<<<<< HEAD
            'status_verifikasi' => 'Pending'
=======
            'status_verifikasi' => 'Pending' // Siap diverifikasi Admin
>>>>>>> 67efacadfa6f074c57cb346c7380c20a5f378508
        ]);

        $integritasScore = $this->int_1 + $this->int_2 + $this->int_3 + $this->int_4 + $this->int_5 + $this->int_6 + $this->int_7 + $this->int_8 + $this->int_9;
        $reputasiScore = $this->rep_1 + $this->rep_2;
        $kompetensiScore = $this->kom_1 + $this->kom_2 + $this->kom_3 + $this->kom_4 + $this->kom_5 + $this->kom_6 + $this->kom_7 + $this->kom_8 + $this->kom_9;
        $inovasiScore = $this->ino_1 + $this->ino_2 + $this->ino_3;

        $status = 'Tidak Terpenuhi';
        if ($integritasScore >= 9 && $reputasiScore >= 2 && $kompetensiScore >= 27 && $inovasiScore >= 9) {
            $status = 'Terpenuhi';
        }

        $jawabanRaw = [
            'integritas' => [
                ['skor' => $this->int_1, 'uraian' => $this->uraian_int_1],
                ['skor' => $this->int_2, 'uraian' => $this->uraian_int_2],
                ['skor' => $this->int_3, 'uraian' => $this->uraian_int_3],
                ['skor' => $this->int_4, 'uraian' => $this->uraian_int_4],
                ['skor' => $this->int_5, 'uraian' => $this->uraian_int_5],
                ['skor' => $this->int_6, 'uraian' => $this->uraian_int_6],
                ['skor' => $this->int_7, 'uraian' => $this->uraian_int_7],
                ['skor' => $this->int_8, 'uraian' => $this->uraian_int_8],
                ['skor' => $this->int_9, 'uraian' => $this->uraian_int_9],
            ],
            'reputasi' => [
                ['skor' => $this->rep_1, 'uraian' => $this->uraian_rep_1],
                ['skor' => $this->rep_2, 'uraian' => $this->uraian_rep_2],
            ],
            'kompetensi' => [
                ['skor' => $this->kom_1, 'uraian' => $this->uraian_kom_1],
                ['skor' => $this->kom_2, 'uraian' => $this->uraian_kom_2],
                ['skor' => $this->kom_3, 'uraian' => $this->uraian_kom_3],
                ['skor' => $this->kom_4, 'uraian' => $this->uraian_kom_4],
                ['skor' => $this->kom_5, 'uraian' => $this->uraian_kom_5],
                ['skor' => $this->kom_6, 'uraian' => $this->uraian_kom_6],
                ['skor' => $this->kom_7, 'uraian' => $this->uraian_kom_7],
                ['skor' => $this->kom_8, 'uraian' => $this->uraian_kom_8],
                ['skor' => $this->kom_9, 'uraian' => $this->uraian_kom_9],
            ],
            'inovasi' => [
                ['skor' => $this->ino_1, 'uraian' => $this->uraian_ino_1],
                ['skor' => $this->ino_2, 'uraian' => $this->uraian_ino_2],
                ['skor' => $this->ino_3, 'uraian' => $this->uraian_ino_3],
            ],
            'klarifikasi' => [
                $this->klarifikasi_1, $this->klarifikasi_2, $this->klarifikasi_3, $this->klarifikasi_4, $this->klarifikasi_5,
                $this->klarifikasi_6, $this->klarifikasi_7, $this->klarifikasi_8, $this->klarifikasi_9, $this->klarifikasi_10,
                $this->klarifikasi_11, $this->klarifikasi_12, $this->klarifikasi_13, $this->klarifikasi_14, $this->klarifikasi_15
            ]
        ];

        SelfAssessment::create([
            'peserta_id' => $this->peserta->id,
            'skor_mandiri_integritas' => $integritasScore,
            'skor_mandiri_reputasi' => $reputasiScore,
            'skor_mandiri_kompetensi' => $kompetensiScore,
            'skor_mandiri_inovasi' => $inovasiScore,
            'status_mandiri' => $status,
            'integritas_score' => $integritasScore,
            'reputasi_score' => $reputasiScore,
            'kompetensi_score' => $kompetensiScore,
            'inovasi_score' => $inovasiScore,
            'status_kelulusan' => $status,
            'jawaban_raw' => $jawabanRaw,
        ]);

        $this->isSubmitted = true;
    }

    public function render()
    {
        return view('livewire.assessment-form')->layout('layouts.guest');
    }
}
