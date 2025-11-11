# pendaftaran_app/views.py

from django.shortcuts import render
from .models import Pendaftaran

def daftar_pendaftar(request):
    # Ambil semua objek dari model Pendaftaran
    semua_pendaftar = Pendaftaran.objects.all()
    
    # Siapkan data (context) untuk dikirim ke template
    context = {
        'pendaftar_list': semua_pendaftar
    }
    
    # Render template 'daftar.html' dengan data pendaftar
    return render(request, 'daftar.html', context)