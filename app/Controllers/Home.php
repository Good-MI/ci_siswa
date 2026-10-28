<?php

namespace App\Controllers;

use App\Models\SiswaModel;

class Home extends BaseController
{
    protected $helpers = ['url'];

    public function index(): string
    {
        $siswaModel = new SiswaModel();
        $data['siswa'] = $siswaModel->getSiswa();

        return view('siswa_view', $data);
    }

    public function tambah()
    {
        $siswaModel = new SiswaModel();

        // Browser POST nis is strictly ignored; NIS is auto-generated on server
        $nama  = trim((string) $this->request->getPost('nama'));
        $kelas = trim((string) $this->request->getPost('kelas'));

        try {
            $nis = $siswaModel->insertWithNis($nama, $kelas);
            session()->setFlashdata('success', "Student added successfully with NIS {$nis}.");
        } catch (\Throwable $e) {
            session()->setFlashdata('error', "Failed to add student: " . $e->getMessage());
        }

        return redirect()->to(base_url());
    }

    // Method untuk memperbarui data siswa berdasarkan ID
    public function edit($id)
    {
        $siswaModel = new SiswaModel();

        // Browser POST nis is strictly ignored
        $nama  = trim((string) $this->request->getPost('nama'));
        $kelas = trim((string) $this->request->getPost('kelas'));

        try {
            $result = $siswaModel->updateWithNis((int)$id, $nama, $kelas);

            if (!empty($result['nis_changed'])) {
                session()->setFlashdata('success', "Major changed! NIS updated from {$result['old_nis']} to {$result['new_nis']}.");
            } else {
                session()->setFlashdata('success', 'Student data updated successfully.');
            }
        } catch (\Throwable $e) {
            session()->setFlashdata('error', "Failed to update student: " . $e->getMessage());
        }

        return redirect()->to(base_url());
    }

    public function hapus($id)
    {
        $siswaModel = new SiswaModel();

        try {
            $student = $siswaModel->find($id);
            $nama = $student ? $student['nama'] : 'Student';
            $siswaModel->deleteWithHistory((int)$id);
            session()->setFlashdata('success', "Student {$nama} has been deleted.");
        } catch (\Throwable $e) {
            session()->setFlashdata('error', "Failed to delete student: " . $e->getMessage());
        }

        return redirect()->to(base_url());
    }
}