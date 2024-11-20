<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class UserController extends Controller{

    public function dashboard(){
        $breadcrumb = (object) [
            'title' => 'Dashboard',
            'list' => ['Home', 'Dashboard']
        ];

        // Ambil data user untuk dashboard
        $user = UserModel::find(session('user_id'));
        
        return view('dosen.dashboard', compact('breadcrumb', 'user'));
    }

    public function profile(){
        $breadcrumb = (object) [
            'title' => 'Profil',
            'list' => ['Beranda', 'Profil']
        ];

        $user = UserModel::with('level')->find(session('user_id'));
        return view('user.profile', compact('breadcrumb', 'user'));
    }

    public function edit(){
        $breadcrumb = (object) [
            'title' => 'Edit Profil',
            'list' => ['Beranda', 'Profil', 'Edit']
        ];

        $user = UserModel::find(session('user_id'));
        return view('user.edit', compact('breadcrumb', 'user'));
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:20',
            'nama_lengkap' => 'required|string|max:100',
            'nidn' => 'required|string|max:18',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gelar_depan' => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50',
            'jabatan_fungsional' => 'required|string|max:100',
            'program_studi' => 'required|string|max:100',
            'pendidikan_terakhir' => 'required|in:S1,S2,S3,Profesor',
            'asal_perguruan_tinggi' => 'required|string|max:100',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'agama' => 'required|string|max:50',
            'status_nikah' => 'required|in:Menikah,Belum Menikah,Cerai',
            'status_ikatan_kerja' => 'required|string|max:100',
            'alamat' => 'required|string',
            'email' => 'required|email|max:100'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput($request->except(['foto', 'current_password', 'new_password', 'password_confirmation']));
        }

        try {
            $user = UserModel::find(session('user_id'));
            $userData = $request->except(['foto', '_token', '_method','current_password', 'new_password', 'password_confirmation']);

            if ($request->filled('current_password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return redirect()
                        ->back()
                        ->withErrors(['current_password' => 'Password saat ini tidak sesuai'])
                        ->withInput($request->except(['foto', 'current_password', 'new_password', 'password_confirmation']));
                }
                
                // Update password baru
                $userData['password'] = Hash::make($request->new_password);
            }

            // Handle foto upload
            if ($request->hasFile('foto')) {
                try {
                    $image = $request->file('foto');
                    $img = Image::make($image->getRealPath());
                    
                    // Resize jika ukuran melebihi maksimum
                    if ($img->width() > 800 || $img->height() > 800) {
                        $img->resize(800, 800, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    }
                    
                    // Kompres gambar
                    $img->encode('jpg', 80);
                    
                    // Konversi ke base64
                    $base64Image = 'data:image/jpeg;base64,' . base64_encode($img->__toString());
                    $userData['foto'] = $base64Image;
                    
                } catch (\Exception $e) {
                    return redirect()
                        ->back()
                        ->with('error', 'Gagal memproses foto: ' . $e->getMessage())
                        ->withInput($request->except(['foto', 'current_password', 'new_password', 'password_confirmation']));
                }
            }

            // Update data user
            $userData['updated_by'] = session('username');
            $user->update($userData);
            
            // Update session data
            $sessionData = [
                'username' => $user->username,
                'nama_lengkap' => $user->nama_lengkap,
                'gelar_depan' => $user->gelar_depan,
                'gelar_belakang' => $user->gelar_belakang,
                'nidn' => $user->nidn,
                'program_studi' => $user->program_studi,
            ];

            // Update foto di session hanya jika berhasil diupdate
            if (isset($userData['foto'])) {
                $sessionData['foto'] = $userData['foto'];
            }

            session($sessionData);

            $message = 'Profil berhasil diperbarui';
            if ($request->filled('current_password')) {
                $message .= ' dan password berhasil diganti';
            }

            return redirect()
                ->route('profile')
                ->with('success', 'Profil berhasil diperbarui');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui profil: ' . $e->getMessage())
                ->withInput($request->except(['foto', 'current_password', 'new_password', 'password_confirmation']));
        }
    }
}