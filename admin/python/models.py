# pendaftaran_app/models.py

from django.db import models

class Pendaftaran(models.Model):
    # Kolom id_pendaftaran akan otomatis dibuat sebagai Primary Key oleh Django
    tgl_daftar = models.DateField()
    nama = models.CharField(max_length=255)
    tempat_lahir = models.TextField()
    tanggal_lahir = models.DateField()
    anak_ke = models.IntegerField()
    # Pilihan untuk jenis_kelamin
    JENIS_KELAMIN_CHOICES = [
        ('Laki-laki', 'Laki-laki'),
        ('Perempuan', 'Perempuan'),
    ]
    jenis_kelamin = models.CharField(
        max_length=10, 
        choices=JENIS_KELAMIN_CHOICES
    )
    alamat = models.TextField()
    telepon = models.CharField(max_length=30, null=True, blank=True)
    asal_sekolah = models.CharField(max_length=255)
    nisn = models.BigIntegerField()
    hobby = models.CharField(max_length=255)
    citacita = models.CharField(max_length=255)
    # Kolom lainnya... (Dipotong untuk fokus pada `nama` dan `jenis_kelamin`)
    
    # Atur nama tabel agar sesuai dengan skema SQL
    class Meta:
        db_table = 'pendaftaran'
        
    def __str__(self):
        return self.nama