#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
🚀 Script de Setup Automático - Galeria de Fotos PHP
Configura automaticamente o ambiente local com XAMPP
"""

import os
import sys
import shutil
import subprocess
import webbrowser
import time
import requests
from pathlib import Path

class Colors:
    """Cores para output colorido no terminal"""
    HEADER = '\033[95m'
    BLUE = '\033[94m'
    GREEN = '\033[92m'
    YELLOW = '\033[93m'
    RED = '\033[91m'
    ENDC = '\033[0m'
    BOLD = '\033[1m'

def print_header(text):
    """Imprime cabeçalho colorido"""
    print(f"\n{Colors.HEADER}{Colors.BOLD}{'='*60}{Colors.ENDC}")
    print(f"{Colors.HEADER}{Colors.BOLD}🚀 {text}{Colors.ENDC}")
    print(f"{Colors.HEADER}{Colors.BOLD}{'='*60}{Colors.ENDC}\n")

def print_success(text):
    """Imprime mensagem de sucesso"""
    print(f"{Colors.GREEN}✅ {text}{Colors.ENDC}")

def print_error(text):
    """Imprime mensagem de erro"""
    print(f"{Colors.RED}❌ {text}{Colors.ENDC}")

def print_warning(text):
    """Imprime mensagem de aviso"""
    print(f"{Colors.YELLOW}⚠️  {text}{Colors.ENDC}")

def print_info(text):
    """Imprime mensagem informativa"""
    print(f"{Colors.BLUE}ℹ️  {text}{Colors.ENDC}")

def check_xampp():
    """Verifica se XAMPP está instalado e rodando"""
    print_info("Verificando XAMPP...")
    
    # Caminhos comuns do XAMPP
    xampp_paths = [
        "C:\\xampp",
        "C:\\xampp\\htdocs",
        "/opt/lampp",
        "/Applications/XAMPP"
    ]
    
    xampp_found = False
    htdocs_path = None
    
    for path in xampp_paths:
        if os.path.exists(path):
            xampp_found = True
            if "htdocs" in path:
                htdocs_path = path
            else:
                htdocs_path = os.path.join(path, "htdocs")
            break
    
    if not xampp_found:
        print_error("XAMPP não encontrado!")
        print_info("Baixe e instale o XAMPP: https://www.apachefriends.org/")
        return None
    
    print_success(f"XAMPP encontrado: {htdocs_path}")
    
    # Verificar se Apache está rodando
    try:
        response = requests.get("http://localhost", timeout=5)
        if response.status_code == 200:
            print_success("Apache está rodando!")
        else:
            print_warning("Apache pode não estar rodando corretamente")
    except:
        print_warning("Apache não está rodando. Inicie o XAMPP Control Panel!")
        input("Pressione Enter após iniciar Apache e MySQL no XAMPP...")
    
    return htdocs_path

def copy_project_files(htdocs_path):
    """Copia arquivos do projeto para htdocs"""
    print_info("Copiando arquivos do projeto...")
    
    project_path = os.path.join(htdocs_path, "galeria")
    
    # Criar diretório se não existir
    if not os.path.exists(project_path):
        os.makedirs(project_path)
        print_success(f"Diretório criado: {project_path}")
    
    # Lista de arquivos para copiar
    files_to_copy = [
        "index.php",
        "ultimos.php", 
        "README.md",
        "gallerydev"
    ]
    
    current_dir = os.getcwd()
    
    for item in files_to_copy:
        source = os.path.join(current_dir, item)
        destination = os.path.join(project_path, item)
        
        if os.path.exists(source):
            if os.path.isdir(source):
                if os.path.exists(destination):
                    shutil.rmtree(destination)
                shutil.copytree(source, destination)
                print_success(f"Pasta copiada: {item}")
            else:
                shutil.copy2(source, destination)
                print_success(f"Arquivo copiado: {item}")
        else:
            print_warning(f"Arquivo não encontrado: {item}")
    
    return project_path

def create_upload_directories(project_path):
    """Cria diretórios de upload necessários"""
    print_info("Criando diretórios de upload...")
    
    upload_dirs = [
        os.path.join(project_path, "gallerydev", "uploads"),
        os.path.join(project_path, "gallerydev", "uploads", "albums"),
        os.path.join(project_path, "gallerydev", "uploads", "thumbs")
    ]
    
    for directory in upload_dirs:
        if not os.path.exists(directory):
            os.makedirs(directory, mode=0o755)
            print_success(f"Diretório criado: {directory}")
        else:
            print_info(f"Diretório já existe: {directory}")

def test_mysql_connection():
    """Testa conexão com MySQL"""
    print_info("Testando conexão MySQL...")
    
    try:
        response = requests.get("http://localhost/phpmyadmin", timeout=5)
        if response.status_code == 200:
            print_success("MySQL/phpMyAdmin acessível!")
            return True
        else:
            print_warning("phpMyAdmin não acessível")
            return False
    except:
        print_warning("MySQL pode não estar rodando")
        return False

def run_installation():
    """Executa a instalação via web"""
    print_info("Executando instalação do banco de dados...")
    
    install_url = "http://localhost/galeria/gallerydev/install.php"
    
    try:
        # Verificar se página de instalação está acessível
        response = requests.get(install_url, timeout=10)
        if response.status_code == 200:
            print_success("Página de instalação acessível!")
            print_info("Abrindo navegador para completar instalação...")
            webbrowser.open(install_url)
            
            print("\n" + "="*60)
            print("🔧 COMPLETE A INSTALAÇÃO NO NAVEGADOR:")
            print("1. Configure usuário admin (padrão: admin/admin123)")
            print("2. Clique em 'Instalar Sistema'")
            print("3. Aguarde confirmação de sucesso")
            print("="*60)
            
            input("\nPressione Enter após completar a instalação...")
            return True
        else:
            print_error("Página de instalação não acessível")
            return False
    except Exception as e:
        print_error(f"Erro ao acessar instalação: {e}")
        return False

def test_gallery():
    """Testa se a galeria está funcionando"""
    print_info("Testando galeria...")
    
    urls_to_test = [
        ("http://localhost/galeria/", "Página Principal"),
        ("http://localhost/galeria/ultimos.php", "Página Últimos"),
        ("http://localhost/galeria/gallerydev/admin/", "Painel Admin")
    ]
    
    all_working = True
    
    for url, name in urls_to_test:
        try:
            response = requests.get(url, timeout=5)
            if response.status_code == 200:
                print_success(f"{name} funcionando!")
            else:
                print_error(f"{name} com problemas (Status: {response.status_code})")
                all_working = False
        except Exception as e:
            print_error(f"{name} inacessível: {e}")
            all_working = False
    
    return all_working

def open_gallery():
    """Abre a galeria no navegador"""
    print_info("Abrindo galeria no navegador...")
    
    urls_to_open = [
        "http://localhost/galeria/",
        "http://localhost/galeria/gallerydev/admin/"
    ]
    
    for url in urls_to_open:
        webbrowser.open(url)
        time.sleep(1)

def create_development_shortcuts():
    """Cria atalhos úteis para desenvolvimento"""
    print_info("Criando atalhos de desenvolvimento...")
    
    shortcuts_content = """
# 🔗 ATALHOS ÚTEIS - GALERIA DE FOTOS

## URLs Principais:
- Galeria Principal: http://localhost/galeria/
- Últimos Álbuns: http://localhost/galeria/ultimos.php
- Painel Admin: http://localhost/galeria/gallerydev/admin/
- phpMyAdmin: http://localhost/phpmyadmin/

## Credenciais Padrão:
- Admin: admin / admin123

## Caminhos Importantes:
- Projeto: C:\\xampp\\htdocs\\galeria\\
- Uploads: C:\\xampp\\htdocs\\galeria\\gallerydev\\uploads\\
- Config: C:\\xampp\\htdocs\\galeria\\gallerydev\\includes\\config.php

## Comandos Úteis:
- Reiniciar Apache: XAMPP Control Panel → Stop/Start Apache
- Ver logs: C:\\xampp\\apache\\logs\\error.log
- Backup BD: phpMyAdmin → Export

## Para Desenvolvimento:
1. Editar arquivos em C:\\xampp\\htdocs\\galeria\\
2. Salvar arquivo
3. Atualizar navegador (F5)
4. Verificar funcionamento
"""
    
    with open("ATALHOS-DESENVOLVIMENTO.txt", "w", encoding="utf-8") as f:
        f.write(shortcuts_content)
    
    print_success("Arquivo ATALHOS-DESENVOLVIMENTO.txt criado!")

def main():
    """Função principal do script"""
    print_header("SETUP AUTOMÁTICO - GALERIA DE FOTOS PHP")
    
    print("🎯 Este script vai configurar automaticamente:")
    print("   • Copiar arquivos para XAMPP")
    print("   • Criar diretórios necessários") 
    print("   • Configurar banco de dados")
    print("   • Testar funcionamento")
    print("   • Abrir galeria no navegador")
    
    input("\nPressione Enter para continuar...")
    
    # 1. Verificar XAMPP
    htdocs_path = check_xampp()
    if not htdocs_path:
        print_error("Setup cancelado - XAMPP não encontrado")
        return False
    
    # 2. Copiar arquivos
    project_path = copy_project_files(htdocs_path)
    
    # 3. Criar diretórios
    create_upload_directories(project_path)
    
    # 4. Testar MySQL
    mysql_ok = test_mysql_connection()
    if not mysql_ok:
        print_warning("MySQL pode ter problemas - continue mesmo assim")
    
    # 5. Executar instalação
    if run_installation():
        print_success("Instalação iniciada!")
    else:
        print_error("Problema na instalação")
        return False
    
    # 6. Aguardar um pouco para instalação
    print_info("Aguardando instalação...")
    time.sleep(3)
    
    # 7. Testar galeria
    if test_gallery():
        print_success("Galeria funcionando perfeitamente!")
    else:
        print_warning("Alguns problemas detectados - verifique manualmente")
    
    # 8. Criar atalhos
    create_development_shortcuts()
    
    # 9. Abrir navegador
    open_gallery()
    
    # 10. Mensagem final
    print_header("SETUP CONCLUÍDO!")
    print(f"{Colors.GREEN}🎉 Galeria de fotos configurada com sucesso!{Colors.ENDC}")
    print(f"\n{Colors.BOLD}📍 PRÓXIMOS PASSOS:{Colors.ENDC}")
    print("1. Complete a instalação no navegador (se ainda não fez)")
    print("2. Faça login no admin (admin/admin123)")
    print("3. Crie seus primeiros álbuns")
    print("4. Faça upload das suas fotos")
    print("5. Personalize o design conforme necessário")
    
    print(f"\n{Colors.BOLD}🔗 LINKS ÚTEIS:{Colors.ENDC}")
    print("• Galeria: http://localhost/galeria/")
    print("• Admin: http://localhost/galeria/gallerydev/admin/")
    print("• phpMyAdmin: http://localhost/phpmyadmin/")
    
    print(f"\n{Colors.BOLD}📁 ARQUIVOS:{Colors.ENDC}")
    print(f"• Projeto: {project_path}")
    print("• Atalhos: ATALHOS-DESENVOLVIMENTO.txt")
    
    return True

if __name__ == "__main__":
    try:
        success = main()
        if success:
            print(f"\n{Colors.GREEN}✨ Setup concluído com sucesso!{Colors.ENDC}")
        else:
            print(f"\n{Colors.RED}💥 Setup falhou - verifique os erros acima{Colors.ENDC}")
    except KeyboardInterrupt:
        print(f"\n{Colors.YELLOW}⏹️  Setup cancelado pelo usuário{Colors.ENDC}")
    except Exception as e:
        print_error(f"Erro inesperado: {e}")
        print_info("Execute o setup manual seguindo SETUP-LOCAL.txt")