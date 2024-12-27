from flask import Flask, jsonify, request, render_template_string, redirect, url_for,session,send_file
from docx import Document
from fpdf import FPDF
import tempfile
from reportlab.lib.pagesizes import letter
from reportlab.pdfgen import canvas
import firebase_admin
from firebase_admin import credentials, db
from werkzeug.security import generate_password_hash
from flask import Flask, request, jsonify
import json
from reportlab.platypus import SimpleDocTemplate, Table, TableStyle
from reportlab.lib import colors
from reportlab.lib.pagesizes import letter
import os

app = Flask(__name__)

app = Flask(__name__, static_folder='css')
# Inisialisasi Firebase
cred = credentials.Certificate("serviceAccountKey.json")
firebase_admin.initialize_app(cred, {
    'databaseURL': 'https://sensi-17f27-default-rtdb.firebaseio.com'
})

# Tambahkan secret_key
app.secret_key = os.urandom(24)  # Menghasilkan kunci rahasia acak

# bagian tambah pengumuman di dashboard
# Tambah Pengumuman
@app.route('/add_announcement', methods=['POST'])
def add_announcement():
    data = request.json
    text = data.get('text')
    if not text:
        return jsonify({'error': 'Teks tidak boleh kosong'}), 400

    ref = db.reference('announcements')
    ref.push({'text': text})

    return jsonify({'message': 'Pengumuman berhasil ditambahkan'}), 200

# Ambil Semua Pengumuman
@app.route('/get_announcements', methods=['GET'])
def get_announcements():
    ref = db.reference('announcements')
    announcements = ref.get()
    result = [{'id': key, 'text': value['text']} for key, value in announcements.items()] if announcements else []
    return jsonify(result), 200

# Hapus Pengumuman Berdasarkan ID
@app.route('/delete_announcement/<announcement_id>', methods=['DELETE'])
def delete_announcement(announcement_id):
    try:
        ref = db.reference(f'announcements/{announcement_id}')
        if not ref.get():
            return jsonify({'error': 'Pengumuman tidak ditemukan'}), 404
        ref.delete()
        return jsonify({'message': 'Pengumuman berhasil dihapus'}), 200
    except Exception as e:
        return jsonify({'error': str(e)}), 500
# bagian tambah pengumuman di dashboard



# Route untuk menampilkan halaman registDosen.php
@app.route('/registDosen')
def regist_dosen():
    with open('registDosen.php', 'r') as file:
        return render_template_string(file.read())

# Route untuk menerima data formulir dan menambah dosen ke Firebase
@app.route('/register_dosen', methods=['POST'])
def register_dosen():
    try:
        # Ambil data dari request JSON
        data = request.get_json()

        # Ambil input dari form
        username = data.get('username')
        email = data.get('email')
        homebase = data.get('homebase')
        matakuliah = data.get('matakuliah')
        gender = data.get('gender')
        password = data.get('password')
        nuptk = data.get('nuptk')

        # Validasi data (contoh validasi sederhana)
        if not username or not email or not password or not nuptk or not homebase or not matakuliah:
            return jsonify({'error': 'Semua field wajib diisi!'}), 400

        # Validasi format email
        if '@' not in email:
            return jsonify({'error': 'Email tidak valid!'}), 400

        # Simpan data ke Firebase
        ref = db.reference('dosen')
        dosen_ref = ref.push({
            'username': username,
            'email': email,
            'homebase': homebase,
            'nama_matakuliah': matakuliah,
            'password': password,
            'gender': gender,
            'password': password,  # Di dunia nyata, password sebaiknya di-hash!
            'nuptk': nuptk
        })

        # Mengirim response sukses
        return jsonify({'message': 'Dosen berhasil ditambahkan!'}), 200

    except Exception as e:
        # Tangani error jika ada
        return jsonify({'error': str(e)}), 500


@app.route('/get_dosen', methods=['GET'])
def get_dosen():
    try:    
        ref = db.reference('dosen')
        dosen_data = ref.get()

        if not dosen_data:
            return jsonify([]), 200

        result = [
            {
                'id': key,  # Tambahkan ID sebagai key
                'username': value.get('username', 'N/A'),
                'gender': value.get('gender', 'N/A'),
                'nuptk': value.get('nuptk', 'N/A'),
                'homebase': value.get('homebase', 'N/A'),
                'nama_matakuliah': value.get('nama_matakuliah', 'N/A'),
                'email': value.get('email', 'N/A')
            }
            for key, value in dosen_data.items()
        ]

        return jsonify(result), 200
    except Exception as e:
        return jsonify({'error': str(e)}), 500



# Route untuk menampilkan halaman registMahasiswa.php
@app.route('/registMahasis')
def regist_mahasiswa():
    with open('registMahasis.php', 'r') as file:
        return render_template_string(file.read())

# Route untuk menerima data formulir dan menambah mahasiswa ke Firebase
@app.route('/register_mahasiswa', methods=['POST'])
def register_mahasiswa():
    try:
        # Ambil data dari request JSON
        data_mhs = request.get_json()

        # Ambil input dari form
        username = data_mhs.get('username')
        nim = data_mhs.get('nim')
        gender = data_mhs.get('gender')
        email = data_mhs.get('email')
        prodi = data_mhs.get('prodi')
        jurusan = data_mhs.get('jurusan')
        password = data_mhs.get('password')


        # Validasi data (contoh validasi sederhana)
        if not username or not email or not password or not nim or not prodi or not jurusan:
            return jsonify({'error': 'Semua field wajib diisi!'}), 400

        # Validasi format email
        if '@' not in email:
            return jsonify({'error': 'Email tidak valid!'}), 400

        # Simpan data ke Firebase
        ref = db.reference('mahasiswa')
        mahasiswa_ref = ref.push({
            'username': username,
            'nim': nim,
            'email': email,
            'gender': gender,
            'prodi': prodi,
            'password': password,
            'jurusan': jurusan,
            'password': password  # Di dunia nyata, password sebaiknya di-hash!

        })

        # Mengirim response sukses
        return jsonify({'message': 'Mahasiswa berhasil ditambahkan!'}), 200

    except Exception as e:
        # Tangani error jika ada
        return jsonify({'error': str(e)}), 500


@app.route('/get_mahasiswa', methods=['GET'])
def get_mahasiswa():
    try:
        # Ambil data mahasiswa dari Firebase
        ref = db.reference('mahasiswa')
        mahasiswa_data = ref.get()

        # Cek apakah data mahasiswa ada
        if not mahasiswa_data:
            print("Tidak ada data mahasiswa.")
            return jsonify([]), 200  # Kembalikan array kosong jika tidak ada data

        # Format data mahasiswa menjadi bentuk yang diinginkan
        result = [
            {
                'id': key,
                'nim': mahasiswa.get('nim', 'N/A'),   # Menangani jika nuptk tidak ada
                'username': mahasiswa.get('username', 'N/A'),  # Menangani jika username tidak ada
                'gender': mahasiswa.get('gender', 'N/A'),   # Menangani jika homebase tidak ada
                'prodi': mahasiswa.get('prodi', 'N/A'),  # Menangani jika matakuliah tidak ada
                'jurusan': mahasiswa.get('jurusan', 'N/A'),
                'email': mahasiswa.get('email', 'N/A')
            }
            for key, mahasiswa in mahasiswa_data.items()
        ]

        # Kembalikan data dalam format JSON
        return jsonify(result), 200

    except Exception as e:
        print(f"Terjadi kesalahan: {e}")
        return jsonify({'error': str(e)}), 500


# Route untuk menampilkan halaman registAdmin.php
@app.route('/registAdmin')
def regist_admin():
    with open('registAdmin.php', 'r') as file:
        return render_template_string(file.read())

# Route untuk menerima data formulir dan menambah admin ke Firebase
@app.route('/register_admin', methods=['POST'])
def register_admin():
    try:
        # Ambil data dari request JSON
        data_admin = request.get_json()

        # Ambil input dari form
        username = data_admin.get('username')
        email = data_admin.get('email')
        gender = data_admin.get('gender')
        password = data_admin.get('password')

        # Validasi data (contoh validasi sederhana)
        if not username or not email or not password:
            return jsonify({'error': 'Semua field wajib diisi!'}), 400

        # Validasi format email
        if '@' not in email:
            return jsonify({'error': 'Email tidak valid!'}), 400

        # Simpan data ke Firebase
        ref = db.reference('admin')
        admin_ref = ref.push({
            
            'username': username,
            'email': email,
            'gender': gender,
            'password': password,
            'password': password  # Di dunia nyata, password sebaiknya di-hash!

        })

        # Mengirim response sukses
        return jsonify({'message': 'Admin berhasil ditambahkan!'}), 200

    except Exception as e:
        # Tangani error jika ada
        return jsonify({'error': str(e)}), 500


@app.route('/get_admin', methods=['GET'])
def get_admin():
    try:
        # Ambil data admin dari Firebase
        ref = db.reference('admin')
        admin_data = ref.get()

        # Cek apakah data admin ada
        if not admin_data:
            print("Tidak ada data admin.")
            return jsonify([]), 200  # Kembalikan array kosong jika tidak ada data

        # Format data admin menjadi bentuk yang diinginkan
        result = [
            {
                'id': key,  # Pastikan ID Firebase disertakan
                'username': admin.get('username', 'N/A'),
                'email': admin.get('email', 'N/A'),
                'gender': admin.get('gender', 'N/A')
            }
            for key, admin in admin_data.items()
        ]

        # Kembalikan data dalam format JSON
        return jsonify(result), 200

    except Exception as e:
        print(f"Terjadi kesalahan: {e}")
        return jsonify({'error': str(e)}), 500



    # KODE UNTUK HALAMAN LOGIN
@app.route('/login', methods=['POST'])
def login():
    try:
        # Ambil data dari form
        username = request.form.get('username')
        password = request.form.get('password')

        # Validasi input
        if not username or not password:
            return jsonify({'error': 'Username dan password wajib diisi!'}), 400

        # Periksa pengguna di Firebase
        user_roles = ['admin', 'dosen', 'mahasiswa']
        for role in user_roles:
            ref = db.reference(role)
            users = ref.get()

            if users:
                for user_id, user_data in users.items():
                    if user_data.get('username') == username and user_data.get('password') == password:
                        # Simpan data pengguna di session
                        session['username'] = username
                        session['role'] = role
                        # Respons dengan role dan username
                        return jsonify({'username': username, 'role': role}), 200

        # Jika tidak ditemukan
        return jsonify({'error': 'Username atau password salah!'}), 401

    except Exception as e:
        # Tangani error
        return jsonify({'error': str(e)}), 500





#KODE UNTUK EDIT PROFILE
@app.route('/update_profile', methods=['PATCH'])
def update_profile():
    try:
        data = request.get_json()
        role = data.get('role')
        user_id = data.get('id')  # ID pengguna di Firebase
        updated_data = data.get('data')

        print("Role:", role)
        print("User ID:", user_id)
        print("Updated Data:", updated_data)

        if not role or not user_id or not updated_data:
            return jsonify({'error': 'Role, ID, dan data wajib disediakan!'}), 400

        # Cek referensi Firebase
        ref = db.reference(f'{role}/{user_id}')
        print("Firebase Reference:", ref)

        if not ref.get():
            return jsonify({'error': 'Pengguna tidak ditemukan!'}), 404

        # Update data di Firebase
        ref.update(updated_data)
        return jsonify({'message': 'Profil berhasil diperbarui!', 'updated_data': updated_data}), 200

    except Exception as e:
        return jsonify({'error': str(e)}), 500



@app.route('/get_profile', methods=['POST'])
def get_profile():
    try:
        data = request.get_json()
        role = data.get('role')
        username = data.get('username')

        if not role or not username:
            return jsonify({'error': 'Role dan username wajib disediakan!'}), 400

        ref = db.reference(f'{role}')
        users = ref.get()

        if users:
            for user_id, user_data in users.items():
                if user_data.get('username') == username:
                    user_data['id'] = user_id  # Pastikan ID pengguna ditambahkan
                    print("Found User ID:", user_id)  # Debug ID pengguna
                    return jsonify(user_data), 200

        return jsonify({'error': 'Pengguna tidak ditemukan!'}), 404

    except Exception as e:
        return jsonify({'error': str(e)}), 500



#POPOUP rREkap INII naaae
# Route untuk menerima data formulir dan menambah data ke Firebase
@app.route('/absen', methods=['POST'])
def absen():
    try:
        # Ambil data dari request JSON
        data = request.get_json()
        
        # Ambil input dari form
        programStudi = data.get('programStudi')
        semester = data.get('semester')
        mataKuliah = data.get('mataKuliah')
        dosen = data.get('dosen')
        namaKelas = data.get('namaKelas')
        ruangan = data.get('ruangan')
        jumlahPeserta = data.get('jumlahPeserta')
        hari = data.get('hari')
        jam = data.get('jam')

        # Validasi data (contoh validasi sederhana)
        if not programStudi or not semester or not mataKuliah or not dosen or not namaKelas or not ruangan or not jumlahPeserta  or not hari or not jam:
            return jsonify({'error': 'Semua field wajib diisi!'}), 400

        # Validasi format email
        # if '@' not in email:
        #     return jsonify({'error': 'Email tidak valid!'}), 400

        # Simpan data ke Firebase
        ref = db.reference('absen')
        edit_rekap_ref = ref.push({
            'programStudi': programStudi,
            'semester': semester,
            'mataKuliah': mataKuliah,
            'dosen': dosen,
            'namaKelas': namaKelas,
            'ruangan': ruangan,
            'jumlahPeserta': jumlahPeserta,  # Di dunia nyata, password sebaiknya di-hash!
            'hari': hari,
            'jam': jam
        })

        # Mengirim response sukses
        return jsonify({'message': 'Data absen berhasil ditambahkan!'}), 200

    except Exception as e:
        # Tangani error jika ada
        return jsonify({'error': str(e)}), 500



#POPUP ABSEN REKAP INIIIIIIII 

@app.route('/get_absen', methods=['GET'])
def get_absen():
    try:
        # Ambil data absen dari Firebase
        ref = db.reference('absen')
        absen_data = ref.get()

        # Cek apakah data absen ada
        if not absen_data:
            print("Tidak ada data absen.")
            return jsonify([]), 200  # Kembalikan array kosong jika tidak ada data

        # Format data absen menjadi bentuk yang diinginkan
        result = [
            {
                'programStudi': absen.get('programStudi', 'N/A'),
                'semester': absen.get('semester', 'N/A'),
                'mataKuliah': absen.get('mataKuliah', 'N/A'),
                'dosen': absen.get('dosen', 'N/A'),
                'namaKelas': absen.get('namaKelas', 'N/A'),
                'ruangan': absen.get('ruangan', 'N/A'),
                'jumlahPeserta': absen.get('jumlahPeserta', 'N/A'),
                'hari': absen.get('hari', 'N/A'),
                'jam': absen.get('jam', 'N/A')
            }
            for absen in absen_data.values()
        ]

        # Kembalikan data dalam format JSON
        return jsonify(result), 200

    except Exception as e:
        print(f"Terjadi kesalahan: {e}")
        return jsonify({'error': str(e)}), 500



#COBA POST STATIK
# Route untuk menerima data formulir dan menambah data ke Firebase
@app.route('/statik', methods=['POST'])
def statik():
    try:
        # Ambil data dari request JSON
        data = request.get_json()
        
        # Ambil input dari form
        kelas = data.get('kelas')
        matakuliah = data.get('matakuliah')


        # Validasi data (contoh validasi sederhana)
        if not kelas or not matakuliah:
            return jsonify({'error': 'Semua field wajib diisi!'}), 400


        # Simpan data ke Firebase
        ref = db.reference('statik')
        statik_ref = ref.push({
            'kelas': kelas,
            'matakuliah': matakuliah
            
        })

        # Mengirim response sukses
        return jsonify({'message': 'Data berhasil ditambahkan!'}), 200

    except Exception as e:
        # Tangani error jika ada
        return jsonify({'error': str(e)}), 500




@app.route('/get_statik', methods=['GET'])
def get_statik():
    try:
        # Ambil data statik dari Firebase
        ref = db.reference('statik')
        statik_data2 = ref.get()

        # Cek apakah data statik ada
        if not statik_data2:
            print("Tidak ada data statik.")
            return jsonify([]), 200  # Kembalikan array kosong jika tidak ada data

        # Format data statik menjadi bentuk yang diinginkan
        result = [
            {
                'id': key,   
                'kelas': statik.get('kelas', 'N/A'),  # Menangani jika kelas tidak ada
                'matakuliah': statik.get('matakuliah', 'N/A')  # Menangani jika matakuliah tidak ada
                
            }
            for key, statik in statik_data2.items()
        ]

        # Kembalikan data dalam format JSON
        return jsonify(result), 200

    except Exception as e:
        print(f"Terjadi kesalahan: {e}")
        return jsonify({'error': str(e)}), 500


##route dari daftarDosen.php untuk edit dan hapus 

@app.route('/edit_dosen/<dosen_id>', methods=['PATCH'])
def edit_dosen(dosen_id):
    try:
        data = request.get_json()  # Data dari request
        ref = db.reference(f'dosen/{dosen_id}')

        # Pastikan dosen dengan ID tersebut ada
        if not ref.get():
            return jsonify({'error': 'Dosen tidak ditemukan!'}), 404

        # Update data di Firebase
        ref.update(data)
        return jsonify({'message': 'Data dosen berhasil diperbarui!'}), 200
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/delete_dosen/<dosen_id>', methods=['DELETE'])
def delete_dosen(dosen_id):
    try:
        ref = db.reference(f'dosen/{dosen_id}')

        # Pastikan dosen dengan ID tersebut ada
        if not ref.get():
            return jsonify({'error': 'Dosen tidak ditemukan!'}), 404

        # Hapus data dosen
        ref.delete()
        return jsonify({'message': 'Data dosen berhasil dihapus!'}), 200
    except Exception as e:
        return jsonify({'error': str(e)}), 500



##route dari daftarMaha.php untuk edit dan hapus 

@app.route('/edit_mahasiswa/<mahasiswa_id>', methods=['PATCH'])
def edit_mahasiswa(mahasiswa_id):
    try:
        data = request.get_json()
        ref = db.reference(f'mahasiswa/{mahasiswa_id}')

        if not ref.get():
            return jsonify({'error': f'Mahasiswa dengan ID {mahasiswa_id} tidak ditemukan!'}), 404

        ref.update(data)
        return jsonify({'message': 'Data Mahasiswa berhasil diperbarui!'}), 200
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/delete_mahasiswa/<mahasiswa_id>', methods=['DELETE'])
def delete_mahasiswa(mahasiswa_id):
    try:
        ref = db.reference(f'mahasiswa/{mahasiswa_id}')

        if not ref.get():
            return jsonify({'error': 'Mahasiswa tidak ditemukan!'}), 404

        ref.delete()
        return jsonify({'message': 'Data Mahasiswa berhasil dihapus!'}), 200
    except Exception as e:
        return jsonify({'error': str(e)}), 500




##route dari daftarAdmin.php untuk edit dan hapus 

@app.route('/edit_admin/<admin_id>', methods=['PATCH'])
def edit_admin(admin_id):
    try:
        data = request.get_json()  # Data dari request
        ref = db.reference(f'admin/{admin_id}')

        # Pastikan admin dengan ID tersebut ada
        if not ref.get():
            return jsonify({'error': 'Admin tidak ditemukan!'}), 404

        # Update data di Firebase
        ref.update(data)
        return jsonify({'message': 'Data admin berhasil diperbarui!'}), 200
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/delete_admin/<admin_id>', methods=['DELETE'])
def delete_admin(admin_id):
    try:
        ref = db.reference(f'admin/{admin_id}')

        # Pastikan admin dengan ID tersebut ada
        if not ref.get():
            return jsonify({'error': 'Admin tidak ditemukan!'}), 404

        # Hapus data admin
        ref.delete()
        return jsonify({'message': 'Data Admin berhasil dihapus!'}), 200
    except Exception as e:
        return jsonify({'error': str(e)}), 500



@app.route('/delete_statistik/<statistik_id>', methods=['DELETE'])
def delete_statistik(statistik_id):
    try:
        # Update the reference path to 'statik'
        ref = db.reference(f'statik/{statistik_id}')

        # Ensure the statistik with the given ID exists
        if not ref.get():
            return jsonify({'error': 'Statistik tidak ditemukan!'}), 404

        # Delete the statistik entry
        ref.delete()
        return jsonify({'message': 'Data statistik berhasil dihapus!'}), 200
    except Exception as e:
        return jsonify({'error': str(e)}), 500

    



#untuk rekapAbsensi.php untuk tampilkan isi tabel ceklok ( kode untuk hasil dari IoT)
@app.route('/get_ceklok', methods=['GET'])
def get_ceklok():
    try:
        # Ambil data mahasiswa dari Firebase
        ref = db.reference('ceklok')
        ceklok_data = ref.get()

        # Cek apakah data ceklok ada
        if not ceklok_data:
            print("Tidak ada data ceklok.")
            return jsonify([]), 200  # Kembalikan array kosong jika tidak ada data

        # Format data ceklok menjadi bentuk yang diinginkan
        result = [
            {
                'id': ceklok.get('id', 'N/A'),   # Menangani jika homebase tidak ada
                'nim': ceklok.get('nim', 'N/A'),   # Menangani jika nuptk tidak ada
                'username': ceklok.get('username', 'N/A'),  # Menangani jika username tidak ada
                'absen_count': ceklok.get('absen_count', 'N/A'),  # Menangani jika matakuliah tidak ada
            }
            for ceklok in ceklok_data.values()
        ]

        # Kembalikan data dalam format JSON
        return jsonify(result), 200

    except Exception as e:
        print(f"Terjadi kesalahan: {e}")
        return jsonify({'error': str(e)}), 500


@app.route('/generate_pdf', methods=['GET'])
def generate_pdf():
    try:
        # Retrieve data from Firebase
        ref_absen = db.reference('absen')
        ref_ceklok = db.reference('ceklok')
        absen_data = ref_absen.get()
        ceklok_data = ref_ceklok.get()

        # Create a temporary PDF file
        with tempfile.NamedTemporaryFile(delete=False, suffix=".pdf") as tmp_file:
            pdf = SimpleDocTemplate(tmp_file.name, pagesize=letter)

            # Elements for the PDF
            elements = []

            # Header Title
            from reportlab.lib.styles import getSampleStyleSheet
            styles = getSampleStyleSheet()
            title_style = styles['Title']
            elements.append(Table([[f"DAFTAR HADIR"]], style=[('ALIGN', (0, 0), (-1, -1), 'CENTER')]))
            elements.append(Table([[f" "]]))  # Empty row for spacing

            # Absen Data Section
            if absen_data:
                for absen in absen_data.values():
                    data = [
                        ["Program Studi", absen.get('programStudi', 'N/A')],
                        ["Semester", absen.get('semester', 'N/A')],
                        ["Ruangan", absen.get('ruangan', 'N/A')],
                        ["Mata Kuliah", absen.get('mataKuliah', 'N/A')],
                        ["Dosen", absen.get('dosen', 'N/A')],
                        ["Jumlah Peserta", absen.get('jumlahPeserta', 'N/A')],
                        ["Hari", absen.get('hari', 'N/A')],
                        ["Jam", absen.get('jam', 'N/A')],
                    ]
                    t = Table(data, colWidths=[150, 300])
                    t.setStyle(TableStyle([
                        ('BACKGROUND', (0, 0), (-1, 0), colors.grey),
                        ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
                        ('ALIGN', (0, 0), (-1, -1), 'LEFT'),
                        ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
                        ('BOTTOMPADDING', (0, 0), (-1, 0), 12),
                        ('BACKGROUND', (0, 1), (-1, -1), colors.beige),
                        ('GRID', (0, 0), (-1, -1), 1, colors.black),
                    ]))
                    elements.append(t)
                    elements.append(Table([[f" "]]))  # Empty row for spacing

            # Ceklok Data Section (Attendance)
            if ceklok_data:
                header = ["No", "NIM", "Nama Mahasiswa"] + [str(i) for i in range(1, 17)]
                data = [header]
                no = 1
                for ceklok in ceklok_data.values():
                    row = [no, ceklok.get('nim', 'N/A'), ceklok.get('username', 'N/A')]
                    absen_count = int(ceklok.get('absen_count', 0))
                    row.extend(["✓" if i <= absen_count else "" for i in range(1, 17)])
                    data.append(row)
                    no += 1

                t = Table(data, colWidths=[30, 80, 150] + [20] * 16)
                t.setStyle(TableStyle([
                    ('BACKGROUND', (0, 0), (-1, 0), colors.grey),
                    ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
                    ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
                    ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
                    ('BOTTOMPADDING', (0, 0), (-1, 0), 12),
                    ('BACKGROUND', (0, 1), (-1, -1), colors.beige),
                    ('GRID', (0, 0), (-1, -1), 1, colors.black),
                ]))
                elements.append(t)

            # Build the PDF
            pdf.build(elements)

            # Return the PDF as a downloadable file
            tmp_file.seek(0)
            return send_file(tmp_file.name, as_attachment=True, download_name="rekap_absen.pdf")

    except Exception as e:
        print(f"Error in generate_pdf function: {e}")
        return jsonify({'error': str(e)}), 500


if __name__ == '__main__':
    app.run(debug=True, port=5000)
