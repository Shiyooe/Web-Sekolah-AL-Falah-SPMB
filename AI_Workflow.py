# workflow_qwen.py
import torch
from transformers import AutoModelForCausalLM, AutoTokenizer
from typing import List, Dict, Any
import json

class QwenWorkflow:
    def __init__(self, model_name: str = "Qwen/Qwen2.5-0.5B"):
        """
        Inisialisasi model Qwen
        
        Args:
            model_name: Nama model Qwen yang akan digunakan
        """
        self.model_name = model_name
        self.device = "cuda" if torch.cuda.is_available() else "cpu"
        
        print(f"Loading model {model_name} on {self.device}...")
        
        # Load tokenizer dan model
        self.tokenizer = AutoTokenizer.from_pretrained(model_name, trust_remote_code=True)
        self.model = AutoModelForCausalLM.from_pretrained(
            model_name,
            torch_dtype=torch.float16 if self.device == "cuda" else torch.float32,
            device_map="auto",
            trust_remote_code=True
        )
        
        print("Model loaded successfully!")
    
    def generate_response(self, 
                         prompt: str, 
                         max_length: int = 512,
                         temperature: float = 0.7,
                         top_p: float = 0.9) -> str:
        """
        Generate response dari prompt
        
        Args:
            prompt: Input text
            max_length: Panjang maksimum output
            temperature: Kontrol kreativitas (0.1-1.0)
            top_p: Nucleus sampling parameter
        
        Returns:
            Generated text
        """
        # Tokenize input
        inputs = self.tokenizer(prompt, return_tensors="pt").to(self.device)
        
        # Generate response
        with torch.no_grad():
            outputs = self.model.generate(
                **inputs,
                max_length=max_length,
                temperature=temperature,
                top_p=top_p,
                do_sample=True,
                pad_token_id=self.tokenizer.eos_token_id
            )
        
        # Decode response
        response = self.tokenizer.decode(outputs[0], skip_special_tokens=True)
        
        # Remove the prompt from response
        if response.startswith(prompt):
            response = response[len(prompt):].strip()
        
        return response
    
    def chat_workflow(self, system_prompt: str = None) -> None:
        """
        Workflow chat interaktif
        
        Args:
            system_prompt: Prompt sistem untuk mengatur perilaku AI
        """
        print("\n" + "="*50)
        print("Qwen AI Chat Workflow")
        print("="*50)
        print("Ketik 'quit' untuk keluar")
        
        if system_prompt:
            print(f"System: {system_prompt}")
        
        conversation_history = []
        
        while True:
            user_input = input("\nYou: ").strip()
            
            if user_input.lower() in ['quit', 'exit', 'keluar']:
                print("Terima kasih telah menggunakan Qwen AI!")
                break
            
            if not user_input:
                continue
            
            # Build prompt dengan history
            if system_prompt:
                full_prompt = f"System: {system_prompt}\n\nUser: {user_input}\nAssistant:"
            else:
                full_prompt = f"User: {user_input}\nAssistant:"
            
            # Tambahkan history conversation jika ada
            if conversation_history:
                history_text = "\n".join(conversation_history[-4:])  # Ambil 4 pesan terakhir
                full_prompt = f"{history_text}\n{full_prompt}"
            
            print("AI sedang memproses...")
            
            try:
                response = self.generate_response(
                    prompt=full_prompt,
                    max_length=800,
                    temperature=0.7
                )
                
                print(f"AI: {response}")
                
                # Simpan ke history
                conversation_history.append(f"User: {user_input}")
                conversation_history.append(f"Assistant: {response}")
                
            except Exception as e:
                print(f"Error: {e}")
                print("Silakan coba lagi...")

# Contoh workflow khusus
class SpecializedWorkflows:
    def __init__(self, qwen_workflow: QwenWorkflow):
        self.qwen = qwen_workflow
    
    def content_creation_workflow(self, topic: str, content_type: str = "artikel") -> str:
        """
        Workflow untuk pembuatan konten
        
        Args:
            topic: Topik konten
            content_type: Jenis konten (artikel, blog, social_media, dll)
        """
        prompts = {
            "artikel": f"Buat artikel yang informatif tentang {topic}. Sertakan pendahuluan, isi, dan kesimpulan.",
            "blog": f"Tulis postingan blog yang engaging tentang {topic}. Gunakan tone yang friendly dan informatif.",
            "social_media": f"Buat caption media sosial yang menarik tentang {topic}. Maksimal 150 kata.",
            "email": f"Tulis email profesional tentang {topic}. Sertakan subject line yang menarik."
        }
        
        prompt = prompts.get(content_type, prompts["artikel"])
        return self.qwen.generate_response(prompt)
    
    def translation_workflow(self, text: str, target_language: str = "English") -> str:
        """
        Workflow untuk translasi teks
        """
        prompt = f"Terjemahkan teks berikut ke dalam bahasa {target_language}: '{text}'"
        return self.qwen.generate_response(prompt)
    
    def code_generation_workflow(self, requirement: str, language: str = "python") -> str:
        """
        Workflow untuk generate kode
        """
        prompt = f"Buat kode {language} untuk: {requirement}. Sertakan komentar penjelasan."
        return self.qwen.generate_response(prompt, temperature=0.3)

# Contoh penggunaan
def main():
    # Inisialisasi workflow
    qwen_workflow = QwenWorkflow("Qwen/Qwen2.5-0.5B")  # Gunakan model yang lebih kecil untuk demo
    
    # Workflow khusus
    specialized = SpecializedWorkflows(qwen_workflow)
    
    print("Pilih workflow:")
    print("1. Chat Interaktif")
    print("2. Pembuatan Konten")
    print("3. Translasi")
    print("4. Generate Kode")
    
    choice = input("Masukkan pilihan (1-4): ").strip()
    
    if choice == "1":
        system_prompt = "Anda adalah asisten AI yang helpful dan informatif. Jawablah dalam bahasa Indonesia."
        qwen_workflow.chat_workflow(system_prompt)
    
    elif choice == "2":
        topic = input("Masukkan topik: ")
        content_type = input("Jenis konten (artikel/blog/social_media/email): ") or "artikel"
        result = specialized.content_creation_workflow(topic, content_type)
        print(f"\nHasil:\n{result}")
    
    elif choice == "3":
        text = input("Masukkan teks untuk diterjemahkan: ")
        language = input("Bahasa target: ") or "English"
        result = specialized.translation_workflow(text, language)
        print(f"\nHasil translasi:\n{result}")
    
    elif choice == "4":
        requirement = input("Deskripsi kode yang diinginkan: ")
        language = input("Bahasa pemrograman: ") or "python"
        result = specialized.code_generation_workflow(requirement, language)
        print(f"\nKode generated:\n{result}")
    
    else:
        print("Pilihan tidak valid")

if __name__ == "__main__":
    main()