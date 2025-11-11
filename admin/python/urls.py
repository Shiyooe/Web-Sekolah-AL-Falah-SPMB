# pendaftaran_app/urls.py

from django.urls import path
from . import views

urlpatterns = [
    path('pendaftar/', views.daftar_pendaftar, name='daftar_pendaftar'),
]