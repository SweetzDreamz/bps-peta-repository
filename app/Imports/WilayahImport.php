<?php

namespace App\Imports;

use App\Models\Wilayah;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class WilayahImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    public function model(array $row): ?Wilayah
    {
        return new Wilayah([
            'id_subsls'     => $row['id_subsls']      ?? null,
            'nama_sls'      => $row['nama_sls']        ?? '',
            'nama_ketua'    => $row['nama_ketua']      ?? null,
            'jenis'         => $row['jenis']           ?? null,
            'kode_prop'     => $row['kode_prop']       ?? null,
            'kode_kab'      => $row['kode_kab']        ?? null,
            'kode_kec'      => $row['kode_kec']        ?? null,
            'kode_des'      => $row['kode_des']        ?? null,
            'kode_sls'      => $row['kode_sls']        ?? null,
            'kode_subsls'   => $row['kode_subsls']     ?? null,
            'klas'          => $row['klas']            ?? null,
            'nama_prop'     => $row['nama_prop']       ?? null,
            'nama_kab'      => $row['nama_kab']        ?? null,
            'nama_kec'      => $row['nama_kec']        ?? null,
            'nama_des'      => $row['nama_des']        ?? null,
            'kk'            => $row['kk']              ?? 0,
            'btt'           => $row['btt']             ?? 0,
            'bttk'          => $row['bttk']            ?? 0,
            'bku'           => $row['bku']             ?? 0,
            'bbtt_nonusaha' => $row['bbtt_nonusaha']   ?? 0,
            'usaha'         => $row['usaha']           ?? 0,
            'muatan'        => $row['muatan']          ?? 0,
            'status'        => 'aktif',
            'asal'          => 'import',
        ]);
    }
}