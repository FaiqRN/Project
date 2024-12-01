<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgendaRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Authorization dihandle oleh middleware
    }

    public function rules()
    {
        return [
            'agenda' => 'required|array',
            'agenda.*.nama_agenda' => 'required|string|max:200',
            'agenda.*.tanggal_agenda' => 'required|date',
            'agenda.*.deskripsi' => 'required',
            'agenda.*.file_surat_agenda' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'kegiatan_type' => 'required|in:jurusan,prodi',
            'kegiatan_id' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'agenda.required' => 'Data agenda harus diisi',
            'agenda.*.nama_agenda.required' => 'Nama agenda harus diisi',
            'agenda.*.nama_agenda.max' => 'Nama agenda maksimal 200 karakter',
            'agenda.*.tanggal_agenda.required' => 'Tanggal agenda harus diisi',
            'agenda.*.tanggal_agenda.date' => 'Format tanggal tidak valid',
            'agenda.*.deskripsi.required' => 'Deskripsi agenda harus diisi',
            'agenda.*.file_surat_agenda.mimes' => 'File harus berupa PDF, DOC, atau DOCX',
            'agenda.*.file_surat_agenda.max' => 'Ukuran file maksimal 2MB',
            'kegiatan_type.required' => 'Tipe kegiatan harus diisi',
            'kegiatan_type.in' => 'Tipe kegiatan tidak valid',
            'kegiatan_id.required' => 'ID kegiatan harus diisi',
            'kegiatan_id.integer' => 'ID kegiatan harus berupa angka'
        ];
    }
}